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

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    include "./views/layout/partials/overlay_loading.php";
    ?>
</div>