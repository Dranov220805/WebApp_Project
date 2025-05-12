<!-- Main container with sidebar and content -->
<div class="container main-container d-flex" style="margin-top: 56px;">

    <?php
    include "views/layout/partials/sidebar.php";
    ?>

    <!-- Main content area -->
    <div id="content" class="content" style="margin-left: 80px;">
        <div class="small-container">

            <!-- Trash Notes grid -->
            <div class="pinned-note">
                <h6 class="note-layout__title">Trash</h6>
                <div class="note-grid d-flex justify-content-center">
                    <div class="trash-note__load load-grid" style="display: flex; flex-wrap: wrap; gap: 16px; justify-content: center">
<!--                    Render other notes -->
                        <?php if (!empty($data['trashNotes'])): ?>
                            <?php foreach ($data['trashNotes'] as $note): ?>
                                <div class="note-sheet-trash note-sheet-trash-trash d-flex"
                                     data-note-id="<?= htmlspecialchars($note['noteId']) ?>"
                                     data-note-title="<?= htmlspecialchars($note['title']) ?>"
                                     data-note-content="<?= htmlspecialchars($note['content'])?>"
                                     data-note-image="<?= htmlspecialchars($note['imageLink'])?>">
                                    <?php if (!empty($note['imageLink'])) {?>
                                    <div class="note-sheet-trash__image" style="overflow: visible">
                                        <img src="<?= htmlspecialchars($note['imageLink'])?>" style="display: block">
                                    </div>
                                    <?php } ?>
                                    <div class="note-sheet-trash__title-content flex-column flex-grow-1" style="padding: 16px;">
                                        <h3 class="note-sheet-trash__title"><?= htmlspecialchars($note['title']) ?></h3>
                                        <div class="note-sheet-trash__content" style="overflow-x: hidden">
                                            <?= htmlspecialchars($note['content']) ?>
                                        </div>
                                    </div>
                                    <div class="note-sheet-trash__menu"">
                                        <div class="note-sheet__menu--item">
                                            <button class="note-restore-btn" title="Restore this note" data-note-id="<?= htmlspecialchars($note['noteId']) ?>"><i class="fa-solid fa-trash-arrow-up"></i></i></button>
                                            <button class="note-trash-delete-btn" title="Delete permanently" data-note-id="<?= htmlspecialchars($note['noteId']) ?>"><i class="fa-solid fa-eraser"></i></button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No trash notes available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Restore Confirmation Modal -->
    <div class="modal fade" id="restoreTrashNoteModal" tabindex="-1" aria-labelledby="restoreTrashNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content" style="width: 100%; height: 100%">
                <div class="modal-header">
                    <h5 class="modal-title" id="restoreTrashNoteModalLabel">Confirm Restore Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to restore this note?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="confirmRestoreTrashNoteBtn" type="button" class="btn btn-danger">Restore this note</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteTrashNoteModal" tabindex="-1" aria-labelledby="deleteTrashNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTrashNoteModalLabel">Confirm Delete Permanently</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to permanently delete this note?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="confirmDeleteTrashNoteBtn" type="button" class="btn btn-danger">Delete Permanently</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    include "./views/layout/partials/overlay_loading.php";
    ?>
</div>