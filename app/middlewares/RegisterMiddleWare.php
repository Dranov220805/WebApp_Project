<?php

class RegisterMiddleWare {
    private RegisterController $registerController;
    public function __construct()
    {
        $this->registerController = new RegisterController();
    }

    public function index() {
        if (!isset($_SESSION['roleId'])) {
            header('location: /');
        }
        else {
            $this->registerController->index();
        }
    }
}