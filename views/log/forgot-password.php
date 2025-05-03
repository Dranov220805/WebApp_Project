<!doctype html>
<html lang="en">
<?php
include "./views/layout/partials/header.php";
?>

<body>
<section class="login-section py-5">
    <div class="container">
        <div class="row justify-content-center " style="width=30%">
            <div class=" login-section-2">
                <form action="#" onsubmit="return false" class="login-section-2__form bg-white p-4 shadow rounded">
                    <figure class="text-center mb-4 mt-3">
                        <img src="/public/img/logo/logo-pernote-brand-nobg.png" alt="login img" style="width: 200px;" draggable="false" class="img-fluid login-section-2__form--image">
                    </figure>

                    <h2 class="text-center h4 mb-1 login-section-2__form--greeting">Enter your sign up email</h2>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control login-section-2__form--input-box" id="reset-email-input" placeholder="example@gmail.com">
                        <!--                        <input type="text" class="form-control login-section-2__form--input-box" id="email-input" placeholder="example@gmail.com" oninput="enableButton()" onclick="enableButton()">-->
                    </div>

                    <div class="d-grid mb-3">
                        <button id="reset-password-button" class="btn btn-primary login-section-2__form--button">Send new password through email</button>
                        <a href="/log/login" id="back-button" class="btn btn-primary login-section-2__form--button" style="margin-top: 10px;">Go back to login</a>
                    </div>

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
        if(window.forgotPassword){
            forgotPassword();
        }
    })
</script>