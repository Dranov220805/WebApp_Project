class TrashNotes {
    static instance = null;

    constructor() {
        if (TrashNotes.instance) {
            // console.log('Returning existing TrashNotes instance');
            return TrashNotes.instance;
        }

        // console.log('Creating new TrashNotes instance');
        TrashNotes.instance = this;

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
            const noteEl = event.target.closest('.note-sheet-trash');

            if (!noteEl) return;

            const note = {
                noteId: noteEl.dataset.noteId || noteEl.dataset.id,
                title: noteEl.dataset.noteTitle || noteEl.dataset.title,
                content: noteEl.dataset.noteContent || noteEl.dataset.content,
                imageLink: noteEl.dataset.noteImage  || noteEl.dataset.image,
            };

            if (deleteBtn) {
                this.expandDeleteNote(note);
                return;
            }

            if (restoreBtn) {
                this.expandRestoreNote(note);
                return;
            }

            // Prevent expanding the note when clicking buttons inside .note-sheet-trash__menu
            if (event.target.closest('.note-sheet-trash__menu button')) return;

            this.expandTrashNote(note);
        };

        document.addEventListener('click', this.handleNoteClick);
        // console.log('Attached note click listener');
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

    loadTrashedNotes({ reset = false } = {}) {
        if (this.isLoadingTrash) return;
        this.isLoadingTrash = true;

        if (reset) this.currentPage = 1;

        fetch(`/note/trash-list?page=${this.currentPage}&limit=${this.limit}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => {
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

    appendTrashedNotesToDOM(notes) {
        const container = document.querySelector(".trash-note__load");
        if (!container) return;

        notes.forEach(note => {
            if (document.querySelector(`.note-sheet-trash[data-note-id="${note.noteId}"]`)) {
                // console.log(`Skipping duplicate note: ${note.noteId}`);
                return;
            }

            const div = document.createElement("div");
            div.className = "note-sheet-trash d-flex";
            div.id = note.noteId;
            div.dataset.noteId = note.noteId;
            div.dataset.noteTitle = note.title;
            div.dataset.noteContent = note.content;
            div.dataset.imageLink = note.imageLink || '';

            const imageHTML = note.imageLink?.trim()
                ? `<div class="note-sheet-trash__image" style="">
                     <img src="${note.imageLink}" style="">
                   </div>`
                : '';

            div.innerHTML = `
                ${imageHTML}
                <div class="note-sheet-trash__title-content flex-column flex-grow-1" style="padding: 16px;">
                    <h3 class="note-sheet-trash__title">${note.title}</h3>
                    <div class="note-sheet-trash__content" style="overflow-x: hidden">
                        ${note.content.replace(/\n/g, '<br>')}
                    </div>
                </div>
                <div class="note-sheet-trash__menu">
                    <div>
                        <button class="note-restore-btn" title="Restore this note" data-note-id="${note.noteId}">
                            <i class="fa-solid fa-trash-arrow-up"></i>
                        </button>
                        <button class="note-trash-delete-btn" title="Delete Permanently" data-bs-target="deleteNoteModal" data-note-id="${note.noteId}">
                            <i class="fa-solid fa-eraser"></i>
                        </button>
                    </div>
                </div>
            `;

            container.appendChild(div);
            // console.log(`Appended note: ${note.noteId}`);
        });
    }

    expandRestoreNote(note) {
        const modalEl = document.getElementById('restoreTrashNoteModal');
        const modal = new bootstrap.Modal(modalEl);
        const confirmBtn = modalEl.querySelector('#confirmRestoreTrashNoteBtn');

        const titleDisplay = modalEl.querySelector('.note-title');
        if (titleDisplay) titleDisplay.innerText = note.title;

        modal.show();

        const newConfirmHandler = () => {
            this.restoreNote_POST(note.noteId);
            confirmBtn.removeEventListener('click', newConfirmHandler);
            modal.hide();
        };

        confirmBtn.addEventListener('click', newConfirmHandler);
    }

    restoreNote_POST(noteId) {
        fetch(`/note/restore`, {
            method: 'PUT',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ noteId })
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === true) {
                    this.showToast("Note restored successfully", "success");

                    const noteEl = document.getElementById(noteId);
                    if (noteEl) noteEl.remove();

                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    document.querySelector('.trash-note__load').innerHTML = '';
                    this.loadTrashedNotes({ reset: true });
                } else {
                    this.showToast(data.message || "Failed to restore note", "danger");
                }
            })
            .catch(err => {
                console.error("Restore error:", err);
                this.showToast("An error occurred while restoring the note", "danger");
            });
    }

    expandDeleteNote(note) {
        const modalEl = document.getElementById('deleteTrashNoteModal');
        const modal = new bootstrap.Modal(modalEl);
        const confirmBtn = modalEl.querySelector('#confirmDeleteTrashNoteBtn');

        const titleDisplay = modalEl.querySelector('.note-title');
        if (titleDisplay) titleDisplay.innerText = note.title;

        modal.show();

        const newConfirmHandler = () => {
            this.deleteNote_POST(note.noteId);
            confirmBtn.removeEventListener('click', newConfirmHandler);
            modal.hide();
        };

        confirmBtn.addEventListener('click', newConfirmHandler);
    }

    deleteNote_POST(noteId) {
        fetch(`/note/hard-delete`, {
            method: 'DELETE',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ noteId })
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === true) {
                    this.showToast("Note deleted successfully", "success");

                    const noteEl = document.getElementById(noteId);
                    if (noteEl) noteEl.remove();

                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    document.querySelector('.trash-note__load').innerHTML = '';
                    this.loadTrashedNotes({ reset: true });
                } else {
                    this.showToast(data.message || "Failed to delete note", "danger");
                }
            })
            .catch(err => {
                console.error("Delete error:", err);
                this.showToast("An error occurred while deleting the note", "danger");
            });
    }

    expandTrashNote(note) {
        const modalEl = document.getElementById('noteTrashModal');
        const modal = new bootstrap.Modal(modalEl);
        const noteId = note.noteId;

        const imageLink = modalEl.querySelector('.note-sheet__image');
        const titleInput = modalEl.querySelector('.note-title-input-autosave');
        const contentInput = modalEl.querySelector('.note-content-input-autosave');

        imageLink.innerHTML = note.imageLink ? `<img src="${note.imageLink}" style="width: 100%; height: auto; display: block">` : '';
        titleInput.value = note.title || '';
        contentInput.value = note.content || '';

        // Store noteId and image DOM ref on class instance
        this.currentNoteId = noteId;
        this.imageLinkRef = imageLink;

        const inputTextarea = modalEl.querySelector('.note-content-input-autosave');
        inputTextarea.style.height = '100%';

        // Block editing if user doesn't have permission
        const canEdit = note.canEdit === 'true' || note.canEdit === true;

        titleInput.readOnly = !canEdit;
        contentInput.readOnly = !canEdit;
        modal.show();
    }
}

const trashNotesInstance = new TrashNotes();
export default trashNotesInstance;
