<?php
?>

<nav class="navbar fixed-top">
    <div class="d-flex align-items-center w-100">
        <!-- Left side: Menu icon (sidebar toggle) and Logo -->
        <div class="d-flex align-items-center" style="padding-right: 25px">
            <button id="sidebar-toggle" class="sidebar-toggle">
                <i class="navbar__item--icon fa-solid fa-bars"></i>
            </button>

            <a href="/home" class="d-flex align-items-center logo-link">
                <img height="24" src="/public/img/logo/logo-pernote-brand-nobg.png" alt="Pernote Logo">
            </a>
        </div>

        <!-- Middle: Search bar -->
        <div class="flex-grow-1 d-flex justify-content-center">
            <div class="search-bar-container">
                <div id="search-container" class="search-expanded">
                    <button id="search-icon" class="search-icon-btn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <input id="search-input" type="text" placeholder="Search notes" class="search-input">
                </div>
            </div>
        </div>

        <!-- Right side: Icons -->
        <div class="d-flex align-items-center">
            <button type="button" class="btn toggle-grid icon-btn">
                <i class="navbar__item--icon fa-solid fa-border-all"></i>
            </button>

            <a href="/user/preferences" type="button" class="btn icon-btn">
                <i class="navbar__item--icon fa-solid fa-gear"></i>
            </a>

            <button data-bs-toggle="modal" class="info-modal" data-bs-target="#myModal" data-bs-backdrop="false" data-bs-scroll="true">
                <i class="navbar__item--icon fa-regular fa-circle-user"></i>
            </button>
        </div>
    </div>
</nav>

<!-- The Modal -->
<div class="modal fade" id="myModal" data-bs-backdrop="false" data-bs-scroll="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Modal Heading</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <strong>AccountId:</strong> <?=$_SESSION['accountId']?>
                <br>
                <strong>Name:</strong> <?=$_SESSION['userName']?>
                <br>
                <strong>Email:</strong> <?=$_SESSION['email']?>
                <br>
                <strong>Role:</strong> <?=$_SESSION['roleId']?>
                <br>
                <strong>Verify status:</strong> <?=$_SESSION['isVerified']?>
            </div>

            <div class="modal-footer">
                <a href="/log/logout" type="button" class="btn btn-danger">Log out</a>
            </div>

        </div>
    </div>
</div>
