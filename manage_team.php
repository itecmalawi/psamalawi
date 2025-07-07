<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$categories = [
  'Patron',
  'Members of the Executive',
  'Editorial Team',
  'Secretariat'
];

function getTeamMembers($conn, $category) {
  $stmt = $conn->prepare("SELECT * FROM team_members WHERE category = ? ORDER BY id DESC");
  $stmt->bind_param("s", $category);
  $stmt->execute();
  return $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Team - PSA Admin</title>
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
<body class="bg-gray-100 flex">

<!-- Sidebar -->
<aside class="w-64 bg-black text-white p-6">
  <h2 class="text-xl font-bold text-psaGreen mb-6">PSA Admin</h2>
  <nav class="space-y-2">
    <a href="dashboard.php" class="block hover:text-psaGreen">Dashboard</a>
    <a href="manage_team.php" class="block font-bold text-psaGreen">Manage Team</a>
    <a href="add_team_member.php" class="block hover:text-psaGreen">Add Member</a>
    <a href="auth/logout.php" class="block text-psaRed hover:text-red-400">Logout</a>
  </nav>
</aside>

<!-- Main Content -->
<main class="flex-1 p-6">
  <h1 class="text-3xl font-bold mb-6 text-gray-800">Manage Team Members</h1>

  <?php foreach ($categories as $cat): 
    $members = getTeamMembers($conn, $cat); 
    if ($members->num_rows > 0):
  ?>
    <div class="mb-10">
      <h2 class="text-2xl font-semibold text-psaGreen mb-4"><?= htmlspecialchars($cat) ?></h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php while ($m = $members->fetch_assoc()): ?>
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition transform hover:-translate-y-1 p-4 text-center">
          <img src="uploads/team/<?= htmlspecialchars($m['image']) ?>" alt="<?= htmlspecialchars($m['name']) ?>" class="w-32 h-32 mx-auto rounded-full object-cover mb-3">
          <h3 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($m['name']) ?></h3>
          <p class="text-sm text-gray-500 mb-3"><?= htmlspecialchars($m['position']) ?></p>
          <div class="flex justify-center space-x-3 text-psaGreen text-lg">
            <?php if ($m['facebook']): ?><a href="<?= $m['facebook'] ?>" target="_blank"><i class="fab fa-facebook"></i></a><?php endif; ?>
            <?php if ($m['twitter']): ?><a href="<?= $m['twitter'] ?>" target="_blank"><i class="fab fa-twitter"></i></a><?php endif; ?>
            <?php if ($m['linkedin']): ?><a href="<?= $m['linkedin'] ?>" target="_blank"><i class="fab fa-linkedin"></i></a><?php endif; ?>
            <?php if ($m['google']): ?><a href="<?= $m['google'] ?>" target="_blank"><i class="fab fa-google"></i></a><?php endif; ?>
          </div>
          <div class="mt-4 space-x-2">
            <a href="edit_team_member.php?id=<?= $m['id'] ?>" class="text-blue-600 hover:underline text-sm"><i class="fas fa-edit"></i> Edit</a>
            <a href="delete_team_member.php?id=<?= $m['id'] ?>" class="text-red-600 hover:underline text-sm" onclick="return confirm('Delete this member?')"><i class="fas fa-trash-alt"></i> Delete</a>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>
  <?php endif; endforeach; ?>

</main>

<!-- FontAwesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>
