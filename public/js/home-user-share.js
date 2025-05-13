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
            const noteEl = event.target.closest('.note-sheet-trash');

            if (!noteEl) return;

            const note = {
                noteId: noteEl.dataset.noteId || noteEl.dataset.id,
                title: noteEl.dataset.noteTitle || noteEl.dataset.title,
                content: noteEl.dataset.noteContent || noteEl.dataset.content,
            };

            // if (deleteBtn) {
            //     console.log('Clicked delete button:', note);
            //     this.expandDeleteNote(note);
            //     return;
            // }
            //
            // if (restoreBtn) {
            //     console.log('Clicked restore button:', note);
            //     this.expandRestoreNote(note);
            //     return;
            // }

            // Prevent expanding the note when clicking buttons inside .note-sheet-trash__menu
            if (event.target.closest('.note-sheet-trash__menu button')) return;

            console.log('Clicked note:', note);
        };

        document.addEventListener('click', this.handleNoteClick);
        console.log('Attached note click listener');

        // window.addEventListener('scroll', this.handleScroll.bind(this));
        // console.log('Attached scroll listener');
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

    loadSharedNotes({ reset = false } = {}) {
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
}

const ShareNotesInstance = new ShareNotes();
export default ShareNotesInstance;
