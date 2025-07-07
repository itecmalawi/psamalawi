<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

function countTable($conn, $table) {
  return $conn->query("SELECT id FROM $table")->num_rows;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PSA Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<body class="flex min-h-screen bg-gray-100 font-sans">

  <!-- Sidebar -->
  <aside class="w-64 bg-psaBlack text-white flex flex-col px-4 py-6">
    <div class="text-center mb-10">
      <img src="images/logo.png" alt="PSA Logo" class="mx-auto mb-3 w-16">
      <h2 class="text-xl font-semibold">PSA Admin</h2>
    </div>
    <nav class="flex flex-col gap-4 text-sm">
      <a href="dashboard.php" class="hover:bg-psaGreen py-2 px-3 rounded transition">Dashboard</a>
      <a href="manage_news.php" class="hover:bg-psaGreen py-2 px-3 rounded transition">Manage News</a>
      <a href="add_news.php" class="hover:bg-psaGreen py-2 px-3 rounded transition">Add News</a>
      <a href="manage_team.php" class="hover:bg-psaGreen py-2 px-3 rounded transition">Manage Team</a>
      <a href="manage_journals.php" class="hover:bg-psaGreen py-2 px-3 rounded transition">Manage Journals</a>
      <a href="manage_subscribers.php" class="hover:bg-psaGreen py-2 px-3 rounded transition">Subscribers</a>
      <a href="manage_comments.php" class="hover:bg-psaGreen py-2 px-3 rounded transition">Comments</a>
      <a href="manage_users.php" class="hover:bg-psaGreen py-2 px-3 rounded transition">Manage Users</a>
      <a href="auth/logout.php" class="mt-4 bg-psaRed py-2 px-3 text-center rounded hover:bg-red-700 transition">Logout</a>
    </nav>
  </aside>

  <!-- Main -->
  <main class="flex-1 p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Welcome, <?= htmlspecialchars($_SESSION['admin']) ?> 👋</h1>
      <button id="darkToggle" class="text-sm text-white bg-gray-700 px-3 py-1 rounded hover:bg-gray-900 transition">Toggle Dark Mode</button>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
      <div class="bg-white shadow rounded p-6 hover:shadow-xl transition transform hover:-translate-y-1">
        <h3 class="text-gray-600">News</h3>
        <p class="text-3xl font-bold text-psaRed"><?= countTable($conn, 'news') ?></p>
      </div>
      <div class="bg-white shadow rounded p-6 hover:shadow-xl transition transform hover:-translate-y-1">
        <h3 class="text-gray-600">Team Members</h3>
        <p class="text-3xl font-bold text-psaGreen"><?= countTable($conn, 'team_members') ?></p>
      </div>
      <div class="bg-white shadow rounded p-6 hover:shadow-xl transition transform hover:-translate-y-1">
        <h3 class="text-gray-600">Journals</h3>
        <p class="text-3xl font-bold text-yellow-500"><?= countTable($conn, 'journals') ?></p>
      </div>
      <div class="bg-white shadow rounded p-6 hover:shadow-xl transition transform hover:-translate-y-1">
        <h3 class="text-gray-600">Subscribers</h3>
        <p class="text-3xl font-bold text-blue-600"><?= countTable($conn, 'subscribers') ?></p>
      </div>
      <div class="bg-white shadow rounded p-6 hover:shadow-xl transition transform hover:-translate-y-1">
        <h3 class="text-gray-600">Comments</h3>
        <p class="text-3xl font-bold text-indigo-600"><?= countTable($conn, 'comments') ?></p>
      </div>
    </div>

    <!-- Chart Placeholder -->
    <div class="bg-white p-6 rounded shadow">
      <h3 class="text-lg font-semibold mb-3">Submission Overview</h3>
      <canvas id="statsChart" height="100"></canvas>
    </div>

    <footer class="mt-10 text-center text-sm text-gray-500">
      &copy; <?= date('Y') ?> PSA Admin Panel v2.0 — By <b>Leonard Mhone</b>
    </footer>
  </main>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('statsChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['News', 'Team', 'Journals', 'Subscribers', 'Comments'],
        datasets: [{
          label: 'Entries',
          data: [<?= countTable($conn, 'news') ?>, <?= countTable($conn, 'team_members') ?>, <?= countTable($conn, 'journals') ?>, <?= countTable($conn, 'subscribers') ?>, <?= countTable($conn, 'comments') ?>],
          backgroundColor: ['#cc0000', '#117733', '#facc15', '#3b82f6', '#6366f1']
        }]
      },
      options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // Dark Mode Toggle
    document.getElementById('darkToggle').onclick = () => {
      document.body.classList.toggle('bg-gray-900');
      document.body.classList.toggle('text-white');
    };
  </script>
</body>
</html>
