<?php

include "./app/controllers/AuthController.php";
include "./app/middlewares/AuthMiddleWare.php";
$authController = new AuthController();
$authMiddleware = new AuthMiddleware();

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['param_1']) && isset($_GET['param_2']) && isset($_GET['param_3'])){

    } else if (isset($_GET['param_1']) && isset($_GET['param_2'])){
        switch ($_GET['param_1']){
            case 'log':
                if($_GET['param_2'] == 'login'){
                    $authMiddleware->index();
                } else if($_GET['param_2'] == 'logout'){
                    $authMiddleware->logout();
                }
                break;
            case 'auth':
                if($_GET['param_2'] == 'verification'){
                    $authMiddleware->checkVerification();
                }
                break;
        }
    } else if (isset($_GET['param_1'])){

    }

} else if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_GET['param_1']) && isset($_GET['param_2']) && isset($_GET['param_3'])){

    } else if (isset($_GET['param_1']) && isset($_GET['param_2'])){
        switch ($_GET['param_1']){
            case 'log':
                if($_GET['param_2'] == 'login'){
                    $authMiddleware->login_POST();
                }
                break;
            case 'auth':
                if($_GET['param_2'] == 'heartbeat'){
                    $authMiddleware->checkSession();
                }
                break;
        }
    } else if (isset($_GET['param_1'])){

    }
}
?>