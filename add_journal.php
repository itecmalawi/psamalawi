<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

// Helper: Generate slug from title
function slugify($text) {
  return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $author = $_POST['author'];
  $summary = $_POST['summary'];
  $date = $_POST['date'];
  $slug = slugify($title);
  $pdfName = '';

  if (!empty($_FILES['pdf']['name'])) {
    $pdfName = time() . '_' . basename($_FILES['pdf']['name']);
    $target = 'uploads/journals/' . $pdfName;
    move_uploaded_file($_FILES['pdf']['tmp_name'], $target);
  }

  $stmt = $conn->prepare("INSERT INTO journals (title, author, summary, file, slug, created_at) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssss", $title, $author, $summary, $pdfName, $slug, $date);

  if ($stmt->execute()) {
    $success = "Journal added successfully!";
  } else {
    $error = "Failed to add journal.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Journal - PSA Admin</title>
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
      <h2 class="text-2xl font-bold text-green-800 mb-4">Add Journal</h2>

      <?php if ($success): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $success ?></div>
      <?php elseif ($error): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= $error ?></div>
      <?php endif; ?>

      <form action="" method="post" enctype="multipart/form-data" class="bg-white p-6 rounded shadow max-w-xl">
        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">Title</label>
          <input name="title" required class="w-full border border-gray-300 rounded px-3 py-2" />
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">Author</label>
          <input name="author" required class="w-full border border-gray-300 rounded px-3 py-2" />
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">Summary</label>
          <textarea name="summary" rows="4" required class="w-full border border-gray-300 rounded px-3 py-2"></textarea>
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">PDF File (optional)</label>
          <input type="file" name="pdf" accept="application/pdf" class="w-full border border-gray-300 rounded px-3 py-2" />
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-semibold text-gray-700">Date</label>
          <input type="date" name="date" required class="w-full border border-gray-300 rounded px-3 py-2" />
        </div>

        <div class="flex justify-between">
          <a href="manage_journals.php" class="text-gray-600 hover:text-black">← Back</a>
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
