<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$search = $_GET['search'] ?? '';
$searchSql = $search ? "WHERE name LIKE ? OR email LIKE ?" : "";
$stmt = $conn->prepare("SELECT * FROM subscribers $searchSql ORDER BY created_at DESC");
if ($search) {
  $like = "%$search%";
  $stmt->bind_param("ss", $like, $like);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Subscribers - PSA Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

<div class="flex min-h-screen">

  <!-- Sidebar -->
  <aside class="w-64 bg-gradient-to-b from-red-700 via-black to-green-700 text-white p-6">
    <div class="text-2xl font-bold mb-6">PSA Admin</div>
    <nav class="flex flex-col gap-2 text-sm">
      <a href="dashboard.php" class="hover:bg-white/10 p-2 rounded">📊 Dashboard</a>
      <a href="manage_news.php" class="hover:bg-white/10 p-2 rounded">📰 News</a>
      <a href="manage_team.php" class="hover:bg-white/10 p-2 rounded">👥 Team</a>
      <a href="manage_comments.php" class="hover:bg-white/10 p-2 rounded">💬 Comments</a>
      <a href="manage_journals.php" class="hover:bg-white/10 p-2 rounded">📘 Journals</a>
      <a href="manage_users.php" class="hover:bg-white/10 p-2 rounded">🧑‍💼 Users</a>
      <a href="manage_subscribers.php" class="bg-white/10 p-2 rounded font-bold">📩 Subscribers</a>
      <a href="auth/logout.php" class="bg-red-500 hover:bg-red-700 mt-6 p-2 rounded text-white">🚪 Logout</a>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6">
    <h2 class="text-2xl font-bold text-green-800 mb-4">Manage Subscribers</h2>

    <!-- Search Form -->
    <form class="mb-4 flex gap-2" method="get">
      <input type="text" name="search" placeholder="Search name or email" value="<?= htmlspecialchars($search) ?>"
        class="w-full border rounded px-4 py-2" />
      <button class="bg-green-700 text-white px-4 rounded hover:bg-green-800"><i class="fas fa-search"></i> Search</button>
    </form>

    <div class="bg-white p-4 rounded shadow-md overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-green-700 text-white">
            <th class="p-2">#</th>
            <th class="p-2">Name</th>
            <th class="p-2">Email</th>
            <th class="p-2">Status</th>
            <th class="p-2">Subscribed On</th>
            <th class="p-2">Actions</th>
          </tr>
        </thead>
        <tbody class="text-gray-800">
          <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="p-2"><?= $i++ ?></td>
              <td class="p-2"><?= htmlspecialchars($row['name'] ?? '-') ?></td>
              <td class="p-2"><?= htmlspecialchars($row['email'] ?? '-') ?></td>
              <td class="p-2">
                <?php if (isset($row['approved']) && $row['approved']): ?>
                  <span class="text-green-600 font-medium">Approved</span>
                <?php else: ?>
                  <span class="text-yellow-600 font-medium">Pending</span>
                <?php endif; ?>
              </td>
              <td class="p-2">
                <?= isset($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : 'N/A' ?>
              </td>
              <td class="p-2 space-x-2">
                <?php if (empty($row['approved'])): ?>
                  <a href="subscriber_approve.php?id=<?= $row['id'] ?>" class="text-green-600 hover:text-green-800"><i class="fas fa-check-circle"></i> Approve</a>
                <?php endif; ?>
                <a href="subscriber_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete subscriber?')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i> Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<footer class="text-center text-gray-600 text-sm py-4 bg-gray-100">
  &copy; <?= date('Y') ?> PSA Admin Panel v1.0 — By <b>Leonard Mhone</b>
</footer>

</body>
</html>
