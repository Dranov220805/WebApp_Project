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
                                <?php
                                $labelAttr = htmlspecialchars(json_encode($note['labels'] ?? []));
                                ?>
                                <div class="note-sheet note-sheet-label d-flex"
                                     data-note-id="<?= htmlspecialchars($note['noteId']) ?>"
                                     data-note-title="<?= htmlspecialchars($note['title']) ?>"
                                     data-note-content="<?= htmlspecialchars($note['content']) ?>"
                                    <?php if (!empty($note['imageLink'])): ?>
                                        data-note-image="<?= htmlspecialchars($note['imageLink']) ?>"
                                    <?php endif; ?>
                                    <?php if (!empty($note['labels'])) :?>
                                        data-note-labels='<?= $labelAttr ?>'>
                                    <?php endif;?>
                                    <?php if (!empty($note['imageLink'])): ?>
                                        <div class="note-sheet__image" style="overflow: visible">
                                            <img src="<?= htmlspecialchars($note['imageLink']) ?>" style="display: block">
                                        </div>
                                    <?php endif; ?>

                                    <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                                        <h3 class="note-sheet__title"><?= htmlspecialchars($note['title']) ?></h3>
                                        <div class="note-sheet__content" style="overflow-x: hidden">
                                            <?= nl2br(htmlspecialchars($note['content'])) ?>
                                        </div>
                                    </div>

                                    <div class="note-sheet__menu">
                                        <div class="note-sheet__menu--item">
                                            <button title="Add Label" data-bs-target="listLabelNoteModal" id="note-label-list-btn" class="note-label-list-btn"><i class="fa-solid fa-tags"></i></button>
                                            <button class="note-label-delete-btn" title="Delete This Note" data-bs-target="deleteNoteModal" data-note-id="<?= htmlspecialchars($note['noteId']) ?>"><i class="fa-solid fa-trash"></i></button>
                                            <button class="note-share-btn" title="Share this Note"><i class="fa-solid fa-users"></i></button>
                                            <button class="note-lock-btn" title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
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
    <div class="modal fade" id="noteLabelModal" tabindex="-1" aria-labelledby="noteLabelModalLabel" data-bs-backdrop="true" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered note-detail__modal--dialog" style="margin-left: auto; margin-right: auto">
            <div class="modal-content note-detail__modal" style="overflow: auto">
                <div class="modal-content-body" style="height: inherit; overflow-y: auto; display: flex; flex-direction: column">
                    <div class="note-sheet__image" style="width: 100%; height: auto; overflow: visible">
                        <!--                        Render Image Link here-->
                    </div>
                    <div class="modal-header">
                        <input type="text" class="modal-title note-label-title-input-autosave form-control border-0" id="noteModalLabel"/>
                    </div>
                    <div class="modal-body" style="flex-grow: 1; min-height: 300px; height: fit-content; overflow-y: visible">
                        <textarea class="note-label-content-input-autosave form-control" style=" overflow-y: visible;"></textarea>
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