<?php

include "./app/controllers/HomeUserController.php";
include "./app/middlewares/HomeUserMiddleWare.php";
$homeUserMiddleWare = new HomeUserMiddleWare();
$homeUserController = new HomeUserController();

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['param_1']) && isset($_GET['param_2']) && isset($_GET['param_3'])){

    } else if (isset($_GET['param_1']) && isset($_GET['param_2'])){
        switch ($_GET['param_1']){
            case 'home-user':
                if(isset($_GET['param_2']) == 'account'){
                    $homeUserMiddleWare->userAccount();
                }
                break;
        }
    } else if (isset($_GET['param_1'])){
        switch ($_GET['param_1']){
            case 'home':
                $homeUserMiddleWare->index();
                break;
        }
    }

}