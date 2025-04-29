<?php

include "./app/controllers/HomeUserController.php";
include "./app/middlewares/HomeUserMiddleWare.php";

$homeUserMiddleWare = new HomeUserMiddleWare();
$homeUserController = new HomeUserController();

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['param_1']) && isset($_GET['param_2']) && isset($_GET['param_3'])){
        switch ($_GET['param_1']) {
            case 'home':
                if($_GET['param_2'] == 'upload'){
                    switch ($_GET['param_3']) {
                        case 'upload':
                            $homeUserMiddleWare->uploadAvatar();
                    }
                }
        }
    } else if (isset($_GET['param_1']) && isset($_GET['param_2'])){
        switch ($_GET['param_1']){
            case 'home':
                if($_GET['param_2'] == 'account'){
                    $homeUserMiddleWare->homeReference();
                } else if($_GET['param_2'] == 'label'){
                    $homeUserMiddleWare->homeLabel();
                } else if($_GET['param_2'] == 'archive'){
                    $homeUserMiddleWare->homeArchive();
                } else if($_GET['param_2'] == 'trash'){
                    $homeUserMiddleWare->homeTrash();
                } else if($_GET['param_2'] == 'preferences'){
                    $homeUserMiddleWare->userPreference();
                }
                break;
        }
    } else if (isset($_GET['param_1'])){
        switch ($_GET['param_1']){
            case 'home':
                $homeUserMiddleWare->index();
                break;
            case '':
                $homeUserMiddleWare->redirectToIndex();
                break;
        }

    } else {
        $homeUserMiddleWare->redirectToIndex();
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['param_1'], $_GET['param_2'], $_GET['param_3'])) {
        if ($_GET['param_1'] === 'home' && $_GET['param_2'] === 'upload' && $_GET['param_3'] === 'avatar') {
            $homeUserMiddleWare->uploadAvatar();
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (isset($_GET['param_1'], $_GET['param_2'])) {
        if ($_GET['param_1'] === 'home' && $_GET['param_2'] === 'preferences') {
            $homeUserMiddleWare->updatePreference();
        }
    }
}


//if ($_SERVER['REQUEST_METHOD'] == 'GET') {
//    $param1 = $_GET['param_1'] ?? null;
//    $param2 = $_GET['param_2'] ?? null;
//    $param3 = $_GET['param_3'] ?? null;
//
//    if ($param1 === 'home') {
//        if ($param2 === 'preference') {
//            $homeUserMiddleWare->homeReference();
//        } elseif ($param2 === 'label') {
//            $homeUserMiddleWare->homeLabel();
//        } elseif ($param2 === 'archive') {
//            $homeUserMiddleWare->homeArchive();
//        } elseif ($param2 === 'trash') {
//            $homeUserMiddleWare->homeTrash();
//        } elseif ($param2 === null) {
//            $homeUserMiddleWare->index();
//        } else {
//            $homeUserMiddleWare->redirectToIndex();
//        }
//    } else {
//        $homeUserMiddleWare->showError();
//    }
//}