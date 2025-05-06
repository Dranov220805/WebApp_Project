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
                    <div style="display: flex; justify-content: space-between; margin-top: 12px; height: 40px">
                        <div class="note-post__menu">

                        </div>
                        <button class="btn create-note-btn">
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
        <div class="modal-dialog modal-lg modal-dialog-scrollable note-modal-display position-fixed top-50 start-50 translate-middle fade show note-detail__modal--dialog" style="height: 80%">
            <div class="modal-content note-detail__modal" style="overflow: auto">
                <div class="modal-content-body" style="height: inherit; overflow-y: auto; display: flex; flex-direction: column">
                    <div class="note-sheet__image" style="width: 100%; height: auto; overflow: visible">
<!--                        Render Image Link here-->
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

    <?php
        include "./views/layout/partials/overlay_loading.php";
    ?>
</div>

<!-- JavaScript for loading note content -->
<script src="/public/js/note.js" type="module"></script>

<script>

</script>
