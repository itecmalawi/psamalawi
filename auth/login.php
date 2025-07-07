<?php
session_start();
include '../includes/db.php';

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $user = $stmt->get_result()->fetch_assoc();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['admin'] = $username;
    header("Location: ../dashboard.php");
    exit;
  } else {
    $loginError = "Invalid username or password";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>PSA Admin Login</title>
  <link rel="icon" href="../images/favicon.png" type="image/x-icon">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    * {
      box-sizing: border-box;
    }
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
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 30px 15px;
    }

    .login-box {
      background: white;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 400px;
      animation: slideUp 0.7s ease;
    }

    .login-box h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #117733;
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
      font-size: 16px;
    }

    button {
      width: 100%;
      background: #cc0000;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    button:hover {
      background: #a30000;
    }

    .links {
      text-align: center;
      margin-top: 20px;
    }

    .links a {
      color: #117733;
      margin: 0 8px;
      font-size: 14px;
      text-decoration: none;
    }

    .error {
      color: red;
      text-align: center;
      margin-bottom: 15px;
    }

    footer {
      text-align: center;
      font-size: 13px;
      color: #fff;
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
    <div class="login-box">
      <div class="logo">
        <img src="../images/logo.png" alt="PSA Logo">
      </div>
      <h2>PSA Admin Login</h2>
      <?php if ($loginError): ?>
        <div class="error"><?= $loginError ?></div>
      <?php endif; ?>
      <form method="post">
        <input name="username" type="text" placeholder="Username" required>
        <input name="password" type="password" placeholder="Password" required>
        <button type="submit">Login</button>
      </form>
      <div class="links">
        <a href="register.php">Register</a> |
        <a href="forgot_password.php">Forgot Password?</a> |
        <a href="reset_password.php">Reset</a>
      </div>
    </div>
  </div>

  <footer>
    &copy; <?= date('Y') ?> PSA Admin Panel v1.0 — By <b>Leonard Mhone</b>
  </footer>

</body>
</html>
