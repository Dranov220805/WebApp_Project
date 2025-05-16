<?php

include "./app/controllers/AuthController.php";
include_once "./app/middlewares/AuthMiddleWare.php";
$authController = new AuthController();
$authMiddleware = new AuthMiddleware();

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['param_1']) && isset($_GET['param_2']) && isset($_GET['param_3'])){
        switch($_GET['param_1']){
            case 'log':
                if(isset($_GET['param_2']) == 'auth'){
                    switch($_GET['param_3']){
                        case 'forgot':
                            $authMiddleware->forgotPassword();
                    }
                }
        }
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
                } else if ($_GET['param_2'] == 'activate'){
                    $authMiddleware->getUrlActivationLink();
                }
                break;
        }
    } else if (isset($_GET['param_1'])){
    }

} else if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_GET['param_1']) && isset($_GET['param_2']) && isset($_GET['param_3'])){
        switch($_GET['param_1']){
            case 'log':
                if(isset($_GET['param_2']) == 'auth'){
                    switch($_GET['param_3']){
                        case 'forgot':
                            $authMiddleware->forgotPassword();
                    }
                }
        }
    } else if (isset($_GET['param_1']) && isset($_GET['param_2'])){
        switch ($_GET['param_1']){
            case 'log':
                if($_GET['param_2'] == 'login'){
                    $authMiddleware->login_POST();
                } else if($_GET['param_2'] == 'change-password'){
                    $authMiddleware->changePassword();
                }
                break;
            case 'auth':
                if($_GET['param_2'] == 'heartbeat'){
                    $authMiddleware->checkSession();
                } else if($_GET['param_2'] == 'forgot'){
                    $authMiddleware->resetPassword();
                }
                break;
        }
    } else if (isset($_GET['param_1'])){

    }
}

// else if($_SERVER['REQUEST_METHOD'] == 'PUT'){
//    if (isset($_GET['param_1']) && isset($_GET['param_2']) && isset($_GET['param_3'])) {
//
//    } else if (isset($_GET['param_1']) && isset($_GET['param_2'])) {
//        switch ($_GET['param_1']){
//            case 'auth':
//                if($_GET['param_2'] == 'activate'){
//                    $authMiddleware->accountActivate();
//                }
//        }
//    }
//}
?>