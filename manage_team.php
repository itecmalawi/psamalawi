<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$team = $conn->query("SELECT * FROM team_members ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Team - PSA Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="bg-gray-100 font-sans">

<div class="flex min-h-screen">
  <!-- Sidebar -->
  <div class="w-64 bg-gradient-to-b from-black via-red-700 to-green-800 text-white flex flex-col p-4 shadow-lg">
    <div class="text-2xl font-bold mb-6 text-center">PSA Admin</div>
    <nav class="space-y-2">
      <a href="dashboard.php" class="block px-3 py-2 rounded hover:bg-white hover:text-black"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</a>
      <a href="manage_news.php" class="block px-3 py-2 rounded hover:bg-white hover:text-black"><i class="fas fa-newspaper mr-2"></i>Manage News</a>
      <a href="add_news.php" class="block px-3 py-2 rounded hover:bg-white hover:text-black"><i class="fas fa-plus-circle mr-2"></i>Add News</a>
      <a href="manage_team.php" class="block px-3 py-2 rounded bg-white text-black font-semibold"><i class="fas fa-users mr-2"></i>Manage Team</a>
      <a href="manage_journals.php" class="block px-3 py-2 rounded hover:bg-white hover:text-black"><i class="fas fa-book mr-2"></i>Manage Journals</a>
      <a href="manage_subscribers.php" class="block px-3 py-2 rounded hover:bg-white hover:text-black"><i class="fas fa-envelope mr-2"></i>Subscribers</a>
      <a href="manage_comments.php" class="block px-3 py-2 rounded hover:bg-white hover:text-black"><i class="fas fa-comments mr-2"></i>Comments</a>
      <a href="manage_users.php" class="block px-3 py-2 rounded hover:bg-white hover:text-black"><i class="fas fa-user-shield mr-2"></i>Users</a>
      <a href="auth/logout.php" class="block mt-4 px-3 py-2 rounded bg-red-700 hover:bg-red-800"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="flex-1 p-6">
    <h2 class="text-3xl font-bold text-green-800 mb-4">👥 Manage Team Members</h2>

    <div class="mb-4">
      <a href="add_team_member.php" class="inline-block bg-green-700 text-white px-5 py-2 rounded hover:bg-green-800 transition">
        <i class="fas fa-user-plus mr-1"></i> Add Team Member
      </a>
    </div>

    <div class="bg-white rounded shadow p-4 overflow-x-auto">
      <table class="min-w-full text-left text-sm">
        <thead>
          <tr class="bg-green-700 text-white">
            <th class="p-3">#</th>
            <th class="p-3">Name</th>
            <th class="p-3">Position</th>
            <th class="p-3">Email</th>
            <th class="p-3">Image</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody class="text-gray-700">
          <?php $i = 1; while ($member = $team->fetch_assoc()): ?>
            <tr class="border-b hover:bg-gray-100 transition duration-150">
              <td class="p-3"><?= $i++ ?></td>
              <td class="p-3"><?= htmlspecialchars($member['name']) ?></td>
              <td class="p-3"><?= htmlspecialchars($member['position']) ?></td>
              <td class="p-3"><?= htmlspecialchars($member['email']) ?></td>
              <td class="p-3">
                <?php if (!empty($member['photo']) && file_exists($member['photo'])): ?>
                  <img src="<?= $member['photo'] ?>" alt="Photo" class="h-10 rounded"/>
                <?php else: ?>
                  <span class="text-gray-400">No Image</span>
                <?php endif; ?>
              </td>
              <td class="p-3 space-x-2">
                <a href="edit_team.php?id=<?= $member['id'] ?>" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i> Edit</a>
                <a href="delete_team.php?id=<?= $member['id'] ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Delete this member?')"><i class="fas fa-trash-alt"></i> Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="text-center py-4 mt-6 text-sm text-gray-600 bg-gray-200 border-t">
  &copy; <?= date('Y') ?> PSA Admin Panel v1.0 — By <strong>Leonard Mhone</strong>
</footer>

</body>
</html>
