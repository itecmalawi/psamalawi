<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) die("Missing user ID");

$stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) die("User not found");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  if (!empty($password)) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE admins SET username=?, email=?, password=? WHERE id=?");
    $stmt->bind_param("sssi", $username, $email, $hashed, $id);
  } else {
    $stmt = $conn->prepare("UPDATE admins SET username=?, email=? WHERE id=?");
    $stmt->bind_param("ssi", $username, $email, $id);
  }

  $stmt->execute();
  header("Location: manage_users.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User - PSA Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            psaGreen: '#117733',
            psaRed: '#cc0000',
            psaBlack: '#000000'
          }
        }
      }
    }
  </script>
</head>
<body class="bg-gray-100 font-sans text-gray-800">
<div class="flex min-h-screen">

  <!-- Sidebar -->
  <aside class="w-64 bg-psaBlack text-white flex flex-col px-4 py-6">
    <div class="text-center mb-8">
      <img src="images/logo.png" alt="PSA Logo" class="mx-auto mb-3 w-16">
      <h2 class="text-xl font-semibold">PSA Admin</h2>
    </div>
    <nav class="flex flex-col gap-3 text-sm">
      <a href="dashboard.php" class="hover:bg-psaGreen py-2 px-3 rounded">Dashboard</a>
      <a href="manage_users.php" class="hover:bg-psaGreen py-2 px-3 rounded bg-psaGreen">Manage Users</a>
      <a href="add_user.php" class="hover:bg-psaGreen py-2 px-3 rounded">Add User</a>
      <a href="manage_news.php" class="hover:bg-psaGreen py-2 px-3 rounded">Manage News</a>
      <a href="manage_team.php" class="hover:bg-psaGreen py-2 px-3 rounded">Manage Team</a>
      <a href="manage_journals.php" class="hover:bg-psaGreen py-2 px-3 rounded">Manage Journals</a>
      <a href="manage_subscribers.php" class="hover:bg-psaGreen py-2 px-3 rounded">Subscribers</a>
      <a href="manage_comments.php" class="hover:bg-psaGreen py-2 px-3 rounded">Comments</a>
      <a href="auth/logout.php" class="mt-4 bg-psaRed py-2 px-3 text-center rounded hover:bg-red-700">Logout</a>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6">
    <h1 class="text-2xl font-bold text-psaGreen mb-6">Edit Admin User</h1>

    <form method="POST" class="bg-white shadow rounded p-6 space-y-4 max-w-lg">
      <label class="block">
        <span class="text-gray-700 font-medium">Username</span>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required class="w-full px-4 py-2 border rounded mt-1">
      </label>

      <label class="block">
        <span class="text-gray-700 font-medium">Email</span>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="w-full px-4 py-2 border rounded mt-1">
      </label>

      <label class="block">
        <span class="text-gray-700 font-medium">Password <small class="text-gray-500">(leave blank to keep current)</small></span>
        <input type="password" name="password" class="w-full px-4 py-2 border rounded mt-1">
      </label>

      <div class="flex justify-between items-center">
        <a href="manage_users.php" class="text-psaRed hover:underline text-sm">← Back</a>
        <button type="submit" class="bg-psaGreen hover:bg-green-800 text-white px-6 py-2 rounded shadow">Update User</button>
      </div>
    </form>

    <footer class="text-center text-sm text-gray-500 mt-10">
      &copy; <?= date('Y') ?> PSA Admin Panel v2.0 — By <b>Leonard Mhone</b>
    </footer>
  </main>
</div>
</body>
</html>
