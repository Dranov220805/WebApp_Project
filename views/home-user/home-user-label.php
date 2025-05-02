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
                <h6 class="note-layout__title"><?= htmlspecialchars($data['labelName']) ?></h6>
                <div class="note-grid d-flex justify-content-center">
                    <div class="label-note__load load-grid" style="display: flex; flex-wrap: wrap; gap: 16px; justify-content: center">
                        <?php if (!empty($data['data'])): ?>
                            <?php foreach ($data['data'] as $note): ?>
                                <div class="note-sheet d-flex flex-column"
                                     data-note-id="<?= htmlspecialchars($note['noteId']) ?>"
                                     data-note-title="<?= htmlspecialchars($note['title']) ?>"
                                     data-note-content="<?= htmlspecialchars($note['content']) ?>">
                                    <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                                        <h3 class="note-sheet__title"><?= htmlspecialchars($note['title']) ?></h3>
                                        <div class="note-sheet__content">
                                            <?= nl2br(htmlspecialchars($note['content'])) ?>
                                        </div>
                                    </div>
                                    <div class="note-sheet__menu">
                                        <div>
                                            <button class="note-restore-btn" title="Restore this note" data-note-id="<?= htmlspecialchars($note['noteId']) ?>"><i class="fa-solid fa-trash-arrow-up"></i></button>
                                            <button class="note-trash-delete-btn" title="Delete permanently" data-note-id="<?= htmlspecialchars($note['noteId']) ?>"><i class="fa-solid fa-eraser"></i></button>
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

    <?php
    include "./views/layout/partials/overlay_loading.php";
    ?>
</div>