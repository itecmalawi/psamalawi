<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$id = (int) $_GET['id'];
$member = $conn->query("SELECT * FROM team_members WHERE id = $id")->fetch_assoc();
if (!$member) die("Team member not found.");

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $position = $_POST['position'];
  $category = $_POST['category'];

  $image = $member['image']; // keep old image
  if (!empty($_FILES['image']['name'])) {
    $image = time() . '_' . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/team/' . $image);
  }

  $stmt = $conn->prepare("UPDATE team_members SET name=?, position=?, category=?, image=? WHERE id=?");
  $stmt->bind_param("ssssi", $name, $position, $category, $image, $id);

  if ($stmt->execute()) {
    $success = "Member updated successfully!";
    $member = $conn->query("SELECT * FROM team_members WHERE id = $id")->fetch_assoc(); // Refresh
  } else {
    $error = "Update failed.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Team Member - PSA Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
<div class="max-w-xl mx-auto p-6 mt-10 bg-white shadow rounded">
  <h2 class="text-xl font-bold text-green-800 mb-4">Edit Team Member</h2>

  <?php if ($success): ?>
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= $error ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <div class="mb-4">
      <label class="block mb-1 font-semibold">Full Name</label>
      <input type="text" name="name" value="<?= htmlspecialchars($member['name']) ?>" class="w-full border border-gray-300 rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
      <label class="block mb-1 font-semibold">Position</label>
      <input type="text" name="position" value="<?= htmlspecialchars($member['position']) ?>" class="w-full border border-gray-300 rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
      <label class="block mb-1 font-semibold">Category</label>
      <select name="category" class="w-full border border-gray-300 rounded px-3 py-2" required>
        <option value="">-- Select Category --</option>
        <option value="Executive" <?= $member['category'] == 'Executive' ? 'selected' : '' ?>>Executive</option>
        <option value="Secretariate" <?= $member['category'] == 'Secretariate' ? 'selected' : '' ?>>Secretariate</option>
        <option value="Editorial Team" <?= $member['category'] == 'Editorial Team' ? 'selected' : '' ?>>Editorial Team</option>
      </select>
    </div>
    <div class="mb-4">
      <label class="block mb-1 font-semibold">Profile Image</label>
      <input type="file" name="image" accept="image/*" class="w-full">
      <?php if (!empty($member['image'])): ?>
        <div class="mt-2">
          <img src="uploads/team/<?= $member['image'] ?>" alt="Current" class="w-24 h-24 rounded-full border">
        </div>
      <?php endif; ?>
    </div>
    <div class="flex justify-between">
      <a href="manage_team.php" class="text-gray-600 hover:text-black">← Back</a>
      <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">Update</button>
    </div>
  </form>
</div>
</body>
</html>
