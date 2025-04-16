<?php
?>

<!doctype html>
<html lang="en">
<?php
include "./views/layout/partials/header.php";
?>

<body>
<!--<section class="min-vh-100 d-flex align-items-center justify-content-center bg-light">-->
<!--    <div class="card shadow-sm p-4 rounded-4" style="max-width: 400px; width: 100%;">-->
<!--        <div class="text-center mb-4">-->
<!--            <i class="bi bi-journal-text fs-2 mb-2"></i>-->
<!--            <figure class="text-center mb-4 mt-3">-->
<!--                <img src="/public/img/logo/logo-pernote-brand-nobg.png" alt="login img" style="width: 200px;" draggable="false" class="img-fluid login-section-2__form--image">-->
<!--            </figure>-->
<!--            <p class="text-muted">Create your account</p>-->
<!--        </div>-->
<!---->
<!--        <form action="#" onsubmit="return false">-->
<!--            <div class="mb-3">-->
<!--                <label for="email" class="form-label">Email address</label>-->
<!--                <input type="email" class="form-control rounded-3" id="email" placeholder="Enter email" required>-->
<!--            </div>-->
<!---->
<!--            <div class="mb-3">-->
<!--                <label for="displayName" class="form-label">Display name</label>-->
<!--                <input type="text" class="form-control rounded-3" id="displayName" placeholder="Enter display name" required>-->
<!--            </div>-->
<!---->
<!--            <div class="mb-3">-->
<!--                <label for="password" class="form-label">Password</label>-->
<!--                <input type="password" class="form-control rounded-3" id="password" placeholder="Enter password" required>-->
<!--            </div>-->
<!---->
<!--            <div class="mb-3">-->
<!--                <label for="confirmPassword" class="form-label">Confirm password</label>-->
<!--                <input type="password" class="form-control rounded-3" id="confirmPassword" placeholder="Re-enter password" required>-->
<!--            </div>-->
<!---->
<!--            <p class="small text-muted mb-3">-->
<!--                By signing up, you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a>.-->
<!--            </p>-->
<!---->
<!--            <div class="d-grid mb-2">-->
<!--                <button id="register-button" class="btn btn-primary rounded-3 fw-semibold">Create account</button>-->
<!--            </div>-->
<!---->
<!--            <p class="text-center mt-3 mb-0">-->
<!--                Already have an account? <a href="/log/login">Sign in</a>-->
<!--            </p>-->
<!--        </form>-->
<!--    </div>-->
<!--</section>-->

<section class="login-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="login-section-2">
                <form action="#" onsubmit="return false" class="login-section-2__form bg-white p-4 shadow rounded">
                    <figure class="text-center mb-4 mt-3">
                        <img src="/public/img/logo/logo-pernote-brand-nobg.png" alt="login img" style="width: 200px;" draggable="false" class="img-fluid login-section-2__form--image">
                    </figure>

                    <h1 class="text-center h4 mb-1 login-section-2__form--greeting">Create your account</h1>
                    <h2 class="text-center h6 text-muted mb-4 login-section-2__form--greeting-2">Sign up to start using Pernote</h2>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control login-section-2__form--input-box" id="email" placeholder="example@gmail.com" required>
                    </div>

                    <div class="mb-3">
                        <label for="displayName" class="form-label">Display name:</label>
                        <input type="text" class="form-control login-section-2__form--input-box" id="displayName" placeholder="Enter your display name" required>
                    </div>

<!--                    <div class="mb-3">-->
<!--                        <label for="password" class="form-label">Password:</label>-->
<!--                        <input type="password" class="form-control login-section-2__form--input-box" id="password" placeholder="Enter your password" required>-->
<!--                    </div>-->
<!---->
<!--                    <div class="mb-3">-->
<!--                        <label for="confirmPassword" class="form-label">Confirm password:</label>-->
<!--                        <input type="password" class="form-control login-section-2__form--input-box" id="confirmPassword" placeholder="Re-enter your password" required>-->
<!--                    </div>-->

                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <div class="input-group">
                            <input type="password" class="form-control login-section-2__form--input-box" id="password-input" placeholder="Password" oninput="enableButton()" onclick="enableButton()">
                            <span class="input-group-text bg-white">
                                <i id="toggle-password" class="fa-regular fa-eye-slash" onclick="togglePassword()" style="cursor: pointer;"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password:</label>
                        <div class="input-group">
                            <input type="password" class="form-control login-section-2__form--input-box" id="password-input-confirm" placeholder="Password" oninput="enableButton()" onclick="enableButton()">
                            <span class="input-group-text bg-white">
                                <i id="toggle-password-confirm" class="fa-regular fa-eye-slash" onclick="toggleConfirmPassword()" style="cursor: pointer;"></i>
                            </span>
                        </div>
                    </div>

                    <p class="small text-muted mb-3">
                        By signing up, you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a>.
                    </p>

                    <div class="d-grid mb-3">
                        <button id="register-button" class="btn btn-primary login-section-2__form--button">Create account</button>
                    </div>

                    <p class="text-center">Already have an account?
                        <a href="/" id="login-button" class="login-section-2__form--link-signup">Sign in</a>
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

