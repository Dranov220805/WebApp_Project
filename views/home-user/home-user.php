<?php

$currentPage = $_SERVER['REQUEST_URI'];
$segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));

$showDiv = (count($segments) === 1 && $segments[0] === 'home');

    /*
     * @var $data
     * */

?>
<div class="container main-container d-flex" style="margin-top: 56px;">

    <?php
        include "views/layout/partials/sidebar.php";
    ?>

    <!-- Main content area -->
    <div id="content" class="content" style="margin-left: 80px;">
        <div class="small-container">
            <!-- Note creation area -->
            <div class="note-post">
                <form class="note-post__content" style="display: flex; flex-direction: column;" action="#" onsubmit="return false">
                    <input class="note-text__content d-none" placeholder="Title">
                    <textarea class="note-post__input" placeholder="Take a note..." rows="1"></textarea>
                    <div style="display: flex; justify-content: space-between; margin-top: 12px; height: 40px">
                        <div class="note-post__menu">

                        </div>
                        <button class="btn create-note-btn">
                            Create
                        </button>
                    </div>
                </form>
            </div>

            <?php if ($showDiv) {?>
                <!-- Middle: Search bar -->
                <div class="search-main-container mt-4">
                    <div class="search-bar-container">
                        <div id="search-container" class="search-expanded">
                            <button id="search-icon" class="search-icon-btn">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            <input id="search-input" type="text" placeholder="Search notes" class="search-input">
                        </div>
                    </div>
                </div>
            <?php } ?>

<!--            Search Note grid-->
            <div class="pinned-note">
                <h6 class="note-layout__title">Search Result</h6>
                <div class="note-grid d-flex justify-content-center">
                    <div class="search-note__load load-grid" style="display: flex; flex-wrap: wrap; gap: 16px; justify-content: center">
<!--                        Search Result Note Grid-->
                    </div>
                </div>
            </div>

<!--            Pinned Notes grid -->
            <div class="pinned-note">
                <h6 class="note-layout__title">Pinned</h6>
                <div class="note-grid d-flex justify-content-center">
                    <div class="pinned-note__load load-grid" style="display: flex; flex-wrap: wrap; gap: 16px; justify-content: center">
                        <!-- Render pinned notes -->
                    </div>
                </div>
            </div>

<!--            Other Note Grid-->
            <div class="other-note">
                <h6 class="note-layout__title">Others</h6>
                <div class="note-grid d-flex justify-content-center">
                    <div class="other-note__load load-grid" style="display: flex; flex-wrap: wrap; gap: 16px; justify-content: center">
                        <!-- Render other notes -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Structure for show Note Detail-->
    <div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" data-bs-backdrop="true" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable note-detail__modal--dialog" style="margin-left: auto; margin-right: auto">
            <div class="modal-content note-detail__modal" style="overflow: auto">
                <div class="modal-content-body" style="height: inherit; overflow-y: auto; display: flex; flex-direction: column">
                    <div class="note-sheet__image" style="width: 100%; height: auto; overflow: visible">
                        <!--                        Render Image Link here-->
                    </div>
                    <div class="modal-header">
                        <input type="text" class="modal-title note-title-input-autosave form-control border-0" id="noteModalLabel"/>
                    </div>
                    <div class="modal-body" style="flex-grow: 1; min-height: 300px; height: fit-content; overflow-y: visible">
                        <textarea class="note-content-input-autosave form-control" style="overflow-y: visible;"></textarea>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-start align-items-center">
                    <div class="save-status-icon d-flex flex-row flex-grow-1">
                        <p class="text-success" style="padding-right: 5px; margin-bottom: 0px; align-items: center;">Saved</p>
                        <span>
                            <i class="fa-solid fa-check-circle text-success"></i>
                        </span>
                    </div>
                    <form id="imageUploadForm" action="#" onsubmit="return false" enctype="multipart/form-data">
                        <input type="file" name="image" id="imageInput" style="display: none;">
                        <a type="button" class="btn btn-success note-image__post" id="triggerImageUpload">
                            <i class="fa-regular fa-images"></i>
                        </a>
                        <input type="hidden" name="noteId" id="noteIdInput">
                    </form>
                    <a type="button" class="btn btn-danger note-image__delete" id="triggerImageDelete">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteNoteModal" tabindex="-1" aria-labelledby="deleteNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="margin-left: auto; margin-right: auto">
            <div class="modal-content" style="width: 100%; height: 100%">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteNoteModalLabel">Confirm Delete</h5>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this note?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="confirmDeleteNoteBtn" type="button" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Label Note Modal -->
    <div class="modal fade" id="addLabelNoteModal" tabindex="-1" aria-labelledby="addLabelNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="margin-left: auto; margin-right: auto">
            <div class="modal-content" style="width: 100%; height: 100%">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLabelNoteModalLabel">Confirm Delete</h5>
                </div>
                <div class="modal-body">
                    Add this note to which label ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="confirmDeleteNoteBtn" type="button" class="btn btn-danger">Okay</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Note Modal -->
    <div class="modal fade" id="shareNoteModal" tabindex="-1" aria-labelledby="shareNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="margin-left: auto; margin-right: auto">
            <div class="modal-content share-note-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareNoteModalLabel">Share Note</h5>
                </div>

                <div class="modal-body">
                    <!-- Note Details -->
                    <div class="mb-3">
                        <h6 class="fw-bold mb-1">Note Details</h6>
                        <div class="share-note__detail p-2" style="height: 90px; overflow: hidden">
                            <strong class="shared-note--title">Nothing</strong><br>
                            <small class="shared-note--content" style="max-height: 100px; height: 100%">Nothing in here...</small>
                        </div>
                    </div>

                    <!-- Share with -->
                    <div class="mb-3">
                        <label class="form-label">Share with</label>
                        <div class="d-flex">
                            <input id="share--email__input" type="email" class="form-control me-2" placeholder="Enter email address">
                            <button id="share--email__btn" class="btn btn-primary">Add</button>
                        </div>
                    </div>

                    <!-- People with access -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">People with access</label>
                        <div id="email--shared__list" class="list-group">
                            <!-- Fill data in here -->
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CreateNote Password Modal -->
<div class="modal fade" id="createPasswordNoteModal" tabindex="-1" aria-labelledby="createPasswordNoteModalLabel" aria-hidden="true" style="height: 100%; max-height: 100%">
    <div class="modal-dialog modal-dialog-centered" style="margin-left: auto; margin-right: auto;">
        <div class="modal-content" style="width: 100%; height: 100%; max-height: fit-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPasswordNoteModalLabel">Set up new password</h5>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="password-input" class="form-label">Enter your new note password:</label>
                    <div class="input-group">
                        <input type="password" class="form-control login-section-2__form--input-box" id="note-password--create__input" placeholder="Password">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password-input" class="form-label">Confirm new note password:</label>
                    <div class="input-group">
                        <input type="password" class="form-control login-section-2__form--input-box" id="note-password--create-confirm__input" placeholder="Password">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button id="confirmPasswordCreateNoteBtn" type="button" class="btn btn-success">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Password Modal -->
