class HomeUser {
    constructor() {
        document.addEventListener('DOMContentLoaded', this.initialize.bind(this));
    }

    initialize() {
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        const sidebarItems = document.querySelectorAll('.sidebar-item');
        const toggleGridBtns = document.querySelectorAll('.toggle-grid');
        const toggleListBtns = document.querySelectorAll('.toggle-list');
        const searchContainer = document.getElementById('search-container');
        const searchIcon = document.getElementById('search-icon');
        const searchInput = document.getElementById('search-input');

        let sidebarVisible = false;
        let searchExpanded = false;

        const toggleSidebar = () => {
            if (window.innerWidth <= 780) {
                // Mobile
                if (sidebarVisible) {
                    sidebar.style.transform = 'translateX(-100%)';
                    content.style.marginLeft = '0';
                    document.body.classList.remove('sidebar-visible');
                } else {
                    sidebar.style.transform = 'translateX(0)';
                    content.style.marginLeft = '0';
                    document.body.classList.add('sidebar-visible');
                }
            } else {
                // Desktop
                if (sidebarVisible) {
                    sidebar.classList.remove('expanded');
                    sidebar.classList.add('collapsed');
                    content.style.marginLeft = '80px';
                } else {
                    sidebar.classList.remove('collapsed');
                    sidebar.classList.add('expanded');
                    content.style.marginLeft = '240px';
                }
            }

            sidebarVisible = !sidebarVisible;
        };

        const toggleLayout = (e) => {
            const pinnedContainer = document.querySelector('.pinned-note__load');
            const otherContainer = document.querySelector('.other-note__load');
            const icon = e.currentTarget.querySelector('i');

            if (!icon) return;

            const isGrid = icon.classList.contains('fa-border-all');

            // Toggle icons
            icon.classList.toggle('fa-border-all');
            icon.classList.toggle('fa-bars');

            // Define transition helper
            const animateTransition = (container, toClass, fromClass) => {
                const cards = container.querySelectorAll('.note-sheet');
                cards.forEach(card => {
                    card.classList.add('animate-out');
                });

                setTimeout(() => {
                    container.classList.remove(fromClass);
                    container.classList.add(toClass);

                    cards.forEach(card => {
                        card.classList.remove('animate-out');
                        card.classList.add('animate-in');

                        // Remove animation class after it's done
                        setTimeout(() => card.classList.remove('animate-in'), 300);
                    });
                }, 150); // Allow animate-out to play
            };

            if (isGrid) {
                // Grid → List
                animateTransition(pinnedContainer, 'load-list', 'load-grid');
                animateTransition(otherContainer, 'load-list', 'load-grid');
            } else {
                // List → Grid
                animateTransition(pinnedContainer, 'load-grid', 'load-list');
                animateTransition(otherContainer, 'load-grid', 'load-list');
            }
        };

        toggleGridBtns.forEach(btn => {
            btn.addEventListener('click', toggleLayout);
        });

        // Optional search toggle
        const toggleSearch = () => {
            if (!searchExpanded) {
                searchContainer.classList.add('expanded');
                searchInput.focus();
            } else {
                searchContainer.classList.remove('expanded');
                searchInput.value = '';
            }
            searchExpanded = !searchExpanded;
        };

        sidebarToggle?.addEventListener('click', toggleSidebar);
        searchIcon?.addEventListener('click', toggleSearch);

        document.addEventListener('click', event => {
            if (searchExpanded &&
                !searchContainer.contains(event.target) &&
                !searchIcon.contains(event.target)) {
                toggleSearch();
            }

            if (sidebarVisible && window.innerWidth < 768 &&
                !sidebar.contains(event.target) &&
                !sidebarToggle.contains(event.target)) {
                toggleSidebar();
            }
        });
    }

}

export default new HomeUser();
