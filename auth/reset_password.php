<?php
session_start();
include '../includes/db.php';

$token = $_GET['token'] ?? '';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token = $_POST['token'];
  $newpass = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $stmt = $conn->prepare("SELECT id FROM admins WHERE reset_token = ? AND reset_token_expiry > NOW()");
  $stmt->bind_param("s", $token);
  $stmt->execute();
  $admin = $stmt->get_result()->fetch_assoc();

  if ($admin) {
    $stmt = $conn->prepare("UPDATE admins SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
    $stmt->bind_param("si", $newpass, $admin['id']);
    $stmt->execute();
    $msg = "Password updated. You may now login.";
  } else {
    $msg = "Invalid or expired token.";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Reset Password - PSA Admin</title>
  <link rel="icon" href="../images/favicon.png" type="image/x-icon">
  <style>
    body {
      background: linear-gradient(135deg, #000, #117733, #cc0000);
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
      animation: fadeIn 1s ease-in-out;
    }
    .container {
      background: #fff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 420px;
      animation: slideIn 0.6s ease-out;
    }
    .container h3 {
      text-align: center;
      color: #117733;
      margin-bottom: 20px;
    }
    input[type="password"] {
      width: 100%;
      padding: 10px 14px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }
    button {
      background: #cc0000;
      color: #fff;
      padding: 12px 16px;
      border: none;
      width: 100%;
      font-size: 16px;
      border-radius: 6px;
      transition: 0.3s ease;
      cursor: pointer;
    }
    button:hover {
      background: #b30000;
    }
    .message {
      text-align: center;
      margin-bottom: 15px;
      color: #333;
    }
    footer {
      text-align: center;
      color: #eee;
      font-size: 13px;
      margin-top: 30px;
    }
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    @keyframes slideIn {
      from { transform: translateY(40px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
  </style>
</head>
<body>
  <div class="container">
    <h3>Reset Password</h3>
    <?php if (!empty($msg)): ?>
      <div class="message"><?= $msg ?></div>
    <?php endif; ?>
    <form method="post">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <input name="password" type="password" placeholder="New password" required>
      <button type="submit">Update Password</button>
    </form>
    <a href="login.php" class="back">← Back to Login</a>
    <footer>&copy; <?= date('Y') ?> PSA Admin Panel v1.0 — By Leonard Mhone</footer>
  </div>
</body>
</html>
