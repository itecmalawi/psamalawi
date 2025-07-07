<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $position = trim($_POST['position']);
  $category = $_POST['category'];
  $facebook = trim($_POST['facebook']);
  $twitter = trim($_POST['twitter']);
  $linkedin = trim($_POST['linkedin']);
  $google = trim($_POST['google']);

  $image = '';
  if (!empty($_FILES['image']['name'])) {
    $image = time() . '_' . basename($_FILES['image']['name']);
    $target = 'uploads/team/' . $image;
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
  }

  $stmt = $conn->prepare("INSERT INTO team_members (name, position, category, image, facebook, twitter, linkedin, google) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssssss", $name, $position, $category, $image, $facebook, $twitter, $linkedin, $google);

  if ($stmt->execute()) {
    $success = "Team member added!";
  } else {
    $error = "Failed to add member.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Team Member</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">

<!-- Sidebar -->
<aside class="w-64 bg-black text-white p-6">
  <h2 class="text-2xl font-bold text-green-400 mb-6">PSA Admin</h2>
  <nav class="space-y-3 text-sm">
    <a href="dashboard.php" class="block hover:text-green-300">Dashboard</a>
    <a href="manage_team.php" class="block text-green-300 font-semibold">Manage Team</a>
    <a href="add_team_member.php" class="block">Add Member</a>
    <a href="auth/logout.php" class="block text-red-400">Logout</a>
  </nav>
</aside>

<!-- Main -->
<main class="flex-1 p-8">
  <h1 class="text-3xl font-bold mb-6 text-green-800">Add Team Member</h1>

  <?php if ($success): ?>
    <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-4"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4"><?= $error ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="bg-white p-6 rounded shadow max-w-xl">
    <div class="mb-4">
      <label class="block font-semibold">Name</label>
      <input type="text" name="name" required class="w-full border rounded px-3 py-2" />
    </div>
    <div class="mb-4">
      <label class="block font-semibold">Position</label>
      <input type="text" name="position" required class="w-full border rounded px-3 py-2" />
    </div>
    <div class="mb-4">
      <label class="block font-semibold">Category</label>
      <select name="category" required class="w-full border rounded px-3 py-2">
        <option value="">-- Select --</option>
        <option>Patron</option>
        <option>Members of the Executive</option>
        <option>Editorial Team</option>
        <option>Secretariat</option>
      </select>
    </div>
    <div class="mb-4">
      <label class="block font-semibold">Image</label>
      <input type="file" name="image" accept="image/*" required class="w-full border rounded px-3 py-2" />
    </div>
    <div class="mb-4">
      <label class="block font-semibold">Facebook URL</label>
      <input type="url" name="facebook" class="w-full border rounded px-3 py-2" />
    </div>
    <div class="mb-4">
      <label class="block font-semibold">Twitter URL</label>
      <input type="url" name="twitter" class="w-full border rounded px-3 py-2" />
    </div>
    <div class="mb-4">
      <label class="block font-semibold">LinkedIn URL</label>
      <input type="url" name="linkedin" class="w-full border rounded px-3 py-2" />
    </div>
    <div class="mb-4">
      <label class="block font-semibold">Google URL</label>
      <input type="url" name="google" class="w-full border rounded px-3 py-2" />
    </div>
    <button type="submit" class="bg-green-700 text-white px-5 py-2 rounded hover:bg-green-800">Submit</button>
  </form>
</main>
</body>
</html>
