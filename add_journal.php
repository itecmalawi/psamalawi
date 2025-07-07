<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$success = '';
$error = '';

function generateSlug($str) {
  return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $str)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title']);
  $author = trim($_POST['author']);
  $summary = trim($_POST['summary']);
  $tags = trim($_POST['tags']);
  $date = $_POST['date'];
  $slug = generateSlug($title);

  $pdfName = '';
  if (!empty($_FILES['pdf']['name'])) {
    $pdfName = time() . '_' . basename($_FILES['pdf']['name']);
    move_uploaded_file($_FILES['pdf']['tmp_name'], 'uploads/journals/' . $pdfName);
  }

  $stmt = $conn->prepare("INSERT INTO journals (title, author, summary, tags, file, created_at, slug) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sssssss", $title, $author, $summary, $tags, $pdfName, $date, $slug);

  if ($stmt->execute()) {
    $journal_id = $conn->insert_id;

    // Handle topics
    if (!empty($_POST['topic']) && is_array($_POST['topic'])) {
      foreach ($_POST['topic'] as $i => $topic) {
        $topic_title = trim($topic);
        $topic_pdf = '';

        if (!empty($_FILES['topic_pdf']['name'][$i])) {
          $tName = time() . '_' . basename($_FILES['topic_pdf']['name'][$i]);
          move_uploaded_file($_FILES['topic_pdf']['tmp_name'][$i], 'uploads/journals/' . $tName);
          $topic_pdf = $tName;
        }

        $conn->query("INSERT INTO journal_topics (journal_id, topic, file) VALUES ($journal_id, '{$conn->real_escape_string($topic_title)}', '{$conn->real_escape_string($topic_pdf)}')");
      }
    }

    $success = "Journal and topics added successfully!";
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

    <!-- Main -->
    <main class="flex-1 p-6">
      <h2 class="text-2xl font-bold text-green-800 mb-4">Add Journal</h2>

      <?php if ($success): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $success ?></div>
      <?php elseif ($error): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= $error ?></div>
      <?php endif; ?>

      <form action="" method="post" enctype="multipart/form-data" class="bg-white p-6 rounded shadow max-w-3xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block mb-1 font-semibold">Title</label>
            <input name="title" required class="w-full border px-3 py-2 rounded" />
          </div>
          <div>
            <label class="block mb-1 font-semibold">Author</label>
            <input name="author" required class="w-full border px-3 py-2 rounded" />
          </div>
        </div>

        <div class="mt-4">
          <label class="block mb-1 font-semibold">Summary</label>
          <textarea name="summary" rows="4" class="w-full border px-3 py-2 rounded" required></textarea>
        </div>

        <div class="mt-4">
          <label class="block mb-1 font-semibold">Tags (comma separated)</label>
          <input name="tags" class="w-full border px-3 py-2 rounded" />
        </div>

        <div class="mt-4">
          <label class="block mb-1 font-semibold">PDF File (Optional full journal)</label>
          <input type="file" name="pdf" accept="application/pdf" class="w-full border px-3 py-2 rounded" />
        </div>

        <div class="mt-4">
          <label class="block mb-1 font-semibold">Date</label>
          <input type="date" name="date" required class="w-full border px-3 py-2 rounded" />
        </div>

        <hr class="my-6">

        <h3 class="text-lg font-semibold mb-2">Journal Topics</h3>
        <div id="topic-container">
          <div class="topic-entry flex gap-4 mb-4">
            <input type="text" name="topic[]" placeholder="Topic title" class="flex-1 border px-3 py-2 rounded" />
            <input type="file" name="topic_pdf[]" accept="application/pdf" class="flex-1 border px-3 py-2 rounded" />
          </div>
        </div>

        <button type="button" id="add-topic" class="bg-blue-600 text-white px-3 py-1 rounded mb-4">+ Add Topic</button>

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

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<script>
  document.getElementById('add-topic').addEventListener('click', () => {
    const container = document.getElementById('topic-container');
    const entry = document.createElement('div');
    entry.className = 'topic-entry flex gap-4 mb-4';
    entry.innerHTML = `
      <input type="text" name="topic[]" placeholder="Topic title" class="flex-1 border px-3 py-2 rounded" />
      <input type="file" name="topic_pdf[]" accept="application/pdf" class="flex-1 border px-3 py-2 rounded" />
    `;
    container.appendChild(entry);
  });
</script>
</body>
</html>
