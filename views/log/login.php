<?php
?>

<!doctype html>
<html lang="en">
<?php
include "./views/layout/partials/header.php";
?>

<body>
<!--<section class="login-section">-->
<!--    <form action="#" onsubmit="return false" class="login-section-2__form">-->
<!--        <figure class="login-section-2__form--figure mb-5">-->
<!--            <img style="width: 200px; " src="/public/img/logo/logo-pernote-brand-nobg.png" draggable="false" alt="login img" class="login-section-2__form--image">-->
<!--        </figure>-->
<!--        <h1 class="login-section-2__form--greeting mb-1">Welcome to Pernote</h1>-->
<!--        <h2 class="login-section-2__form--greeting-2 mb-3">Sign in to access your notes</h2>-->
<!--        <div class="login-section-2__form--input input-group mb-3">-->
<!--            <label class="form-label" for="username">Username:</label>-->
<!--            <input id="id-input" class="login-section-2__form--input-box form-control" oninput="enableButton()" onclick="enableButton()" placeholder="Username" type="text">-->
<!--        </div>-->
<!--        <div class="login-section-2__form--input mb-3">-->
<!--            <label class="form-label" for="login-button">Password:</label>-->
<!--            <div>-->
<!--                <input id="password-input" class="login-section-2__form--input-box form-control" oninput="enableButton()" onclick="enableButton()" placeholder="Password" type="password">-->
<!--                <span><i id="toggle-password" class="fa-regular fa-eye-slash" onclick="togglePassword()"></i></span>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="login-section-2__form--link-forgot-password">-->
<!--            <p class="d-flex flex-direction-column"><span class="d-flex align-content-center"><input id="login-section-2__form--remember-me" type="checkbox" value="isRemembered"></span>Remember me</p>-->
<!--            <a id="login-section-2__form--forgot_password" class="mr-0" href="#">Forgot password?</a>-->
<!--        </div>-->
<!--        <button id="login-button" class="login-section-2__form--button">Sign in</button>-->
<!--        <p>Don't have an account? <span><a id="register-button" class="login-section-2__form--link-signup">Sign up</a></span></p>-->
<!--    </form>-->
<!--</section>-->

<section class="login-section py-5">
    <div class="container">
        <div class="row justify-content-center " style="width=30%">
            <div class=" login-section-2">
                <form action="#" onsubmit="return false" class="login-section-2__form bg-white p-4 shadow rounded">
                    <figure class="text-center mb-4 mt-3">
                        <img src="/public/img/logo/logo-pernote-brand-nobg.png" alt="login img" style="width: 200px;" draggable="false" class="img-fluid login-section-2__form--image">
                    </figure>

                    <h1 class="text-center h4 mb-1 login-section-2__form--greeting">Welcome to Pernote</h1>
                    <h2 class="text-center h6 text-muted mb-4 login-section-2__form--greeting-2">Sign in to access your Pernote</h2>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control login-section-2__form--input-box" id="email-input" placeholder="example@gmail.com" required>
<!--                        <input type="text" class="form-control login-section-2__form--input-box" id="email-input" placeholder="example@gmail.com" oninput="enableButton()" onclick="enableButton()">-->
                    </div>

                    <div class="mb-3">
                        <label for="password-input" class="form-label">Password:</label>
                        <div class="input-group">
                            <input type="password" class="form-control login-section-2__form--input-box" id="password-input" placeholder="Password">
<!--                            <input type="password" class="form-control login-section-2__form--input-box" id="password-input" placeholder="Password" oninput="enableButton()" onclick="enableButton()">-->
                            <span class="input-group-text bg-white">
                                <i id="toggle-password" class="fa-regular fa-eye-slash" onclick="togglePassword()" style="cursor: pointer;"></i>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="login-section-2__form--remember-me" value="isRemembered">
                            <label class="form-check-label" for="login-section-2__form--remember-me">Remember me</label>
                        </div>
                        <a href="#" id="login-section-2__form--forgot_password" class="small">Forgot password?</a>
                    </div>

                    <div class="d-grid mb-3">
                        <button id="login-button" class="btn btn-primary login-section-2__form--button">Sign in</button>
                    </div>

                    <p class="text-center">Don't have an account?
                        <a href="/reg/register" id="register-button" class="login-section-2__form--link-signup">Sign up</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>


<?php
    include './views/layout/partials/toast.php';
?>
<?php
    include "./views/layout/partials/overlay_loading.php";
?>
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
