<?php
    /**
     * @var $content
     */
    if($content == 'home'){
        include "./views/site/home.php";
    } else if($content == 'location'){
        include "./views/location/location.php";
    }
    //home-user
    else if($content == 'home-user'){
        include "./views/home-user/home-user.php";
    } else if($content == 'home-user-account') {
        include "./views/home-user/home-user-account.php";
    }

?>