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
                    $authMiddleware->login();
                } else if($_GET['param_2'] == 'logout'){
                    $authMiddleware->logout();
                }
                break;
        }
    } else if (isset($_GET['param_1'])){
        switch ($_GET['param_1']){
            case '':
                $authMiddleware->login();
        }
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
                if($_GET['param_2'] == 'token-refresh'){
                    $authMiddleware->tokenRefresh();
                }
                break;
        }
    } else if (isset($_GET['param_1'])){

    }
}
?>