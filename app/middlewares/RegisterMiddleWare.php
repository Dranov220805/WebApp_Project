<?php

class RegisterMiddleWare {
    private RegisterController $registerController;
    public function __construct()
    {
        $this->registerController = new RegisterController();
    }

    public function index() {
        $this->registerController->index();
    }
}