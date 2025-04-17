<?php
?>

<nav class="navbar" style="padding: 8px 16px; background-color: #ffffff; border-bottom: 1px solid #e0e0e0; position: fixed; top: 0; width: 100%; z-index: 200;">
    <div class="d-flex align-items-center w-100">
        <!-- Left side: Menu icon (sidebar toggle) and Logo -->
        <div class="d-flex align-items-center">
            <button id="sidebar-toggle" class="sidebar-toggle" style="background: none; border: none; cursor: pointer; padding-left: 0px; padding-right: 10px">
                <i style="width: 40px; height: 40px; padding-top: 8px; padding-bottom: 8px; font-size: 24px" class="navbar__item--icon fa-solid fa-bars"></i>
            </button>

            <div class="d-flex align-items-center" style="margin-left: 4px;">
                <img class="" height="24" src="/public/img/logo/logo-pernote-brand-nobg.png" alt="Pernote Logo">
            </div>
        </div>

        <!-- Middle: Search bar -->
        <div class="flex-grow-1 d-flex justify-content-center">
            <div style="max-width: 720px; width: 100%; position: relative;">
                <div id="search-container" class="search-expanded" style="display: flex; background-color: #f1f3f4; border-radius: 8px; padding: 6px 8px; align-items: center; transition: width 0.3s ease;">
                    <button id="search-icon" style="background: none; border: none; cursor: pointer; padding: 8px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#5f6368">
                            <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path>
                        </svg>
                    </button>
                    <input id="search-input" type="text" placeholder="Search notes" style="border: none; background: transparent; flex-grow: 1; padding: 4px 8px; outline: none; font-size: 14px; width: 100%; opacity: 1; transition: width 0.3s ease, opacity 0.3s ease;">
                </div>
            </div>
        </div>

        <!-- Right side: Icons -->
        <div class="d-flex align-items-center">
            <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="#5f6368">
                    <path d="M4 8h4V4H4v4zm6 12h4v-4h-4v4zm-6 0h4v-4H4v4zm0-6h4v-4H4v4zm6 0h4v-4h-4v4zm6-10v4h4V4h-4zm-6 4h4V4h-4v4zm6 6h4v-4h-4v4zm0 6h4v-4h-4v4z"></path>
                </svg>
            </button>
            <button style="background: none; border: none; cursor: pointer; padding: 8px; margin-left: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="#5f6368">
                    <path d="M19.43 12.98c.04-.32.07-.64.07-.98s-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65A.488.488 0 0 0 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.23-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98s.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.23.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zM12 15.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z"></path>
                </svg>
            </button>
            <button style="background: none; border: none; cursor: pointer; padding: 8px; margin-left: 8px;">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="#5f6368">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"></path>
                </svg>
            </button>
        </div>
    </div>
</nav>

</div>

<!-- CSS for styling -->
<style>

    .sidebar-item:not(.active):hover {
        background-color: #ffffff;
    }

    .search-collapsed {
        width: 48px;
    }

    .search-expanded {
        width: 100%;
    }

    /* For mobile devices */
    @media (max-width: 780px) {
        .content {
            width: 100%;
            margin-left: 0 !important; /* Ensure margin is always 0 on mobile */
        }

        .note-sheet {
            width: 100%;
        }

        /* When sidebar is visible on mobile, add overlay */
        body.sidebar-visible::after {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 1);
            z-index: 50;
        }
    }

    @media (max-width: 480px) {
        .navbar .flex-grow-1 {
            margin: 0 8px;
        }

        .navbar .d-flex.align-items-center:last-child {
            display: none !important;
        }
    }
</style>
