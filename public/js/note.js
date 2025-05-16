import { NoteCollaborator } from './noteCollaborator.js';

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

        this.currentNoteId = null;
        this.imageLinkRef = null;

        this.setupEvents();
        this.loadPinnedNotes();
        this.loadNotes();

        this.boundHandleUpload = this.handleFileUpload.bind(this);
        this.boundHandleDelete = this.handleDeleteImage.bind(this);
        this.boundTriggerInput = this.triggerImageInputClick.bind(this);
        this.boundEmailShareHandler = this.handleAddShareEmail.bind(this); // bind once
        document.querySelector('#share--email__btn').addEventListener('click', this.boundEmailShareHandler);
    }

    setupEvents() {
        // Remove existing listeners to prevent duplicates
        document.removeEventListener('click', this.handleNoteClick);
        document.removeEventListener('click', this.handleDeleteClick);

        this.handleNoteClick = (event) => {
            const deleteBtn = event.target.closest(".note-delete-btn, .pinned-note-delete-btn");
            const labelNote = event.target.closest(".note-label-add-btn");
            const listLabelNote = event.target.closest(".note-label-list-btn");
            const pinBtn = event.target.closest(".note-pin-btn");
            const unpinBtn = event.target.closest(".pinned-note-pin-btn");
            const shareBtn = event.target.closest(".note-share-btn");
            const lockBtn = event.target.closest(".note-lock-btn");
            const noteEl = event.target.closest('.note-sheet');

            if (!noteEl) return;

            const note = {
                noteId: noteEl.dataset.noteId || noteEl.dataset.id,
                title: noteEl.dataset.noteTitle || noteEl.dataset.title,
                content: noteEl.dataset.noteContent || noteEl.dataset.content,
                imageLink: noteEl.dataset.imageLink || noteEl.dataset.imageLink,
                labels: noteEl.dataset.labels || noteEl.dataset.labels,
            };

            if (labelNote) {
                console.log('Clicked add label for note:', note);
                this.expandAddLabelNote(note);
                return;
            }

            if (listLabelNote) {
                console.log('Clicked list label for note:', note);
                this.expandListLabelNote(note);
                return;
            }

            if (deleteBtn) {
                console.log('Clicked delete button:', note);
                this.expandDeleteNote(note);
                return;
            }

            if (pinBtn) {
                console.log('Clicked pin button:', note);
                this.pinNote_POST(note.noteId, note.title, note.content, note.imageLink);
                return;
            }

            if (unpinBtn) {
                console.log('Clicked unpin button:', note);
                this.unpinNote_POST(note.noteId, note.title, note.content, note.imageLink);
                return;
            }

            if (shareBtn) {
                console.log('Clicked share button:', note);
                this.expandShareNote(note);
            }

            if (lockBtn) {
                console.log('Clicked lock button:', note);
                this.expandLockNote(note);
            }

            // Prevent expanding the note when clicking buttons inside .note-sheet__menu
            if (event.target.closest('.note-sheet__menu button')) return;

            console.log('Clicked note:', note);
            this.expandNote(note);
        };

        document.querySelector('#email--shared__list').addEventListener('click', (event) => {
            const removeBtn = event.target.closest('button.btn-danger');

            if (removeBtn) {
                const container = removeBtn.closest('.list-group-item');
                const email = container.querySelector('strong')?.textContent;
                const noteId = this.currentNote.noteId;

                if (email) {
                    this.handleRemoveShareEmail(noteId, email, container);
                }
            }
        });

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

        // this.initNotePostTrigger();

        const modalEl = document.getElementById('noteModal');
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', () => {
                document.body.style.overflow = '';
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            });
        }

        const autoResizeTextarea = (textarea) => {
            textarea.style.height = '100%';
            textarea.style.minHeight = '300px';
            textarea.style.height = textarea.scrollHeight + 'px'; // Set to scroll height
        };

        const myTextarea = document.querySelector('.note-content-input-autosave');
        myTextarea.addEventListener('input', () => autoResizeTextarea(myTextarea));

        autoResizeTextarea(myTextarea);

        const titleInput = document.querySelector('.note-text__content');
        const contentTextarea = document.querySelector('.note-post__input');

        const toggleTitleInput = () => {
            const hasContent = contentTextarea.value.trim() !== '';

            if (hasContent) {
                titleInput.classList.remove('d-none');
                titleInput.style.height = '30' + 'px';
                titleInput.style.opacity = '1';
            } else {
                titleInput.classList.add('d-none');
                titleInput.value = '';
                titleInput.style.height = '0' + 'px';
                titleInput.style.opacity = '0';
            }
        };

        // Bind the event
        contentTextarea.addEventListener('input', toggleTitleInput);

        // Run on page load in case textarea is pre-filled
        toggleTitleInput();

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

    loadNotes() {
        if (this.isLoading) return;

        this.isLoading = true;

        fetch(`/note/list?page=${this.currentPage}&limit=${this.limit}`, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer '
            }
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
            div.className = "note-sheet d-flex";
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
                ? `<div class="note-sheet__image" style="overflow-y: visible">
                   <img src="${note.imageLink}" style="display: block">
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
                <div class="note-sheet__menu--item">
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

    loadPinnedNotes() {
        if (this.isLoadingPinned) return;
        this.isLoadingPinned = true;

        fetch(`/note/pinned/list`, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer '
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.data?.length > 0) {
                    this.appendPinnedNotesToDOM(data.data);
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
            div.className = "note-sheet d-flex";
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
                ? `<div class="note-sheet__image" style="overflow-y: visible">
                   <img src="${note.imageLink}" style="display: block">
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
                    <div class="note-sheet__menu--item">
                        <button class="pinned-note-pin-btn" title="Unpin Note"><i class="fa-solid fa-thumbtack"></i></button>
                        <button title="Add Label" data-bs-target="listLabelNoteModal" id="note-label-list-btn" class="note-label-list-btn"><i class="fa-solid fa-tags"></i></button>
                        <button class="pinned-note-delete-btn" title="Delete This Note" data-bs-target="deleteNoteModal" data-note-id="${note.noteId}"><i class="fa-solid fa-trash"></i></button>
                        <button class="note-share-btn" title="Share this Note"><i class="fa-solid fa-users"></i></button>
                        <button class="note-lock-btn" title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
                    </div>
                </div>
            `;
            container.appendChild(div);
        });
    }

    loadNewNotes() {
        if (this.isLoadingNotes) return;
        this.isLoadingNotes = true;
        this.currentPage = 1;

        fetch(`/note/list?page=${this.currentPage}&limit=${this.limit}`, {
            headers: {
                'Authorization': 'Bearer '
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
                'Authorization': 'Bearer '
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

    // Utility method
    clearAndBindListener(element, event, handler) {
        element.removeEventListener(event, handler);
        element.addEventListener(event, handler);
    }

    // Triggers file input
    triggerImageInputClick(e) {
        e.preventDefault();
        const imageInput = document.querySelector('#imageInput');
        imageInput?.click();
    }

    // Handles image upload
    async handleFileUpload() {
        const imageInput = document.querySelector('#imageInput');
        const selectedFile = imageInput.files[0];
        if (!selectedFile || !this.currentNoteId) return;

        const formData = new FormData();
        formData.append('image', selectedFile);
        formData.append('noteId', this.currentNoteId);

        // === Show full-screen loading overlay ===
        const overlay = document.getElementById('overlay-loading');
        if (overlay) overlay.classList.remove('d-none');

        try {
            const response = await fetch('/note/upload-image', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer '
                },
                body: formData
            });
            const result = await response.json();

            if (result.status === true) {
                this.showToast('Image uploaded successfully!', 'success');

                if (this.imageLinkRef) {
                    this.imageLinkRef.innerHTML = `<img src="${result.url}" style="width: 100%; height: auto; display: block">`;
                }

                imageInput.value = '';
                document.querySelector('.pinned-note__load').innerHTML = '';
                document.querySelector('.other-note__load').innerHTML = '';
                this.loadNewNotes();
                this.loadNewPinnedNotes();
            } else {
                this.showToast('Upload failed: ' + result.error, 'danger');
            }
        } catch (err) {
            console.error('Upload error:', err);
            this.showToast('Something went wrong while uploading image', 'danger');
        } finally {
            // === Always hide overlay after upload attempt ===
            if (overlay) overlay.classList.add('d-none');
        }
    }

    // Handles image delete
    async handleDeleteImage() {
        if (!this.currentNoteId) return;

        const imageInput = document.querySelector('#imageInput');
        const noteIdInput = document.querySelector('#noteIdInput');
        const imageUrl = noteIdInput?.dataset?.imageUrl;
        if (!imageUrl) return;

        const formData = new FormData();
        formData.append('imageUrl', imageUrl);
        formData.append('noteId', this.currentNoteId);

        // === Show full-screen loading overlay ===
        const overlay = document.getElementById('overlay-loading');
        if (overlay) overlay.classList.remove('d-none');

        try {
            const response = await fetch('/note/delete-image', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer '
                },
                body: formData
            });
            const result = await response.json();

            if (result.status === true) {
                this.showToast('Image deleted successfully!', 'success');

                if (this.imageLinkRef) {
                    this.imageLinkRef.innerHTML = '';
                }

                imageInput.value = '';
                document.querySelector('.pinned-note__load').innerHTML = '';
                document.querySelector('.other-note__load').innerHTML = '';
                this.loadNewNotes();
                this.loadNewPinnedNotes();
            } else {
                this.showToast('Delete failed: ' + result.error, 'danger');
            }
        } catch (err) {
            console.error('Delete error:', err);
            this.showToast('Something went wrong while deleting image', 'danger');
        } finally {
            // === Always hide overlay after delete attempt ===
            if (overlay) overlay.classList.add('d-none');
        }
    }

    // Main function to open note modal
    expandNote(note) {
        const modalEl = document.getElementById('noteModal');
        const modal = new bootstrap.Modal(modalEl);
        const noteId = note.noteId;

        // Check if the note is shared with other users
        this.checkIfNoteIsShared(noteId)
            .then(isShared => {
                // Setup modal UI
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

                // Store reference to current note
                this.currentNoteId = noteId;
                this.imageLinkRef = imageLink;

                // Setup file management
                const triggerUploadBtn = modalEl.querySelector('#triggerImageUpload');
                const triggerDeleteBtn = modalEl.querySelector('#triggerImageDelete');
                const imageInput = modalEl.querySelector('#imageInput');
                const noteIdInput = modalEl.querySelector('#noteIdInput');

                if (imageInput) imageInput.dataset.noteId = noteId;
                if (noteIdInput) {
                    noteIdInput.value = noteId;
                    noteIdInput.dataset.imageUrl = note.imageLink || '';
                }

                // Setup event listeners for image management
                if (triggerUploadBtn) this.clearAndBindListener(triggerUploadBtn, 'click', this.boundTriggerInput);
                if (imageInput) this.clearAndBindListener(imageInput, 'change', this.boundHandleUpload);
                if (triggerDeleteBtn) this.clearAndBindListener(triggerDeleteBtn, 'click', this.boundHandleDelete);

                // Setup auto-save functionality
                this.setupAutoSaveModal(noteId, titleInput, contentInput, icon, iconText, isShared);

                // Create WebSocket collaborator if note is shared
                if (isShared === true) {
                    console.log("Note is shared, establishing WebSocket connection");

                    // Create collaboration UI elements if they don't exist
                    // this.setupCollaborationUI(modalEl);

                    // Initialize collaborator
                    this.noteCollaborator = new NoteCollaborator(noteId, titleInput, contentInput);
                    this.noteCollaborator.connect();
                } else {
                    console.log("This note is not shared with any users. No WebSocket connection established.");
                }

                // Clean up when modal is closed
                modalEl.addEventListener('hidden.bs.modal', () => {
                    if (this.noteCollaborator) {
                        this.noteCollaborator.disconnect();
                        this.noteCollaborator = null;
                    }
                }, { once: true });

                modal.show();
            })
            .catch(error => {
                console.error("Error checking note sharing status:", error);
                // Fallback to regular note opening
                this.setupNoteModal(modalEl, note);
            });
    }

    // Function to check if the note is shared with other users
    checkIfNoteIsShared(noteId) {
        return fetch(`/share-list`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer '
            },
            body: JSON.stringify({ noteId })
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === true) {
                    return true;
                }
            })
            .catch(error => {
                console.error("Failed to check note sharing status", error);
                return false; // In case of error, treat it as not shared
            });
    }

    // Separate function to handle setting up the modal
    setupNoteModal(modalEl, note) {
        const modal = new bootstrap.Modal(modalEl);
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

        this.setupAutoSaveModal(noteId, titleInput, contentInput, icon, iconText, false);

        const triggerUploadBtn = modalEl.querySelector('#triggerImageUpload');
        const triggerDeleteBtn = modalEl.querySelector('#triggerImageDelete');
        const imageInput = modalEl.querySelector('#imageInput');
        const noteIdInput = modalEl.querySelector('#noteIdInput');
        const inputTextarea = modalEl.querySelector('.note-content-input-autosave');

        inputTextarea.style.height = '100%';
        imageInput.dataset.noteId = noteId;
        noteIdInput.value = noteId;
        noteIdInput.dataset.imageUrl = note.imageLink || '';

        this.clearAndBindListener(triggerUploadBtn, 'click', this.boundTriggerInput);
        this.clearAndBindListener(imageInput, 'change', this.boundHandleUpload);
        this.clearAndBindListener(triggerDeleteBtn, 'click', this.boundHandleDelete);
    }

    setupAutoSaveModal(noteId, titleInput, contentInput, icon, iconText, isShared) {
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

            const title = titleInput.value;
            const content = contentInput.value;

            if (isShared && this.noteCollaborator) {
                this.noteCollaborator.sendUpdate(title, content);

                // TEMPORARY: also persist via HTTP API
                fetch('/note/update', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ noteId, title, content })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === true) {
                            showSavedIcon();
                            const pinNoteGrid = document.querySelector('.pinned-note__load');
                            const otherNoteGrid = document.querySelector('.other-note__load');
                            pinNoteGrid.innerHTML = '';
                            otherNoteGrid.innerHTML = '';
                            this.loadNewPinnedNotes();
                            this.loadNewNotes();
                        } else {
                            showErrorIcon();
                            this.showToast('Failed to save shared note.', 'warning');
                        }
                    })
                    .catch(() => {
                        showErrorIcon();
                        this.showToast('Error saving shared note.', 'danger');
                    })
                    .finally(() => { isSaving = false; });

                return;
            }

            // Normal API update for non-shared notes
            fetch('/note/update', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ noteId, title, content })
            })
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP error ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    const { status, noteId, title, content, imageLink } = data;
                    if (status === true) {
                        showSavedIcon();
                        const noteElement = document.querySelector(`.note-sheet[data-note-id="${noteId}"]`);
                        if (noteElement) {
                            noteElement.querySelector('.note-sheet__title').textContent = title;
                            noteElement.querySelector('.note-sheet__content').innerHTML = content.replace(/\n/g, '<br>');
                            noteElement.dataset.noteTitle = title;
                            noteElement.dataset.noteContent = content;
                            noteElement.dataset.imageLink = imageLink;
                        }
                        const pinNoteGrid = document.querySelector('.pinned-note__load');
                        const otherNoteGrid = document.querySelector('.other-note__load');
                        pinNoteGrid.innerHTML = '';
                        otherNoteGrid.innerHTML = '';
                        this.loadNewPinnedNotes();
                        this.loadNewNotes();
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

    createImageNote(notes, imageInput) {
        if (!imageInput) return;

        // Listen for file selection
        imageInput.addEventListener('change', async () => {
            const selectedFile = imageInput.files[0];
            if (!selectedFile) return;

            const noteId = imageInput.dataset.noteId; // Assumes the input has a data-note-id attribute
            if (!noteId) {
                this.showToast('Missing note ID for image upload.', 'danger');
                return;
            }

            const formData = new FormData();
            formData.append('noteImage', selectedFile);
            formData.append('noteId', noteId);

            try {
                const response = await fetch('/note/upload/image', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                console.log(result);

                if (result.status === true) {
                    this.showToast('Image uploaded successfully!', 'success');

                    // Update image preview if available
                    const noteEl = document.querySelector(`[data-note-id="${noteId}"]`);
                    if (noteEl) {
                        let imgContainer = noteEl.querySelector('.note-sheet__image');
                        if (!imgContainer) {
                            imgContainer = document.createElement('div');
                            imgContainer.className = 'note-sheet__image';
                            noteEl.insertBefore(imgContainer, noteEl.firstChild);
                        }

                        imgContainer.innerHTML = `<img src="${result.imageLink}" style="width: 100%; height: auto; display: block;">`;
                    }
                } else {
                    this.showToast('Image upload failed: ' + result.message, 'danger');
                }
            } catch (error) {
                console.error('Upload error:', error);
                this.showToast('An error occurred while uploading the image.', 'danger');
            }
        });
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

        const overlay = document.getElementById('overlay-loading');
        if (overlay) overlay.classList.remove('d-none');

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
                    titleInput.classList.add('d-none');
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
            })
            .finally(() => {
            if (overlay) overlay.classList.add('d-none');
        });;
    }

    noteSheetModel(noteId, title, content, imageLink) {
        const otherNoteGrid = document.querySelector('.other-note__load');
        if (!otherNoteGrid) return;

        const div = document.createElement("div");
        div.className = "note-sheet d-flex";
        div.dataset.noteId = noteId;
        div.dataset.title = title ?? "";
        div.dataset.content = content ?? "";
        div.dataset.imageLink = imageLink ?? "";

        const safeTitle = title?.trim() || "(No Title)";
        const safeContent = (content ?? "").replace(/\n/g, "<br>");

        if (imageLink && imageLink.trim() !== '') {
            div.dataset.imageLink = imageLink;
        }

        const imageHTML = imageLink && imageLink.trim() !== ''
            ? `<div class="note-sheet__image" style="overflow-y: visible">
                   <img src="${imageLink}" style="display: block">
               </div>`
            : '';

        div.innerHTML = `
        ${imageHTML}
        <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
            <h3 class="note-sheet__title">${safeTitle}</h3>
            <div class="note-sheet__content" style="overflow-x: hidden">
                ${safeContent}
            </div>
        </div>
        <div class="note-sheet__menu">
            <div class="note-sheet__menu--item">
                <button class="note-pin-btn" title="Pin Note"><i class="fa-solid fa-thumbtack-slash"></i></button>
                <button title="Add Label" data-bs-target="listLabelNoteModal" id="note-label-list-btn" class="note-label-list-btn"><i class="fa-solid fa-tags"></i></button>
                <button class="note-delete-btn" title="Delete This Note" data-note-id="${noteId}"><i class="fa-solid fa-trash"></i></button>
                <button class="note-share-btn" title="Share this Note"><i class="fa-solid fa-users"></i></button>
                <button class="note-lock-btn" title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
            </div>
        </div>
    `;

        otherNoteGrid.prepend(div);
    }

    pinNoteSheetModel(noteId, title, content, imageLink) {
        const pinnedNoteGrid = document.querySelector('.pinned-note__load');

        const div = document.createElement("div");
        div.className = "note-sheet d-flex";
        div.dataset.id = noteId;
        div.dataset.title = title;
        div.dataset.content = content;
        div.dataset.imageLink = imageLink;

        const safeTitle = title ?? "(No Title)";
        const safeContent = (content ?? "").replace(/\n/g, '<br>');

        if (imageLink && imageLink.trim() !== '') {
            div.dataset.imageLink = imageLink;
        }

        const imageHTML = imageLink && imageLink.trim() !== ''
            ? `<div class="note-sheet__image" style="overflow-y: visible">
                   <img src="${imageLink}" style="display: block">
               </div>`
            : '';

        div.innerHTML = `
            ${imageHTML}
            <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                <h3 class="note-sheet__title" data-note-title="${title}">${title}</h3>
                <div class="note-sheet__content" style="overflow-x: hidden">
                    ${content.replace(/\n/g, '<br>')}
                </div>      
            </div>
            <div class="note-sheet__menu">
                <div class="note-sheet__menu--item">
                    <button class="pinned-note-pin-btn" title="Unpin Note"><i class="fa-solid fa-thumbtack"></i></button>
                    <button title="Add Label" data-bs-target="listLabelNoteModal" id="note-label-list-btn" class="note-label-list-btn"><i class="fa-solid fa-tags"></i></button>
                    <button class="pinned-note-delete-btn" title="Delete This Note" data-bs-target="deleteNoteModal" data-note-id="${noteId}"><i class="fa-solid fa-trash"></i></button>
                    <button class="note-share-btn" title="Share this Note"><i class="fa-solid fa-users"></i></button>
                    <button class="note-lock-btn" title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
                </div>
            </div>
        `;
        pinnedNoteGrid.prepend(div);
    }

    expandAddLabelNote(note) {
        const modalEl = document.getElementById('addLabelNoteModal');
        const modal = new bootstrap.Modal(modalEl);

        modal.show();
    }

    expandListLabelNote(note) {
        const modalEl = document.getElementById('listLabelNoteModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        const labelListContainer = document.getElementById('label-checkbox-list');

        // Parse labels (from data-labels attribute or object)
        let noteLabels = [];
        if (typeof note.labels === 'string') {
            try {
                noteLabels = JSON.parse(note.labels);
            } catch (e) {
                console.error('Failed to parse note.labels:', e);
            }
        } else if (Array.isArray(note.labels)) {
            noteLabels = note.labels;
        }

        const currentLabelNames = noteLabels.map(l => l.labelName);

        // Pre-check checkboxes
        const allCheckboxes = labelListContainer.querySelectorAll('.label-checkbox');
        allCheckboxes.forEach(checkbox => {
            checkbox.checked = currentLabelNames.includes(checkbox.value);
        });

        // Remove old listeners
        if (this.labelCheckboxHandler) {
            labelListContainer.removeEventListener('change', this.labelCheckboxHandler);
        }

        // Define and assign new handler
        this.labelCheckboxHandler = (e) => {
            if (e.target.classList.contains('label-checkbox')) {
                const label = e.target.value;
                const isChecked = e.target.checked;

                if (isChecked) {
                    this.addNoteToLabel_POST(note.noteId, label);
                } else {
                    this.removeNoteFromLabel_POST(note.noteId, label);
                }
            }
        };

        labelListContainer.addEventListener('change', this.labelCheckboxHandler);
    }

    addNoteToLabel_POST(noteId, labelName) {
        fetch(`/label/note-create`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                noteId,
                labelName
            })
        })
            .then(res => res.json())
            .then(data => {
                this.showToast("Label added to note", 'success');
            })
            .catch(err => {
                console.error("Error adding label to note:", err);
                this.showToast("Error adding label");
            });
    }

    removeNoteFromLabel_POST(noteId, labelName) {
        fetch(`/label/note-delete`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                noteId,
                labelName
            })
        })
            .then(res => res.json())
            .then(data => {
                this.showToast("Label removed from note", 'success');
            })
            .catch(err => {
                console.error("Error removing label from note:", err);
                this.showToast("Error removing label");
            });
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

    pinNote_POST(noteId, title, content, imageLink) {
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
                console.log(noteId, title, content, imageLink);
                const newPinnedNote = this.pinNoteSheetModel(noteId, title, content, imageLink);
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

    unpinNote_POST(noteId, title, content, imageLink) {
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
                console.log(noteId, title, content, imageLink);

                const newOtherNote = this.noteSheetModel(noteId, title, content, imageLink);
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
            method: 'DELETE',
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
                    const searchNoteGrid = document.querySelector('.search-note__load');
                    console.log(noteId, title, content);
                    searchNoteGrid.innerHTML = '';
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

    expandShareNote(note) {
        const modalEl = document.getElementById('shareNoteModal');
        const modal = new bootstrap.Modal(modalEl);

        this.currentNote = note;

        document.querySelector('.shared-note--title').innerText = note.title;
        document.querySelector('.shared-note--content').innerText = note.content;

        this.getSharedEmail(note.noteId);
        modal.show();
    }

    handleAddShareEmail() {
        const newSharedEmail = $('#share--email__input').val();
        const noteId = this.currentNote.noteId;
        const emailSharedList = document.querySelector('#email--shared__list');

        fetch(`/share-list/add`, {
            method: 'POST',
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                noteId,
                newSharedEmail
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    console.log("Shared successfully.");
                    this.showToast("Note shared successfully!", 'success');
                    $('#share--email__input').val('');
                    this.getSharedEmail(noteId);
                } else {
                    console.error(data.message || "Failed to share note.");
                    this.showToast(data.message || "Failed to share note.", 'danger');
                    $('#share--email__input').val('');
                }
            })
            .catch(err => console.error("Error sharing:", err));
    }

    handleRemoveShareEmail(noteId, email, container) {
        const overlay = document.getElementById('overlay-loading');
        if (overlay) overlay.classList.remove('d-none');

        fetch(`/share-list/delete`, {
            method: 'DELETE',
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                noteId,
                sharedEmail: email
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    this.showToast('Sharing removed successfully.', 'success');
                    // Remove the entry from the UI
                    if (container) container.remove();
                } else {
                    this.showToast(data.message || 'Failed to remove sharing.', 'danger');
                }
            })
            .catch(err => {
                console.error("Error removing sharing:", err);
                this.showToast("Error removing sharing.", 'danger');
            })
            .finally(() => {
                if (overlay) overlay.classList.add('d-none');
            });
    }

    getSharedEmail(noteId) {
        fetch(`/share-list`, {
            method: 'POST',
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ noteId })
        })
            .then(res => {
                if (!res.ok) {
                    throw new Error(`Server returned ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                const emailSharedList = document.querySelector("#email--shared__list");
                emailSharedList.innerHTML = '';

                const { status, result, message } = data;

                if (!status || !result || result.length === 0) {
                    emailSharedList.innerText = message || 'No shared email found!';
                    return;
                }

                result.forEach(entry => {
                    const email = entry.receivedEmail || entry.email || '[Unknown Email]';
                    const date = entry.timeShared ? new Date(entry.timeShared).toLocaleDateString('en-US', {
                        year: 'numeric', month: 'long', day: 'numeric'
                    }) : 'Unknown date';

                    const item = document.createElement('div');
                    item.className = "list-group-item d-flex note-share--menu__title";

                    item.innerHTML = `
                        <div class="note-share--menu__content">
                            <strong>${email}</strong><br>
                            <small>Added ${date}</small>
                        </div>
                        <div class="note-share--menu__item">
                            <select class="form-select w-auto permission-select">
                                <option value="1" ${entry.canEdit ? 'selected' : ''}>Can edit</option>
                                <option value="0" ${!entry.canEdit ? 'selected' : ''}>Can view</option>
                            </select>
                            <button class="btn btn-danger flex-end" style="margin-top: 5px">Remove</button>
                        </div>
                    `;

                    const select = item.querySelector('.permission-select');
                    select.addEventListener('change', () => {
                        const newPermission = select.value === '1';
                        fetch(`/share-list/update-permission`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                noteId,
                                receivedEmail: email,
                                canEdit: newPermission
                            })
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.status) {
                                    this.showToast("Permission updated", 'success');
                                } else {
                                    this.showToast("Failed to update permission", 'danger');
                                }
                            })
                            .catch(err => {
                                console.error("Error updating permission:", err);
                                this.showToast("Error occurred while updating permission", 'danger');
                            });
                    });

                    emailSharedList.appendChild(item);
                });
            })
            .catch(err => {
                const emailSharedList = document.querySelector("#email--shared__list");
                emailSharedList.innerHTML = '';
                emailSharedList.innerText = 'Error loading shared emails. Please try again.';
                console.error("Fetch error:", err);
            });
    }

    expandLockNote(note) {

    }

}

const notesInstance = new Notes();
