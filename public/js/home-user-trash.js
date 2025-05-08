class TrashNotes {
    static instance = null;

    constructor() {
        if (TrashNotes.instance) {
            console.log('Returning existing TrashNotes instance');
            return TrashNotes.instance;
        }
        console.log('Creating new TrashNotes instance');
        TrashNotes.instance = this;

        this.currentPage = 1;
        this.limit = 10;
        this.isLoadingTrash = false;
        this.lastScrollTop = 0;

        this.setupEvents();
    }

    setupEvents() {
        // Remove existing listeners to prevent duplicates
        document.removeEventListener('click', this.handleNoteClick);
        document.removeEventListener('click', this.handleDeleteClick);

        this.handleNoteClick = (event) => {
            const deleteBtn = event.target.closest(".note-trash-delete-btn");
            const restoreBtn = event.target.closest(".note-restore-btn");
            const noteEl = event.target.closest('.note-sheet-trash');

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

            if (restoreBtn) {
                console.log('Clicked restore button:', note);
                this.expandRestoreNote(note);
                return;
            }

            // Prevent expanding the note when clicking buttons inside .note-sheet__menu
            if (event.target.closest('.note-sheet__menu button')) return;

            console.log('Clicked note:', note);
            this.ex
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

    loadTrashedNotes() {
        if (this.isLoadingTrash) return;
        this.isLoadingTrash = true;
        this.currentPage = 1;

        fetch(`/note/trash-list?page=${this.currentPage}&limit=${this.limit}`, {
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

    appendTrashedNotesToDOM(notes) {
        const container = document.querySelector(".trash-note__load");
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
            div.dataset.imageLink = note.imageLink;

            if (note.imageLink && note.imageLink.trim() !== '') {
                div.dataset.imageLink = note.imageLink;
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
                        <button class="note-restore-btn" title="Restore this note"><i class="fa-solid fa-trash-arrow-up"></i></i></button>
                        <button class="note-trash-delete-btn" title="Delete Permanently" data-bs-target="deleteNoteModal" data-note-id="${note.noteId}"><i class="fa-solid fa-eraser"></i></button>
                    </div>
                </div>
            `;
            container.appendChild(div);
            console.log(`Appended note: ${note.noteId}`);
        });
    }

    expandRestoreNote(note) {
        const modalEl = document.getElementById('restoreTrashNoteModal');
        const modal = new bootstrap.Modal(modalEl);
        const confirmBtn = modalEl.querySelector('#confirmRestoreTrashNoteBtn');

        modal.show();

        // Cleanup any old event listeners to prevent duplicates
        const newConfirmHandler = () => {
            this.restoreNote_POST(note.noteId, note.title, note.content);
            confirmBtn.removeEventListener('click', newConfirmHandler); // prevent multiple bindings
            modal.hide();
        };

        confirmBtn.addEventListener('click', newConfirmHandler);
    }

    restoreNote_POST(noteId, title, content) {
        fetch(`/note/restore`, {
            method: 'PUT',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ noteId })
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === true) {
                    this.showToast("Note restored successfully", "success");
                    const noteEl = document.querySelector(`.note-sheet[id="${noteId}"]`);
                    if (noteEl) noteEl.remove(); // Remove from DOM
                    const trashNoteGrid = document.querySelector('.trash-note__load');
                    console.log(noteId, title, content);
                    trashNoteGrid.innerHTML = '';
                    this.loadTrashedNotes();

                } else {
                    this.showToast(data.message || "Failed to restore note", "danger");
                }
            })
            .catch(err => {
                console.error("Delete error:", err);
                this.showToast("An error occurred while restoring the note", "danger");
            });
    }

    expandDeleteNote(note) {
        const modalEl = document.getElementById('deleteTrashNoteModal');
        const modal = new bootstrap.Modal(modalEl);
        const confirmBtn = modalEl.querySelector('#confirmDeleteTrashNoteBtn');

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
        fetch(`/note/hard-delete`, {
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
                    const trashNoteGrid = document.querySelector('.trash-note__load');
                    console.log(noteId, title, content);
                    trashNoteGrid.innerHTML = '';
                    this.loadTrashedNotes();

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

const trashNotesInstance = new TrashNotes();
export default trashNotesInstance;