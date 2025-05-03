class LabelNote {
    static instance = null;

    constructor() {
        if (LabelNote.instance) {
            console.log('Returning existing LabelNotes instance');
            return LabelNote.instance;
        }
        console.log('Creating new LabelNotes instance');
        LabelNote.instance = this;

        this.currentPage = 1;
        this.limit = 10;
        this.isLoadingTrash = false;
        this.lastScrollTop = 0;

        this.setupEvents();
    }

    setupEvents() {
        document.removeEventListener('click', this.handleNoteClick);
        document.removeEventListener('click', this.handleDeleteClick);

        this.handleNoteClick = (event) => {
            const deleteBtn = event.target.closest(".note-delete-btn, .pinned-note-delete-btn");
            const pinBtn = event.target.closest(".note-pin-btn");
            const unpinBtn = event.target.closest(".pinned-note-pin-btn");
            const noteEl = event.target.closest('.note-sheet');

            if (!noteEl) return;

            const note = {
                noteId: noteEl.dataset.noteId || noteEl.dataset.id,
                title: noteEl.dataset.noteTitle || noteEl.dataset.title,
                content: noteEl.dataset.noteContent || noteEl.dataset.content,
            };

            if (deleteBtn) {
                console.log('Clicked delete button:', note);
                this.expandDeleteNote(note); // Show modal
                return;
            }

            if (pinBtn) {
                console.log('Clicked pin button:', note);
                this.pinNote_POST(note.noteId, note.title, note.content);
                return;
            }

            if (unpinBtn) {
                console.log('Clicked unpin button:', note);
                this.unpinNote_POST(note.noteId, note.title, note.content);
                return;
            }

            // Prevent expanding the note when clicking buttons inside .note-sheet__menu
            if (event.target.closest('.note-sheet__menu button')) return;

            console.log('Clicked note:', note);
            this.expandNote(note);
        };

        document.addEventListener('click', this.handleNoteClick);
        console.log('Attached note click listener');
        document.addEventListener('click', this.handleDeleteClick);

        // Attach scroll event listener
        window.addEventListener('scroll', this.handleScroll.bind(this));
        console.log('Attached scroll listener');
    }

    handleScroll() {
        const currentScrollTop = window.scrollY;
        if (currentScrollTop > this.lastScrollTop &&
            (window.innerHeight + currentScrollTop >= document.body.offsetHeight - 200)) {
            // this.loadNotes();
        }
        this.lastScrollTop = Math.max(currentScrollTop, 0);
    }

    showToast(message, type = 'danger', duration = 2000) {
        const toast = document.getElementById("toast");
        const messageElement = document.getElementById("toast-message");
        const closeBtn = document.getElementById("toast-close");

        if (!toast || !messageElement || !closeBtn) return;

        messageElement.innerText = message;
        toast.classList.remove("d-none", "bg-success", "bg-danger");
        toast.classList.add(`bg-${type}`);

        toast.classList.remove("d-none");

        const hideTimeout = setTimeout(() => toast.classList.add("d-none"), duration);
    }

    loadNewNotes() {
        if (this.isLoadingNotes) return;
        this.isLoadingNotes = true;
        this.currentPage = 1;

        fetch(`/note/list?page=${this.currentPage}&limit=${this.limit}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('accessToken')
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.data?.length > 0) {
                    this.appendNotesToDOM(data.data);
                    this.currentPage++;
                }
            })
            .catch(err => {
                console.error('Fetch failed:', err);
                this.showToast('Failed to load notes. Please try again.');
            })
            .finally(() => this.isLoadingNotes = false);
    }

    appendNotesToDOM(notes) {
        const container = document.querySelector(".other-note__load");
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
                div.dataset.noteImage = note.imageLink;
            }

            const imageHTML = note.imageLink && note.imageLink.trim() !== ''
                ? `<div class="note-sheet__image" style="width: 100%; height: auto; overflow: hidden">
                   <img src="${note.imageLink}" style="width: 100%; height: auto; display: block">
               </div>`
                : '';

            div.innerHTML = `
            ${imageHTML}
            <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                <h3 class="note-sheet__title">${note.title}</h3>
                <div class="note-sheet__content">
                    ${note.content.replace(/\n/g, '<br>')}
                </div>
            </div>
            <div class="note-sheet__menu">
                <div>
                    <button class="note-pin-btn" title="Pin Note"><i class="fa-solid fa-thumbtack"></i></button>
                    <button title="Add Label"><i class="fa-solid fa-tags"></i></button>
                    <button class="note-delete-btn" title="Delete This Note" data-bs-target="deleteNoteModal" data-note-id="${note.noteId}"><i class="fa-solid fa-trash"></i></button>
                    <button title="Share this Note"><i class="fa-solid fa-users"></i></button>
                    <button title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
                </div>
            </div>
        `;

            container.appendChild(div);
            console.log(`Appended note: ${note.noteId}`);
        });
    }

    expandNote(note) {
        const modalEl = document.getElementById('noteModal');
        const modal = new bootstrap.Modal(modalEl);
        const noteId = note.noteId;

        const titleInput = modalEl.querySelector('.note-title-input-autosave');
        const contentInput = modalEl.querySelector('.note-content-input-autosave');
        const icon = modalEl.querySelector('.save-status-icon i');
        const iconText = modalEl.querySelector('.save-status-icon p');

        titleInput.value = note.title || '';
        contentInput.value = note.content || '';
        icon.className = 'fa-solid fa-check-circle text-success';
        iconText.innerHTML = 'Saved';

        modal.show();
        // Setup auto-save functionality
        this.setupAutoSaveModal(noteId, titleInput, contentInput, icon, iconText);
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
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
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
                    const { status, noteId, title, content } = data;
                    if (status === true) {
                        showSavedIcon();
                        console.log(data);
                        const noteElement = document.querySelector(`.note-sheet[data-note-id="${noteId}"]`);
                        if (noteElement) {
                            noteElement.querySelector('.note-sheet__title').textContent = title;
                            noteElement.querySelector('.note-sheet__content').innerHTML = content.replace(/\n/g, '<br>');
                            noteElement.dataset.noteTitle = title;
                            noteElement.dataset.noteContent = content;
                        } else {
                            if (!document.querySelector(`.note-sheet[data-note-id="${noteId}"]`)) {
                                const newNote = this.noteSheetModel(noteId, title, content);
                                // document.querySelector('.other-note__load')?.prepend(newNote);
                                console.log(`Prepended note: ${noteId}`);
                            }
                        }
                        const pinNoteGrid = document.querySelector('.pinned-note__load');
                        const otherNoteGrid = document.querySelector('.other-note__load');
                        pinNoteGrid.innerHTML = '';
                        otherNoteGrid.innerHTML = '';
                        this.loadNewPinnedNotes();
                        this.loadNewNotes();
                        // const otherNoteGrid = document.querySelector('.other-note__load');
                        // otherNoteGrid.innerHTML = '';
                        // otherNoteGrid.loadNotes();
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
            timeout = setTimeout(autoSave, 300); // Delay 300ms
        };

        titleInput.removeEventListener('input', this.handleTyping);
        contentInput.removeEventListener('input', this.handleTyping);
        this.handleTyping = handleTyping;
        titleInput.addEventListener('input', handleTyping);
        contentInput.addEventListener('input', handleTyping);
    }

    expandDeleteNote(note) {
        const modalEl = document.getElementById('deleteNoteModal');
        const modal = new bootstrap.Modal(modalEl);
        const confirmBtn = modalEl.querySelector('#confirmDeleteNoteBtn');

        modal.show();

        // Cleanup any old event listeners to prevent duplicates
        const newConfirmHandler = () => {
            this.deleteNote_POST(note.noteId, note.title, note.content);
            confirmBtn.removeEventListener('click', newConfirmHandler); // prevent multiple bindings
            modal.hide();
        };

        confirmBtn.addEventListener('click', newConfirmHandler);
    }

    deleteNote_POST(noteId, title, content) {
        fetch(`/note/delete`, {
            method: 'POST',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ noteId })
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === true) {
                    this.showToast("Note deleted successfully", "success");
                    const noteEl = document.querySelector(`.note-sheet[id="${noteId}"]`);
                    if (noteEl) noteEl.remove(); // Remove from DOM
                    const labelNoteGrid = document.querySelector('.label-note__load');
                    console.log(noteId, title, content);
                    labelNoteGrid.innerHTML = '';
                    try {
                        console.log("Calling loadNewNotes()");
                        this.loadNewNotes();
                    } catch (e) {
                        console.error("loadNewNotes() failed:", e);
                    }
                } else {
                    this.showToast(data.message || "Failed to delete note", "danger");
                }
            })
            .catch(err => {
                console.error("Delete error:", err);
                this.showToast("An error occurred while deleting the note", "danger");
            });
    }

}

const labelNotesInstance = new LabelNote();
export default labelNotesInstance;