<div class="modal fade" id="updatePasswordNoteModal" tabindex="-1" aria-labelledby="updatePasswordNoteModalLabel" aria-hidden="true" style="height: 100%; max-height: 100%">
    <div class="modal-dialog modal-dialog-centered" style="margin-left: auto; margin-right: auto;">
        <div class="modal-content" style="width: 100%; height: 100%; max-height: fit-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatePasswordNoteModalLabel">Update note password</h5>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="password-input" class="form-label">Enter your old note password:</label>
                    <div class="input-group">
                        <input type="password" class="form-control login-section-2__form--input-box" id="note-password--old__input" placeholder="Password">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password-input" class="form-label">Enter your new note password:</label>
                    <div class="input-group">
                        <input type="password" class="form-control login-section-2__form--input-box" id="note-password--update__input" placeholder="Password">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password-input" class="form-label">Confirm your new note password:</label>
                    <div class="input-group">
                        <input type="password" class="form-control login-section-2__form--input-box" id="note-password--update-confirm__input" placeholder="Password">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="passwordDeleteNoteBtn" type="button" class="btn btn-danger">Delete password</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button id="confirmPasswordUpdateNoteBtn" type="button" class="btn btn-success">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Note Password Modal -->
<div class="modal fade" id="passwordNoteModal" tabindex="-1" aria-labelledby="passwordNoteModalLabel" aria-hidden="true" style="height: 100%; max-height: 100%">
    <div class="modal-dialog modal-dialog-centered" style="margin-left: auto; margin-right: auto;">
        <div class="modal-content" style="width: 100%; height: 100%; max-height: fit-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordNoteModalLabel">This note is locked</h5>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="password-input" class="form-label">Enter your note password:</label>
                    <div class="input-group">
                        <input type="password" class="form-control login-section-2__form--input-box" id="note-password__input" placeholder="Password">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button id="confirmPasswordNoteBtn" type="button" class="btn btn-success">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Note Password Modal -->
<div class="modal fade" id="deletePasswordNoteModal" tabindex="-1" aria-labelledby="deletePasswordNoteModalLabel" aria-hidden="true" style="height: 100%; max-height: 100%">
    <div class="modal-dialog modal-dialog-centered" style="margin-left: auto; margin-right: auto;">
        <div class="modal-content" style="width: 100%; height: 100%; max-height: fit-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePasswordNoteModalLabel">This note is locked</h5>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="password-input" class="form-label">Enter your note password:</label>
                    <div class="input-group">
                        <input type="password" class="form-control login-section-2__form--input-box" id="note-password-delete__input" placeholder="Password">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button id="confirmDeletePasswordNoteBtn" type="button" class="btn btn-success">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for loading note content -->
<script src="/public/js/note.js" type="module"></script>
