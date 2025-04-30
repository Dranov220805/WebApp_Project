<?php
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
if ($_SESSION['isDarkTheme'] == 1) {
    echo 'dark-mode';
} else {
    echo '';
} ?>">
<?= print_r('Is Dark Theme: ', $_SESSION['isDarkTheme']);?>
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
