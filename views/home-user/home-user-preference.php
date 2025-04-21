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
            <div class="preferences-container">
                <h2 class="mb-4">User Preferences</h2>

                <!-- Appearance Section -->
                <div class="section-title">Appearance</div>

                <div class="setting-row">
                    <div class="setting-label">Theme</div>
                    <select id="theme-selector" class="form-select dropdown-select">
                        <option value="light">Light</option>
                        <option value="dark">Dark</option>
                        <option value="system" selected>System default</option>
                    </select>
                </div>

                <div class="setting-row">
                    <div class="setting-label">Font Size</div>
                    <select class="form-select dropdown-select">
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
                    <button class="btn btn-reset text-white">Reset Password</button>
                </div>

                <div class="section-divider"></div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="btn btn-cancel">Cancel</button>
                    <button class="btn btn-save text-white">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    <?php
    include "./views/layout/partials/overlay_loading.php";
    ?>


    <!-- JavaScript and Css for functionality -->

    <script>
        // Script to handle color option selection
        document.addEventListener('DOMContentLoaded', function() {
            const colorOptions = document.querySelectorAll('.color-option');

            colorOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove active class from all options
                    colorOptions.forEach(opt => opt.classList.remove('active'));

                    // Add active class to clicked option
                    this.classList.add('active');
                });
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

        .btn-cancel {
            background-color: transparent;
            border: 1px solid #dadce0;
            color: #202124;
        }

        .btn-save {
            background-color: #1a2e44;
            border: none;
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