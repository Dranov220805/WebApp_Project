class HomeUser {
    constructor() {
        document.addEventListener('DOMContentLoaded', () => this.initialize());
    }

    initialize() {
        this.sidebarToggle = document.getElementById('sidebar-toggle');
        this.sidebar = document.getElementById('sidebar');
        this.sidebar = document.getElementById('sidebar');
        this.content = document.getElementById('content');
        this.sidebarItems = document.querySelectorAll('.sidebar-item');
        this.toggleGridBtns = document.querySelectorAll('.toggle-grid');
        this.toggleListBtns = document.querySelectorAll('.toggle-list');
        this.searchContainer = document.getElementById('search-container');
        this.searchIcon = document.getElementById('search-icon');
        this.searchInput = document.getElementById('search-input');
        this.searchTimeout = null;

        this.sidebarVisible = false;
        this.searchExpanded = false;

        this.attachEventListeners();
        // this.checkVerification();
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
                this.showToast('Account has not been verified','warning');
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
        }, 500); // Delay of 500ms
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