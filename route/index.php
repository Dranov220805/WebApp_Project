<?php
    $maintain = false;
    if($maintain)
        include "./views/error/maintain.php";
    else {
        include "./route/site.php";
        include "./route/log.php";
        include "./route/homeUser.php";
    }
?>