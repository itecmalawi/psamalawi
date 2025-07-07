<?php
session_start();
include '../includes/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name     = trim($_POST['name']);
  $username = trim($_POST['username']);
  $email    = trim($_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  // Check if username or email exists
  $check = $conn->prepare("SELECT id FROM admins WHERE username=? OR email=?");
  $check->bind_param("ss", $username, $email);
  $check->execute();
  $exists = $check->get_result()->fetch_assoc();

  if ($exists) {
    $error = "Username or email already exists.";
  } else {
    $stmt = $conn->prepare("INSERT INTO admins (name, username, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $username, $email, $password);
    if ($stmt->execute()) {
      $success = "Account created. You may now login.";
    } else {
      $error = "Something went wrong. Please try again.";
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register — PSA Admin Panel</title>
  <link rel="icon" href="../images/favicon.png" type="image/x-icon">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    * { box-sizing: border-box; }
    body {
      background: linear-gradient(135deg, #000, #117733, #cc0000);
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      animation: fadeIn 1s ease-in-out;
    }

    .container {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 30px 15px;
    }

    .register-box {
      background: white;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 450px;
      animation: slideUp 0.6s ease;
    }

    .register-box h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #cc0000;
    }

    .logo {
      text-align: center;
      margin-bottom: 10px;
    }

    .logo img {
      height: 70px;
    }

    input {
      width: 100%;
      padding: 12px;
      margin-bottom: 18px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
    }

    button {
      width: 100%;
      background: #117733;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    button:hover {
      background: #0d5526;
    }

    .msg {
      text-align: center;
      margin-bottom: 15px;
      font-weight: bold;
    }

    .msg.success { color: green; }
    .msg.error { color: red; }

    .links {
      text-align: center;
      margin-top: 15px;
    }

    .links a {
      color: #117733;
      text-decoration: none;
      margin: 0 8px;
      font-size: 14px;
    }

    footer {
      text-align: center;
      font-size: 13px;
      color: #eee;
      padding: 12px;
      background: rgba(0, 0, 0, 0.4);
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideUp {
      from { transform: translateY(40px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
  </style>
</head>
<body>

<div class="container">
  <div class="register-box">
    <div class="logo">
      <img src="../images/logo.png" alt="PSA Logo">
    </div>
    <h2>Admin Registration</h2>

    <?php if ($success): ?>
      <div class="msg success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="msg error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
      <input name="name" placeholder="Full Name" required>
      <input name="username" placeholder="Username" required>
      <input name="email" type="email" placeholder="Email" required>
      <input name="password" type="password" placeholder="Password" required>
      <button type="submit">Register</button>
    </form>

    <div class="links">
      <a href="login.php">Already have an account?</a>
    </div>
  </div>
</div>

<footer>
  &copy; <?= date('Y') ?> PSA Admin Panel v1.0 — By <b>Leonard Mhone</b>
</footer>

</body>
</html>
