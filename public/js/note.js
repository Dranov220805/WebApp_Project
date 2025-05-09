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

        const autoResizeTextarea = (textarea) => {
            textarea.style.height = 'auto'; // Reset height
            // textarea.style.minHeight = '200';
            textarea.style.height = textarea.scrollHeight + 'px'; // Set to scroll height
        };

        const myTextarea = document.querySelector('.note-content-input-autosave');
        myTextarea.addEventListener('input', () => autoResizeTextarea(myTextarea));

        autoResizeTextarea(myTextarea);

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
                    <button title="Share this Note"><i class="fa-solid fa-users"></i></button>
                    <button title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
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
                        <button class="pinned-note-pin-btn" title="Unpin Note"><i class="fa-solid fa-thumbtack"></i></button>
                        <button title="Add Label" data-bs-target="listLabelNoteModal" id="note-label-list-btn" class="note-label-list-btn"><i class="fa-solid fa-tags"></i></button>
                        <button class="pinned-note-delete-btn" title="Delete This Note" data-bs-target="deleteNoteModal" data-note-id="${note.noteId}"><i class="fa-solid fa-trash"></i></button>
                        <button title="Share this Note"><i class="fa-solid fa-users"></i></button>
                        <button title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
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

        this.setupAutoSaveModal(noteId, titleInput, contentInput, icon, iconText);

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
                    const { status, noteId, title, content, imageLink } = data;
                    if (status === true) {
                        showSavedIcon();
                        console.log(data);
                        const noteElement = document.querySelector(`.note-sheet[data-note-id="${noteId}"]`);
                        if (noteElement) {
                            noteElement.querySelector('.note-sheet__title').textContent = title;
                            noteElement.querySelector('.note-sheet__content').innerHTML = content.replace(/\n/g, '<br>');
                            noteElement.dataset.noteTitle = title;
                            noteElement.dataset.noteContent = content;
                            noteElement.dataset.imageLink = imageLink;
                        } else {
                            if (!document.querySelector(`.note-sheet[data-note-id="${noteId}"]`)) {
                                const newNote = this.noteSheetModel(noteId, title, content, imageLink);
                            }
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
            })
            .finally(() => {
            if (overlay) overlay.classList.add('d-none');
        });;
    }

    noteSheetModel(noteId, title, content, imageLink) {
        const otherNoteGrid = document.querySelector('.other-note__load');
        if (!otherNoteGrid) return;

        const div = document.createElement("div");
        div.className = "note-sheet d-flex flex-column";
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
            ? `<div class="note-sheet__image" style="width: 100%; height: auto; overflow-y: visible">
                   <img src="${imageLink}" style="width: 100%; height: auto; display: block">
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
            <div>
                <button class="note-pin-btn" title="Pin Note"><i class="fa-solid fa-thumbtack-slash"></i></button>
                <button title="Add Label" data-bs-target="listLabelNoteModal" id="note-label-list-btn" class="note-label-list-btn"><i class="fa-solid fa-tags"></i></button>
                <button class="note-delete-btn" title="Delete This Note" data-note-id="${noteId}"><i class="fa-solid fa-trash"></i></button>
                <button title="Share this Note"><i class="fa-solid fa-users"></i></button>
                <button title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
            </div>
        </div>
    `;

        otherNoteGrid.prepend(div);
    }

    pinNoteSheetModel(noteId, title, content, imageLink) {
        const pinnedNoteGrid = document.querySelector('.pinned-note__load');

        const div = document.createElement("div");
        div.className = "note-sheet d-flex flex-column";
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
            ? `<div class="note-sheet__image" style="width: 100%; height: auto; overflow-y: visible">
                   <img src="${imageLink}" style="width: 100%; height: auto; display: block">
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
                <div>
                    <button class="pinned-note-pin-btn" title="Unpin Note"><i class="fa-solid fa-thumbtack"></i></button>
                    <button title="Add Label" data-bs-target="listLabelNoteModal" id="note-label-list-btn" class="note-label-list-btn"><i class="fa-solid fa-tags"></i></button>
                    <button class="pinned-note-delete-btn" title="Delete This Note" data-bs-target="deleteNoteModal" data-note-id="${noteId}"><i class="fa-solid fa-trash"></i></button>
                    <button title="Share this Note"><i class="fa-solid fa-users"></i></button>
                    <button title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
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
            method: 'POST',
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

}

const notesInstance = new Notes();
