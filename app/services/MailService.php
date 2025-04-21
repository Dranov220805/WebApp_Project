<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class MailService
{
    private PHPMailer $mail;
    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/.../config/mail.php';

        $this->mail = new PHPMailer(true);
        $this->setupSMTP();
    }

    private function setupSMTP(): void
    {
        $this->mail->isSMTP();
        $this->mail->Host = $this->config['host'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $this->config['username'];
        $this->mail->Password = $this->config['password'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = $this->config['port'];

        $this->mail->setFrom($this->config['from_email'], $this->config['from_name']);
        $this->mail->isHTML(true);
    }

    public function sendEmailVerification(string $toEmail, string $token): bool
    {
        try {
            $this->mail->clearAllRecipients();
            $this->mail->addAddress($toEmail);
            $this->mail->Subject = 'Verify Your Email Address';

            $verificationLink = "https://yourdomain.com/verify.php?token=$token";

            $this->mail->Body = "
                <p>Hello,</p>
                <p>Please verify your email address by clicking the link below:</p>
                <p><a href='$verificationLink'>$verificationLink</a></p>
                <p>This link will expire in 1 hour.</p>
            ";

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Email sending failed: " . $this->mail->ErrorInfo);
            return false;
        }
    }
}
