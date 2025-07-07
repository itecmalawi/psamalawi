<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  // Check for duplicates
  $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
  $stmt->bind_param("ss", $username, $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $error = "Username or Email already exists.";
  } else {
    $stmt = $conn->prepare("INSERT INTO admins (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $username, $email, $password);
    if ($stmt->execute()) {
      $success = "Admin user added successfully!";
    } else {
      $error = "Something went wrong while adding the user.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Admin User - PSA</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<!-- Layout -->
<div class="flex min-h-screen">

  <!-- Sidebar -->
  <div class="w-64 bg-gradient-to-br from-red-800 via-black to-green-800 text-white p-5">
    <h2 class="text-2xl font-bold mb-6">PSA Admin</h2>
    <nav class="space-y-2">
      <a href="dashboard.php" class="block hover:bg-gray-800 px-3 py-2 rounded">Dashboard</a>
      <a href="manage_users.php" class="block bg-gray-800 px-3 py-2 rounded">Manage Users</a>
      <a href="add_user.php" class="block hover:bg-gray-800 px-3 py-2 rounded">Add User</a>
      <a href="auth/logout.php" class="block mt-10 text-red-300 hover:text-white">Logout</a>
    </nav>
  </div>

  <!-- Main -->
  <div class="flex-1 p-8">
    <h2 class="text-2xl font-bold text-green-700 mb-6">Add Admin User</h2>

    <?php if ($success): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="bg-white shadow p-6 rounded max-w-lg">
      <div class="mb-4">
        <label class="block mb-1 text-gray-700">Username</label>
        <input type="text" name="username" required class="w-full border px-4 py-2 rounded focus:outline-none focus:ring focus:border-green-600">
      </div>
      <div class="mb-4">
        <label class="block mb-1 text-gray-700">Email</label>
        <input type="email" name="email" required class="w-full border px-4 py-2 rounded focus:outline-none focus:ring focus:border-green-600">
      </div>
      <div class="mb-4">
        <label class="block mb-1 text-gray-700">Password</label>
        <input type="password" name="password" required class="w-full border px-4 py-2 rounded focus:outline-none focus:ring focus:border-green-600">
      </div>
      <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-5 py-2 rounded transition">Add User</button>
    </form>
  </div>
</div>

<!-- Footer -->
<footer class="text-center py-4 text-sm text-gray-600 w-full mt-6">
  &copy; <?= date('Y') ?> PSA Admin Panel v1.0 — By <strong>Leonard Mhone</strong>
</footer>

</body>
</html>
