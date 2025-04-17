<?php

?>

<!-- Main container with sidebar and content -->
<div class="container main-container d-flex" style="margin-top: 56px;">

    <?php
        include "views/layout/partials/sidebar.php";
    ?>

    <!-- Main content area -->
    <div id="content" class="content">
        <div class="container">
            <!-- Note creation area -->
            <div class="note-post">
                <form class="note-post__content" style="display: flex; flex-direction: column;" action="#" onsubmit="return false">
                    <input class="note-post__input" type="text" placeholder="Take a note...">
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
                        <button style="background-color: #f1f3f4; border: none; border-radius: 4px; color: #202124; cursor: pointer; font-size: 14px; font-weight: 500; padding: 8px 16px;">
                            Create
                        </button>
                    </div>
                </form>
            </div>

            <!-- Pinned Notes grid -->
            <div class="pinned-note">
                <h6>Pinned</h6>
                <div class="note-grid d-flex justify-content-center">
                    <div style="display: flex; flex-direction: row; flex-wrap: wrap; gap: 16px; justify-content: center">
                        <!-- Shopping List Note -->
                        <div class="note-sheet">
                            <div style="padding: 16px;">
                                <h3 style="color: #202124; font-size: 16px; font-weight: 500; margin: 0 0 12px 0;">Shopping List</h3>
                                <div style="color: #5f6368; font-size: 14px; line-height: 1.5;">
                                    <div>- Milk</div>
                                    <div>- Eggs</div>
                                    <div>- Bread</div>
                                    <div>- Fruits</div>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 8px;">
                                <div>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M13 9h-2v2H9v2h2v2h2v-2h2v-2h-2z"/>
                                            <path d="M12 20c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-18C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                        </svg>
                                    </button>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16zM16 17H5V7h11l3.55 5L16 17z"/>
                                        </svg>
                                    </button>
                                </div>
                                <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Meeting Notes -->
                        <div class="note-sheet">
                            <div style="padding: 16px;">
                                <h3 style="color: #202124; font-size: 16px; font-weight: 500; margin: 0 0 12px 0;">Meeting Notes</h3>
                                <div style="color: #5f6368; font-size: 14px; line-height: 1.5;">
                                    Project review meeting at 2 PM with the design team. Discuss new feature implementations.
                                </div>
                            </div>
                            <div style="background-color: #f8f9fa; display: flex; justify-content: center; align-items: center; padding: 24px;">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="#80868b">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5-7l-3 3.72L9 13l-3 4h12l-4-5z"/>
                                </svg>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 8px;">
                                <div>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M13 9h-2v2H9v2h2v2h2v-2h2v-2h-2z"/>
                                            <path d="M12 20c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-18C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                        </svg>
                                    </button>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16zM16 17H5V7h11l3.55 5L16 17z"/>
                                        </svg>
                                    </button>
                                </div>
                                <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Ideas Note -->
                        <div class="note-sheet">
                            <div style="padding: 16px;">
                                <h3 style="color: #202124; font-size: 16px; font-weight: 500; margin: 0 0 12px 0;">Ideas</h3>
                                <div style="color: #5f6368; font-size: 14px; line-height: 1.5;">
                                    New app concept sketches and wireframes
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 8px;">
                                <div>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M13 9h-2v2H9v2h2v2h2v-2h2v-2h-2z"/>
                                            <path d="M12 20c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-18C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                        </svg>
                                    </button>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16zM16 17H5V7h11l3.55 5L16 17z"/>
                                        </svg>
                                    </button>
                                </div>
                                <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Shopping List Note -->
                        <div class="note-sheet">
                            <div style="padding: 16px;">
                                <h3 style="color: #202124; font-size: 16px; font-weight: 500; margin: 0 0 12px 0;">Shopping List</h3>
                                <div style="color: #5f6368; font-size: 14px; line-height: 1.5;">
                                    <div>- Milk</div>
                                    <div>- Eggs</div>
                                    <div>- Bread</div>
                                    <div>- Fruits</div>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 8px;">
                                <div>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M13 9h-2v2H9v2h2v2h2v-2h2v-2h-2z"/>
                                            <path d="M12 20c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-18C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                        </svg>
                                    </button>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16zM16 17H5V7h11l3.55 5L16 17z"/>
                                        </svg>
                                    </button>
                                </div>
                                <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Meeting Notes -->
                        <div class="note-sheet">
                            <div style="padding: 16px;">
                                <h3 style="color: #202124; font-size: 16px; font-weight: 500; margin: 0 0 12px 0;">Meeting Notes</h3>
                                <div style="color: #5f6368; font-size: 14px; line-height: 1.5;">
                                    Project review meeting at 2 PM with the design team. Discuss new feature implementations.
                                </div>
                            </div>
                            <div style="background-color: #f8f9fa; display: flex; justify-content: center; align-items: center; padding: 24px;">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="#80868b">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5-7l-3 3.72L9 13l-3 4h12l-4-5z"/>
                                </svg>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 8px;">
                                <div>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M13 9h-2v2H9v2h2v2h2v-2h2v-2h-2z"/>
                                            <path d="M12 20c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-18C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                        </svg>
                                    </button>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16zM16 17H5V7h11l3.55 5L16 17z"/>
                                        </svg>
                                    </button>
                                </div>
                                <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Ideas Note -->
                        <div class="note-sheet">
                            <div style="padding: 16px;">
                                <h3 style="color: #202124; font-size: 16px; font-weight: 500; margin: 0 0 12px 0;">Ideas</h3>
                                <div style="color: #5f6368; font-size: 14px; line-height: 1.5;">
                                    New app concept sketches and wireframes
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 8px;">
                                <div>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M13 9h-2v2H9v2h2v2h2v-2h2v-2h-2z"/>
                                            <path d="M12 20c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-18C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                        </svg>
                                    </button>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16zM16 17H5V7h11l3.55 5L16 17z"/>
                                        </svg>
                                    </button>
                                </div>
                                <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Other Notes grid -->
            <div class="pinned-note">
                <h6>Others</h6>
                <div class="note-grid d-flex justify-content-center">
                    <div style="display: flex; flex-direction: row; flex-wrap: wrap; gap: 16px; justify-content: center">
                        <!-- Shopping List Note -->
                        <div class="note-sheet">
                            <div style="padding: 16px;">
                                <h3 style="color: #202124; font-size: 16px; font-weight: 500; margin: 0 0 12px 0;">Shopping List</h3>
                                <div style="color: #5f6368; font-size: 14px; line-height: 1.5;">
                                    <div>- Milk</div>
                                    <div>- Eggs</div>
                                    <div>- Bread</div>
                                    <div>- Fruits</div>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 8px;">
                                <div>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M13 9h-2v2H9v2h2v2h2v-2h2v-2h-2z"/>
                                            <path d="M12 20c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-18C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                        </svg>
                                    </button>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16zM16 17H5V7h11l3.55 5L16 17z"/>
                                        </svg>
                                    </button>
                                </div>
                                <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Meeting Notes -->
                        <div class="note-sheet">
                            <div style="padding: 16px;">
                                <h3 style="color: #202124; font-size: 16px; font-weight: 500; margin: 0 0 12px 0;">Meeting Notes</h3>
                                <div style="color: #5f6368; font-size: 14px; line-height: 1.5;">
                                    Project review meeting at 2 PM with the design team. Discuss new feature implementations.
                                </div>
                            </div>
                            <div style="background-color: #f8f9fa; display: flex; justify-content: center; align-items: center; padding: 24px;">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="#80868b">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5-7l-3 3.72L9 13l-3 4h12l-4-5z"/>
                                </svg>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 8px;">
                                <div>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M13 9h-2v2H9v2h2v2h2v-2h2v-2h-2z"/>
                                            <path d="M12 20c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-18C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                        </svg>
                                    </button>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16zM16 17H5V7h11l3.55 5L16 17z"/>
                                        </svg>
                                    </button>
                                </div>
                                <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Ideas Note -->
                        <div class="note-sheet">
                            <div style="padding: 16px;">
                                <h3 style="color: #202124; font-size: 16px; font-weight: 500; margin: 0 0 12px 0;">Ideas</h3>
                                <div style="color: #5f6368; font-size: 14px; line-height: 1.5;">
                                    New app concept sketches and wireframes
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 8px;">
                                <div>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M13 9h-2v2H9v2h2v2h2v-2h2v-2h-2z"/>
                                            <path d="M12 20c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-18C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                        </svg>
                                    </button>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16zM16 17H5V7h11l3.55 5L16 17z"/>
                                        </svg>
                                    </button>
                                </div>
                                <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Shopping List Note -->
                        <div class="note-sheet">
                            <div style="padding: 16px;">
                                <h3 style="color: #202124; font-size: 16px; font-weight: 500; margin: 0 0 12px 0;">Shopping List</h3>
                                <div style="color: #5f6368; font-size: 14px; line-height: 1.5;">
                                    <div>- Milk</div>
                                    <div>- Eggs</div>
                                    <div>- Bread</div>
                                    <div>- Fruits</div>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 8px;">
                                <div>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M13 9h-2v2H9v2h2v2h2v-2h2v-2h-2z"/>
                                            <path d="M12 20c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-18C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                        </svg>
                                    </button>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16zM16 17H5V7h11l3.55 5L16 17z"/>
                                        </svg>
                                    </button>
                                </div>
                                <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Meeting Notes -->
                        <div class="note-sheet">
                            <div style="padding: 16px;">
                                <h3 style="color: #202124; font-size: 16px; font-weight: 500; margin: 0 0 12px 0;">Meeting Notes</h3>
                                <div style="color: #5f6368; font-size: 14px; line-height: 1.5;">
                                    Project review meeting at 2 PM with the design team. Discuss new feature implementations.
                                </div>
                            </div>
                            <div style="background-color: #f8f9fa; display: flex; justify-content: center; align-items: center; padding: 24px;">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="#80868b">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5-7l-3 3.72L9 13l-3 4h12l-4-5z"/>
                                </svg>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 8px;">
                                <div>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M13 9h-2v2H9v2h2v2h2v-2h2v-2h-2z"/>
                                            <path d="M12 20c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-18C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                        </svg>
                                    </button>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16zM16 17H5V7h11l3.55 5L16 17z"/>
                                        </svg>
                                    </button>
                                </div>
                                <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Ideas Note -->
                        <div class="note-sheet">
                            <div style="padding: 16px;">
                                <h3 style="color: #202124; font-size: 16px; font-weight: 500; margin: 0 0 12px 0;">Ideas</h3>
                                <div style="color: #5f6368; font-size: 14px; line-height: 1.5;">
                                    New app concept sketches and wireframes
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 8px;">
                                <div>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M13 9h-2v2H9v2h2v2h2v-2h2v-2h-2z"/>
                                            <path d="M12 20c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-18C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                        </svg>
                                    </button>
                                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                            <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16zM16 17H5V7h11l3.55 5L16 17z"/>
                                        </svg>
                                    </button>
                                </div>
                                <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
include "./views/layout/partials/overlay_loading.php";
?>


<!-- JavaScript for toggling sidebar and search functionality -->
<script>

</script>