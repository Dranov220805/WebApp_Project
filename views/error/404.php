<?php
?>
<!doctype html>
<html lang="en">
<?php
    include "./views/layout/partials/header.php";
?>
<body>
    <div class="container-xxl error container-p-y text-center d-flex justify-content-center align-items-center">
        <div class="misc-wrapper error-wrapper">
            <h2 class="mb-2 mx-2 error-title"></h2>
            <p class="mb-4 mx-2 error-text">Oops! You have lost in here, let me help you back!</p>
            <a href="/home" class="btn btn-primary">Back to home page</a>
            <div class="mt-3">
                <img src="/public/img/error/404.png" alt="page-misc-error-light" width="500"
                     class="img-fluid" data-app-dark-img="illustrations/page-misc-error-dark.png"
                     data-app-light-img="illustrations/page-misc-error-light.png" />
            </div>
        </div>
    </div>

<?php
    include './views/layout/partials/toast.php';
?>
</body>
</html>
