<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: auth/login.php");
  exit;
}

include 'includes/db.php';

// Validate the ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("Invalid user ID.");
}

$id = (int) $_GET['id'];

// Prevent deletion of self
$currentAdmin = $_SESSION['admin'];
$check = $conn->prepare("SELECT username FROM admins WHERE id = ?");
$check->bind_param("i", $id);
$check->execute();
$checkResult = $check->get_result();
$user = $checkResult->fetch_assoc();

if (!$user) {
  die("User not found.");
}

if ($user['username'] === $currentAdmin) {
  die("You cannot delete yourself.");
}

// Delete user
$stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Redirect back
header("Location: manage_users.php");
exit;
