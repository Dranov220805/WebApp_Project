<?php
    $maintain = false;
    if($maintain)
        include "./views/error/maintain.php";
    else {
//        include "./app/middlewares/ErrorMiddleWare.php";
        include "./route/auth.php";
        include "./route/register.php";
//        include "./route/error.php";
        include "./route/homeUser.php";
        include "./route/note.php";
    }
?>