import { NoteCollaborator } from './noteCollaborator.js';

class ShareNotes {
    static instance = null;

    constructor() {
        if (ShareNotes.instance) {
            console.log('Returning existing ShareNotes instance');
            return ShareNotes.instance;
        }

        console.log('Creating new ShareNotes instance');
        ShareNotes.instance = this;

        this.currentPage = 1;
        this.limit = 10;
        this.isLoadingTrash = false;
        this.lastScrollTop = 0;
        this.scrollThrottle = false;

        this.noteCollaborator = null;
        this.currentNoteId = null;
        this.imageLinkRef = null;

        this.setupEvents();
    }

    setupEvents() {
        document.removeEventListener('click', this.handleNoteClick);

        this.handleNoteClick = (event) => {
            const deleteBtn = event.target.closest(".note-trash-delete-btn");
            const restoreBtn = event.target.closest(".note-restore-btn");
            const noteEl = event.target.closest('.shared-note-card');

            if (!noteEl) return;

            const note = {
                noteId: noteEl.dataset.noteId || noteEl.dataset.id,
                title: noteEl.dataset.noteTitle || noteEl.dataset.title,
                content: noteEl.dataset.noteContent || noteEl.dataset.content,
                imageLink: noteEl.dataset.noteImage  || noteEl.dataset.image,
                canEdit: noteEl.dataset.noteEdit || noteEl.dataset.canEdit
            };


            if (event.target.closest('.note-sheet-trash__menu button')) return;

            console.log('Clicked note:', note);
            this.expandShareNote(note);
        };

        document.addEventListener('click', this.handleNoteClick);
        console.log('Attached note click listener');

        // window.addEventListener('scroll', this.handleScroll.bind(this));
        // console.log('Attached scroll listener');



        const myTextarea = document.querySelector('.note-content-input-autosave');
        if (myTextarea) {
            const autoResizeTextarea = (textarea) => {
                textarea.style.height = '100%';
                textarea.style.minHeight = '300px';
                textarea.style.height = textarea.scrollHeight + 'px'; // Set to scroll height
            };

            myTextarea.addEventListener('input', () => autoResizeTextarea(myTextarea));

            autoResizeTextarea(myTextarea);
        }
    }

    handleScroll() {
        const currentScrollTop = window.scrollY;

        if (
            currentScrollTop > this.lastScrollTop &&
            window.innerHeight + currentScrollTop >= document.body.offsetHeight - 200
        ) {
            if (!this.scrollThrottle) {
                this.scrollThrottle = true;
                this.loadTrashedNotes();
                setTimeout(() => this.scrollThrottle = false, 300);
            }
        }

        this.lastScrollTop = Math.max(currentScrollTop, 0);
    }

    showToast(message, type = 'success') {
        const toastEl = document.getElementById('shareToast');
        const toastBody = document.getElementById('shareToastMessage');

        // Set toast message
        toastBody.textContent = message;

        // Change toast background color based on type
        toastEl.className = `toast align-items-center text-white bg-${type} border-0`;

        // Create a Bootstrap Toast instance with autoHide enabled
        const toast = new bootstrap.Toast(toastEl, {
            delay: 1000,      // Duration in milliseconds
            autohide: true    // Enables auto-hiding
        });

        toast.show();
    }

