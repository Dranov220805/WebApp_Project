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
        // this.checkVerification();
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

    showToast(message, type, duration = 3000) {
        const toast = document.getElementById("toast");
        const messageElement = document.getElementById("toast-message");
        const closeBtn = document.getElementById("toast-close");

        if (!toast || !messageElement || !closeBtn) return;

        messageElement.innerText = message;
        toast.classList.remove("d-none", "bg-success", "bg-danger");
        toast.classList.add(`bg-${type}`);

        toast.classList.remove("d-none");

        const hideTimeout = setTimeout(() => toast.classList.add("d-none"), duration);

        console.log("Toast:", { message, type, toastVisible: !toast.classList.contains('d-none') });
    }

    attachPreferenceSaveHandler() {
        const saveBtn = document.querySelector('.btn-save-preference');
        if (!saveBtn) return;

        saveBtn.addEventListener('click', async () => {
            const theme = document.getElementById('theme-selector')?.value || 'system';
            const fontSize = document.getElementById('font-size-selector')?.value || '14px';
            const selectedColorElement = document.querySelector('.color-option.active');
            const noteColor = selectedColorElement ? selectedColorElement.dataset.color : '#000000';

            const body = {
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
            this.performSearch(query);
        }, 300); // Delay of 300ms
    }

    performSearch(query) {
        fetch(`/note/search?query=${encodeURIComponent(query)}`, {
            method: 'GET',
            headers: {
                "Content-Type": "application/json" ,
                'Authorization': 'Bearer '
            },
        })
            .then(res => res.json())
            .then(data => {
                if (data.status && data.data.length > 0) {
                    this.displaySearchResults(data.data);
                } else {
                    this.clearSearchResults();
                }
            })
            .catch(err => console.error('Search failed:', err));
    }

    displaySearchResults(notes) {
        const container = document.querySelector('.search-note__load');
        if (!container) return;

        notes.forEach(note => {
            const div = document.createElement("div");
            div.className = "note-sheet d-flex flex-column";
            div.dataset.noteId = note.noteId;
            div.dataset.noteTitle = note.title;
            div.dataset.noteContent = note.content;

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
                <div class="note-sheet__content">
                    ${note.content.replace(/\n/g, '<br>')}
                </div>
            </div>
            <div class="note-sheet__menu">
                <div>
                    <button class="note-pin-btn" title="Pin Note"><i class="fa-solid fa-thumbtack"></i></button>
                    <button title="Add Label" data-bs-target="listLabelNoteModal" id="note-label-list-btn" class="note-label-list-btn"><i class="fa-solid fa-tags"></i></button>
                    <button class="note-delete-btn" title="Delete This Note" data-bs-target="deleteNoteModal" data-note-id="${note.noteId}"><i class="fa-solid fa-trash"></i></button>
                    <button title="Share this Note"><i class="fa-solid fa-users"></i></button>
                    <button title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
                </div>
            </div>
        `;
            // Append to container
            container.appendChild(div);
        });
    }

    clearSearchResults() {
        const container = document.querySelector('.search-note__load');
        container.innerHTML = '';
    }


}

export default new HomeUser();