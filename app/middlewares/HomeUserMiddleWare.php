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
        $this->homeUserController->index($checkToken['user']);
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
        $this->homeUserController->homeReference($checkToken['user']);
    }
    public function homeLabel() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->homeLabel($checkToken['user']);
    }
    public function homeLabel_POST($labelName) {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->homeLabel_POST($checkToken['user'], $labelName);
    }
    public function homeShare() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->homeShare($checkToken['user']);
    }
    public function homeTrash() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->homeTrash($checkToken['user']);
    }
    public function userPreference() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->getPreferencesByAccountId($checkToken['user']);
    }
    public function uploadAvatar() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->uploadAvatar($checkToken['user']);
    }

    public function updatePreference() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->updatePreference($checkToken['user']);
    }

    public function addNewSharedEmail_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->addNewSharedEmail_POST($checkToken['user']);
    }

    public function deleteSharedEmail_DELETE() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->deleteSharedEmail_DELETE();
    }

    public function updateShareEmail_PUT() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->updateShareEmail_PUT();
    }

    public function sharedEmailList() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->homeUserController->sharedEmailList($checkToken['user']);
    }
}