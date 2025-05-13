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
    public function homeShare() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->homeShare();
    }
    public function homeTrash() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->homeTrash();
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

    public function addNewSharedEmail_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->addNewSharedEmail_POST();
    }

    public function sharedEmailList() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->sharedEmailList();
    }
}