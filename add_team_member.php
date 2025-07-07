<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $position = trim($_POST['position']);
  $category = trim($_POST['category']);
  $facebook = trim($_POST['facebook']);
  $twitter = trim($_POST['twitter']);
  $linkedin = trim($_POST['linkedin']);
  $google = trim($_POST['google']);

  // Image Upload
  $imageName = '';
  if (!empty($_FILES['image']['name'])) {
    $imageName = time() . '_' . basename($_FILES['image']['name']);
    $target = 'uploads/team/' . $imageName;
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
  }

  $stmt = $conn->prepare("INSERT INTO team_members (name, position, category, image, facebook, twitter, linkedin, google) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssssss", $name, $position, $category, $imageName, $facebook, $twitter, $linkedin, $google);

  if ($stmt->execute()) {
    $success = "Team member added successfully!";
  } else {
    $error = "Failed to add team member.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Team Member - PSA Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex flex-col">
  <div class="flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-gradient-to-b from-green-800 to-black text-white p-6 shadow-md">
      <h2 class="text-xl font-bold mb-6">PSA Admin</h2>
      <ul class="space-y-3">
        <li><a href="dashboard.php" class="block hover:text-green-300">Dashboard</a></li>
        <li><a href="manage_team.php" class="block text-green-300">Team Members</a></li>
        <li><a href="auth/logout.php" class="block hover:text-red-400">Logout</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6">
      <h2 class="text-2xl font-bold text-green-800 mb-4">Add Team Member</h2>

      <?php if ($success): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $success ?></div>
      <?php elseif ($error): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= $error ?></div>
      <?php endif; ?>

      <form action="" method="post" enctype="multipart/form-data" class="bg-white p-6 rounded shadow max-w-xl">
        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">Name</label>
          <input name="name" required class="w-full border border-gray-300 rounded px-3 py-2" />
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">Position</label>
          <input name="position" required class="w-full border border-gray-300 rounded px-3 py-2" />
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">Membership Category</label>
          <select name="category" required class="w-full border border-gray-300 rounded px-3 py-2">
            <option value="">-- Select Category --</option>
            <option value="Members of the Executive">Members of the Executive</option>
            <option value="Editorial Team">Editorial Team</option>
            <option value="Secretariate">Secretariate</option>
          </select>
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">Image</label>
          <input type="file" name="image" required accept="image/*" class="w-full border border-gray-300 rounded px-3 py-2" />
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">Facebook URL</label>
          <input type="text" name="facebook" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="https://facebook.com/username" />
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">Twitter URL</label>
          <input type="text" name="twitter" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="https://twitter.com/username" />
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">LinkedIn URL</label>
          <input type="text" name="linkedin" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="https://linkedin.com/in/username" />
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">Google URL</label>
          <input type="text" name="google" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="https://plus.google.com/username" />
        </div>

        <div class="flex justify-between">
          <a href="manage_team.php" class="text-gray-600 hover:text-black">← Back</a>
          <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">
            Submit
          </button>
        </div>
      </form>
    </main>
  </div>

  <footer class="text-center text-sm text-gray-500 mt-auto py-4">
    &copy; <?= date('Y') ?> PSA Admin Panel v1.0 — By <strong>Leonard Mhone</strong>
  </footer>
</div>

<!-- FontAwesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>
