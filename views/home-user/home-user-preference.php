<!-- Main container with sidebar and content -->
<div class="container main-container d-flex" style="margin-top: 56px;">

    <?php
    include "views/layout/partials/sidebar.php";
    ?>

    <!-- Main content area -->
    <div id="content" class="content" style="margin-left: 80px;">
        <div class="container">
            <div class="preferences-container">
                <h2 class="mb-4">User Preferences</h2>

                <div class="section-title">Avatar</div>
                <div class="setting-row" style="display: flex; flex-grow: 1; width: 100%; height: 80px">
                    <div class="setting-label setting-avatar" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center">
<!--                        --><?php //if ($_SESSION['isVerified'] == 1) {echo '<i style="width: 80px; height: 80px" class="user__item--icon fa-regular fa-circle-user"></i>';} else {echo 'no avatar';} ?>
                        <?php if (!empty($_SESSION['avatar_url'])): ?>
                            <img src="<?= $_SESSION['avatar_url'] ?>" style="width: 70px; height: 70px;">
                        <?php else: ?>
                            <i class="fa-regular fa-circle-user"></i>
                        <?php endif; ?>

                    </div>
                    <form id="avatar-upload-form" enctype="multipart/form-data">
                        <input type="file" id="avatar-input" name="avatar" accept="image/*" required>
                        <a class="btn btn-primary btn-upload">Upload Avatar</a>
                    </form>
                </div>

                <!-- Appearance Section -->
                <div class="section-title">Appearance</div>

                <div class="setting-row">
                    <div class="setting-label">Theme</div>
                    <select id="theme-selector" class="form-select dropdown-select" style="width: 20%">
                        <option value="light">Light</option>
                        <option value="dark">Dark</option>
                        <option value="system" selected>System default</option>
                    </select>
                </div>

                <div class="setting-row">
                    <div class="setting-label">Font Size</div>
                    <select class="form-select dropdown-select" style="width: 20%">
                        <option selected>Small</option>
                        <option>Medium</option>
                        <option>Large</option>
                    </select>
                </div>

                <div class="section-divider"></div>

                <!-- Note Settings Section -->
                <div class="section-title">Note Settings</div>

                <div class="setting-row">
                    <div class="setting-label">Default Note Color</div>
                    <div class="d-flex align-items-center">
                        <div class="color-option color-white active"></div>
                        <div class="color-option color-light"></div>
                        <div class="color-option color-gray"></div>
                        <div class="color-option color-blue"></div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Security Section -->
                <div class="section-title">Security</div>

                <div class="setting-row">
                    <div class="setting-label">Change Password</div>
                    <button id="change-password-btn" class="btn btn-reset text-white d-button" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>
                </div>

                <div class="section-divider"></div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="/home" class="btn btn-cancel d-button">Cancel</a>
                    <a class="btn btn-save btn-save-preference text-white d-button">Save Changes</a>
                </div>
            </div>

            <!-- Change Password Modal -->
            <div class="modal fade" id="changePasswordModal" tabindex="-1" style="height: fit-content; min-height: 600px;" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                <div class="modal-dialog" style="margin-top: 100px; margin-bottom: auto">
                    <div class="modal-content" style="width: 100%; max-width: 500px">
                        <div class="modal-header">
                            <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="change-password-form" action="#" onsubmit="return false">
                                <div class="mb-3">
                                    <label for="current-password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current-password-input" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password:</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control login-section-2__form--input-box" id="new-password-input" placeholder="Password">
                                        <span class="input-group-text bg-white">
                                        <i id="toggle-change-password" class="fa-regular fa-eye-slash" onclick="toggleChangePassword()" style="cursor: pointer;"></i>
                                    </span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="confirmPassword" class="form-label">Confirm Password:</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control login-section-2__form--input-box" id="confirm-new-password-input" placeholder="Confirm Password">
                                                <span class="input-group-text bg-white">
                                        <i id="toggle-change-password-confirm" class="fa-regular fa-eye-slash" onclick="toggleConfirmChangePassword()" style="cursor: pointer;"></i>
                                    </span>
                                    </div>
                                </div>
                                <button id="post-change-password-btn" class="btn btn-primary w-100">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    include "./views/layout/partials/overlay_loading.php";
    ?>


    <!-- JavaScript and Css for functionality -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            if (window.changePassword) {
                changePassword();
            }

            // Existing color option logic
            const colorOptions = document.querySelectorAll('.color-option');
            colorOptions.forEach(option => {
                option.addEventListener('click', function () {
                    colorOptions.forEach(opt => opt.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Theme switch logic
            const themeSelector = document.getElementById('theme-selector');

            // Load stored theme from localStorage
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
                themeSelector.value = 'dark';
            } else if (savedTheme === 'light') {
                document.body.classList.remove('dark-mode');
                themeSelector.value = 'light';
            }

            themeSelector.addEventListener('change', function () {
                const selectedTheme = themeSelector.value;

                if (selectedTheme === 'dark') {
                    document.body.classList.add('dark-mode');
                    localStorage.setItem('theme', 'dark');
                } else if (selectedTheme === 'light') {
                    document.body.classList.remove('dark-mode');
                    localStorage.setItem('theme', 'light');
                } else {
                    // For system default, clear preference
                    localStorage.removeItem('theme');
                    document.body.classList.remove('dark-mode');
                }
            });
        });
    </script>


    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
        }

        .preferences-container {
            max-width: 700px;
            margin: 50px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #202124;
            margin-bottom: 20px;
        }

        .setting-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .setting-label {
            font-size: 16px;
            color: #202124;
        }

        .dropdown-select {
            min-width: 120px;
        }

        .section-divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 25px 0;
        }

        .color-option {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            cursor: pointer;
            margin-right: 10px;
            border: 2px solid transparent;
        }

        .color-option.active {
            border-color: #202124;
        }

        .color-white {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
        }

        .color-light {
            background-color: #f1f3f4;
        }

        .color-gray {
            background-color: #e8eaed;
        }

        .color-blue {
            background-color: #aecbfa;
        }

        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .upload-btn {
            background-color: #1a2e44;
            border: none;
        }

        .upload-btn:hover {
            background-color: #5771ff;
        }

        .btn-cancel {
            background-color: transparent;
            border: 1px solid #dadce0;
            color: #202124;
        }

        #change-password-btn:hover {
            background-color: #5771ff;
        }

        .btn-cancel:hover {
            background-color: #c9302c;
            color: white;
        }

        .btn-cancel:hover {
            background-color: #5771ff;
        }

        .btn-save {
            background-color: #1a2e44;
            border: none;
        }

        .btn-save:hover {
            background-color: #5771ff;
        }

        .btn-reset {
            background-color: #1a2e44;
            border: none;
        }

        @media (max-width: 576px) {
            .preferences-container {
                margin: 0;
                border-radius: 0;
                min-height: 100vh;
            }

            .setting-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .setting-row select,
            .setting-row .color-options,
            .setting-row .btn {
                width: 100%;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>