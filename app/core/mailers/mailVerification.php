<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//thanhlongduong6a3@gmail.com
//khbijbyipibbhplv

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Config
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'thanhlongduong6a3@gmail.com'; // My email
        $mail->Password = 'khbijbyipibbhplv'; // App password (GG Account -> Search -> App password -> Create one)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and Receiver
        $mail->setFrom('thanhlongduong6a3@gmail.com', 'Pernote');
        $mail->addAddress($to);

        // Nội dung email
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        // Set content-type header for UTF-8
        $mail->CharSet = 'UTF-8';

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>