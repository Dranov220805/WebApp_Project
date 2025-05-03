<?php

?>

<!-- Main container with sidebar and content -->
<div class="container main-container d-flex" style="margin-top: 56px;">

    <?php
    include "views/layout/partials/sidebar.php";
    ?>

    <!-- Main content area -->
    <div id="content" class="content" style="margin-left: 80px;">
        <div class="small-container">

            <!-- Label Notes grid -->
            <div class="label-note">
                <h6 class="note-layout__title" id="note-layout__title"><?= htmlspecialchars($data['labelName']) ?></h6>
                <div class="note-grid d-flex justify-content-center">
                    <div class="label-note__load load-grid" style="display: flex; flex-wrap: wrap; gap: 16px; justify-content: center">
                        <?php if (!empty($data['data'])): ?>
                            <?php foreach ($data['data'] as $note): ?>
                                <div class="note-sheet-label d-flex flex-column"
                                     data-note-id="<?= htmlspecialchars($note['noteId']) ?>"
                                     data-note-title="<?= htmlspecialchars($note['title']) ?>"
                                     data-note-content="<?= htmlspecialchars($note['content']) ?>">
                                    <div class="note-sheet__image" style="width: 100%; height: auto; overflow: hidden">
                                        <img src="https://placehold.co/600x400" style="width: 100%; height: auto; display: block">
                                    </div>
                                    <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                                        <h3 class="note-sheet__title"><?= htmlspecialchars($note['title']) ?></h3>
                                        <div class="note-sheet__content">
                                            <?= nl2br(htmlspecialchars($note['content'])) ?>
                                        </div>
                                    </div>
                                    <div class="note-sheet__menu">
                                        <div>
                                            <button title="Add Label"><i class="fa-solid fa-tags"></i></button>
                                            <button class="note-label-delete-btn" title="Delete This Note" data-bs-target="deleteNoteModal" data-note-id="<?= htmlspecialchars($note['noteId']) ?>"><i class="fa-solid fa-trash"></i></button>
                                            <button title="Share this Note"><i class="fa-solid fa-users"></i></button>
                                            <button title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No <?= htmlspecialchars($data['labelName']) ?> notes available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Structure for show Note Detail-->
    <div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" data-bs-backdrop="true" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable note-modal-display position-fixed top-50 start-50 translate-middle fade show note-detail__modal--dialog" style="height: 60%">
            <div class="modal-content note-detail__modal">
                <div class="note-sheet__image" style="width: 100%; height: auto; overflow: hidden">
                    <img src="https://placehold.co/600x400" style="width: 100%; height: auto; display: block">
                </div>
                <div class="modal-header">
                    <input type="text" class="modal-title note-label-title-input-autosave form-control border-0" id="noteModalLabel"/>
                </div>
                <div class="modal-body">
                    <textarea class="note-label-content-input-autosave form-control"></textarea>
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
                        <a type="button" class="btn btn-primary note-image__post" id="triggerImageUpload">
                            <i class="fa-regular fa-images"></i> Add Image
                        </a>
                        <input type="hidden" name="noteId" id="noteIdInput">
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteNoteModal" tabindex="-1" aria-labelledby="deleteNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content" style="width: 100%; height: 100%">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteNoteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

    <?php
    include "./views/layout/partials/overlay_loading.php";
    ?>
</div>