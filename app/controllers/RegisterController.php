<?php

class RegisterController {
    private RegisterService $registerService;
    public function __construct() {
        $this->registerService = new RegisterService();
    }

    public function index() {
        $content = 'register';
        $footer = 'home';
        include "views/log/register.php";
    }
}