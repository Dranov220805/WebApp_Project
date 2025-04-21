<?php

class HomeUserMiddleWare {

    private HomeUserController $homeUserController;

    public function __construct() {
        $this->homeUserController = new HomeUserController();
    }
    public function index() {
        if (!isset($_SESSION['roleId'])) {
            header('location:/log/login');
        } else {
            $this->homeUserController->index();
        }
    }
    public function userAccount() {
        if (!isset($_SESSION['roleId'])) {
            header('location:/log/login');
        } else {
            $this->homeUserController->homeAccount();
        }
    }
    public function userPreference() {
        if (!isset($_SESSION['roleId'])) {
            header('location:/log/login');
        } else {
            $this->homeUserController->userPreference();
        }
    }
}