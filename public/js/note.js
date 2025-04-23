class Notes {
    static instance = null;

    constructor() {
        if (Notes.instance) {
            console.log('Returning existing Notes instance');
            return Notes.instance;
        }
        console.log('Creating new Notes instance');
        Notes.instance = this;

        this.currentPage = 1;
        this.limit = 10;
        this.isLoading = false;
        this.isLoadingPinned = false;
        this.lastScrollTop = 0;

        this.setupEvents();
        this.loadNotes();
    }

    setupEvents() {
        // Remove existing listeners to prevent duplicates
        document.removeEventListener('click', this.handleNoteClick);
        document.removeEventListener('click', this.handleDeleteClick);

        this.handleNoteClick = (event) => {
            const noteEl = event.target.closest('.note-sheet');
            if (noteEl) {
                const note = {
                    noteId: noteEl.dataset.noteId,
                    title: noteEl.dataset.noteTitle,
                    content: noteEl.dataset.noteContent
                };
                console.log('Clicked note:', note);
                this.expandNote(note);
            }
        };

        this.handleDeleteClick = (event) => {
            const deleteBtn = event.target.closest(".note-delete-btn, .pinned-note-delete-btn");
            if (deleteBtn) {
                const noteId = deleteBtn.dataset.noteId;
                const note = { noteId };
                this.deleteNoteInModal(note);
            }
        };

        document.addEventListener('click', this.handleNoteClick);
        console.log('Attached note click listener');
        document.addEventListener('click', this.handleDeleteClick);

        const createNoteBtn = document.querySelector(".create-note-btn");
        const noteInput = document.querySelector(".note-post__input");
        const notePinBtn = document.querySelector(".note-pin-btn");

        if (createNoteBtn) {
            createNoteBtn.removeEventListener("click", this.createNoteHandler);
            this.createNoteHandler = () => this.createNote_POST();
            createNoteBtn.addEventListener("click", this.createNoteHandler);
        }
        if (noteInput) {
            noteInput.removeEventListener("input", this.autoResizeInput);
            noteInput.addEventListener("input", this.autoResizeInput);
        }
        if (notePinBtn) {
            notePinBtn.removeEventListener("click", this.pinNoteHandler);
            this.pinNoteHandler = () => this.pinNewNote();
            notePinBtn.addEventListener("click", this.pinNoteHandler);
        }

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
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
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
            if (document.querySelector(`.note-sheet[data-note-id="${note.noteId}"]`)) {
                console.log(`Skipping duplicate note: ${note.noteId}`);
                return;
            }

            const div = document.createElement("div");
            div.className = "note-sheet d-flex flex-column";
            div.dataset.noteId = note.noteId;
            div.dataset.noteTitle = note.title;
            div.dataset.noteContent = note.content;

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
                        <button class="note-delete-btn" title="Delete" data-note-id="${note.noteId}"><i class="fa-solid fa-trash"></i></button>
                    </div>
                    <button><i class="fa-solid fa-ellipsis-vertical"></i></button>
                </div>
            `;
            container.appendChild(div);
            console.log(`Appended note: ${note.noteId}`);
        });
    }

    // loadPinnedNotes() {
    //     if (this.isLoadingPinned) return;
    //     this.isLoadingPinned = true;
    //
    //     fetch(`/note/pinned/list`, {
    //         method: 'GET',
    //         headers: {
    //             'Content-Type': 'application/json'
    //         }
    //     })
    //         .then(res => res.json())
    //         .then(data => {
    //             console.log(data);
    //             if (data.data?.length > 0) {
    //                 this.appendPinnedNotesToDOM(data.data);
    //                 this.currentPage++;
    //             }
    //         })
    //         .catch(err => {
    //             console.error('Fetch failed:', err);
    //             this.showToast('Failed to load notes. Please try again.');
    //         })
    //         .finally(() => this.isLoading = false);
    // }

    // appendPinnedNotesToDOM(notes) {
    //     const container = document.querySelector(".pinned-note__load");
    //     if (!container) return;
    //
    //     notes.forEach(note => {
    //         const div = document.createElement("div");
    //         div.className = "note-sheet d-flex flex-column";
    //         div.dataset.id = note.noteId;
    //         // div.onclick = () => this.expandNote(div);
    //         div.onclick = () => this.openNoteInModal(note);
    //
    //
    //         div.innerHTML = `
    //         <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
    //             <h3 class="note-sheet__title">${note.title}</h3>
    //             <div class="note-sheet__content">
    //                 ${note.content.replace(/\n/g, '<br>')}
    //             </div>
    //         </div>
    //         <div class="note-sheet__menu" onclick="event.stopPropagation()">
    //             <div>
    //                 <button class="pinned-note-pin-btn" title="Unpin Note"><i class="fa-solid fa-thumbtack"></i></button>
    //                 <button title="Label"><i class="fa-solid fa-tags"></i></button>
    //                 <button title="Image"><i class="fa-solid fa-images"></i></button>
    //                 <button class="pinned-note-edit-btn" title="Edit"><i class="fa-regular fa-pen-to-square"></i></button>
    //                 <button class="pinned-note-delete-btn" title="Delete" data-note-id="${note.noteId}"><i class="fa-solid fa-trash"></i></button>
    //             </div>
    //             <button><i class="fa-solid fa-ellipsis-vertical"></i></button>
    //         </div>
    //     `;
    //         // Append to container
    //         container.appendChild(div);
    //     });
    // }

    // loadNewNotes() {
    //     if (this.isLoading) return;
    //     this.isLoading = true;
    //
    //     fetch(`/note/list?page=${1}&limit=${this.limit}`, {
    //         headers: {
    //             'Authorization': 'Bearer ' + localStorage.getItem('accessToken')
    //         }
    //     })
    //         .then(res => res.json())
    //         .then(data => {
    //             if (data.data?.length > 0) {
    //                 this.appendNotesToDOM(data.data);
    //                 this.currentPage++;
    //             }
    //         })
    //         .catch(err => {
    //             console.error('Fetch failed:', err);
    //             this.showToast('Failed to load notes. Please try again.');
    //         })
    //         .finally(() => this.isLoading = false);
    // }

    autoResizeInput(event) {
        event.target.style.height = 'auto';
        event.target.style.height = `${event.target.scrollHeight}px`;
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

    // openNoteInModal(note) {
    //     const modalEl = document.getElementById('noteModal');
    //     const modal = new bootstrap.Modal(modalEl);

    //     // Ensure modal elements exist
    //     if (!modalEl) {
    //         console.error("Modal element not found");
    //         return;
    //     }

    //     const titleInput = modalEl.querySelector('.note-title-input');
    //     const contentInput = modalEl.querySelector('.note-content-input');
    //     const icon = modalEl.querySelector('.save-status-icon i');
    //     const iconText = modalEl.querySelector('.save-status-icon p');

    //     if (!titleInput || !contentInput || !icon || !iconText) {
    //         console.error("Modal fields are missing");
    //         return;
    //     }

    //     // Populate modal fields with note data
    //     titleInput.value = note.title || '';
    //     contentInput.value = note.content || '';
    //     icon.className = 'fa-solid fa-check-circle text-success';
    //     iconText.innerHTML = 'Saved';

    //     // Show the modal
    //     modal.show();

    //     // Setup auto-save functionality for the modal
    //     this.setupAutoSaveModal(note.noteId, titleInput, contentInput, icon, iconText);
    // }

    expandNote(note) {
        const modalEl = document.getElementById('noteModal');
        const modal = new bootstrap.Modal(modalEl);
        const noteId = note.noteId;

        const titleInput = modalEl.querySelector('.note-title-input');
        const contentInput = modalEl.querySelector('.note-content-input');
        const icon = modalEl.querySelector('.save-status-icon i');
        const iconText = modalEl.querySelector('.save-status-icon p');

        titleInput.value = note.title || '';
        contentInput.value = note.content || '';
        icon.className = 'fa-solid fa-check-circle text-success';
        iconText.innerHTML = 'Saved';

        // Show the modal
        modal.show();
        // Setup auto-save functionality for the modal
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
                                document.querySelector('.other-note__load')?.prepend(newNote);
                                console.log(`Prepended note: ${noteId}`);
                            }
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
            timeout = setTimeout(autoSave, 3000);
        };

        titleInput.removeEventListener('input', this.handleTyping);
        contentInput.removeEventListener('input', this.handleTyping);
        this.handleTyping = handleTyping;
        titleInput.addEventListener('input', handleTyping);
        contentInput.addEventListener('input', handleTyping);
    }

    createNote_POST() {
        const titleInput = document.querySelector(".note-text__content");
        const contentInput = document.querySelector(".note-post__input");

        const title = titleInput?.value.trim() || '';
        const content = contentInput?.value.trim() || '';

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
                const { status, noteId, title, content, message } = data;
                if (status === true) {
                    this.showToast('Note created successfully!', 'success');
                    titleInput.value = '';
                    contentInput.value = '';
                    if (!document.querySelector(`.note-sheet[data-note-id="${noteId}"]`)) {
                        const newNote = this.noteSheetModel(noteId, title, content);
                        document.querySelector(".other-note__load")?.prepend(newNote);
                        console.log(`Prepended note: ${noteId}`);
                    }
                } else {
                    this.showToast(message || 'Failed to create note.', 'danger');
                }
            })
            .catch(err => {
                console.error('Create note error:', err);
                this.showToast('An error occurred while creating the note.', 'danger');
            });
    }

    deleteNoteInModal(note) {
        const modalEl = document.getElementById('deleteNoteModal');
        const modal = new bootstrap.Modal(modalEl);
        const confirmBtn = modalEl.querySelector('#confirmDeleteNoteBtn');

        // Set the noteId to the confirm button for reference
        confirmBtn.noteId = note.noteId;

        // Show modal
        modal.show();

        // Add click event listener to the confirm button
        const onConfirm = () => {
            this.deleteNote_POST(note.noteId);
            confirmBtn.removeEventListener("click", onConfirm); // Remove listener after execution
            modal.hide(); // Hide the modal after deletion
        };

        confirmBtn.addEventListener("click", onConfirm);
    }

    deleteNote_POST(note) {
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
    }

}

// Initialize
const notesInstance = new Notes();
// window.refreshNotes = () => notesInstance.loadNotes();
