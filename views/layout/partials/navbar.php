<?php
?>

<nav class="navbar" style="padding: 8px 16px; background-color: #ffffff; border-bottom: 1px solid #e0e0e0; position: fixed; top: 0; width: 100%; z-index: 200;">
    <div class="d-flex align-items-center w-100">
        <!-- Left side: Menu icon (sidebar toggle) and Logo -->
        <div class="d-flex align-items-center">
            <button id="sidebar-toggle" class="sidebar-toggle" style="background: none; border: none; cursor: pointer; padding-left: 0px; padding-right: 30px">
                <i class="navbar__item--icon fa-solid fa-bars" style="color: #5f6368"></i>
            </button>

            <a href="/home" class="d-flex align-items-center" style="margin-left: 4px;">
                <img class="" height="24" src="/public/img/logo/logo-pernote-brand-nobg.png" alt="Pernote Logo">
            </a>
        </div>

        <!-- Middle: Search bar -->
        <div class="flex-grow-1 d-flex justify-content-center">
            <div style="max-width: 720px; width: 100%; position: relative;">
                <div id="search-container" class="search-expanded" style="display: flex; background-color: #f1f3f4; border-radius: 8px; padding: 6px 8px; align-items: center; transition: width 0.3s ease;">
                    <button id="search-icon" style="color: #5f6368; background: none; border: none; cursor: pointer; padding: 8px;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <input id="search-input" type="text" placeholder="Search notes" style="border: none; background: transparent; flex-grow: 1; padding: 4px 8px; outline: none; font-size: 14px; width: 100%; opacity: 1; transition: width 0.3s ease, opacity 0.3s ease;">
                </div>
            </div>
        </div>

        <!-- Right side: Icons -->
        <div class="d-flex align-items-center">
            <!-- Grid button with a popover -->
            <button type="button" class="btn toggle-grid" style="background: none; border: none; cursor: pointer; padding: 8px; margin-left: 8px;">
                <i class="navbar__item--icon fa-solid fa-border-all" style="color: #5f6368;"></i>
            </button>

            <!-- Setting button with a popover -->
            <a href="/user/preferences" type="button" class="btn" style="color: #5f6368; background: none; border: none; cursor: pointer; padding: 8px; margin-left: 8px;">
                <i class="navbar__item--icon fa-solid fa-gear"></i>
            </a>

            <!-- Your button with a popover -->
            <button type="button" class="btn" data-bs-toggle="popover" data-bs-html="true" title="User Info"
                    data-bs-content="<strong>Name:</strong> <?=$_SESSION['userName']?>
                    <br><strong>Email:</strong> <?=$_SESSION['email']?>
                    <br><strong>Role:</strong> <?=$_SESSION['roleId']?>
                    <br><strong>Verify Status:</strong> <?=$_SESSION['isVerified']?>"
                    style="background: none; border: none; cursor: pointer; padding: 8px; margin-left: 8px;">
                <i class="navbar__item--icon fa-regular fa-circle-user" style="color: #5f6368;"></i>
            </button>
            <button data-bs-toggle="modal" class="info-modal" data-bs-target="#myModal" data-bs-backdrop="false" data-bs-scroll="true">
                <i class="navbar__item--icon fa-regular fa-circle-user" style="color: #5f6368;"></i>
            </button>
        </div>
    </div>
</nav>

<!-- Modal place here-->

<!-- The Modal -->
<div class="modal fade" id="myModal" data-bs-backdrop="false" data-bs-scroll="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Modal Heading</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <strong>AccountId:</strong> <?=$_SESSION['accountId']?>
                <br>
                <strong>Name:</strong> <?=$_SESSION['userName']?>
                <br>
                <strong>Email:</strong> <?=$_SESSION['email']?>
                <br>
                <strong>Role:</strong> <?=$_SESSION['roleId']?>
                <br>
                <strong>Verfy status:</strong> <?=$_SESSION['isVerified']?>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <a href="/log/logout" type="button" class="btn btn-danger">Log out</a>
            </div>

        </div>
    </div>
</div>

</div>

<!-- CSS for styling -->
<style>
    .navbar__item--icon {
        border: 1px solid white;
        border-radius: 50%;
        transition: background-color 0.3s ease;

        width: 40px;
        height: 40px;
        padding-top: 8px;
        padding-bottom: 8px;
        font-size: 24px
    }

    .info-modal {
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px;
        margin-left: 8px;
    }

    .navbar__item--icon:hover {
        background-color: #f1f3f4;
    }

    .modal-dialog {
        margin-right: 30px;
        margin-left: auto;
    }

    .modal-backdrop {
        display: none !important;
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

<!-- Initialize the popover with JavaScript -->
<script>
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popoverList = [...popoverTriggerList].map(
        popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl)
    );
</script>