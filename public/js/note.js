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

        this.isLoadingNotes = false;
        this.isLoadingPinnedNotes = false;

        this.setupEvents();
        this.loadPinnedNotes();
        this.loadNotes();
    }

    setupEvents() {
        // Remove existing listeners to prevent duplicates
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
        window.addEventListener('scroll', this.handleScroll.bind(this)); // Add this to attach scroll listener
        console.log('Attached scroll listener');

        const createNoteBtn = document.querySelector(".create-note-btn");
        const noteInput = document.querySelector(".note-post__input");

        if (createNoteBtn) {
            createNoteBtn.removeEventListener("click", this.createNoteHandler);
            this.createNoteHandler = () => this.createNote_POST();
            createNoteBtn.addEventListener("click", this.createNoteHandler);
        }
        if (noteInput) {
            noteInput.removeEventListener("input", this.autoResizeInput);
            noteInput.addEventListener("input", this.autoResizeInput);
        }

        this.initNotePostTrigger();

        const modalEl = document.getElementById('noteModal');
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', () => {
                document.body.style.overflow = '';
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            });
        }
    }

    handleScroll() {
        const currentScrollTop = window.scrollY;
        const nearBottom = (window.innerHeight + currentScrollTop >= document.body.offsetHeight - 200);

        // Only load more notes when scrolling down and near bottom
        if (!this.isLoading && currentScrollTop > this.lastScrollTop && nearBottom) {
            this.loadNotes(); // Only call when not already loading
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

    loadNotes() {
        if (this.isLoading) return;

        this.isLoading = true;

        fetch(`/note/list?page=${this.currentPage}&limit=${this.limit}`, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.data?.length > 0) {
                    this.appendNotesToDOM(data.data);
                    this.currentPage++; // Only increment page after successful append
                } else {
                    console.log('No more notes to load');
                }
            })
            .catch(err => {
                console.error('Failed to fetch notes:', err);
                this.showToast('Error loading notes');
            })
            .finally(() => {
                this.isLoading = false;
            });
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

    loadPinnedNotes() {
        if (this.isLoadingPinned) return;
        this.isLoadingPinned = true;

        fetch(`/note/pinned/list`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => {
                console.log('Pin note loaded', data);
                if (data.data?.length > 0) {
                    this.appendPinnedNotesToDOM(data.data);
                    // this.currentPage++;
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
            if (document.querySelector(`.note-sheet[data-note-id="${note.noteId}"]`)) {
                console.log(`Skipping duplicate note: ${note.noteId}`);
                return;
            }

            const div = document.createElement("div");
            div.className = "note-sheet d-flex flex-column";
            div.dataset.noteId = note.noteId;
            div.dataset.noteTitle = note.title;
            div.dataset.noteContent = note.content;
            div.dataset.noteImage = note.imageLink;

            div.innerHTML = `
                <div class="note-sheet__image" style="width: 100%; height: auto; overflow: hidden">
                    <img src="${note.imageLink}" style="width: 100%; height: auto; display: block">
                </div>
                <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                    <h3 class="note-sheet__title">${note.title}</h3>
                    <div class="note-sheet__content">
                        ${note.content.replace(/\n/g, '<br>')}
                    </div>
                </div>
                <div class="note-sheet__menu">
                    <div>
                        <button class="pinned-note-pin-btn" title="Unpin Note"><i class="fa-solid fa-thumbtack"></i></button>
                        <button title="Add Label"><i class="fa-solid fa-tags"></i></button>
                        <button class="pinned-note-delete-btn" title="Delete This Note" data-bs-target="deleteNoteModal" data-note-id="${note.noteId}"><i class="fa-solid fa-trash"></i></button>
                        <button title="Share this Note"><i class="fa-solid fa-users"></i></button>
                        <button title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
                    </div>
                </div>
            `;
            container.appendChild(div);
            console.log(`Appended note: ${note.noteId}`);
        });
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

    loadNewPinnedNotes() {
        if (this.isLoadingPinnedNotes) return;
        this.isLoadingPinnedNotes = true;
        this.currentPage = 1;

        fetch(`/note/pinned-list?page=${this.currentPage}&limit=${this.limit}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('accessToken')
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.data?.length > 0) {
                    this.appendPinnedNotesToDOM(data.data);
                    this.currentPage++;
                }
            })
            .catch(err => {
                console.error('Fetch failed:', err);
                this.showToast('Failed to load notes. Please try again.');
            })
            .finally(() => this.isLoadingPinnedNotes = false);
    }

    loadTrashedNotes() {
        if (this.isLoading) return;
        this.isLoading = true;
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
            div.dataset.noteImage = note.imageLink;

            div.innerHTML = `
                <div class="note-sheet__image" style="width: 100%; height: auto; overflow: hidden">
                    <img src="${note.imageLink}" style="width: 100%; height: auto; display: block">
                </div>
                <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                    <h3 class="note-sheet__title">${note.title}</h3>
                    <div class="note-sheet__content">
                        ${note.content.replace(/\n/g, '<br>')}
                    </div>
                </div>
                <div class="note-sheet__menu">
                    <div>
                        <button class="note-edit-btn" title="Edit"><i class="fa-regular fa-pen-to-square"></i></button>
                        <button class="note-delete-btn" title="Delete Permanently" data-bs-target="deleteNoteModal" data-note-id="${note.noteId}"><i class="fa-solid fa-trash-can-arrow-up"></i></button>
                    </div>
                </div>
            `;
            container.appendChild(div);
            console.log(`Appended note: ${note.noteId}`);
        });
    }

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

    expandNote(note) {
        const modalEl = document.getElementById('noteModal');
        const modal = new bootstrap.Modal(modalEl);
        const noteId = note.noteId;

        const titleInput = modalEl.querySelector('.note-title-input-autosave');
        const contentInput = modalEl.querySelector('.note-content-input-autosave');
        const icon = modalEl.querySelector('.save-status-icon i');
        const iconText = modalEl.querySelector('.save-status-icon p');

        // Set modal inputs
        titleInput.value = note.title || '';
        contentInput.value = note.content || '';
        icon.className = 'fa-solid fa-check-circle text-success';
        iconText.innerHTML = 'Saved';

        // Show modal
        modal.show();

        // Setup auto-save
        this.setupAutoSaveModal(noteId, titleInput, contentInput, icon, iconText);

        // Setup image upload
        const triggerUploadBtn = modalEl.querySelector('#triggerImageUpload');
        const imageInput = modalEl.querySelector('#imageInput');
        const noteIdInput = modalEl.querySelector('#noteIdInput');

        // Open file chooser
        triggerUploadBtn.addEventListener('click', () => {
            imageInput.click();
        });

        // Submit form when file is selected
        imageInput.addEventListener('change', () => {
            modalEl.querySelector('#imageUploadForm').submit();
        });
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
                    titleInput.style.display = 'none';
                    contentInput.value = '';
                    const newNote = this.noteSheetModel(noteId, title, content);
                    console.log(`Prepended note: ${noteId}`);
                    this.initNotePostTrigger();
                } else {
                    this.showToast(message || 'Failed to create note.', 'danger');
                }
            })
            .catch(err => {
                console.error('Create note error:', err);
                this.showToast('An error occurred while creating the note.', 'danger');
            });
    }

    noteSheetModel(noteId, title, content) {
        const otherNoteGrid = document.querySelector('.other-note__load');

        const div = document.createElement("div");
        div.className = "note-sheet d-flex flex-column";
        div.dataset.noteId = noteId;
        div.dataset.noteTitle = title;
        div.dataset.noteContent = content;
        div.dataset.noteImage = note.imageLink;

        div.innerHTML = `
            <div class="note-sheet__image" style="width: 100%; height: auto; overflow: hidden">
                    <img src="${note.imageLink}" style="width: 100%; height: auto; display: block">
                </div>
            <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                <h3 class="note-sheet__title" data-note-title="${title}">${title}</h3>
                <div class="note-sheet__content" data-note-content="${content}">
                    ${content.replace(/\n/g, '<br>')}
                </div>
            </div>
            <div class="note-sheet__menu">
                <div>
                    <button class="note-pin-btn" title="Pin Note"><i class="fa-solid fa-thumbtack"></i></button>
                    <button title="Label"><i class="fa-solid fa-tags"></i></button>
                    <button class="note-delete-btn" title="Delete" data-note-id="${noteId}"><i class="fa-solid fa-trash"></i></button>
                    <button title="Share this Note"><i class="fa-solid fa-users"></i></button>
                    <button title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
                </div>
            </div>
        `;
        otherNoteGrid.prepend(div);
    }

    pinNoteSheetModel(noteId, title, content) {
        const pinnedNoteGrid = document.querySelector('.pinned-note__load');

        const div = document.createElement("div");
        div.className = "note-sheet d-flex flex-column";
        div.dataset.id = noteId;
        div.dataset.title = title;
        div.dataset.content = content;
        div.dataset.noteImage = note.imageLink;

        const safeTitle = title ?? "(No Title)";
        const safeContent = (content ?? "").replace(/\n/g, '<br>');

        div.innerHTML = `
            <div class="note-sheet__image" style="width: 100%; height: auto; overflow: hidden">
                    <img src="${note.imageLink}" style="width: 100%; height: auto; display: block">
                </div>
            <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                <h3 class="note-sheet__title" data-note-title="${title}">${title}</h3>
                <div class="note-sheet__content">
                    ${content.replace(/\n/g, '<br>')}
                </div>      
            </div>
            <div class="note-sheet__menu">
                <div>
                    <button class="pinned-note-pin-btn" title="Unpin Note"><i class="fa-solid fa-thumbtack"></i></button>
                    <button title="Add Label"><i class="fa-solid fa-tags"></i></button>
                    <button class="pinned-note-delete-btn" title="Delete This Note" data-bs-target="deleteNoteModal" data-note-id="${noteId}"><i class="fa-solid fa-trash"></i></button>
                    <button title="Share this Note"><i class="fa-solid fa-users"></i></button>
                    <button title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
                </div>
            </div>
        `;
        pinnedNoteGrid.prepend(div);
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

    pinNote_POST(noteId, title, content) {
        fetch('/note/pin', {
            method: 'POST',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ noteId })
        })
            .then(res => res.json())
            .then(data => {
                console.log(data);

                if (data.status === false) {
                    this.showToast("An error occurred: " + data.message, "danger");
                    return;
                }

                this.showToast("Note pinned successfully!", "success");

                // Remove note from 'other notes'
                const noteEl = document.querySelector(`.other-note__load .note-sheet[id="${noteId}"]`);
                if (noteEl) noteEl.remove();

                // Create and prepend the pinned note
                const pinNoteGrid = document.querySelector('.pinned-note__load');
                const otherNoteGrid = document.querySelector('.other-note__load');
                console.log(noteId, title, content);
                const newPinnedNote = this.pinNoteSheetModel(noteId, title, content);
                pinNoteGrid.innerHTML = '';
                otherNoteGrid.innerHTML = '';
                this.loadNewNotes();
                this.loadNewPinnedNotes();

            })
            .catch(err => {
                console.error("Error:", err);
                this.showToast("An error occurred while pinning the note", "danger");
            });
    }

    unpinNote_POST(noteId, title, content) {
        fetch('/note/unpin', {
            method: 'POST',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ noteId })
        })
            .then(res => res.json())
            .then(data => {
                console.log(data);

                if (data.status === false) {
                    this.showToast("An error occurred: " + data.message, "danger");
                    return;
                }

                this.showToast("Note unpinned successfully!", "success");

                // Remove note from 'pinned notes'
                const noteEl = document.querySelector(`.pinned-note__load .note-sheet[id="${noteId}"]`);
                if (noteEl) noteEl.remove();

                // Create and prepend the unpinned note to the 'other notes' section
                const otherNoteGrid = document.querySelector('.other-note__load');
                const pinNoteGrid = document.querySelector('.pinned-note__load');
                console.log(noteId, title, content);

                const newOtherNote = this.noteSheetModel(noteId, title, content);
                pinNoteGrid.innerHTML = '';
                this.loadNewPinnedNotes();
            })
            .catch(err => {
                console.error("Error:", err);
                this.showToast("An error occurred while unpinning the note", "danger");
            });
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
                    const otherNoteGrid = document.querySelector('.other-note__load');
                    const pinNoteGrid = document.querySelector('.pinned-note__load');
                    console.log(noteId, title, content);
                    pinNoteGrid.innerHTML = '';
                    otherNoteGrid.innerHTML = '';
                    try {
                        console.log("Calling loadNewNotes()");
                        this.loadNewNotes();
                    } catch (e) {
                        console.error("loadNewNotes() failed:", e);
                    }

                    try {
                        console.log("Calling loadNewPinnedNotes()");
                        this.loadNewPinnedNotes();
                    } catch (e) {
                        console.error("loadNewPinnedNotes() failed:", e);
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

const notesInstance = new Notes();
