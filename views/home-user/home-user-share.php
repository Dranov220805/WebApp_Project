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
            <!-- Shared Notes grid -->
            <div class="label-note">
                <h6 class="note-layout__title" id="note-layout__title">Shared with me</h6>
                <div class="note-grid d-flex justify-content-center">
                    <div class="share-note__load load-grid" style="display: flex; flex-wrap: wrap; gap: 16px; justify-content: center; width: 100%">
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
                                <?php
                                $labelAttr = htmlspecialchars(json_encode($note['labels'] ?? []));
                                ?>
                                <div class="col-12">
                                    <div class="card shared-note-card" style="max-height: 230px"
                                        data-note-id="<?= htmlspecialchars($note['noteId']) ?>"
                                        data-note-title="<?= htmlspecialchars($note['title']) ?>"
                                        data-note-content="<?= htmlspecialchars($note['content']) ?>"
                                        <?php if (!empty($note['imageLink'])): ?>
                                        data-note-image="<?= htmlspecialchars($note['imageLink']) ?>"
                                        <?php endif; ?>
                                        <?php if (!empty($note['labels'])) : ?>
                                        data-note-labels='<?= $labelAttr ?>'
                                        <?php endif; ?>
                                        <?php if ($note['canEdit']): ?>
                                        data-note-edit="true"
                                        <?php else: ?>
                                        data-note-edit="false"
                                        <?php endif; ?>>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-start" style="width: 100%; max-width: 100%;">
                                                <div class="" style="width: 100%">
                                                    <div class="small mb-1 note--share__by">
                                                        Shared by <strong><?= htmlspecialchars($note['sharedEmail']) ?></strong>
                                                    </div>
                                                    <div class="small mb-2 note--share__time">
                                                        Shared on <?= date('M d, Y', strtotime($note['timeShared'])) ?>
                                                    </div>

                                                    <!-- Optional: Title & Content if available -->
                                                    <?php if (!empty($note['title'])): ?>
                                                        <h6 class="fw-bold note--share__title"><?= htmlspecialchars($note['title']) ?></h6>
                                                    <?php endif; ?>
                                                    <?php if (!empty($note['content'])): ?>
                                                        <p class="mb-1 note--share__content" style="overflow-y: hidden; max-height: 48px; padding-right: 20%"><?= htmlspecialchars($note['content']) ?></p>
                                                    <?php endif; ?>

                                                    <!-- Labels -->
                                                    <?php if (!empty($note['labels'])) { ?>
                                                        <div class="mt-2">
                                                            <?php foreach ($note['labels'] as $label): ?>
                                                                <?php if (!empty($label)) { ?>
                                                                    <span class="badge bg-secondary me-1"><?= htmlspecialchars($label) ?></span>
                                                                <?php } ?>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>

                                                <div class="text-end" style="display: flex; flex-direction: column; width: 120px; justify-content: space-between; align-items: end">
                                                    <?php if ($note['canEdit']): ?>
                                                        <span class="access-label access-edit" style="width: fit-content">Can edit</span>
                                                    <?php else: ?>
                                                        <span class="access-label access-readonly" style="width: fit-content">Read-only</span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($note['imageLink'])) { ?>
                                                        <div class="note--share__image">
                                                            <img src="<?= htmlspecialchars($note['imageLink']) ?>" class="rounded" alt="User Avatar" style="margin-top: 0px; height: 100%; width: auto">
                                                        </div>
                                                    <?php } ?>
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
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Modal Structure for show Note Detail-->
<div class="modal fade" id="noteShareModal" tabindex="-1" aria-labelledby="noteShareModalLabel" data-bs-backdrop="true" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable note-detail__modal--dialog" style="margin-left: auto; margin-right: auto">
        <div class="modal-content note-detail__modal" style="overflow: auto">
            <div class="modal-content-body" style="height: inherit; overflow-y: auto; display: flex; flex-direction: column">
                <div class="note-sheet__image" style="width: 100%; height: auto; overflow: visible">
                    <!--                        Render Image Link here-->
                </div>
                <div class="modal-header">
                    <input type="text" class="modal-title note-title-input-autosave form-control border-0" id="noteModalLabel" />
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

<!--Add this inside your note share modal -->
<!--<div id="noteShareModal" class="modal fade" tabindex="-1" aria-hidden="true">-->
<!--    ... existing modal content ... -->
<!---->
<!--    Add this inside the modal-body -->
<!--    <div class="collaboration-container position-absolute top-0 end-0 m-3">-->
<!--        <div class="collaboration-status p-2 rounded mb-2">-->
<!--            <i class="fas fa-circle me-2"></i>-->
<!--            <span>Disconnected</span>-->
<!--        </div>-->
<!--        <div class="collaboration-activity p-2 bg-light rounded mb-2 d-none"></div>-->
<!--        <div class="collaboration-typing-indicator p-2 bg-light rounded mb-2 d-none"></div>-->
<!--        <div class="collaboration-join-leave p-2 bg-light rounded mb-2 d-none"></div>-->
<!--    </div>-->
<!---->
<!--    ... rest of modal content ... -->
<!--</div>-->
<!---->
<!--Add styles -->
<!--<style>-->
<!--    .collaboration-container {-->
<!--        z-index: 1050;-->
<!--        font-size: 0.8rem;-->
<!--    }-->
<!---->
<!--    .collaboration-status {-->
<!--        background-color: rgba(255, 255, 255, 0.8);-->
<!--        transition: all 0.3s ease;-->
<!--    }-->
<!---->
<!--    .collaboration-status i {-->
<!--        color: #dc3545;-->
<!--    }-->
<!---->
<!--    .collaboration-status.connected i {-->
<!--        color: #198754;-->
<!--    }-->
<!---->
<!--    .collaboration-activity,-->
<!--    .collaboration-typing-indicator,-->
<!--    .collaboration-join-leave {-->
<!--        display: none;-->
<!--        background-color: rgba(255, 255, 255, 0.8);-->
<!--        border-radius: 0.25rem;-->
<!--        padding: 0.5rem;-->
<!--    }-->
<!---->
<!--    .collaboration-activity.active,-->
<!--    .collaboration-typing-indicator.active,-->
<!--    .collaboration-join-leave.active {-->
<!--        display: block;-->
<!--    }-->
<!--</style>-->