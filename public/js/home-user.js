class HomeUser {
    constructor() {
        document.addEventListener('DOMContentLoaded', () => this.initialize());
    }

    initialize() {
        this.sidebarToggle = document.getElementById('sidebar-toggle');
        this.sidebar = document.getElementById('sidebar');
        this.content = document.getElementById('content');
        this.sidebarItems = document.querySelectorAll('.sidebar-item');
        this.toggleGridBtns = document.querySelectorAll('.toggle-grid');
        this.toggleListBtns = document.querySelectorAll('.toggle-list');
        this.searchContainer = document.getElementById('search-container');
        this.searchIcon = document.getElementById('search-icon');
        this.searchInput = document.getElementById('search-input');
        this.btnSavePreference = document.getElementById('btn-save-preference');
        this.searchTimeout = null;

        this.sidebarVisible = false;
        this.searchExpanded = false;

        // this.loadUserPreference();
        this.attachEventListeners();
        this.checkVerification();
        this.handleSendVerificationLink();
        this.handleAvatarUpload();
        this.attachPreferenceSaveHandler();
    }

    closeToast = () => {
        $('#toast-close').click(() => {
            $('#toast').addClass('d-none')
        })
    }

    translateItem = () => {
        $('.loading-item').click(() => {
            $('#overlay-loading').removeClass('d-none');
        })
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

    handleSendVerificationLink() {
        const sendBtn = document.getElementById('btn-send-verification');
        if (sendBtn) {
            sendBtn.addEventListener('click', async (e) => {
                const overlay = document.getElementById('overlay-loading');
                if (overlay) overlay.classList.remove('d-none'); // Show loading overlay

                try {
                    const response = await fetch(`/auth/send-verification`, {
                        method: 'POST',
                        headers: {
                            "Content-Type": "application/json"
                        }
                    });

                    const data = await response.json();

                    if (data.status === true || data.success === true) {
                        this.showToast('Verification successfully sent', 'success');
                    } else {
                        this.showToast(data.message || 'Failed to send verification', 'danger');
                    }
                } catch (err) {
                    console.error('Error sending verification link:', err);
                    this.showToast('Something went wrong while sending verification link', 'danger');
                } finally {
                    if (overlay) overlay.classList.add('d-none'); // Hide overlay
                }
            });
        }
    }

    attachPreferenceSaveHandler() {
        const saveBtn = document.querySelector('.btn-save-preference');
        if (!saveBtn) return;

        saveBtn.addEventListener('click', async () => {
            const userName = document.querySelector('.username--rename__input').value;
            const theme = document.getElementById('theme-selector')?.value || 'system';
            const fontSize = document.getElementById('font-size-selector')?.value || '14px';
            const selectedColorElement = document.querySelector('.color-option.active');
            const noteColor = selectedColorElement ? selectedColorElement.dataset.color : '#000000';

            const body = {
                userName,
                theme,
                noteFont: fontSize,
                noteColor
            };

            const overlay = document.getElementById('overlay-loading');
            if (overlay) overlay.classList.remove('d-none'); // Show loading overlay

            try {
                const response = await fetch('/home/preferences', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(body)
                });

                const result = await response.json();
                console.log(result);

                if (result.status === true) {
                    const userNameTag = document.querySelector('.username--title__modal');
                    const userNameTag2 = document.querySelector('.username--title__modal-2');
                    userNameTag.innerText = result.userName;
                    userNameTag2.innerText = result.userName;
                    this.showToast('Preferences saved successfully', 'success');
                    // Optionally apply changes: this.applyPreferences(result.data);
                } else {
                    this.showToast('Failed to save preferences', 'danger');
                }
            } catch (err) {
                console.error('Error saving preferences:', err);
                this.showToast('Something went wrong while saving preferences', 'danger');
            } finally {
                if (overlay) overlay.classList.add('d-none'); // Hide overlay
            }
        });
    }

    handleAvatarUpload() {
        const fileInput = document.getElementById('avatar-input');
        const uploadButton = document.querySelector('.btn-upload');

        if (!fileInput || !uploadButton) return;

        // Step 1: Trigger file selection
        uploadButton.addEventListener('click', () => {
            fileInput.click();
        });

        // Step 2: Auto-upload once a file is selected
        fileInput.addEventListener('change', async () => {
            const selectedFile = fileInput.files[0];
            if (!selectedFile) return;

            const formData = new FormData();
            formData.append('avatar', selectedFile);

            const overlay = document.getElementById('overlay-loading');
            if (overlay) overlay.classList.remove('d-none');

            try {
                const response = await fetch('/home/upload/avatar', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                console.log(result);

                if (result.status === true) {
                    const {token, picture} = result;

                    this.showToast('Avatar uploaded successfully!', 'success');

                    // Update avatar previews
                    const avatarIcon = document.querySelector('#preference--image__icon');
                    const navbarAvatar = document.querySelector('#navbar--image__icon');
                    const modalAvatar = document.querySelector('#modal--image__icon');

                    if (avatarIcon) avatarIcon.src = picture;
                    if (navbarAvatar) navbarAvatar.src = picture;
                    if (modalAvatar) modalAvatar.src = picture;

                    fileInput.value = ''; // Reset for future uploads
                } else {
                    this.showToast('Upload failed: ' + result.error, 'danger');
                }
            } catch (err) {
                console.error('Upload error:', err);
                this.showToast('Something went wrong while uploading avatar', 'danger');
            } finally {
                if (overlay) overlay.classList.add('d-none');
            }
        });
    }

    attachEventListeners() {
        this.sidebarToggle?.addEventListener('click', () => this.toggleSidebar());
        this.searchInput?.addEventListener('input', () => this.handleSearch());

        this.toggleGridBtns.forEach(btn =>
            btn.addEventListener('click', (e) => this.toggleLayout(e))
        );
    }

    checkVerification() {
        if (document.querySelector('.username--title__modal')) {
            fetch('/auth/verification', {
                method: 'GET'
            })
                .then(res => res.json())
                .then(data => {
                    const {status, message} = data;
                    if (status === true) {

                    } else {
                        this.showToast('Account has not been verified','warning');
                    }
                })
                .catch(
                    err => console.log(err)
                );
        }
    }

    toggleSidebar() {
        const isTablet = window.innerWidth <= 1100;
        const isMobile = window.innerWidth <= 800;

        if (isTablet) {
            if (this.sidebarVisible) {
                this.content.style.marginLeft = '0';
                this.sidebar.classList.remove('expanded');
                this.sidebar.classList.add('collapsed');
                this.content.style.marginLeft = '80px';
            } else {
                this.content.style.marginLeft = '0';
                this.sidebar.classList.remove('collapsed');
                this.sidebar.classList.add('expanded');
                this.content.style.marginLeft = '80px';
            }
        } else if (isMobile) {
            if (this.sidebarVisible) {
                this.content.style.marginLeft = '0';
                this.sidebar.classList.remove('expanded');
                this.sidebar.classList.add('collapsed');
                // this.content.style.marginLeft = '80px';
            } else {
                this.content.style.marginLeft = '0';
                this.sidebar.classList.remove('collapsed');
                this.sidebar.classList.add('expanded');
                // this.content.style.marginLeft = '80px';
            }
        } else {
            if (this.sidebarVisible) {
                this.sidebar.classList.remove('expanded');
                this.sidebar.classList.add('collapsed');
                this.content.style.marginLeft = '80px';
            } else {
                this.sidebar.classList.remove('collapsed');
                this.sidebar.classList.add('expanded');
                this.content.style.marginLeft = '240px';
            }
        }

        this.sidebarVisible = !this.sidebarVisible;
    }

    toggleLayout(event) {
        const icon = event.currentTarget.querySelector('i');
        if (!icon) return;

        const pinnedContainer = document.querySelector('.pinned-note__load');
        const otherContainer = document.querySelector('.other-note__load');
        const trashContainer = document.querySelector('.trash-note__load');
        const labelContainer = document.querySelector('.label-note__load');

        icon.classList.toggle('fa-border-all');
        icon.classList.toggle('fa-bars');

        const containers = [pinnedContainer, otherContainer, trashContainer, labelContainer];

        containers.forEach(container => {
            if (!container) return;

            const hasGrid = container.classList.contains('load-grid');
            const fromClass = hasGrid ? 'load-grid' : 'load-list';
            const toClass = hasGrid ? 'load-list' : 'load-grid';

            this.animateTransition(container, toClass, fromClass);
        });
    }

    animateTransition(container, toClass, fromClass) {
        if (!container) {
            console.warn('animateTransition: container is null');
            return;
        }

        const noteCards = container.querySelectorAll('.note-sheet');
        const trashCards = container.querySelectorAll('.note-sheet-trash');
        const labelCards = container.querySelectorAll('.note-sheet-label');

        // Prioritize: normal notes > trash > label
        let cardsToAnimate = noteCards.length > 0 ? noteCards
            : trashCards.length > 0 ? trashCards
                : labelCards;

        if (cardsToAnimate.length === 0) return;

        cardsToAnimate.forEach(card => card.classList.add('animate-out'));

        setTimeout(() => {
            container.classList.remove(fromClass);
            container.classList.add(toClass);

            cardsToAnimate.forEach(card => {
                card.classList.remove('animate-out');
                card.classList.add('animate-in');

                setTimeout(() => card.classList.remove('animate-in'), 300);
            });
        }, 150);
    }

    handleSearch() {
        clearTimeout(this.searchTimeout);

        const query = this.searchInput.value.trim();
        if (!query) {
            this.clearSearchResults();
            return;
        }

        this.searchTimeout = setTimeout(() => {
            // Always clear before a new request to avoid stale UI
            this.clearSearchResults();
            this.performSearch(query);
        }, 300);
    }

    performSearch(query) {
        fetch(`/note/search?query=${encodeURIComponent(query)}`, {
            method: 'GET',
            headers: {
                "Content-Type": "application/json"
                // Add 'Authorization' if needed
            },
        })
            .then(res => res.json())
            .then(data => {
                if (data.status && Array.isArray(data.data) && data.data.length > 0) {
                    this.displaySearchResults(data.data);
                } else {
                    this.clearSearchResults(); // Show nothing if no results
                    const container = document.querySelector('.search-note__load');
                    container.innerHTML = 'No notes found';
                }
            })
            .catch(err => {
                console.error('Search failed:', err);
                this.clearSearchResults();
            });
    }

    displaySearchResults(notes) {
        const container = document.querySelector('.search-note__load');
        if (!container) return;

        // Clear existing search results
        container.innerHTML = '';

        const fragment = document.createDocumentFragment();

        notes.forEach(note => {
            const div = document.createElement("div");
            div.className = "note-sheet d-flex";
            div.dataset.noteId = note.noteId;
            div.dataset.noteTitle = note.title;
            div.dataset.noteContent = note.content;

            // Handle single image or array of images
            let imageHTML = '';
            if (Array.isArray(note.imageLinks)) {
                note.imageLinks.forEach(link => {
                    if (link && link.trim() !== '') {
                        imageHTML += `
                        <div class="note-sheet__image" style="width: 100%; height: auto; overflow-y: visible">
                            <img src="${link}" style="width: 100%; height: auto; display: block">
                        </div>`;
                    }
                });
            } else if (note.imageLink && note.imageLink.trim() !== '') {
                imageHTML = `
                <div class="note-sheet__image" style="width: 100%; height: auto; overflow-y: visible">
                    <img src="${note.imageLink}" style="width: 100%; height: auto; display: block">
                </div>`;
            }

            // Handle label storage
            if (note.labels) {
                try {
                    const labelsArray = typeof note.labels === 'string' ? note.labels.split(',') : note.labels;
                    div.dataset.labels = JSON.stringify(labelsArray);
                } catch (e) {
                    console.warn("Failed to parse or stringify labels:", e);
                }
            }

            div.innerHTML = `
            ${imageHTML}
            <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                <h3 class="note-sheet__title">${note.title}</h3>
                <div class="note-sheet__content" style="overflow-x: hidden;">
                    ${note.content.replace(/\n/g, '<br>')}
                </div>
            </div>
            <div class="note-sheet__menu">
                <div class="note-sheet__menu--item">
                    <button class="note-pin-btn" title="Pin Note"><i class="fa-solid fa-thumbtack-slash"></i></button>
                    <button title="Add Label" data-bs-target="listLabelNoteModal" class="note-label-list-btn"><i class="fa-solid fa-tags"></i></button>
                    <button class="note-delete-btn" title="Delete This Note" data-bs-target="deleteNoteModal" data-note-id="${note.noteId}"><i class="fa-solid fa-trash"></i></button>
                    <button class="note-share-btn" title="Share this Note"><i class="fa-solid fa-users"></i></button>
                    <button class="note-lock-btn" title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
                </div>
            </div>
        `;

            fragment.appendChild(div);
        });

        container.appendChild(fragment);
    }

    // Clear all results
    clearSearchResults() {
        const container = document.querySelector('.search-note__load');
        if (container) {
            container.innerHTML = '';
        }
    }

    // Prevent script injection via HTML (basic protection)
    escapeHTML(str) {
        if (!str) return '';
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

}

export default new HomeUser();