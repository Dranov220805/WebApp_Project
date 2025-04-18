<?php

?>

<!-- Main container with sidebar and content -->
<div class="container main-container d-flex" style="margin-top: 56px;">

    <?php
        include "views/layout/partials/sidebar.php";
    ?>

    <!-- Main content area -->
    <div id="content" class="content" style="margin-left: 80px;">
        <div class="container">
            <!-- Note creation area -->
            <div class="note-post">
                <form class="note-post__content" style="display: flex; flex-direction: column;" action="#" onsubmit="return false">
                    <textarea class="note-post__input" placeholder="Take a note..." rows="1"></textarea>
                    <div style="display: flex; justify-content: space-between; margin-top: 12px;">
                        <div>
                            <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="#5f6368">
                                    <path d="M19 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/>
                                    <path d="M18 9l-1.4-1.4-6.6 6.6-2.6-2.6L6 13l4 4z"/>
                                </svg>
                            </button>
                            <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="#5f6368">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-2-7H7c-.55 0-1-.45-1-1s.45-1 1-1h10c.55 0 1 .45 1 1s-.45 1-1 1z"/>
                                </svg>
                            </button>
                        </div>
                        <button class="btn btn-primary" style="background-color: #f1f3f4; border: none; border-radius: 4px; color: #202124; cursor: pointer; font-size: 14px; font-weight: 500; padding: 8px 16px;">
                            Create
                        </button>
                    </div>
                </form>
            </div>

            <!-- Pinned Notes grid -->
            <div class="pinned-note">
                <h6>Pinned</h6>
                <div class="note-grid d-flex justify-content-center">
                    <div class="pinned-note__load load-grid" style="display: flex; flex-wrap: wrap; gap: 16px; justify-content: center">
<!--                        Start of Pinned Note Grid-->

<!--                        End of Pinned Note Grid-->
                    </div>
                </div>
            </div>

            <!-- Other Notes grid -->
            <div class="pinned-note">
                <h6>Others</h6>
                <div class="note-grid d-flex justify-content-center">
                    <div class="other-note__load load-grid" style="display: flex; flex-wrap: wrap; gap: 16px; justify-content: center">
<!--                        Start of Others Note Grid-->

<!--                        End of Others Note Grid-->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
include "./views/layout/partials/overlay_loading.php";
?>


<!-- JavaScript for loading note content -->
<script src="/public/js/note.js" type="module"></script>
