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
            <!-- Note creation area -->
            <div class="note-post">
                <form class="note-post__content" style="display: flex; flex-direction: column;" action="#" onsubmit="return false">
                    <input class="note-text__content d-none" placeholder="Title">
                    <textarea class="note-post__input" placeholder="Take a note..." rows="1"></textarea>
                    <div style="display: flex; justify-content: space-between; margin-top: 12px;">
                        <div>
                            <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                <i class="fa-solid fa-images"></i>
                            </button>
                            <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                <i class="fa-solid fa-images"></i>
                            </button>
                        </div>
                        <button class="btn btn-primary create-note-btn" style="background-color: #f1f3f4; border: none; border-radius: 4px; color: #202124; cursor: pointer; font-size: 14px; font-weight: 500; padding: 8px 16px;">
                            Create
                        </button>
                    </div>
                </form>
            </div>

<!--            Search Note grid-->
            <div class="pinned-note">
                <h6 class="note-layout__title">Search Result</h6>
                <div class="note-grid d-flex justify-content-center">
                    <div class="search-note__load load-grid" style="display: flex; flex-wrap: wrap; gap: 16px; justify-content: center">
<!--                        Search Result Note Grid-->
<!--                        End of Pinned Note-->
                    </div>
                </div>
            </div>

<!--            Pinned Notes grid -->
            <div class="pinned-note">
                <h6 class="note-layout__title">Pinned</h6>
                <div class="note-grid d-flex justify-content-center">
                    <div class="pinned-note__load load-grid" style="display: flex; flex-wrap: wrap; gap: 16px; justify-content: center">
                        <!-- Render pinned notes -->
<!--                        --><?php //if (!empty($data['pinnedNotes'])): ?>
<!--                            --><?php //foreach ($data['pinnedNotes'] as $note): ?>
<!--                                <div class="note-sheet d-flex flex-column"-->
<!--                                     data-note-id="--><?php //= htmlspecialchars($note['noteId']) ?><!--"-->
<!--                                     data-note-title="--><?php //= htmlspecialchars($note['title']) ?><!--"-->
<!--                                     data-note-content="--><?php //= htmlspecialchars($note['content']) ?><!--">-->
<!--                                    <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">-->
<!--                                        <h3 class="note-sheet__title">--><?php //= htmlspecialchars($note['title']) ?><!--</h3>-->
<!--                                        <div class="note-sheet__content">-->
<!--                                            --><?php //= htmlspecialchars($note['content']) ?>
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                    <div class="note-sheet__menu" onclick="event.stopPropagation()">-->
<!--                                        <div>-->
<!--                                            <button class="pinned-note-pin-btn" title="Unpin Note"><i class="fa-solid fa-thumbtack"></i></button>-->
<!--                                            <button title="Label"><i class="fa-solid fa-tags"></i></button>-->
<!--                                            <button title="Image"><i class="fa-solid fa-images"></i></button>-->
<!--                                            <button class="pinned-note-edit-btn" title="Edit"><i class="fa-regular fa-pen-to-square"></i></button>-->
<!--                                            <button class="pinned-note-delete-btn" title="Delete" data-note-id="--><?php //= htmlspecialchars($note['noteId']) ?><!--"><i class="fa-solid fa-trash"></i></button>-->
<!--                                        </div>-->
<!--                                        <button><i class="fa-solid fa-ellipsis-vertical"></i></button>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            --><?php //endforeach; ?>
<!--                        --><?php //else: ?>
<!--                            <p>No pinned notes available.</p>-->
<!--                        --><?php //endif; ?>
                    </div>
                </div>
            </div>

<!--            Other Note Grid-->
            <div class="other-note">
                <h6 class="note-layout__title">Others</h6>
                <div class="note-grid d-flex justify-content-center">
                    <div class="other-note__load load-grid" style="display: flex; flex-wrap: wrap; gap: 16px; justify-content: center">
                        <!-- Render other notes -->
<!--                        --><?php //if (!empty($data['otherNotes'])): ?>
<!--                            --><?php //foreach ($data['otherNotes'] as $note): ?>
<!--                                <div class="note-sheet d-flex flex-column"-->
<!--                                     data-note-id="--><?php //= htmlspecialchars($note['noteId']) ?><!--"-->
<!--                                     data-note-title="--><?php //= htmlspecialchars($note['title']) ?><!--"-->
<!--                                     data-note-content="--><?php //= htmlspecialchars($note['content']) ?><!--">-->
<!--                                    <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">-->
<!--                                        <h3 class="note-sheet__title">--><?php //= htmlspecialchars($note['title']) ?><!--</h3>-->
<!--                                        <div class="note-sheet__content">-->
<!--                                            --><?php //= htmlspecialchars($note['content']) ?>
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                    <div class="note-sheet__menu" onclick="event.stopPropagation()">-->
<!--                                        <div>-->
<!--                                            <button class="note-pin-btn" title="Pin Note"><i class="fa-solid fa-thumbtack"></i></button>-->
<!--                                            <button title="Label"><i class="fa-solid fa-tags"></i></button>-->
<!--                                            <button title="Image"><i class="fa-solid fa-images"></i></button>-->
<!--                                            <button class="note-edit-btn" title="Edit"><i class="fa-regular fa-pen-to-square"></i></button>-->
<!--                                            <button class="note-delete-btn" title="Delete" data-note-id="--><?php //= htmlspecialchars($note['noteId']) ?><!--"><i class="fa-solid fa-trash"></i></button>-->
<!--                                        </div>-->
<!--                                        <button><i class="fa-solid fa-ellipsis-vertical"></i></button>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            --><?php //endforeach; ?>
<!--                        --><?php //else: ?>
<!--                            <p>No other notes available.</p>-->
<!--                        --><?php //endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Structure for show Note Detail-->
    <div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" data-bs-backdrop="true" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable note-modal-display position-fixed top-50 start-50 translate-middle fade show note-detail__modal--dialog">
            <div class="modal-content note-detail__modal">
                <div class="modal-header">
                    <input type="text" class="modal-title note-title-input form-control border-0" id="noteModalLabel" />
                    <button type="button" class="btn-close note-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <textarea class="note-content-input form-control" style="height: 300px; resize: none;"></textarea>
                </div>
                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <div class="save-status-icon d-flex flex-row">
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
            <div class="modal-content">
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

<!-- JavaScript for loading note content -->
<script src="/public/js/note.js" type="module"></script>

<script>

</script>
