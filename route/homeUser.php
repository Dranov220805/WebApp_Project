<?php

include "./app/controllers/HomeUserController.php";
include "./app/middlewares/HomeUserMiddleWare.php";

$homeUserMiddleWare = new HomeUserMiddleWare();
$homeUserController = new HomeUserController();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $param1 = $_GET['param_1'] ?? null;
    $param2 = $_GET['param_2'] ?? null;
    $param3 = $_GET['param_3'] ?? null;

    if ($param1 === 'home') {
        if ($param2 === 'preference') {
            $homeUserMiddleWare->homeReference();
        } elseif ($param2 === 'label') {
            $homeUserMiddleWare->homeLabel();
        } elseif ($param2 === 'archive') {
            $homeUserMiddleWare->homeArchive();
        } elseif ($param2 === 'trash') {
            $homeUserMiddleWare->homeTrash();
        } elseif ($param2 === null) {
            $homeUserMiddleWare->index();
        } else {
            $homeUserMiddleWare->redirectToIndex();
        }
    } else {
        $homeUserMiddleWare->redirectToIndex();
    }
}