<?php
include 'includes/db.php';

if (!isset($_GET['id'])) {
  die("No topic specified.");
}

$topicId = (int)$_GET['id'];

// Fetch file
$stmt = $conn->prepare("SELECT file FROM journal_topics WHERE id = ?");
$stmt->bind_param("i", $topicId);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if (!$res || !file_exists("downloads/" . $res['file'])) {
  die("File not found.");
}

// Update download count
$conn->query("UPDATE journal_topics SET downloads = downloads + 1 WHERE id = $topicId");

// Redirect to the file
header("Location: downloads/" . $res['file']);
exit;
?>
