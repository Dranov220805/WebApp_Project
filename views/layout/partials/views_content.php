<?php
    /**
     * @var $content
     */
    //home-user
    if($content == 'home'){
        include "./views/home-user/home-user.php";
    } else if($content == 'home-user-account') {
        include "./views/home-user/home-user-account.php";
    } else if($content == 'home-user-preference') {
        include "./views/home-user/home-user-preference.php";
    }


?>