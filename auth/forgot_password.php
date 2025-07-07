<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
include '../includes/db.php';
require '../vendor/autoload.php'; // if installed via Composer

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $stmt = $conn->prepare("SELECT * FROM admins WHERE email=?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $admin = $stmt->get_result()->fetch_assoc();

  if ($admin) {
    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", strtotime("+30 minutes"));

    $stmt = $conn->prepare("UPDATE admins SET reset_token=?, token_expire=? WHERE email=?");
    $stmt->bind_param("sss", $token, $expires, $email);
    $stmt->execute();

    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'leonardjjmhone@gmail.com'; // 🔴 your Gmail
      $mail->Password = '@Itecictesolutionz2025'; // 🔴 Gmail password or app password
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;

      $mail->setFrom('leonardjmhone@gmail.com', 'PSA Admin');
      $mail->addAddress($email);
      $mail->isHTML(true);
      $mail->Subject = 'Password Reset Request';
      $reset_link = "http://localhost/psamalawi/reset_password.php?token=$token";
      $mail->Body = "Click <a href='$reset_link'>here</a> to reset your password. Link expires in 30 minutes.";
      $mail->send();
      $msg = "✅ Reset link sent. Check your email.";
    } catch (Exception $e) {
      $msg = "❌ Mail Error: " . $mail->ErrorInfo;
    }
  } else {
    $msg = "❌ Email not found.";
  }
}
?>

<form method="post">
  <h3>Forgot Password</h3>
  <?= $msg ?>
  <input name="email" placeholder="Enter your email" required>
  <button type="submit">Send Reset Link</button>
</form>
