<?php

class HomeUserMiddleWare {

    private HomeUserController $homeUserController;

    public function __construct() {
        $this->homeUserController = new HomeUserController();
    }
    public function index() {
        if (!isset($_SESSION['roleId'])) {
            header('location:/log/login');
            exit();
        } else {
            $this->homeUserController->index();
        }
    }
    public function redirectToIndex() {
        if (!isset($_SESSION['roleId'])) {
            header('location:/log/login');
            exit();
        } else {
            header('location:/home');
        }
    }
    public function showError() {
        $this->homeUserController->showError();
    }
    public function homeReference() {
        if (!isset($_SESSION['roleId'])) {
            header('location:/log/login');
        } else {
            $this->homeUserController->homeReference();
        }
    }
    public function homeLabel() {
        if (!isset($_SESSION['roleId'])) {
            header('location:/log/login');
        } else {
            $this->homeUserController->homeLabel();
        }
    }
    public function homeArchive() {
        if (!isset($_SESSION['roleId'])) {
            header('location:/log/login');
        } else {
            $this->homeUserController->homeArchive();
        }
    }
    public function homeTrash() {
        if (!isset($_SESSION['roleId'])) {
            header('location:/log/login');
        } else {
            $this->homeUserController->homeTrash();
        }
    }
    public function userPreference() {
        if (!isset($_SESSION['roleId'])) {
            header('location:/log/login');
        } else {
            $this->homeUserController->getPreferencesByAccountId();
        }
    }
    public function uploadAvatar() {
        if (!isset($_SESSION['roleId'])) {
            header('location:/log/login');
        } else {
            $this->homeUserController->uploadAvatar();
        }
    }
}