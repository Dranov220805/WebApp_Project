<?php

class HomeUserMiddleWare {

    private HomeUserController $homeUserController;

    public function __construct() {
        $this->homeUserController = new HomeUserController();
    }
    public function index() {
        if (!isset($_SESSION['roleId'])) {
            header('location:/');
        } else {
            $this->homeUserController->index();
        }
    }
}