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

        this.sidebarVisible = false;
        this.searchExpanded = false;

        this.attachEventListeners();
        this.checkVerification();
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
        // this.sidebar?.addEventListener('mouseenter', () => this.toggleSidebar());
        // this.sidebar?.addEventListener('mouseleave', () => this.toggleSidebar());
        this.searchIcon?.addEventListener('click', () => this.toggleSearch());

        this.toggleGridBtns.forEach(btn =>
            btn.addEventListener('click', (e) => this.toggleLayout(e))
        );

        // document.addEventListener('click', (event) => this.handleGlobalClick(event));
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

    toggleSearch() {
        if (!this.searchExpanded) {
            this.searchContainer.classList.add('expanded');
            this.searchInput.focus();
        } else {
            this.searchContainer.classList.remove('expanded');
            this.searchInput.value = '';
        }

        this.searchExpanded = !this.searchExpanded;
    }

    // handleGlobalClick(event) {
    //     if (
    //         this.searchExpanded &&
    //         !this.searchContainer.contains(event.target) &&
    //         !this.searchIcon.contains(event.target)
    //     ) {
    //         this.toggleSearch();
    //     }
    //
    //     if (
    //         this.sidebarVisible &&
    //         window.innerWidth < 800 &&
    //         !this.sidebar.contains(event.target) &&
    //         !this.sidebarToggle.contains(event.target)
    //     ) {
    //         this.toggleSidebar();
    //     }
    // }

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
}

export default new HomeUser();