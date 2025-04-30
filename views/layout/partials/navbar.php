<?php
?>

<nav class="navbar fixed-top">
    <div class="d-flex align-items-center w-100 navbar__content">
        <!-- Left side: Menu icon (sidebar toggle) and Logo -->
        <div class="d-flex align-items-center" style="padding-right: 20px">
            <button id="sidebar-toggle" class="sidebar-toggle">
                <i class="navbar__item--icon fa-solid fa-bars"></i>
            </button>

            <a href="/home" class="d-flex align-items-center logo-link">
                <img height="24" src="/public/img/logo/logo-pernote-brand-nobg.png" alt="Pernote Logo">
            </a>
        </div>

        <!-- Middle: Search bar -->
        <div class="search-main-container">
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

            <a href="/home/account" type="button" class="btn icon-btn">
                <i class="navbar__item--icon fa-solid fa-gear"></i>
            </a>

            <button data-bs-toggle="modal" class="info-modal" data-bs-target="#infoModal" data-bs-backdrop="false" data-bs-scroll="true" style="display: flex; justify-content: center; align-items: center; height: 40px">
<!--                <i class="navbar__item--icon fa-regular fa-circle-user"></i>-->
                <?php if (!empty($_SESSION['profilePicture'])): ?>
                    <img src="<?= $_SESSION['profilePicture'] ?>" style="width: 30px; border-radius: 50px">
                <?php else: ?>
                    <i class="fa-regular fa-circle-user" style="font-size: 25px"></i>
                <?php endif; ?>
            </button>
        </div>
    </div>
</nav>

<!-- The Modal -->
<div class="modal fade" id="infoModal" data-bs-backdrop="false" data-bs-scroll="true">
    <div class="modal-dialog" style="margin-right: 10px; margin-left: auto">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" style="flex-grow: 1">Hello, <?=$_SESSION['userName']?></h4>
                <span style="display: flex; justify-content: center; align-items: center; width: 40px; height: 40px">
                    <?php if (!empty($_SESSION['profilePicture'])): ?>
                        <img src="<?= $_SESSION['profilePicture'] ?>" style="width: 30px; border-radius: 50px">
                    <?php else: ?>
                        <i class="fa-regular fa-circle-user" style="font-size: 25px"></i>
                    <?php endif; ?>
                </span>
<!--                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>-->
                <i class="fa-solid fa-xmark close-info-modal" data-bs-dismiss="modal" style="width: 40px; height: 40px; margin-left: 10px"></i>
            </div>

            <div class="modal-body">
                <strong>Username: </strong> <?=$_SESSION['userName']?>
                <br>
                <strong>Email: </strong> <?=$_SESSION['email']?>
                <br>
                <strong>Verify status: </strong>
                <?php if ($_SESSION['isVerified'] = 1) {
                    echo 'Verified';
                } else {
                    echo 'Not Verified';
                }
                ?>
            </div>

            <div class="modal-footer">
                <a href="/log/logout" type="button" class="btn btn-danger">Log out</a>
            </div>

        </div>
    </div>
</div>
