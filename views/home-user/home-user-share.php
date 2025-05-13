<!-- Main container with sidebar and content -->
<?php
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Shared with me</h4>
            </div>

    <div class="row g-3 share-note-grid">
        <?php if (!empty($data['data'])): ?>
            <?php
            // Group notes by noteId and aggregate labels
            $groupedNotes = [];
            foreach ($data['data'] as $note) {
                $noteId = $note['noteId'];
                if (!isset($groupedNotes[$noteId])) {
                    $groupedNotes[$noteId] = $note;
                    $groupedNotes[$noteId]['labels'] = [];
                }
                $groupedNotes[$noteId]['labels'][] = $note['labelName'];
            }
            ?>

            <?php foreach ($groupedNotes as $note): ?>
                <div class="col-12">
                    <div class="card shared-note-card" style="max-height: 230px">
                        <div class="card-body">
                            <div class="d-flex justify-content-start" style="width: 100%">
                                <div class="" style="width: 100%">
                                    <div class="small text-muted mb-1">
                                        Shared by <strong><?= htmlspecialchars($note['sharedEmail']) ?></strong>
                                    </div>
                                    <div class="text-muted small mb-2">
                                        Shared on <?= date('M d, Y', strtotime($note['timeShared'])) ?>
                                    </div>

                                    <!-- Optional: Title & Content if available -->
                                    <?php if (!empty($note['title'])): ?>
                                        <h6 class="fw-bold"><?= htmlspecialchars($note['title']) ?></h6>
                                    <?php endif; ?>
                                    <?php if (!empty($note['content'])): ?>
                                        <p class="mb-1 text-muted" style="overflow-y: hidden; max-height: 48px"><?= htmlspecialchars($note['content']) ?></p>
                                    <?php endif; ?>

                                    <!-- Labels -->
                                    <?php if (!empty($note['labels'])) {?>
                                        <div class="mt-2">
                                            <?php foreach ($note['labels'] as $label): ?>
                                                <?php if(!empty($label)) {?>
                                                    <span class="badge bg-secondary me-1"><?= htmlspecialchars($label) ?></span>
                                            <?php }?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php }?>
                                </div>

                                <div class="text-end" style="display: flex; flex-direction: column; width: 100px; justify-content: space-between; align-items: center">
                                    <?php if ($note['canEdit']): ?>
                                        <span class="access-label access-edit" style="width: fit-content">Can edit</span>
                                    <?php else: ?>
                                        <span class="access-label access-readonly" style="width: fit-content">Read-only</span>
                                    <?php endif; ?>
                                    <?php if (!empty($note['imageLink'])) {?>
                                        <div style="height: 100%; width: auto; max-height: 100px">
                                            <img src="<?= htmlspecialchars($note['imageLink']) ?>" class="rounded mt-2" alt="User Avatar" style="margin-top: 0px; height: 100%; width: auto">
                                        </div>
                                    <?php }?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No shared notes found.</p>
        <?php endif; ?>
    </div>

<!--            <div class="row g-3 share-note-grid">-->
<!--                Card 1 -->
<!--                <div class="col-12">-->
<!--                    <div class="card shared-note-card">-->
<!--                        <div class="card-body">-->
<!--                            <div class="d-flex justify-content-between">-->
<!--                                <div>-->
<!--                                    <div class="small text-muted mb-1">Shared by <strong>alex@example.com</strong></div>-->
<!--                                    <div class="text-muted small mb-2">Shared on May 12, 2025</div>-->
<!--                                    <h6 class="fw-bold">Project Timeline</h6>-->
<!--                                    <p class="mb-1 text-muted">Timeline and milestones for Q2 2025...</p>-->
<!--                                </div>-->
<!--                                <div class="text-end">-->
<!--                                    <span class="access-label access-readonly">Read-only</span>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
        </div>
    </div>

    <!-- Modal Structure for show Note Detail-->
    <div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" data-bs-backdrop="true" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable note-modal-display position-fixed top-50 start-50 translate-middle fade show note-detail__modal--dialog" style="height: 80%">
            <div class="modal-content note-detail__modal" style="overflow: auto">
                <div class="modal-content-body" style="height: inherit; overflow-y: auto; display: flex; flex-direction: column">
                    <div class="note-sheet__image" style="width: 100%; height: auto; overflow: visible">
                        <!--Render Image Link here-->
                    </div>
                    <div class="modal-header">
                        <input type="text" class="modal-title note-title-input-autosave form-control border-0" id="noteModalLabel"/>
                    </div>
                    <div class="modal-body" style="flex-grow: 1; min-height: 300px; height: fit-content; overflow-y: visible">
                        <textarea class="note-content-input-autosave form-control" style=" overflow-y: visible;"></textarea>
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

    <!-- Add Label Note Modal -->
    <div class="modal fade" id="addLabelNoteModal" tabindex="-1" aria-labelledby="addLabelNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content" style="width: 100%; height: 100%">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLabelNoteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
</div>