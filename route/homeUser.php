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
//                $result = $homeUserMiddleWare->checkVerification();
//                $_SESSION['isVerified'] = $result;
                break;
            case '':
                $homeUserMiddleWare->redirectToIndex();
//                $homeUserMiddleWare->checkVerification();
                break;
        }

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