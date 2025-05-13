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
                } else if ($_GET['param_2'] == 'label') {
                    $labelName = urldecode($_GET['param_3']);
                    $homeUserMiddleWare->homeLabel_POST($labelName);
                }
                break;
        }
    } else if (isset($_GET['param_1']) && isset($_GET['param_2'])){
        switch ($_GET['param_1']){
            case 'home':
                if($_GET['param_2'] == 'account'){
                    $homeUserMiddleWare->homeReference();
                } else if($_GET['param_2'] == 'label'){
                    $homeUserMiddleWare->homeLabel();
                } else if($_GET['param_2'] == 'share'){
                    $homeUserMiddleWare->homeShare();
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

    }

} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['param_1'], $_GET['param_2'], $_GET['param_3'])) {
        if ($_GET['param_1'] === 'home' && $_GET['param_2'] === 'upload' && $_GET['param_3'] === 'avatar') {
            $homeUserMiddleWare->uploadAvatar();
        }
    } else if (isset($_GET['param_1'], $_GET['param_2'])) {
        switch ($_GET['param_1']) {
            case 'share-list':
                if ($_GET['param_2'] == 'add') {
                    $homeUserMiddleWare->addNewSharedEmail_POST();
                }
                break;
        }
    } else if (isset($_GET['param_1'])) {
        switch ($_GET['param_1']) {
            case 'share-list':
                $homeUserMiddleWare->sharedEmailList();
                break;
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (isset($_GET['param_1'], $_GET['param_2'])) {
        if ($_GET['param_1'] === 'home' && $_GET['param_2'] === 'preferences') {
            $homeUserMiddleWare->updatePreference();
        }
        switch ($_GET['param_1']) {
            case 'share-list':
                if ($_GET['param_2'] == 'update-permission') {
                    $homeUserMiddleWare->updateShareEmail_PUT();
                }
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isset($_GET['param_1']) && isset($_GET['param_2'])) {
        switch ($_GET['param_1']) {
            case 'share-list':
                if ($_GET['param_2'] == 'delete') {
                    $homeUserMiddleWare->deleteSharedEmail_DELETE();
                }
        }
    }
}