<?php

class RegisterMiddleWare {
    private RegisterController $registerController;

    public function __construct() {
        $this->registerController = new RegisterController();
    }

    public function index() {
        $this->registerController->index();
    }

    public function register_POST() {
        $this->registerController->register_POST();
    }
}
?>
