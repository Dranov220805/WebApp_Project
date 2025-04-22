class Notes {
    constructor() {
        this.currentPage = 1;
        this.limit = 10;
        this.isLoading = false;
        this.isLoadingPinned = false;
        this.lastScrollTop = 0;

        this.setupEvents();
        this.loadPinnedNotes();
        this.loadNotes();
    }

    setupEvents() {
        const createNoteBtn = document.querySelector(".create-note-btn");
        const noteInput = document.querySelector(".note-post__input");
        const noteModalClose = document.querySelector(".note-modal-close");
        const notePinBtn = document.querySelector(".note-pin-btn");

        if (createNoteBtn) createNoteBtn.addEventListener("click", () => this.createNote_POST());
        if (noteInput) noteInput.addEventListener("input", this.autoResizeInput);
        if (notePinBtn) notePinBtn.addEventListener("click", () => this.pinNewNote());


        window.addEventListener("scroll", () => this.handleScroll());
        this.initNotePostTrigger();
    }

    handleScroll() {
        const currentScrollTop = window.scrollY;
        if (currentScrollTop > this.lastScrollTop &&
            (window.innerHeight + currentScrollTop >= document.body.offsetHeight - 200)) {
            this.loadNotes();
        }
        this.lastScrollTop = Math.max(currentScrollTop, 0);
    }

    showToast(message, type = 'danger', duration = 3000) {
        const toast = document.getElementById("toast");
        const messageElement = document.getElementById("toast-message");
        const closeBtn = document.getElementById("toast-close");

        if (!toast || !messageElement || !closeBtn) return;

        messageElement.innerText = message;
        toast.classList.remove("d-none", "bg-success", "bg-danger");
        toast.classList.add(`bg-${type}`);

        toast.classList.remove("d-none");

        const hideTimeout = setTimeout(() => toast.classList.add("d-none"), duration);
        closeBtn.onclick = () => {
            toast.classList.add("d-none");
            clearTimeout(hideTimeout);
        };
    }

    loadNotes() {
        if (this.isLoading) return;
        this.isLoading = true;

        fetch(`/note/list?page=${this.currentPage}&limit=${this.limit}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('accessToken')
            }
        })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                if (data.data?.length > 0) {
                    this.appendNotesToDOM(data.data);
                    this.currentPage++;
                }
            })
            .catch(err => {
                console.error('Fetch failed:', err);
                this.showToast('Failed to load notes. Please try again.');
            })
            .finally(() => this.isLoading = false);
    }

    appendNotesToDOM(notes) {
        const container = document.querySelector(".other-note__load");
        if (!container) return;

        notes.forEach(note => {
            const div = document.createElement("div");
            div.className = "note-sheet d-flex flex-column";
            div.dataset.id = note.noteId;
            // div.onclick = () => this.expandNote(div);
            div.onclick = () => this.openNoteInModal(note);


            div.innerHTML = `
            <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                <h3 class="note-sheet__title">${note.title}</h3>
                <div class="note-sheet__content">
                    ${note.content.replace(/\n/g, '<br>')}
                </div>
            </div>
            <div class="note-sheet__menu" onclick="event.stopPropagation()">
                <div>
                    <button class="note-pin-btn" title="Pin Note"><i class="fa-solid fa-thumbtack"></i></button>
                    <button title="Label"><i class="fa-solid fa-tags"></i></button>
                    <button title="Image"><i class="fa-solid fa-images"></i></button>
                    <button class="note-edit-btn" title="Edit"><i class="fa-regular fa-pen-to-square"></i></button>
                    <button class="note-delete-btn" title="Delete"><i class="fa-solid fa-trash"></i></button>
                </div>
                <button><i class="fa-solid fa-ellipsis-vertical"></i></button>
            </div>
        `;
            // Append to container
            container.appendChild(div);
        });
    }

    loadPinnedNotes() {
        if (this.isLoadingPinned) return;
        this.isLoadingPinned = true;

        fetch(`/note/pinned/list`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('accessToken')
            }
        })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                if (data.data?.length > 0) {
                    this.appendPinnedNotesToDOM(data.data);
                    this.currentPage++;
                }
            })
            .catch(err => {
                console.error('Fetch failed:', err);
                this.showToast('Failed to load notes. Please try again.');
            })
            .finally(() => this.isLoading = false);
    }

    appendPinnedNotesToDOM(notes) {
        const container = document.querySelector(".pinned-note__load");
        if (!container) return;

        notes.forEach(note => {
            const div = document.createElement("div");
            div.className = "note-sheet d-flex flex-column";
            div.dataset.id = note.noteId;
            // div.onclick = () => this.expandNote(div);
            div.onclick = () => this.openNoteInModal(note);


            div.innerHTML = `
            <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                <h3 class="note-sheet__title">${note.title}</h3>
                <div class="note-sheet__content">
                    ${note.content.replace(/\n/g, '<br>')}
                </div>
            </div>
            <div class="note-sheet__menu" onclick="event.stopPropagation()">
                <div>
                    <button class="pinned-note-pin-btn" title="Unpin Note"><i class="fa-solid fa-thumbtack"></i></button>
                    <button title="Label"><i class="fa-solid fa-tags"></i></button>
                    <button title="Image"><i class="fa-solid fa-images"></i></button>
                    <button class="pinned-note-edit-btn" title="Edit"><i class="fa-regular fa-pen-to-square"></i></button>
                    <button class="pinned-note-delete-btn" title="Delete"><i class="fa-solid fa-trash"></i></button>
                </div>
                <button><i class="fa-solid fa-ellipsis-vertical"></i></button>
            </div>
        `;
            // Append to container
            container.appendChild(div);
        });
    }

    loadNewNotes() {
        if (this.isLoading) return;
        this.isLoading = true;

        fetch(`/note/list?page=${1}&limit=${this.limit}`, {
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
            .finally(() => this.isLoading = false);
    }

    autoResizeInput(event) {
        event.target.style.height = 'auto';
        event.target.style.height = `${event.target.scrollHeight}px`;
    }

    openNoteModal(title, content) {
        // Update the modal with the note title and content
        document.getElementById("noteModalLabel").textContent = title;
        document.getElementById("modalContent").innerHTML = content
            .split(',')
            .map(item => `<div>- ${item.trim()}</div>`)
            .join('');
    }

    initNotePostTrigger() {
        const inputArea = document.querySelector(".note-post__input");
        const titleField = document.querySelector(".note-text__content");

        if (!inputArea || !titleField || inputArea.dataset.listenerAttached === "true") return;
        inputArea.dataset.listenerAttached = "true";

        // Resize inputArea dynamically as user types
        inputArea.addEventListener("input", function () {
            const value = this.value.trim();

            if (value !== "") {
                // Show the title field if hidden
                if (titleField.classList.contains("d-none")) {
                    requestAnimationFrame(() => {
                        titleField.style.height = "23px";
                        titleField.style.opacity = "1";
                        titleField.classList.remove("d-none");
                    });
                }

                // Auto-resize the inputArea (optional)
                this.style.height = "auto";
                this.style.height = this.scrollHeight + "px";
            } else {
                // Hide the title field
                titleField.style.height = "0px";
                titleField.style.opacity = "0";
                titleField.classList.add("d-none");

                // Optional: clear title input when hidden
                titleField.addEventListener("transitionend", function handleEnd() {
                    titleField.value = "";
                    titleField.removeEventListener("transitionend", handleEnd);
                });
            }
        });

        // Auto-resize titleField as user types
        titleField.addEventListener("input", function () {
            this.style.height = "auto";
            this.style.height = this.scrollHeight + "px";
        });
    }

    openNoteInModal(note) {
        const modalEl = document.getElementById('noteModal');
        const modal = new bootstrap.Modal(modalEl);

        // Fill modal fields
        const titleInput = modalEl.querySelector('.note-title-input');
        const contentInput = modalEl.querySelector('.note-content-input');
        const icon = modalEl.querySelector('.save-status-icon i');

        titleInput.value = note.title;
        contentInput.value = note.content;
        icon.className = 'fa-solid fa-check-circle text-success';

        // Show modal
        modal.show();

        // Setup auto-save (reuse logic)
        this.setupAutoSaveModal(note.noteId, titleInput, contentInput, icon);
    }

    setupAutoSaveModal(noteId, titleInput, contentInput, icon) {
        let timeout = null;
        let isSaving = false;

        const showSavingIcon = () => {
            icon.className = 'fa-solid fa-spinner fa-spin text-warning';
        };

        const showSavedIcon = () => {
            icon.className = 'fa-solid fa-check-circle text-success';
        };

        const showErrorIcon = () => {
            icon.className = 'fa-solid fa-exclamation-circle text-danger';
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
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        showSavedIcon();
                        document.querySelector(".other-note__load").innerHTML = '';
                        this.currentPage = 1;
                        this.loadNewNotes();
                    }
                    else showErrorIcon();
                })
                .catch(showErrorIcon)
                .finally(() => {
                    isSaving = false;
                });
        };

        const handleTyping = () => {
            showSavingIcon();
            clearTimeout(timeout);
            timeout = setTimeout(autoSave, 1000);
        };

        titleInput.addEventListener('input', handleTyping);
        contentInput.addEventListener('input', handleTyping);
    }

    createNote_POST() {
        const titleInput = document.querySelector(".note-text__content");
        const contentInput = document.querySelector(".note-post__input");

        const title = titleInput?.value.trim() || '';
        const content = contentInput?.value.trim() || '';

        console.log(titleInput);
        console.log(contentInput);
        console.log(title);
        console.log(content);

        if (!title || !content) {
            this.showToast('Please enter a title and content', 'danger');
            return;
        }

        fetch('note/create', {
            method: 'POST',
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                title,
                content
            })
        })
            .then(response => response.json())
            .then(data => {
                const { status, accountId, title, content, createdDate, message} = data;
                if (status === true) {
                    this.showToast('Note created successfully!', 'success');
                    titleInput.remove();
                    contentInput.value = '';
                    // this.appendNewNotesToDOM(data);
                    document.querySelector(".other-note__load").innerHTML = '';
                    this.currentPage = 1;
                    this.loadNotes();
                } else {
                    this.showToast(data.message || 'Failed to create note.', 'danger');
                }
            })
            .catch(err => {
                console.error('Create note error:', err);
                this.showToast('An error occurred while creating the note.', 'danger');
            });
    }

    deleteNote_POST(noteId, noteElement) {
        const modalEl = document.getElementById('deleteNoteModal');
        const confirmBtn = modalEl.querySelector('#confirmDeleteBtn');
        const modal = new bootstrap.Modal(modalEl);

        const onConfirm = () => {
            fetch(`/note/delete`, {
                method: 'POST',
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ noteId })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === true) {
                        this.showToast("Note deleted successfully", "success");
                        noteElement.remove(); // Remove from DOM
                    } else {
                        this.showToast(data.message || "Failed to delete note", "danger");
                    }
                })
                .catch(err => {
                    console.error("Delete error:", err);
                    this.showToast("An error occurred while deleting the note", "danger");
                })
                .finally(() => {
                    confirmBtn.removeEventListener("click", onConfirm);
                    modal.hide();
                });
        };

        confirmBtn.addEventListener("click", onConfirm);
        modal.show();
    }

    expandNote(noteElement) {
        const noteId = noteElement.dataset.id;
        const note = this.notes.find(n => n.noteId === noteId);

        const editorHTML = `
        <div class="note-editor" data-id="${note.noteId}">
            <input class="note-title-input" value="${note.title}" />
            <textarea class="note-content-input">${note.content}</textarea>
            <div class="save-status-icon"><i class="fa-solid fa-check-circle text-success"></i></div>
        </div>
    `;

        const target = document.querySelector(".note-editor-container");
        target.innerHTML = editorHTML;

        this.setupAutoSave(note.noteId);
    }

    pinNewNote() {

    }

}

// Initialize
const notesInstance = new Notes();
window.refreshNotes = () => notesInstance.loadNotes();
