class HomeUser {
    constructor() {
        document.addEventListener('DOMContentLoaded', this.initialize.bind(this));
    }

    initialize() {
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        const sidebarItems = document.querySelectorAll('.sidebar-item');
        let sidebarVisible = false;

        const searchContainer = document.getElementById('search-container');
        const searchIcon = document.getElementById('search-icon');
        const searchInput = document.getElementById('search-input');
        let searchExpanded = false;

        // toggleSidebar function
        const toggleSidebar = () => {
            if (window.innerWidth <= 780) {
                // Mobile behavior - slide in/out
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
                // Desktop behavior - expand/collapse
                if (sidebarVisible) {
                    sidebar.classList.remove('expanded');
                    sidebar.classList.add('collapsed');
                    content.style.marginLeft = '80px'; // Match collapsed width
                } else {
                    sidebar.classList.remove('collapsed');
                    sidebar.classList.add('expanded');
                    content.style.marginLeft = '240px'; // Match expanded width
                }
            }
            sidebarVisible = !sidebarVisible;
        };

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth <= 780) {
                // Mobile layout
                content.style.marginLeft = '0';
                if (!sidebarVisible) {
                    sidebar.style.transform = 'translateX(-100%)';
                }
            } else {
                // Desktop layout
                sidebar.style.transform = 'translateX(0)';
                content.style.marginLeft = sidebar.classList.contains('collapsed') ? '60px' : '280px';
            }
        });

        // Initialize sidebar state
        if (window.innerWidth <= 780) {
            // sidebar.style.transform = 'translateX(-100%)';
        } else {
            // content.style.marginLeft = '60px';
        }

        const toggleSearch = () => {
            // Optional
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

        const checkWindowSize = () => {
            // if (window.innerWidth >= 768 && sidebarVisible) {
            //     content.style.marginLeft = '280px';
            // } else {
            //     content.style.marginLeft = '0';
            // }
        };

        window.addEventListener('resize', checkWindowSize);
        checkWindowSize();
    }
}

export default new HomeUser();
