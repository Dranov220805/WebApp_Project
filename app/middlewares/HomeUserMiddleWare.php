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
        header('location:/home');
        exit();
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
    public function homeLabel_POST($labelName) {
        if (!isset($_SESSION['roleId'])) {
            header('location:/log/login');
        } else {
            $this->homeUserController->homeLabel_POST($labelName);
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
    public function checkVerification() {
        $result = $this->homeUserController->checkVerification();
        if ($result['status'] === true) {
            return $_SESSION['isVerified'] = 1;
        } else {
            return $_SESSION['isVerified'] = 0;
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

    public function updatePreference() {
        if (!isset($_SESSION['roleId'])) {
            header('location:/log/login');
        } else {
            $this->homeUserController->updatePreference();
        }
    }
}