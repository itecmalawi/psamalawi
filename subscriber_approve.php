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
    // Approve the subscriber
    $stmt = $conn->prepare("UPDATE subscribers SET approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Send email
    $to = $subscriber['email'];
    $subject = "Your PSA subscription has been approved";
    $message = "Dear " . $subscriber['name'] . ",\n\nThank you for subscribing to the Malawi PSA newsletter.\nYour subscription has been approved. You can now comment on our news articles.\n\nRegards,\nMalawi PSA Admin";
    $headers = "From: psamalawi@gmail.com";

    mail($to, $subject, $message, $headers);
  }
}

header("Location: manage_subscribers.php");
exit;
