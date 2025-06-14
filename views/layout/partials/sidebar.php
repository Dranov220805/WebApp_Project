<?php

include_once "./app/controllers/HomeUserController.php";
$homeUserController = new HomeUserController();
$user = $homeUserController->getUserInfo();
$labelList = $homeUserController->getUserLabel($user['user']);

?>

<!-- Sidebar -->
<div id="sidebar" class="sidebar collapsed" style="top: 60px">
    <div class="sidebar-content" style="padding: 8px 0;">
        <!-- Notes item -->
        <a href="/home" class="sidebar-item" title="All notes">
            <i class="sidebar__item--icon fa-solid fa-note-sticky"></i>
            <span class="sidebar__item--title">Notes</span>
        </a>

        <!-- Render All Label For User       -->
        <?php foreach ($labelList as $label): ?>
            <a href="/home/label/<?= urlencode($label) ?>" class="sidebar-item" title="<?= htmlspecialchars($label) ?>">
                <i class="sidebar__item--icon fa-solid fa-tag"></i>
                <span class="sidebar__item--title"><?= htmlspecialchars($label) ?></span>
            </a>
        <?php endforeach; ?>

        <!-- Edit Labels Modal Trigger -->
        <a type="button" class="sidebar-item" data-bs-toggle="modal" data-bs-target="#editLabelsModal">
            <i class="sidebar__item--icon fa-solid fa-pen"></i>
            <span class="sidebar__item--title">Edit Labels</span>
        </a>

        <!-- Share item -->
        <a href="/home/share" class="sidebar-item">
            <i class="sidebar__item--icon fa-solid fa-share-nodes" title="Share Note"></i>
            <span class="sidebar__item--title">Shares</span>
        </a>

        <!-- Trash item -->
        <a href="/home/trash" class="sidebar-item">
            <i class="sidebar__item--icon fa-solid fa-trash" title="Trash"></i>
            <span class="sidebar__item--title">Trash</span>
        </a>
    </div>
</div>

<div class="modal fade" id="editLabelsModal" tabindex="-1" aria-labelledby="editLabelsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="width: 100%; height: 100%">
            <div class="modal-header">
                <h5 class="modal-title" id="editLabelsModalLabel">Manage Labels</h5>
            </div>
            <div class="modal-body d-flex flex-column" id="label-management-body">
                <?php foreach ($labelList as $label): ?>
                <div class="note-sheet__menu" style="width: 100%">
                    <div style="width: 100%; display: flex; flex-direction: row; flex-grow: 1">
                        <input class="sidebar__item--input" style="flex-grow: 1; border: none; background-color: inherit" value="<?= htmlspecialchars($label) ?>">
                        <button class="label-rename-btn" title="Rename label" data-label-id="<?= htmlspecialchars($label) ?>"><i class="fa-regular fa-pen-to-square"></i></button>
                        <button class="label-delete-btn" title="Delete label" data-label-id="<?= htmlspecialchars($label) ?>"><i class="fa-solid fa-eraser"></i></button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer d-flex" style="justify-content: end">
                <input class="label-post__input" placeholder="New label here" style="flex-grow: 1; font-size: 18px">
                <button type="button" class="btn btn-primary label-post__submit"> Add new label</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteLabelModal" tabindex="-1" aria-labelledby="deleteLabelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLabelModalLabel">Confirm Delete Permanently</h5>
            </div>
            <div class="modal-body">
                Are you sure you want to permanently delete this note?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button id="confirmDeleteLabelBtn" type="button" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<!--    Modal structure for showing list of labels-->
<div class="modal fade" id="listLabelNoteModal" tabindex="-1" aria-labelledby="listLabelNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" style="margin: auto">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLabelNoteModalLabel">Assign Labels</h5>
            </div>
            <div class="modal-body" id="label-checkbox-list">
                <!-- Labels will be loaded dynamically here -->
                <?php foreach ($labelList as $label): ?>
                    <div class="note-sheet__menu" style="width: 100%">
                        <label style="width: 100%; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" class="label-checkbox" value="<?= htmlspecialchars($label) ?>" style="transform: scale(1.5);"/>
                            <span><?= htmlspecialchars($label) ?></span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>