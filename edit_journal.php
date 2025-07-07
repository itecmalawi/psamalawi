<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$success = $error = '';
$id = (int)$_GET['id'];
$journal = $conn->query("SELECT * FROM journals WHERE id = $id")->fetch_assoc();

if (!$journal) die("Journal not found.");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = $_POST['title'];
  $summary = $_POST['summary'];
  $author = $_POST['author'];
  $date = $_POST['created_at'];

  $fileName = $journal['pdf'];
  if (!empty($_FILES['pdf']['name'])) {
    $fileName = time() . '_' . basename($_FILES['pdf']['name']);
    move_uploaded_file($_FILES['pdf']['tmp_name'], "uploads/journals/" . $fileName);
  }

  $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

  $stmt = $conn->prepare("UPDATE journals SET title=?, summary=?, author=?, pdf=?, slug=?, created_at=? WHERE id=?");
  $stmt->bind_param("ssssssi", $title, $summary, $author, $fileName, $slug, $date, $id);

  if ($stmt->execute()) {
    $success = "Journal updated.";
    $journal = $conn->query("SELECT * FROM journals WHERE id = $id")->fetch_assoc();
  } else {
    $error = "Error updating journal.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Edit Journal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light p-4">
<div class="container">
  <h2>Edit Journal</h2>

  <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input name="title" value="<?= htmlspecialchars($journal['title']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Summary</label>
      <textarea name="summary" class="form-control"><?= htmlspecialchars($journal['summary']) ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Author</label>
      <input name="author" value="<?= htmlspecialchars($journal['author']) ?>" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">PDF (optional)</label>
      <input type="file" name="pdf" class="form-control">
      <?php if ($journal['pdf']): ?>
        <small>Current: <a href="uploads/journals/<?= $journal['pdf'] ?>" target="_blank">Download</a></small>
      <?php endif; ?>
    </div>
    <div class="mb-3">
      <label class="form-label">Date</label>
      <input type="date" name="created_at" value="<?= $journal['created_at'] ?>" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success">Update</button>
    <a href="manage_journals.php" class="btn btn-secondary">Back</a>
  </form>
</div>
</body>
</html>
