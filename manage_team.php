<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

// For demo, assume all are active unless marked otherwise
$categories = ['Patron', 'Members of the Executive', 'Editorial Team', 'Secretariate'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Team - PSA Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex min-h-screen">

<!-- Sidebar -->
<aside class="w-64 bg-black text-white p-6 space-y-6 flex-shrink-0">
  <h2 class="text-2xl font-bold text-green-500">PSA Admin</h2>
  <nav class="space-y-2 text-sm">
    <a href="dashboard.php" class="block hover:text-green-400"><i class="fa fa-home mr-2"></i>Dashboard</a>
    <a href="manage_team.php" class="block text-green-300"><i class="fa fa-users mr-2"></i>Manage Team</a>
    <a href="auth/logout.php" class="block hover:text-red-400"><i class="fa fa-sign-out-alt mr-2"></i>Logout</a>
  </nav>
</aside>

<!-- Main Content -->
<main class="flex-1 p-6">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Team Members</h1>
    <a href="add_team_member.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
      <i class="fa fa-plus mr-1"></i>Add Member
    </a>
  </div>

  <?php foreach ($categories as $cat): ?>
    <?php
      $result = $conn->query("SELECT * FROM team_members WHERE category='$cat' ORDER BY ordering ASC, id DESC");
      if ($result->num_rows === 0) continue;
    ?>
    <div class="mb-8">
      <h2 class="text-xl font-semibold text-gray-700 border-l-4 border-green-600 pl-3 mb-4"><?= $cat ?></h2>
      <div id="sortable-<?= strtolower(str_replace(' ', '-', $cat)) ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while($t = $result->fetch_assoc()): ?>
          <div class="bg-white rounded-lg shadow hover:shadow-md transition-all duration-300 text-center p-5 cursor-pointer" data-id="<?= $t['id'] ?>" onclick="openModal(<?= htmlspecialchars(json_encode($t)) ?>)">
            <img src="uploads/team/<?= htmlspecialchars($t['image']) ?>" class="w-24 h-24 rounded-full mx-auto object-cover border border-gray-300 shadow-sm mb-2" alt="<?= $t['name'] ?>">
            <h3 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($t['name']) ?></h3>
            <p class="text-sm text-gray-600"><?= htmlspecialchars($t['position']) ?></p>
            <div class="text-xs italic text-gray-400"><?= htmlspecialchars($t['category']) ?></div>
            <div class="mt-2">
              <span class="inline-block px-2 py-1 text-xs rounded-full <?= $t['status'] === 'inactive' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-700' ?>">
                <?= ucfirst($t['status'] ?? 'active') ?>
              </span>
            </div>
            <div class="mt-3 space-x-2 text-sm">
              <a href="edit_team_member.php?id=<?= $t['id'] ?>" class="text-blue-600 hover:underline"><i class="fa fa-edit"></i> Edit</a>
              <a href="delete_team_member.php?id=<?= $t['id'] ?>" onclick="return confirm('Delete this member?')" class="text-red-600 hover:underline"><i class="fa fa-trash"></i> Delete</a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  <?php endforeach; ?>

  <footer class="text-center text-sm text-gray-500 mt-10 pt-6 border-t">
    &copy; <?= date('Y') ?> PSA Admin Panel v2.0 — By <strong>Leonard Mhone</strong>
  </footer>
</main>

<!-- Modal -->
<div id="memberModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
    <button onclick="closeModal()" class="absolute top-2 right-3 text-xl text-gray-500 hover:text-black">&times;</button>
    <img id="modalImg" src="" class="w-24 h-24 rounded-full mx-auto border my-2" alt="Photo">
    <h3 id="modalName" class="text-xl font-bold text-center text-gray-800"></h3>
    <p id="modalPosition" class="text-center text-gray-600"></p>
    <p id="modalCategory" class="text-center text-sm text-gray-400"></p>
    <div class="mt-3 text-center space-x-2" id="modalSocial"></div>
  </div>
</div>

<!-- SortableJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
  document.querySelectorAll('[id^="sortable-"]').forEach(container => {
    new Sortable(container, {
      animation: 200,
      onEnd: function (evt) {
        // Send updated order to server via fetch or AJAX (not included)
        // console.log('Moved item ID', evt.item.dataset.id);
      }
    });
  });

  function openModal(data) {
    document.getElementById('modalImg').src = 'uploads/team/' + data.image;
    document.getElementById('modalName').innerText = data.name;
    document.getElementById('modalPosition').innerText = data.position;
    document.getElementById('modalCategory').innerText = data.category;

    let socials = '';
    if (data.facebook) socials += `<a href="${data.facebook}" target="_blank"><i class="fab fa-facebook text-blue-600"></i></a>`;
    if (data.twitter) socials += `<a href="${data.twitter}" target="_blank"><i class="fab fa-twitter text-blue-400"></i></a>`;
    if (data.linkedin) socials += `<a href="${data.linkedin}" target="_blank"><i class="fab fa-linkedin text-blue-700"></i></a>`;
    if (data.google) socials += `<a href="${data.google}" target="_blank"><i class="fab fa-google text-red-500"></i></a>`;
    document.getElementById('modalSocial').innerHTML = socials;

    document.getElementById('memberModal').classList.remove('hidden');
  }

  function closeModal() {
    document.getElementById('memberModal').classList.add('hidden');
  }
</script>
</body>
</html>
