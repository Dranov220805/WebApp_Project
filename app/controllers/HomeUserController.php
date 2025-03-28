<?php

class HomeUserController{
    private HomeUserService $homeUserService;

    public function __construct(){
        $this->homeUserService = new HomeUserService();
    }

    public function index() {
        $content = 'home-user';
        include "./views/layout/index.php";
    }
}

?>