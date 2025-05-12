<?php
    /**
     * @var $content
     */
    //home-user
    if($content == 'home'){
        include "./views/home-user/home-user.php";
    } else if($content == 'home-user-preference') {
        include "./views/home-user/home-user-preference.php";
    } else if($content == 'home-user-share') {
        include "./views/home-user/home-user-share.php";
    } else if($content == 'home-user-label') {
        include "./views/home-user/home-user-label.php";
    } else if($content == 'home-user-trash') {
        include "./views/home-user/home-user-trash.php";
    }


?>