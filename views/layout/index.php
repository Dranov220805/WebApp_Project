<?php

    include_once "./app/controllers/HomeUserController.php";
    $homeUserController = new HomeUserController();
    $user = $homeUserController->getUserInfo();
    $userData = $user['user'];

    /**
     * @var $content
    */
?>
<!doctype html>
<html lang="en">
    <?php
        include "./views/layout/partials/header.php";
    ?>
<body class="<?php
<<<<<<< Updated upstream
if (isset($userData) && !empty($userData->isDarkTheme) && $userData->isDarkTheme == true) {
=======
<<<<<<< Updated upstream
<<<<<<< Updated upstream
if (isset($GLOBALS['user']) && !empty($GLOBALS['user']->isDarkTheme) && $GLOBALS['user']->isDarkTheme == true) {
=======
if (isset($userData) && !empty($userData['isDarkTheme']) && $userData['isDarkTheme'] == true) {
>>>>>>> Stashed changes
=======
if (isset($userData) && !empty($userData['isDarkTheme']) && $userData['isDarkTheme'] == true) {
>>>>>>> Stashed changes
>>>>>>> Stashed changes
    echo 'dark-mode';
}
?>">
    <?php
        include "./views/layout/partials/toast.php";
    ?>
    <?php
        include "./views/layout/partials/navbar.php";
    ?>
    <?php
        include "./views/layout/partials/views_content.php";
    ?>
<!--    --><?php
//        include "./views/layout/partials/footer.php";
//    ?>
    <?php
        include "./views/layout/partials/overlay_loading.php";
    ?>
</body>
</html>
