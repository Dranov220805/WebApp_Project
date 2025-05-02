<?php

include_once "./app/controllers/HomeUserController.php";
$homeUserController = new HomeUserController();

$labelList = $homeUserController->getUserLabel();

?>

<!-- Sidebar -->
<div id="sidebar" class="sidebar collapsed">
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

        <!-- Labels item -->
<!--        <a href="/home/label" class="sidebar-item">-->
<!--            <i class="sidebar__item--icon fa-solid fa-tags" title="All labels"></i>-->
<!--            <span class="sidebar__item--title">Labels</span>-->
<!--        </a>-->

        <!-- Archive item -->
        <a href="/home/archive" class="sidebar-item">
            <i class="sidebar__item--icon fa-solid fa-box-archive" title="Archive"></i>
            <span class="sidebar__item--title">Archive</span>
        </a>

        <!-- Trash item -->
        <a href="/home/trash" class="sidebar-item">
            <i class="sidebar__item--icon fa-solid fa-trash" title="Trash"></i>
            <span class="sidebar__item--title">Trash</span>
        </a>
    </div>
</div>

<div class="modal fade" id="editLabelsModal" tabindex="-1" aria-labelledby="editLabelsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="height: fit-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLabelsModalLabel">Manage Labels</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body d-flex flex-column" id="label-management-body">
                <?php foreach ($labelList as $label): ?>
                <div class="note-sheet__menu" style="width: 100%">
                    <div style="width: 100%; display: flex; flex-direction: row; flex-grow: 1">
                        <a class="sidebar__item--title" style="flex-grow: 1"><?= htmlspecialchars($label) ?></a>
                        <button class="note-restore-btn" title="Restore this note" data-note-id="<?= htmlspecialchars($label) ?>"><i class="fa-solid fa-trash-arrow-up"></i></i></button>
                        <button class="note-trash-delete-btn" title="Delete permanently" data-note-id="<?= htmlspecialchars($label) ?>"><i class="fa-solid fa-eraser"></i></button>
                    </div>
                </div>
                <?php endforeach; ?>
                <!-- JavaScript will dynamically inject label management UI here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>