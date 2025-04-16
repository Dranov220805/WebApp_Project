<?php

class SiteMiddleWare {

    private SiteController $siteController;

    public function __construct() {
        $this->siteController = new SiteController();
    }

    public function index() {
        if (isset($_SESSION['accessToken'])) {
            try {
                $jwtHandler = new JWTHandler();
                $decoded = $jwtHandler->decodeToken($_SESSION['accessToken']);

                // Token is valid, now check role
                if (isset($_SESSION['roleId'])) {
                    $role_id = $_SESSION['roleId'];
                    if ($role_id == 1) {
                        header('Location: /home-user');
                        exit;
                    }
                    // You can handle other roles here too if needed
                } else {
                    // Role not in session, force logout
                    session_destroy();
                    $this->siteController->index();
                }

            } catch (Exception $e) {
                // Token invalid or expired
                unset($_SESSION['accessToken']);
                unset($_SESSION['roleId']);
                session_destroy();
                $this->siteController->index();
            }
        } else {
            $this->siteController->index();
        }
    }
}
?>