    loadSharedNotes() {

        fetch(`/note/share-list`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                if (data.data?.length > 0) {
                    this.appendTrashedNotesToDOM(data.data);
                    this.currentPage++;
                }
            })
            .catch(err => {
                console.error('Fetch failed:', err);
                this.showToast('Failed to load notes. Please try again.');
            })
            .finally(() => this.isLoadingTrash = false);
    }

    appendSharedNotesToDOM(notes) {
        const container = document.querySelector(".label-note__load");
        if (!container) return;

        // Group notes by noteId and aggregate labels (just like in PHP)
        const groupedNotes = {};
        notes.forEach(note => {
            const noteId = note.noteId;
            if (!groupedNotes[noteId]) {
                groupedNotes[noteId] = { ...note, labels: [] };
            }
            if (note.labelName && !groupedNotes[noteId].labels.includes(note.labelName)) {
                groupedNotes[noteId].labels.push(note.labelName);
            }
        });

        Object.values(groupedNotes).forEach(note => {
            if (document.querySelector(`.shared-note-card[data-note-id="${note.noteId}"]`)) {
                console.log(`Skipping duplicate note: ${note.noteId}`);
                return;
            }

            const noteDiv = document.createElement("div");
            noteDiv.className = "col-12";

            const canEditText = note.canEdit ? "Can edit" : "Read-only";
            const accessClass = note.canEdit ? "access-edit" : "access-readonly";

            const labelsHTML = (note.labels || []).map(label =>
                label ? `<span class="badge bg-secondary me-1">${label}</span>` : ''
            ).join("");

            const imageHTML = note.imageLink && note.imageLink.trim() !== ''
                ? `<div class="note--share__image">
                    <img src="${note.imageLink}" class="rounded" alt="User Avatar" style="margin-top: 0px; height: 100%; width: auto">
               </div>`
                : '';

            const labelAttr = JSON.stringify(note.labels || []);

            noteDiv.innerHTML = `
            <div class="card shared-note-card" style="max-height: 230px"
                data-note-id="${note.noteId}"
                data-note-title="${note.title || ''}"
                data-note-content="${note.content || ''}"
                ${note.imageLink ? `data-note-image="${note.imageLink}"` : ''}
                ${note.labels.length > 0 ? `data-note-labels='${labelAttr}'` : ''}
                data-note-edit="${note.canEdit ? 'true' : 'false'}">
                <div class="card-body">
                    <div class="d-flex justify-content-start" style="width: 100%; max-width: 100%;">
                        <div style="width: 100%">
                            <div class="small mb-1 note--share__by">
                                Shared by <strong>${note.sharedEmail}</strong>
                            </div>
                            <div class="small mb-2 note--share__time">
                                Shared on ${new Date(note.timeShared).toLocaleDateString('en-US', {
                month: 'short', day: '2-digit', year: 'numeric'
            })}
                            </div>
                            ${note.title ? `<h6 class="fw-bold note--share__title">${note.title}</h6>` : ''}
                            ${note.content ? `<p class="mb-1 note--share__content" style="overflow-y: hidden; max-height: 48px; padding-right: 20%">${note.content}</p>` : ''}
                            ${labelsHTML ? `<div class="mt-2">${labelsHTML}</div>` : ''}
                        </div>
                        <div class="text-end" style="display: flex; flex-direction: column; width: 120px; justify-content: space-between; align-items: end">
                            <span class="access-label ${accessClass}" style="width: fit-content">${canEditText}</span>
                            ${imageHTML}
                        </div>
                    </div>
                </div>
            </div>
        `;

            container.appendChild(noteDiv);
        });
    }

    expandShareNote(note) {
        console.log(note);
        const modalEl = document.getElementById('noteShareModal');
        const modal = new bootstrap.Modal(modalEl);
        const noteId = note.noteId;

        const imageLink = modalEl.querySelector('.note-sheet__image');
        const titleInput = modalEl.querySelector('.note-title-input-autosave');
        const contentInput = modalEl.querySelector('.note-content-input-autosave');
        const icon = modalEl.querySelector('.save-status-icon i');
        const iconText = modalEl.querySelector('.save-status-icon p');

        imageLink.innerHTML = note.imageLink ? `<img src="${note.imageLink}" style="width: 100%; height: auto; display: block">` : '';
        titleInput.value = note.title || '';
        contentInput.value = note.content || '';
        icon.className = 'fa-solid fa-check-circle text-success';
        iconText.innerHTML = 'Saved';

        // Store noteId and image DOM ref on class instance
        this.currentNoteId = noteId;
        this.imageLinkRef = imageLink;

        const triggerUploadBtn = modalEl.querySelector('#triggerImageUpload');
        const triggerDeleteBtn = modalEl.querySelector('#triggerImageDelete');
        const imageInput = modalEl.querySelector('#imageInput');
        const noteIdInput = modalEl.querySelector('#noteIdInput');
        const inputTextarea = modalEl.querySelector('.note-content-input-autosave');

        inputTextarea.style.height = '100%';
        imageInput.dataset.noteId = noteId;
        noteIdInput.value = noteId;
        noteIdInput.dataset.imageUrl = note.imageLink || '';

        // Block editing if user doesn't have permission
        const canEdit = note.canEdit === 'true' || note.canEdit === true;

        titleInput.readOnly = !canEdit;
        contentInput.readOnly = !canEdit;
        triggerUploadBtn.disabled = !canEdit;
        triggerDeleteBtn.disabled = !canEdit;

        // Setup auto-save mechanism if editable
        if (canEdit) {
            this.setupAutoSaveModal(noteId, titleInput, contentInput, icon, iconText);
        }

        // Setup collaboration regardless of edit permission (for viewing changes)
        // Disconnect previous instance if exists
        if (this.noteCollaborator) {
            this.noteCollaborator.disconnect();
        }

        // Create new collaborator instance
        this.noteCollaborator = new NoteCollaborator(noteId, titleInput, contentInput);

        // Connect to WebSocket server
        this.noteCollaborator.connect();

        // Add event listener to disconnect WebSocket when modal is closed
        modalEl.addEventListener('hidden.bs.modal', () => {
            if (this.noteCollaborator) {
                this.noteCollaborator.disconnect();
            }
        }, { once: true });

        modal.show();
    }
    
    setupAutoSaveModal(noteId, titleInput, contentInput, icon, iconText) {
        let timeout = null;
        let isSaving = false;

        const showSavingIcon = () => {
            icon.className = 'fa-solid fa-spinner fa-spin text-warning';
            iconText.innerHTML = 'Saving...';
            iconText.className = 'text-warning';
        };

        const showSavedIcon = () => {
            icon.className = 'fa-solid fa-check-circle text-success';
            iconText.innerHTML = 'Saved';
            iconText.className = 'text-success';
        };

        const showErrorIcon = () => {
            icon.className = 'fa-solid fa-exclamation-circle text-danger';
            iconText.innerHTML = 'Error';
            iconText.className = 'text-danger';
        };

        const autoSave = () => {
            if (isSaving) return;
            isSaving = true;
            showSavingIcon();

            fetch('/note/update', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                },
                body: JSON.stringify({
                    noteId,
                    title: titleInput.value,
                    content: contentInput.value
                })
            })
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP error ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    if (data.status === true) {
                        showSavedIcon();
                        const noteCard = document.querySelector(`[data-note-id="${noteId}"]`);
                        if (noteCard) {
                            noteCard.dataset.noteTitle = titleInput.value;
                            noteCard.dataset.noteContent = contentInput.value;

                            const titleEl = noteCard.querySelector('.note--share__title');
                            const contentEl = noteCard.querySelector('.note--share__content');
                            if (titleEl) titleEl.textContent = titleInput.value;
                            if (contentEl) contentEl.textContent = contentInput.value;
                        }
                    } else {
                        showErrorIcon();
                        this.showToast('Failed to save note.', 'warning');
                    }
                })
                .catch(() => {
                    showErrorIcon();
                    this.showToast('Failed to save note.', 'warning');
                })
                .finally(() => {
                    isSaving = false;
                });
        };

        const handleTyping = () => {
            clearTimeout(timeout);
            showSavingIcon();
            timeout = setTimeout(autoSave, 300);
        };

        // Prevent duplicate listeners
        titleInput.removeEventListener('input', this.handleTyping);
        contentInput.removeEventListener('input', this.handleTyping);
        this.handleTyping = handleTyping;

        titleInput.addEventListener('input', handleTyping);
        contentInput.addEventListener('input', handleTyping);
    }

}

const ShareNotesInstance = new ShareNotes();
export default ShareNotesInstance;
