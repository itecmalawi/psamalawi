<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: auth/login.php");
  exit;
}

include 'includes/db.php';

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);

  // Optional: Delete associated image if applicable
  $stmt = $conn->prepare("SELECT image FROM news WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows && $row = $result->fetch_assoc()) {
    $imagePath = 'uploads/news/' . $row['image'];
    if (file_exists($imagePath)) {
      unlink($imagePath); // Delete image from server
    }
  }

  // Delete the news post
  $delete = $conn->prepare("DELETE FROM news WHERE id = ?");
  $delete->bind_param("i", $id);
  $delete->execute();
}

header("Location: manage_news.php");
exit;
?>
