<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$id = (int)$_GET['id'];

$journal = $conn->query("SELECT pdf FROM journals WHERE id = $id")->fetch_assoc();
if ($journal && $journal['pdf']) {
  @unlink("uploads/journals/" . $journal['pdf']);
}

$conn->query("DELETE FROM journal_topics WHERE journal_id = $id");
$conn->query("DELETE FROM journals WHERE id = $id");

header("Location: manage_journals.php?deleted=1");
exit;
?>
