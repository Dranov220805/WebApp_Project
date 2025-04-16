<?php

include "./app/controllers/RegisterController.php";
include "./app/middlewares/RegisterMiddleWare.php";
$registerController = new RegisterController();
$registerMiddleWare = new RegisterMiddleware();

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['param_1']) && isset($_GET['param_2']) && isset($_GET['param_3'])){

    } else if (isset($_GET['param_1']) && isset($_GET['param_2'])){
        switch ($_GET['param_1']){
            case 'reg':
                if($_GET['param_2'] == 'register'){
                    $registerMiddleWare->index();
                }
                break;
        }
    } else if (isset($_GET['param_1'])){

    }

//} else if($_SERVER['REQUEST_METHOD'] == 'POST'){
//    if(isset($_GET['param_1']) && isset($_GET['param_2']) && isset($_GET['param_3'])){
//
//    } else if (isset($_GET['param_1']) && isset($_GET['param_2'])){
//        switch ($_GET['param_1']){
//            case 'log':
//                if($_GET['param_2'] == 'login'){
//                    $authMiddleware->login_POST();
//                }
//                break;
//        }
//    } else if (isset($_GET['param_1'])){
//
//    }
}
?>