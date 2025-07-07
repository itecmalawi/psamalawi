<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$users = $conn->query("SELECT * FROM admins");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users - PSA Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="flex bg-gray-100 min-h-screen font-sans">

<!-- Sidebar -->
<div class="w-64 bg-black text-white p-6 space-y-6 flex-shrink-0 animate-fadeInLeft">
  <h2 class="text-2xl font-bold text-green-500">PSA Admin</h2>
  <nav class="space-y-2 mt-6">
    <a href="dashboard.php" class="block hover:text-green-400"><i class="fa fa-home mr-2"></i>Dashboard</a>
    <a href="manage_users.php" class="block text-red-500"><i class="fa fa-user mr-2"></i>Manage Users</a>
    <a href="manage_news.php" class="block hover:text-green-400"><i class="fa fa-newspaper mr-2"></i>News</a>
    <a href="manage_team.php" class="block hover:text-green-400"><i class="fa fa-users mr-2"></i>Team</a>
    <a href="manage_journals.php" class="block hover:text-green-400"><i class="fa fa-book mr-2"></i>Journals</a>
    <a href="manage_comments.php" class="block hover:text-green-400"><i class="fa fa-comments mr-2"></i>Comments</a>
    <a href="manage_subscribers.php" class="block hover:text-green-400"><i class="fa fa-envelope mr-2"></i>Subscribers</a>
    <a href="auth/logout.php" class="block mt-4 text-red-400 hover:underline"><i class="fa fa-sign-out-alt mr-2"></i>Logout</a>
  </nav>
</div>

<!-- Main Content -->
<div class="flex-1 p-8 animate-fadeIn">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Manage Admin Users</h1>
    <a href="add_user.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
      <i class="fa fa-plus mr-1"></i>Add Admin
    </a>
  </div>

  <div class="overflow-x-auto shadow rounded-lg bg-white">
    <table class="min-w-full table-auto">
      <thead class="bg-red-600 text-white text-left">
        <tr>
          <th class="py-3 px-4">#</th>
          <th class="py-3 px-4">Username</th>
          <th class="py-3 px-4">Email</th>
          <th class="py-3 px-4">Actions</th>
        </tr>
      </thead>
      <tbody class="text-gray-700">
        <?php $i = 1; while($user = $users->fetch_assoc()): ?>
        <tr class="border-b hover:bg-gray-100 transition">
          <td class="py-3 px-4"><?= $i++ ?></td>
          <td class="py-3 px-4"><?= htmlspecialchars($user['username']) ?></td>
          <td class="py-3 px-4"><?= htmlspecialchars($user['email']) ?></td>
          <td class="py-3 px-4 space-x-2">
            <a href="edit_user.php?id=<?= $user['id'] ?>" class="text-blue-600 hover:underline"><i class="fa fa-edit"></i> Edit</a>
            <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Delete this user?')" class="text-red-600 hover:underline"><i class="fa fa-trash"></i> Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<style>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeInLeft {
  from { opacity: 0; transform: translateX(-20px); }
  to { opacity: 1; transform: translateX(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.7s ease-in-out;
}
.animate-fadeInLeft {
  animation: fadeInLeft 0.7s ease-in-out;
}
</style>

</body>
</html>
