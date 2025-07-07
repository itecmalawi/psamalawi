<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendEmail($to, $subject, $body, $name = '') {
  $mail = new PHPMailer(true);

  try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'leonardjjmhone@gmail.com';      // 🔴 Your Gmail
    $mail->Password   = '@Itecictesolutionz2025';          // 🔒 App Password, NOT your Gmail password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('leonardjjmhone@gmail.com', 'Malawi PSA');
    $mail->addAddress($to, $name);

    // Content
    $mail->isHTML(false);
    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->send();
    return true;
  } catch (Exception $e) {
    error_log("Mailer Error: {$mail->ErrorInfo}");
    return false;
  }
}
