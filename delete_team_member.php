<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid ID");

$member = $conn->query("SELECT image FROM team_members WHERE id = $id")->fetch_assoc();
if ($member && $member['image'] && file_exists("uploads/team/" . $member['image'])) {
  unlink("uploads/team/" . $member['image']); // delete image
}

$conn->query("DELETE FROM team_members WHERE id = $id");

header("Location: manage_team.php");
exit;
