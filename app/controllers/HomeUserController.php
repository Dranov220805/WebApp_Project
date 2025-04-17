<?php

class HomeUserController{
    private HomeUserService $homeUserService;

    public function __construct(){
        $this->homeUserService = new HomeUserService();
    }

    public function index() {
        $content = 'home';
        $footer = 'home';
        include "./views/layout/index.php";
    }
    public function homeAccount() {
        $content = 'home-user-account';
        $footer = 'home';
        include "./views/layout/index.php";
    }
}

?>