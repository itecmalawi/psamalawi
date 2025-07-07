<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$teams = $conn->query("SELECT * FROM team_members ORDER BY category, name ASC");

$grouped = [];
while ($row = $teams->fetch_assoc()) {
  $grouped[$row['category']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Team - PSA Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="flex bg-gray-100 min-h-screen font-sans">

<!-- Sidebar -->
<div class="w-64 bg-black text-white p-6 space-y-6 flex-shrink-0">
  <h2 class="text-2xl font-bold text-green-500">PSA Admin</h2>
  <nav class="space-y-2 mt-6">
    <a href="dashboard.php" class="block hover:text-green-400"><i class="fa fa-home mr-2"></i>Dashboard</a>
    <a href="manage_team.php" class="block text-red-500"><i class="fa fa-users mr-2"></i>Manage Team</a>
    <a href="add_team_member.php" class="block hover:text-green-400"><i class="fa fa-user-plus mr-2"></i>Add Member</a>
    <a href="auth/logout.php" class="block mt-4 text-red-400 hover:underline"><i class="fa fa-sign-out-alt mr-2"></i>Logout</a>
  </nav>
</div>

<!-- Main Content -->
<div class="flex-1 p-8">
  <h1 class="text-3xl font-bold text-gray-800 mb-6">Team Members</h1>

  <?php foreach ($grouped as $category => $members): ?>
    <div class="mb-10">
      <h2 class="text-xl font-semibold mb-4 text-green-800"><?= htmlspecialchars($category) ?></h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($members as $m): ?>
          <div class="bg-white rounded shadow hover:shadow-lg transition transform hover:-translate-y-1">
            <img src="uploads/team/<?= htmlspecialchars($m['image']) ?>" class="w-full h-56 object-cover rounded-t" alt="<?= htmlspecialchars($m['name']) ?>">
            <div class="p-4 text-center">
              <h3 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($m['name']) ?></h3>
              <p class="text-green-700"><?= htmlspecialchars($m['position']) ?></p>
              <div class="mt-2 flex justify-center space-x-3 text-gray-500">
                <?php if ($m['facebook']): ?><a href="<?= $m['facebook'] ?>"><i class="fab fa-facebook"></i></a><?php endif; ?>
                <?php if ($m['twitter']): ?><a href="<?= $m['twitter'] ?>"><i class="fab fa-twitter"></i></a><?php endif; ?>
                <?php if ($m['linkedin']): ?><a href="<?= $m['linkedin'] ?>"><i class="fab fa-linkedin"></i></a><?php endif; ?>
              </div>
              <div class="mt-3">
                <a href="edit_team_member.php?id=<?= $m['id'] ?>" class="text-blue-600 text-sm hover:underline"><i class="fa fa-edit"></i> Edit</a>
                <a href="delete_team_member.php?id=<?= $m['id'] ?>" onclick="return confirm('Delete this member?')" class="text-red-600 text-sm ml-4 hover:underline"><i class="fa fa-trash"></i> Delete</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>

</div>

</body>
</html>
