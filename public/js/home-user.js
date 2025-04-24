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

        this.loadUserPreference();
        this.attachEventListeners();
        this.checkVerification();
        this.handleAvatarUpload();
    }

    loadUserPreference() {
        const storedPrefs = sessionStorage.getItem('userPreferences');

        // If preferences already exist in sessionStorage, use them first
        if (storedPrefs) {
            this.applyPreferences(JSON.parse(storedPrefs)); // Use this.applyPreferences instead of just applyPreferences
        }

        // Fetch fresh preferences from server only if not cached, or for sync check
        fetch('/home/preferences')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load preferences');
                }
                return response.json();
            })
            .then(data => {
                if (!data.status || !data.data) {
                    throw new Error('Invalid preference data');
                }

                const prefs = data.data;

                // Compare with session storage
                const currentPrefs = storedPrefs ? JSON.parse(storedPrefs) : null;

                if (!currentPrefs || JSON.stringify(currentPrefs) !== JSON.stringify(prefs)) {
                    // Preferences differ or not stored — update and apply
                    sessionStorage.setItem('userPreferences', JSON.stringify(prefs));
                    this.applyPreferences(prefs); // Again, use this.applyPreferences
                }
            })
            .catch(error => {
                console.error('Error loading user preferences:', error);
            });
    }

    applyPreferences(prefs) {
        // Theme
        const themeSelector = document.getElementById('theme-selector');
        if (prefs.isDarkTheme === 1) {
            document.body.classList.add('dark-mode');
            if (themeSelector) themeSelector.value = 'dark';
        } else {
            document.body.classList.remove('dark-mode');
            if (themeSelector) themeSelector.value = 'light';
        }

        // Font size
        const fontSizeSelector = document.querySelector('select.form-select:not(#theme-selector)');
        if (prefs.noteFont) {
            document.body.style.fontSize = prefs.noteFont;

            if (fontSizeSelector) {
                fontSizeSelector.value =
                    prefs.noteFont === '14px' ? 'Small' :
                        prefs.noteFont === '16px' ? 'Medium' :
                            prefs.noteFont === '18px' ? 'Large' : '';
            }
        }

        // Font family
        document.body.style.fontFamily = prefs.font || 'Arial';

        // Note color
        document.documentElement.style.setProperty('--note-color', prefs.noteColor || '#000000');

        // Layout (used for note layout maybe?)
        document.body.setAttribute('data-layout', prefs.layout || 'list');
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

    handleAvatarUpload() {
        const form = document.getElementById('avatar-upload-form');
        const fileInput = document.getElementById('avatar-input');
        const uploadButton = document.querySelector('.btn-upload'); // Button for triggering file upload

        if (!form || !fileInput || !uploadButton) return;

        // Trigger file input when the upload button is clicked
        uploadButton.addEventListener('click', async(e) => {
            e.preventDefault();
            const selectedFile = fileInput.files[0];
            if (!selectedFile) return;

            const formData = new FormData();
            formData.append('avatar', selectedFile);

            try {
                const response = await fetch('/home/upload/avatar', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    this.showToast('Avatar uploaded successfully!', 'success');
                    // Optionally update avatar preview
                    const avatarIcon = document.querySelector('.user__item--icon');
                    if (avatarIcon) {
                        avatarIcon.style.backgroundImage = `url(${result.url})`;
                        avatarIcon.style.backgroundSize = 'cover';
                    }
                } else {
                    this.showToast('Upload failed: ' + result.error, 'danger');
                }
            } catch (err) {
                console.error('Upload error:', err);
                this.showToast('Something went wrong while uploading avatar', 'danger');
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
        fetch('auth/verification', {
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

    handleDeviceLayout() {

    }

    toggleLayout(event) {
        const icon = event.currentTarget.querySelector('i');
        if (!icon) return;

        const pinnedContainer = document.querySelector('.pinned-note__load');
        const otherContainer = document.querySelector('.other-note__load');

        const isGrid = icon.classList.contains('fa-border-all');

        icon.classList.toggle('fa-border-all');
        icon.classList.toggle('fa-bars');

        if (isGrid) {
            this.animateTransition(pinnedContainer, 'load-list', 'load-grid');
            this.animateTransition(otherContainer, 'load-list', 'load-grid');
        } else {
            this.animateTransition(pinnedContainer, 'load-grid', 'load-list');
            this.animateTransition(otherContainer, 'load-grid', 'load-list');
        }
    }

    animateTransition(container, toClass, fromClass) {
        const cards = container.querySelectorAll('.note-sheet');
        cards.forEach(card => card.classList.add('animate-out'));

        setTimeout(() => {
            container.classList.remove(fromClass);
            container.classList.add(toClass);

            cards.forEach(card => {
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
        }, 2000); // Delay of 2000ms
    }

    performSearch(query) {
        fetch(`/note/search?query=${encodeURIComponent(query)}`)
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
            div.dataset.id = note.noteId;

            div.innerHTML = `
            <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                <h3 class="note-sheet__title">${note.title}</h3>
                <div class="note-sheet__content">
                    ${note.content.replace(/\n/g, '<br>')}
                </div>
            </div>
            <div class="note-sheet__menu" onclick="event.stopPropagation()">
                <div>
                    <button class="search-note-pin-btn" title="Unpin Note"><i class="fa-solid fa-thumbtack"></i></button>
                    <button title="Label"><i class="fa-solid fa-tags"></i></button>
                    <button title="Image"><i class="fa-solid fa-images"></i></button>
                    <button class="search-note-edit-btn" title="Edit"><i class="fa-regular fa-pen-to-square"></i></button>
                    <button class="search-note-delete-btn" title="Delete" data-note-id="${note.noteId}"><i class="fa-solid fa-trash"></i></button>
                </div>
                <button><i class="fa-solid fa-ellipsis-vertical"></i></button>
            </div>
        `;
            // Append to container
            container.appendChild(div);
        });
    }

    clearSearchResults() {
        const container = document.querySelector('.search-note__load');
        container.innerHTML = ''; // Clear search results
    }
}

export default new HomeUser();