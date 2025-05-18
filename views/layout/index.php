<?php

    include_once "./app/controllers/HomeUserController.php";
    $homeUserController = new HomeUserController();
    $user = $homeUserController->getUserInfo();
    $userData = $user['user'];

    /**
     * @var $content
    */
?>

<script>
    const userFontScale = <?= isset($_SESSION['fontSize']) ? json_encode($_SESSION['fontSize']) : 1.0 ?>;
    const userFontColor = <?= isset($_SESSION['noteColor']) ? json_encode($_SESSION['noteColor']) : 1.0 ?>;
</script>

<!doctype html>
<html lang="en">
    <?php
        include "./views/layout/partials/header.php";
    ?>
<body class="<?php
if (isset($userData) && !empty($userData['isDarkTheme']) && $userData['isDarkTheme'] == true) {
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
