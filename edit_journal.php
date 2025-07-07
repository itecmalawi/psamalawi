<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$success = '';
$error = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("Invalid journal ID.");
}

$id = (int) $_GET['id'];
$journal = $conn->query("SELECT * FROM journals WHERE id = $id")->fetch_assoc();
if (!$journal) {
  die("Journal not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $summary = $_POST['summary'];
  $author = $_POST['author'];
  $tags = $_POST['tags'];
  $date = $_POST['date'];

  $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));

  $pdfName = $journal['file'];
  if (!empty($_FILES['pdf']['name'])) {
    $pdfName = time() . '_' . basename($_FILES['pdf']['name']);
    move_uploaded_file($_FILES['pdf']['tmp_name'], "uploads/journals/" . $pdfName);
  }

  $stmt = $conn->prepare("UPDATE journals SET title=?, summary=?, author=?, tags=?, slug=?, file=?, created_at=? WHERE id=?");
  $stmt->bind_param("sssssssi", $title, $summary, $author, $tags, $slug, $pdfName, $date, $id);

  if ($stmt->execute()) {
    $success = "Journal updated successfully!";
    $journal = $conn->query("SELECT * FROM journals WHERE id = $id")->fetch_assoc(); // Refresh data
  } else {
    $error = "Failed to update journal.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Journal - PSA Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex flex-col">
  <div class="flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-gradient-to-b from-green-800 to-black text-white p-6 shadow-md">
      <h2 class="text-xl font-bold mb-6">PSA Admin</h2>
      <ul class="space-y-3">
        <li><a href="dashboard.php" class="block hover:text-green-300"><i class="fas fa-home mr-2"></i>Dashboard</a></li>
        <li><a href="manage_journals.php" class="block text-green-300"><i class="fas fa-book mr-2"></i>Journals</a></li>
        <li><a href="auth/logout.php" class="block hover:text-red-400"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6">
      <h2 class="text-2xl font-bold text-green-800 mb-4">Edit Journal</h2>

      <?php if ($success): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $success ?></div>
      <?php elseif ($error): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= $error ?></div>
      <?php endif; ?>

      <form action="" method="post" enctype="multipart/form-data" class="bg-white p-6 rounded shadow max-w-xl">
        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">Title</label>
          <input name="title" value="<?= htmlspecialchars($journal['title']) ?>" required class="w-full border border-gray-300 rounded px-3 py-2" />
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">Summary</label>
          <textarea name="summary" rows="4" required class="w-full border border-gray-300 rounded px-3 py-2"><?= htmlspecialchars($journal['summary']) ?></textarea>
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">Author</label>
          <input name="author" value="<?= htmlspecialchars($journal['author']) ?>" required class="w-full border border-gray-300 rounded px-3 py-2" />
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">Tags (comma-separated)</label>
          <input name="tags" value="<?= htmlspecialchars($journal['tags']) ?>" class="w-full border border-gray-300 rounded px-3 py-2" />
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">PDF File (leave blank to keep existing)</label>
          <input type="file" name="pdf" accept="application/pdf" class="w-full border border-gray-300 rounded px-3 py-2" />
          <?php if (!empty($journal['file'])): ?>
            <p class="text-sm text-gray-600 mt-1">Current: <a href="uploads/journals/<?= $journal['file'] ?>" target="_blank" class="text-blue-600 underline"><?= $journal['file'] ?></a></p>
          <?php endif; ?>
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">Date</label>
          <input type="date" name="date" value="<?= htmlspecialchars($journal['created_at']) ?>" required class="w-full border border-gray-300 rounded px-3 py-2" />
        </div>

        <div class="flex justify-between">
          <a href="manage_journals.php" class="text-gray-600 hover:text-black">← Back</a>
          <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">
            Update Journal
          </button>
        </div>
      </form>
    </main>
  </div>

  <footer class="text-center text-sm text-gray-500 mt-auto py-4">
    &copy; <?= date('Y') ?> PSA Admin Panel v1.0 — By <strong>Leonard Mhone</strong>
  </footer>
</div>

<!-- FontAwesome (icons) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>
