<?php

class HomeUserMiddleWare {

    private HomeUserController $homeUserController;
    private AuthMiddleware $authMiddleware;

    public function __construct() {
        $this->homeUserController = new HomeUserController();
        $this->authMiddleware = new AuthMiddleware();
    }
    public function index()
    {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->index();
    }
    public function redirectToIndex() {
        header('location:/home');
        exit();
    }
    public function showError() {
        $this->homeUserController->showError();
    }
    public function homeReference() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        } 
        $this->homeUserController->homeReference();
    }
    public function homeLabel() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->homeLabel();
    }
    public function homeLabel_POST($labelName) {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->homeLabel_POST($labelName);
    }
    public function homeArchive() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->homeArchive();
    }
    public function homeTrash() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->homeTrash();
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
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->getPreferencesByAccountId();
    }
    public function uploadAvatar() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->uploadAvatar();
    }

    public function updatePreference() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->updatePreference();
    }
}