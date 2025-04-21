<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'yourgmail@gmail.com';         // Your Gmail address
    $mail->Password   = 'your-app-password';           // Your App Password (not your Gmail password)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('yourgmail@gmail.com', 'Your App Name');
    $mail->addAddress($userEmail);                     // Recipient's email

    // Email content
    $token = bin2hex(random_bytes(16));
    $verificationLink = "https://yourdomain.com/verify.php?token=$token";

    $mail->isHTML(true);
    $mail->Subject = 'Verify Your Email Address';
    $mail->Body    = "
        <p>Hi there,</p>
        <p>Click the link below to verify your email address:</p>
        <p><a href='$verificationLink'>$verificationLink</a></p>
        <p>This link will expire in 1 hour.</p>
    ";

    $mail->send();
    echo 'Verification email has been sent';

    // Store the token in your DB here...
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
