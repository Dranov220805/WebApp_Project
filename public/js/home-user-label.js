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

        this.boundHandleUpload = this.handleFileUpload.bind(this);
        this.boundHandleDelete = this.handleDeleteImage.bind(this);
        this.boundTriggerInput = this.triggerImageInputClick.bind(this);
    }

    setupEvents() {
        document.removeEventListener('click', this.handleNoteClick);
        document.removeEventListener('click', this.handleDeleteClick);

        this.handleNoteClick = (event) => {
            const deleteBtn = event.target.closest(".note-label-delete-btn");
            const labelNote = event.target.closest(".note-label-add-btn");
            const listLabelNote = event.target.closest(".note-label-list-btn");
            const shareBtn = event.target.closest(".note-share-btn");
            const lockBtn = event.target.closest(".note-lock-btn");
            const noteEl = event.target.closest('.note-sheet-label');

            if (!noteEl) return;

            const note = {
                noteId: noteEl.dataset.noteId || noteEl.dataset.id,
                title: noteEl.dataset.noteTitle || noteEl.dataset.title,
                content: noteEl.dataset.noteContent || noteEl.dataset.content,
                imageLink: noteEl.dataset.noteImage || noteEl.dataset.imageLink,
                labels: noteEl.dataset.noteLabels || noteEl.dataset.labels,
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
                this.expandDeleteLabelNote(note);
                return;
            }

            if (shareBtn) {
                console.log('Clicked share button:', note);
            }

            if (lockBtn) {
                console.log('Clicked lock button:', note);
            }

            // Prevent expanding the note when clicking buttons inside .note-sheet__menu
            if (event.target.closest('.note-sheet__menu button')) return;

            console.log('Clicked note:', note);
            this.expandLabelNote(note);
        };

        document.addEventListener('click', this.handleNoteClick);
        console.log('Attached note click listener');
        document.addEventListener('click', this.handleDeleteClick);

        // Attach scroll event listener
        window.addEventListener('scroll', this.handleScroll.bind(this));
        console.log('Attached scroll listener');

        // Rename label
        document.addEventListener('click', (e) => {
            if (e.target.closest('.label-rename-btn')) {
                const container = e.target.closest('.note-sheet__menu');
                const input = container.querySelector('input');
                const oldLabel = e.target.closest('.label-rename-btn').dataset.labelId;
                const newLabel = input.value.trim();
                if (oldLabel !== newLabel && newLabel) {
                    console.log('Renaming:', oldLabel, 'to', newLabel);
                    this.renameLabel_POST(oldLabel, newLabel);
                }
            }
        });

        // Delete label
        document.addEventListener('click', (e) => {
            if (e.target.closest('.label-delete-btn')) {
                const targetedLabel = e.target.closest('.label-delete-btn').dataset.labelId;
                if (confirm(`Delete label "${targetedLabel}"?`)) {
                    this.deleteLabel_POST(targetedLabel);
                }
            }
        });

        // Create label
        document.querySelector('.label-post__submit')?.addEventListener('click', () => {
            const input = document.querySelector('.label-post__input');
            const newLabel = input.value.trim();
            if (newLabel) {
                this.createLabel_POST(newLabel);
                input.value = '';
            }
        });

        const autoResizeTextarea = (textarea) => {
            textarea.style.height = 'auto'; // Reset height
            textarea.style.minHeight = '300';
            textarea.style.height = textarea.scrollHeight + 'px'; // Set to scroll height
        };

        const myTextarea = document.querySelector('.note-label-content-input-autosave');
        if (myTextarea) {
            myTextarea.addEventListener('input', () => autoResizeTextarea(myTextarea));
            autoResizeTextarea(myTextarea);
        }
    }

    handleScroll() {
        const currentScrollTop = window.scrollY;
        if (currentScrollTop > this.lastScrollTop &&
            (window.innerHeight + currentScrollTop >= document.body.offsetHeight - 200)) {
            // this.loadNotes();
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

    loadLabelNotes() {
        if (this.isLoadingNotes) return;
        this.isLoadingNotes = true;

        this.labelName = document.getElementById("note-layout__title")?.textContent?.trim() || '';

        fetch(`/note/label?label-name=${encodeURIComponent(this.labelName)}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('accessToken')
            }
        })
            // .then(res => res.json())
            .then(data => {
                console.log(data);
                if (data.data?.length > 0) {
                    this.appendLabelNotesToDOM(data.data);
                    this.currentPage++;
                }
            })
            .catch(err => {
                console.error('Fetch failed:', err);
                this.showToast('Failed to load notes. Please try again.');
            })
            .finally(() => this.isLoadingNotes = false);
    }

    loadNewLabelNote() {
        if (this.isLoadingNotes) return;
        this.isLoadingNotes = true;
        this.currentPage = 1;

        this.labelName = document.getElementById("note-layout__title")?.textContent?.trim() || '';

        fetch(`/note/label/${encodeURIComponent(this.labelName)}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('accessToken')
            }
        })
            // .then(res => res.json())
            .then(data => {
                console.log(data);
                if (data.data?.length > 0) {
                    this.appendLabelNotesToDOM(data.data);
                    this.currentPage++;
                }
            })
            .catch(err => {
                console.error('Fetch failed:', err);
                this.showToast('Failed to load notes. Please try again.');
            })
            .finally(() => this.isLoadingNotes = false);
    }

    appendLabelNotesToDOM(notes) {
        const container = document.querySelector(".label-note__load");
        if (!container) return;

        notes.forEach(note => {
            if (document.querySelector(`.note-sheet[data-note-id="${note.noteId}"]`)) {
                console.log(`Skipping duplicate note: ${note.noteId}`);
                return;
            }

            const div = document.createElement("div");
            div.className = "note-sheet note-sheet-label d-flex flex-column";
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

            console.log('All labels: ', labels);

            const imageHTML = note.imageLink && note.imageLink.trim() !== ''
                ? `<div class="note-sheet__image" style="width: 100%; height: auto; overflow: hidden">
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
                    <button title="Add Label" data-bs-target="listLabelNoteModal" id="note-label-list-btn" class="note-label-list-btn"><i class="fa-solid fa-tags"></i></button>
                    <button class="note-label-delete-btn" title="Delete This Note" data-bs-target="deleteNoteModal" data-note-id="${note.noteId}"><i class="fa-solid fa-trash"></i></button>
                    <button title="Share this Note"><i class="fa-solid fa-users"></i></button>
                    <button title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
                </div>
            </div>
        `;

            container.appendChild(div);
            console.log(`Appended note: ${note.noteId}`);
        });
    }

    renameLabel_POST(oldLabel, newLabel) {
        fetch('/label/update', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                oldLabel,
                newLabel
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    this.showToast("Label renamed successfully", "success");

                    // === Update Sidebar Label ===
                    document.querySelectorAll('#sidebar .sidebar-item').forEach(el => {
                        const span = el.querySelector('.sidebar__item--title');
                        if (span && span.textContent.trim() === oldLabel) {
                            span.textContent = newLabel;
                            const encodedLabel = encodeURIComponent(newLabel).replace(/%20/g, '+');
                            el.setAttribute('href', `/home/label/${encodedLabel}`);
                        }
                    });

                    // === Update Rename Input Field ===
                    document.querySelectorAll(`.label-rename-btn[data-label-id="${oldLabel}"]`).forEach(btn => {
                        const input = btn.closest('.note-sheet__menu').querySelector('input');
                        if (input) input.value = newLabel;
                        btn.setAttribute('data-label-id', newLabel);
                    });

                    // === Update Delete Button Reference ===
                    document.querySelectorAll(`.label-delete-btn[data-label-id="${oldLabel}"]`).forEach(btn => {
                        btn.setAttribute('data-label-id', newLabel);
                    });

                    // === Update Checkbox in Assign Labels Modal ===
                    document.querySelectorAll(`.label-checkbox[value="${oldLabel}"]`).forEach(checkbox => {
                        checkbox.value = newLabel;
                        const span = checkbox.closest('label').querySelector('span');
                        if (span) span.textContent = newLabel;
                    });
                } else {
                    this.showToast("Failed to rename label", "danger");
                }
            })
            .catch(() => this.showToast("An error occurred while renaming label", "danger"));
    }

    deleteLabel_POST(labelName) {
        fetch('/label/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                labelName
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    this.showToast("Label deleted successfully", "success");
                    this.removeLabel(labelName);
                } else {
                    this.showToast("Failed to delete label", "danger");
                }
            })
            .catch(() => this.showToast("An error occurred while deleting label", "danger"));
    }

    removeLabel(labelName) {
        const safeLabel = labelName.replace(/</g, "&lt;").replace(/>/g, "&gt;");

        // === Remove from Sidebar ===
        const sidebarLinks = document.querySelectorAll('.sidebar .sidebar-item');
        sidebarLinks.forEach(link => {
            if (link.textContent.trim() === labelName.trim()) {
                link.remove();
            }
        });

        // === Remove from Modal ===
        const modalItems = document.querySelectorAll('#label-management-body .note-sheet__menu');
        modalItems.forEach(item => {
            const input = item.querySelector('input.sidebar__item--input');
            if (input && input.value.trim() === labelName.trim()) {
                item.remove();
            }
        });

        // === Remove from Assign Labels Modal ===
        const assignLabelItems = document.querySelectorAll('#label-checkbox-list .note-sheet__menu');
        assignLabelItems.forEach(item => {
            const span = item.querySelector('label span');
            if (span && span.textContent.trim() === labelName.trim()) {
                item.remove();
            }
        });
    }

    createLabel_POST(labelName) {
        fetch('/label/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                labelName
            })
        })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                if (data) {
                    this.showToast("Label created successfully", "success");
                    this.appendLabel(labelName);
                    // Optionally reload label list or append to DOM
                } else {
                    this.showToast("Failed to create label", "danger");
                }
            })
            .catch(() => this.showToast("An error occurred while creating label", "danger"));
    }

    appendLabel(labelName) {
        const encodedLabel = encodeURIComponent(labelName);
        const safeLabel = labelName.replace(/</g, "&lt;").replace(/>/g, "&gt;");

        // === Append to Sidebar ===
        const sidebar = document.querySelector('.sidebar-content');
        if (sidebar) {
            const a = document.createElement('a');
            const encodedLabel = encodeURIComponent(labelName).replace(/%20/g, '+');
            a.href = `/home/label/${encodedLabel}`;
            a.className = 'sidebar-item';
            a.title = safeLabel;
            a.innerHTML = `
            <i class="sidebar__item--icon fa-solid fa-tag"></i>
            <span class="sidebar__item--title">${safeLabel}</span>
        `;
            // Insert before "Edit Labels" and other fixed items
            const editLabelsItem = sidebar.querySelector('[data-bs-target="#editLabelsModal"]');
            sidebar.insertBefore(a, editLabelsItem);
        }

        // === Append to Modal ===
        const modalBody = document.getElementById('label-management-body');
        if (modalBody) {
            const div = document.createElement('div');
            div.className = 'note-sheet__menu';
            div.style.width = '100%';
            div.innerHTML = `
            <div style="width: 100%; display: flex; flex-direction: row; flex-grow: 1">
                <input class="sidebar__item--input" style="flex-grow: 1; border: none; background-color: inherit" value="${safeLabel}">
                <button class="label-rename-btn" title="Rename label" data-label-id="${safeLabel}">
                    <i class="fa-regular fa-pen-to-square"></i>
                </button>
                <button class="label-delete-btn" title="Delete label" data-label-id="${safeLabel}">
                    <i class="fa-solid fa-eraser"></i>
                </button>
            </div>
        `;
            modalBody.appendChild(div);
        }

        // === Append to Assign Labels Modal ===
        const assignModalBody = document.getElementById('label-checkbox-list');
        if (assignModalBody) {
            const div = document.createElement('div');
            div.className = 'note-sheet__menu';
            div.style.width = '100%';
            div.innerHTML = `
            <label style="width: 100%; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" class="label-checkbox" value="${safeLabel}" style="transform: scale(1.5);"/>
                <span>${safeLabel}</span>
            </label>
        `;
            assignModalBody.appendChild(div);
        }
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

    expandLabelNote(note) {
        const modalEl = document.getElementById('noteLabelModal');
        const modal = new bootstrap.Modal(modalEl);
        const noteId = note.noteId;

        const imageLink = modalEl.querySelector('.note-sheet__image');
        const titleInput = modalEl.querySelector('.note-label-title-input-autosave');
        const contentInput = modalEl.querySelector('.note-label-content-input-autosave');
        const icon = modalEl.querySelector('.save-status-icon i');
        const iconText = modalEl.querySelector('.save-status-icon p');

        imageLink.innerHTML = note.imageLink ? `<img src="${note.imageLink}" style="width: 100%; height: auto; display: block">` : '';
        titleInput.value = note.title || '';
        contentInput.value = note.content || '';
        icon.className = 'fa-solid fa-check-circle text-success';
        iconText.innerHTML = 'Saved';

        modal.show();
        // Setup auto-save functionality
        this.setupLabelAutoSaveModal(noteId, titleInput, contentInput, icon, iconText);

        const triggerUploadBtn = modalEl.querySelector('#triggerImageUpload');
        const triggerDeleteBtn = modalEl.querySelector('#triggerImageDelete');
        const imageInput = modalEl.querySelector('#imageInput');
        const noteIdInput = modalEl.querySelector('#noteIdInput');
        const inputTextarea = modalEl.querySelector('.note-label-content-input-autosave');

        inputTextarea.style.height = '100%';
        imageInput.dataset.noteId = noteId;
        noteIdInput.value = noteId;
        noteIdInput.dataset.imageUrl = note.imageLink || '';

        this.clearAndBindListener(triggerUploadBtn, 'click', this.boundTriggerInput);
        this.clearAndBindListener(imageInput, 'change', this.boundHandleUpload);
        this.clearAndBindListener(triggerDeleteBtn, 'click', this.boundHandleDelete);
    }

    setupLabelAutoSaveModal(noteId, titleInput, contentInput, icon, iconText) {
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
                    const { status, noteId, title, content, imageLink} = data;
                    if (status === true) {
                        showSavedIcon();
                        console.log(data);
                        const noteElement = document.querySelector(`.note-sheet-label[data-note-id="${noteId}"]`);
                        if (noteElement) {
                            noteElement.querySelector('.note-sheet__title').textContent = title;
                            noteElement.querySelector('.note-sheet__content').innerHTML = content.replace(/\n/g, '<br>');
                            noteElement.dataset.noteTitle = title;
                            noteElement.dataset.noteContent = content;
                            noteElement.dataset.imageLink = imageLink;
                        } else {
                            if (!document.querySelector(`.note-sheet-label[data-note-id="${noteId}"]`)) {
                                const newNote = this.noteLabelSheetModel(noteId, title, content, imageLink);
                                // document.querySelector('.other-note__load')?.prepend(newNote);
                                console.log(`Prepended note: ${noteId}`);
                            }
                        }
                        const labelNoteGrid = document.querySelector('.label-note__load');
                        labelNoteGrid.innerHTML = '';
                        this.loadNewLabelNote();
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

    noteLabelSheetModel(noteId, title, content, imageLink) {
        const otherNoteGrid = document.querySelector('.label-note__load');
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
            <div class="note-sheet__content">
                ${safeContent}
            </div>
        </div>
        <div class="note-sheet__menu">
            <div>
                <button class="note-pin-btn" title="Pin Note"><i class="fa-solid fa-thumbtack"></i></button>
                <button title="Add Label" data-bs-target="listLabelNoteModal" id="note-label-list-btn" class="note-label-list-btn"><i class="fa-solid fa-tags"></i></button>
                <button class="note-delete-btn" title="Delete This Note" data-note-id="${noteId}"><i class="fa-solid fa-trash"></i></button>
                <button title="Share this Note"><i class="fa-solid fa-users"></i></button>
                <button title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
            </div>
        </div>
    `;

        otherNoteGrid.prepend(div);
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

        console.log(note.labels);

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

    expandDeleteLabelNote(note) {
        const modalEl = document.getElementById('deleteNoteModal');
        const modal = new bootstrap.Modal(modalEl);
        const confirmBtn = modalEl.querySelector('#confirmDeleteNoteBtn');

        modal.show();

        // Cleanup any old event listeners to prevent duplicates
        const newConfirmHandler = () => {
            this.deleteLabelNote_POST(note.noteId, note.title, note.content);
            confirmBtn.removeEventListener('click', newConfirmHandler); // prevent multiple bindings
            modal.hide();
        };

        confirmBtn.addEventListener('click', newConfirmHandler);
    }

    deleteLabelNote_POST(noteId, title, content) {
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
                        console.log("Calling loadLabelNotes()");
                        this.loadLabelNotes();
                    } catch (e) {
                        console.error("loadLabelNotes() failed:", e);
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