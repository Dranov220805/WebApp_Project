<?php

require 'app/core/mailers/mailVerification.php';

function sendActivationEmail($to, $activation_token) {
    $subject = "Verify your Note account";
    $activation_link = "http://localhost:8080/auth/activate?token=" . $activation_token;

    // Using heredoc for better readability
    $body = <<<EOD
<!DOCTYPE html>
<html>
<head>
    <title>Email Confirmation</title>
</head>
<body style='margin-top:20px;'>
    <table class='body-wrap' style='font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;' bgcolor='#f6f6f6'>
        <tbody>
            <tr>
                <td valign='top'></td>
                <td class='container' width='600' valign='top'>
                    <div class='content' style='padding: 20px;'>
                        <table class='main' width='100%' cellpadding='0' cellspacing='0' style='border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;' bgcolor='#fff'>
                            <tbody>
                                <tr>
                                    <td class='' style='font-size: 16px; vertical-align: top; color: #fff; font-weight: 500; text-align: center; background-color: #989898; padding: 20px;' align='center' bgcolor='#71b6f9' valign='top'>
                                    <img height="24" src="/public/img/logo/logo-pernote-brand-nobg.png" alt="Pernote Logo"><br>
                                        <a href='#' style='font-size:32px;color:#fff;text-decoration: none;'>Hi there!</a> <br>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='content-wrap' style='padding: 20px;' valign='top'>
                                        <table width='100%' cellpadding='0' cellspacing='0'>
                                            <tbody>
                                                <tr>
                                                    <td class='content-block' style='padding: 0 0 20px;' valign='top'>
                                                       Thank you for creating an Pernote account. To continue setting up your workspace, please verify your email by clicking the link below:
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='content-block' style='text-align: center;' valign='top'>
                                                        <a href="$activation_link" class='btn-primary' style='font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; border-radius: 5px; background-color: #D10024; padding: 8px 16px; display: inline-block;'>Verify my email address</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='content-block' style='padding: 0 0 20px;' valign='top'>
                                                        This link will verify your email address, and then you’ll officially be a part of the Pernote community.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='content-block' style='padding: 0 0 20px;' valign='top'>
                                                        See you there!
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='content-block' style='padding: 0 0 20px;' valign='top'>
                                                        Best regards, the Pernote team.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
                <td valign='top'></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
EOD;
    return sendEmail($to, $subject, $body);
}

function sendRessetPasswordEmail($to, $newPassword) {
    $subject = "Password reset";
    $reset_password_link = "http://localhost:8080/log/login";
    $body = <<<EOD
<!DOCTYPE html>
<html>
<head>
    <title>Email Confirmation</title>
</head>
<body style='margin-top:20px;'>
    <table class='body-wrap' style='font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;' bgcolor='#f6f6f6'>
        <tbody>
            <tr>
                <td valign='top'></td>
                <td class='container' width='600' valign='top'>
                    <div class='content' style='padding: 20px;'>
                        <table class='main' width='100%' cellpadding='0' cellspacing='0' style='border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;' bgcolor='#fff'>
                            <tbody>
                                <tr>
                                    <td class='' style='font-size: 16px; vertical-align: top; color: #fff; font-weight: 500; text-align: center; background-color: #989898; padding: 20px;' align='center' bgcolor='#71b6f9' valign='top'>
                                    <img height="24" src="../public/img/logo/logo-pernote-brand-nobg.png" alt="Pernote Logo"><br>
                                        <a href='#' style='font-size:32px;color:#fff;text-decoration: none;'>Hi there!</a> <br>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='content-wrap' style='padding: 20px;' valign='top'>
                                        <table width='100%' cellpadding='0' cellspacing='0'>
                                            <tbody>
                                                <tr>
                                                    <td class='content-block' style='padding: 0 0 20px;' valign='top'>
                                                       You have requested to reset your password. Below is your new password:
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='content-block' style='padding: 0 0 20px;' valign='top'>
                                                       <p>$newPassword</p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='content-block' style='text-align: center;' valign='top'>
                                                        <a href="$reset_password_link" class='btn-primary' style='font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; border-radius: 5px; background-color: #D10024; padding: 8px 16px; display: inline-block;'>Login</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='content-block' style='padding: 0 0 20px;' valign='top'>
                                                        This link will redirect you to Pernote login page.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='content-block' style='padding: 0 0 20px;' valign='top'>
                                                        See you there!
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='content-block' style='padding: 0 0 20px;' valign='top'>
                                                        Best regards, the Pernote team.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
                <td valign='top'></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
EOD;

    return sendEmail($to, $subject, $body);
}

function sendToOtherUser($to, $subject, $body) {

    return SendEmail($to, $subject, $body);
}

?>