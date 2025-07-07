<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Get journals
$journals = $conn->query("SELECT * FROM journals ORDER BY created_at DESC LIMIT $offset, $limit");
$total = $conn->query("SELECT COUNT(*) as total FROM journals")->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Journals - PSA Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen font-sans flex">

<!-- Sidebar -->
<aside class="w-64 bg-black text-white p-6 space-y-6">
  <h2 class="text-2xl font-bold text-green-500">PSA Admin</h2>
  <nav class="space-y-2 mt-4 text-sm">
    <a href="dashboard.php" class="block hover:text-green-400"><i class="fa fa-home mr-2"></i>Dashboard</a>
    <a href="manage_news.php" class="block hover:text-green-400"><i class="fa fa-newspaper mr-2"></i>News</a>
    <a href="manage_team.php" class="block hover:text-green-400"><i class="fa fa-users mr-2"></i>Team</a>
    <a href="manage_journals.php" class="block text-red-400"><i class="fa fa-book mr-2"></i>Journals</a>
    <a href="manage_subscribers.php" class="block hover:text-green-400"><i class="fa fa-envelope mr-2"></i>Subscribers</a>
    <a href="manage_comments.php" class="block hover:text-green-400"><i class="fa fa-comments mr-2"></i>Comments</a>
    <a href="manage_users.php" class="block hover:text-green-400"><i class="fa fa-user mr-2"></i>Admins</a>
    <a href="auth/logout.php" class="block mt-4 text-red-400 hover:underline"><i class="fa fa-sign-out-alt mr-2"></i>Logout</a>
  </nav>
</aside>

<!-- Main Content -->
<main class="flex-1 p-8">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Manage Journals</h1>
    <a href="add_journal.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
      <i class="fa fa-plus mr-1"></i> Add Journal
    </a>
  </div>

  <div class="bg-white p-6 rounded shadow overflow-x-auto">
    <table class="w-full table-auto text-sm">
      <thead class="bg-green-800 text-white">
        <tr>
          <th class="p-2 text-left">#</th>
          <th class="p-2 text-left">Title</th>
          <th class="p-2 text-left">Author</th>
          <th class="p-2 text-left">Date</th>
          <th class="p-2 text-left">Downloads</th>
          <th class="p-2 text-left">File</th>
          <th class="p-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $i = $offset + 1;
        while ($j = $journals->fetch_assoc()):
        ?>
        <tr class="border-b hover:bg-gray-50 transition">
          <td class="p-2"><?= $i++ ?></td>
          <td class="p-2"><?= htmlspecialchars($j['title']) ?></td>
          <td class="p-2"><?= htmlspecialchars($j['author'] ?? 'Unknown') ?></td>
          <td class="p-2"><?= date('d M Y', strtotime($j['created_at'])) ?></td>
          <td class="p-2 text-center"><?= (int)$j['downloads'] ?></td>
          <td class="p-2">
            <?php if (!empty($j['file'])): ?>
              <a href="uploads/journals/<?= urlencode($j['file']) ?>" target="_blank" class="text-blue-600 hover:underline">
                <i class="fa fa-download"></i> PDF
              </a>
            <?php else: ?>
              <span class="text-gray-400 italic">N/A</span>
            <?php endif; ?>
          </td>
          <td class="p-2 space-x-2">
            <a href="edit_journal.php?id=<?= $j['id'] ?>" class="text-blue-600 hover:underline"><i class="fa fa-edit"></i> Edit</a>
            <a href="delete_journal.php?id=<?= $j['id'] ?>" onclick="return confirm('Delete this journal?')" class="text-red-600 hover:underline"><i class="fa fa-trash"></i> Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
  <div class="mt-6 flex justify-center space-x-2">
    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
      <a href="?page=<?= $p ?>" class="px-3 py-1 rounded border <?= $p == $page ? 'bg-green-800 text-white' : 'text-green-800 border-green-800' ?>">
        <?= $p ?>
      </a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>

  <footer class="mt-10 text-center text-sm text-gray-500">
    &copy; <?= date('Y') ?> PSA Admin Panel v2.0 — By <strong>Leonard Mhone</strong>
  </footer>
</main>
</body>
</html>
