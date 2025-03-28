<?php

class SiteMiddleWare{

    private SiteController $siteController;

    public function __construct(){
        $this->siteController = new SiteController();
    }

    public function index(){
        if (isset($_SESSION['roleId'])) {
            $role_id = $_SESSION['roleId'];
            if ($role_id == 1) {
                header('location: /home-user');
            }
        } else{
            $this->siteController->index();
        }
    }

}

?>