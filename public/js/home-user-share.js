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

        const autoResizeTextarea = (textarea) => {
            textarea.style.height = '100%';
            textarea.style.minHeight = '300px';
            textarea.style.height = textarea.scrollHeight + 'px'; // Set to scroll height
        };

        const myTextarea = document.querySelector('.note-content-input-autosave');
        myTextarea.addEventListener('input', () => autoResizeTextarea(myTextarea));

        autoResizeTextarea(myTextarea);
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
        const container = document.querySelector(".share-note__load");
        if (!container) return;

        notes.forEach(note => {
            if (document.querySelector(`.note-sheet[data-note-id="${note.noteId}"]`)) {
                console.log(`Skipping duplicate note: ${note.noteId}`);
                return;
            }

            const div = document.createElement("div");
            div.className = "note-sheet d-flex flex-column";
            div.dataset.noteId = note.noteId;
            div.dataset.noteTitle = note.title;
            div.dataset.noteContent = note.content;

            if (note.imageLink && note.imageLink.trim() !== '') {
                div.dataset.imageLink = note.imageLink;
            }

            if (note.labels && note.labels.length > 0) {
                div.dataset.labels = JSON.stringify(note.labels);
            }

            let labels = [];
            if (div.dataset.labels) {
                try {
                    labels = JSON.parse(div.dataset.labels);
                } catch (e) {
                    console.error("Failed to parse labels:", e);
                }
            }

            const imageHTML = note.imageLink && note.imageLink.trim() !== ''
                ? `<div class="note-sheet__image" style="width: 100%; height: auto; overflow-y: visible">
                   <img src="${note.imageLink}" style="width: 100%; height: auto; display: block">
               </div>`
                : '';

            div.innerHTML = `
            ${imageHTML}
            <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                <h3 class="note-sheet__title">${note.title}</h3>
                <div class="note-sheet__content" style="overflow-x: hidden">
                    ${note.content.replace(/\n/g, '<br>')}
                </div>
            </div>
            <div class="note-sheet__menu">
                <div>
                    <button class="note-pin-btn" title="Pin Note"><i class="fa-solid fa-thumbtack-slash"></i></button>
                    <button title="Add Label" data-bs-target="listLabelNoteModal" id="note-label-list-btn" class="note-label-list-btn"><i class="fa-solid fa-tags"></i></button>
                    <button class="note-delete-btn" title="Delete This Note" data-bs-target="deleteNoteModal" data-note-id="${note.noteId}"><i class="fa-solid fa-trash"></i></button>
                    <button class="note-share-btn" title="Share this Note"><i class="fa-solid fa-users"></i></button>
                    <button class="note-lock-btn" title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
                </div>
            </div>
        `;

            container.appendChild(div);
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

        modal.show();

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

        // Optionally style fields differently if not editable
        if (!canEdit) {
            // titleInput.classList.add('bg-light');
            // contentInput.classList.add('bg-light');
        } else {
            // titleInput.classList.remove('bg-light');
            // contentInput.classList.remove('bg-light');
        }
    }

}

const ShareNotesInstance = new ShareNotes();
export default ShareNotesInstance;
