<?php
    $maintain = false;
    if($maintain)
        include "./views/error/maintain.php";
    else {
        include "./route/auth.php";
//        include "./route/error.php";
        include "./route/homeUser.php";
//        include "./route/log.php";
        include "./route/note.php";
        include "./route/register.php";
        include "./route/site.php";
    }
?>