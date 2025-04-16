<?php
?>

<!doctype html>
<html lang="en">
<?php
include "./views/layout/partials/header.php";
?>

<?php
include './views/layout/partials/toast.php';
?>
<?php
include "./views/layout/partials/overlay_loading.php";
?>
<body>

<h1>Test</h1>

</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.enableButton){
            enableButton();
        }
        if(window.checkLogin){
            checkLogin();
        }
    })
</script>

