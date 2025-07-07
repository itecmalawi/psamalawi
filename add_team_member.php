<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$success = $error = '';

// File upload + DB insert
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $position = trim($_POST['position']);
  $category = trim($_POST['category']);
  $facebook = trim($_POST['facebook']);
  $twitter = trim($_POST['twitter']);
  $linkedin = trim($_POST['linkedin']);
  $google = trim($_POST['google']);

  // Upload image
  $imgName = '';
  if (!empty($_FILES['image']['name'])) {
    $imgName = time() . '_' . basename($_FILES['image']['name']);
    $target = 'uploads/team/' . $imgName;
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
  }

  $stmt = $conn->prepare("INSERT INTO team_members (name, position, category, image, facebook, twitter, linkedin, google) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssssss", $name, $position, $category, $imgName, $facebook, $twitter, $linkedin, $google);

  if ($stmt->execute()) {
    $success = "Team member added successfully.";
  } else {
    $error = "Failed to add member.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Team Member</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex min-h-screen font-sans">

<!-- Sidebar -->
<aside class="w-64 bg-black text-white p-6 space-y-6 flex-shrink-0">
  <h2 class="text-2xl font-bold text-green-500">PSA Admin</h2>
  <nav class="space-y-2 text-sm">
    <a href="dashboard.php" class="block hover:text-green-400">Dashboard</a>
    <a href="manage_team.php" class="block text-green-300">Manage Team</a>
    <a href="auth/logout.php" class="block hover:text-red-400">Logout</a>
  </nav>
</aside>

<!-- Main -->
<main class="flex-1 p-8">
  <h1 class="text-2xl font-bold text-gray-700 mb-6">Add Team Member</h1>

  <?php if ($success): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= $error ?></div>
  <?php endif; ?>

  <form action="" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 max-w-lg">
    <label class="block mb-2 text-sm font-bold text-gray-700">Name</label>
    <input name="name" required class="shadow border rounded w-full py-2 px-3 mb-4" />

    <label class="block mb-2 text-sm font-bold text-gray-700">Position</label>
    <input name="position" required class="shadow border rounded w-full py-2 px-3 mb-4" />

    <label class="block mb-2 text-sm font-bold text-gray-700">Membership Category</label>
    <select name="category" required class="shadow border rounded w-full py-2 px-3 mb-4">
      <option value="">-- Select --</option>
      <option>Patron</option>
      <option>Members of the Executive</option>
      <option>Editorial Team</option>
      <option>Secretariate</option>
    </select>

    <label class="block mb-2 text-sm font-bold text-gray-700">Image</label>
    <input type="file" name="image" accept="image/*" class="w-full mb-4"/>

    <label class="block mb-2 text-sm font-bold text-gray-700">Facebook URL</label>
    <input name="facebook" type="url" class="shadow border rounded w-full py-2 px-3 mb-4" placeholder="https://facebook.com/..." />

    <label class="block mb-2 text-sm font-bold text-gray-700">Twitter URL</label>
    <input name="twitter" type="url" class="shadow border rounded w-full py-2 px-3 mb-4" placeholder="https://twitter.com/..." />

    <label class="block mb-2 text-sm font-bold text-gray-700">LinkedIn URL</label>
    <input name="linkedin" type="url" class="shadow border rounded w-full py-2 px-3 mb-4" placeholder="https://linkedin.com/..." />

    <label class="block mb-2 text-sm font-bold text-gray-700">Google URL</label>
    <input name="google" type="url" class="shadow border rounded w-full py-2 px-3 mb-6" placeholder="https://plus.google.com/..." />

    <div class="flex justify-between">
      <a href="manage_team.php" class="text-gray-600 hover:text-black">← Back</a>
      <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">Add Member</button>
    </div>
  </form>

  <footer class="text-center text-gray-500 text-sm mt-10">
    &copy; <?= date('Y') ?> PSA Admin Panel — By <strong>Leonard Mhone</strong>
  </footer>
</main>
</body>
</html>
