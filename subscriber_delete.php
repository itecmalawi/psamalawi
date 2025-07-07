<?php

session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $id = intval($_GET['id']);

  // Fetch subscriber email
  $stmt = $conn->prepare("SELECT name, email FROM subscribers WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $subscriber = $result->fetch_assoc();
  $stmt->close();

  if ($subscriber) {
    // Delete subscriber
    $stmt = $conn->prepare("DELETE FROM subscribers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Send email
    $to = $subscriber['email'];
    $subject = "Your PSA subscription has been removed";
    $message = "Dear " . $subscriber['name'] . ",\n\nWe regret to inform you that your PSA newsletter subscription has been removed from our system.\n\nIf you believe this was in error, please contact us.\n\nRegards,\nMalawi PSA Admin";
    $headers = "From: psamalawi@gmail.com";

    mail($to, $subject, $message, $headers);
  }
}

header("Location: manage_subscribers.php");
exit;
