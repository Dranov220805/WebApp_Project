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

        // toggleSidebar for desktop
        const toggleSidebar = () => {
            // toggleSidebar for width <= 780px
            if (window.innerWidth <= 780) {
                if (sidebarVisible) {
                    sidebar.style.transform = 'translateX(-100%)';
                    content.style.marginLeft = '0';
                    document.body.classList.remove('sidebar-visible');
                } else {
                    sidebar.style.transform = 'translateX(0)';
                    // content.style.marginLeft = window.innerWidth >= 768 ? '280px' : '0';
                    // content.style.width = '100%';
                    content.style.marginLeft = '0';
                    content.style.width = '100%';
                    document.body.classList.add('sidebar-visible');
                }
                sidebarVisible = !sidebarVisible;
            } else {
                // toggleSidebar for desktop width
                if (sidebarVisible) {
                    sidebar.style.transform = 'translateX(-100%)';
                    content.style.marginLeft = '0';
                    content.style.width = '100%';
                    document.body.classList.remove('sidebar-visible');
                } else {
                    sidebar.style.transform = 'translateX(0)';
                    content.style.transform = 'translateX(+220)';
                    content.style.marginLeft = window.innerWidth >= 768 ? '280px' : '0';
                    // content.style.marginLeft = '280';
                    content.style.width = '85%';
                    document.body.classList.add('sidebar-visible');
                }
                sidebarVisible = !sidebarVisible;
            }
        };

        const toggleSearch = () => {
            // Optional
        };


        sidebarItems.forEach(item => {
            item.addEventListener('mouseenter', () => {
                if (!item.classList.contains('active')) {
                    item.style.backgroundColor = '#f1f3f4';
                    item.style.transform = '0.3s ease-in-out';
                }
            });

            item.addEventListener('mouseleave', () => {
                if (!item.classList.contains('active')) {
                    item.style.backgroundColor = '';
                }
            });

            item.addEventListener('click', () => {
                sidebarItems.forEach(si => {
                    si.classList.remove('active');
                    si.style.backgroundColor = '#4D55CC';
                    si.querySelector('svg')?.setAttribute('fill', '#9aa0a6');
                    si.querySelector('span')?.style.setProperty('color', '#9aa0a6');
                });

                item.classList.add('active');
                item.style.backgroundColor = '#ffb435';
                item.querySelector('svg')?.setAttribute('fill', '#e8eaed');
                item.querySelector('span')?.style.setProperty('color', '#e8eaed');
            });
        });

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
