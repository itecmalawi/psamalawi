<?php
// subscribe.php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim(mysqli_real_escape_string($conn, $_POST['name'] ?? ''));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email'] ?? ''));

    // Basic validation
    if (empty($name) || empty($email)) {
        echo "❌ Please enter both name and email.";
        exit;
    }

    // Check for duplicate
    $check = $conn->query("SELECT id FROM subscribers WHERE email='$email'");
    if ($check->num_rows > 0) {
        echo "⚠️ This email is already subscribed.";
        exit;
    }

    // Insert new subscriber
    $stmt = $conn->prepare("INSERT INTO subscribers (name, email, status) VALUES (?, ?, 'pending')");
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();

    // ✅ Notify Admin
    $adminEmail = "leonardjjmhone@gmail.com";
    $adminSubject = "📥 New PSA Subscriber: $name";
    $adminMessage = "A new user has subscribed on PSA Website:\n\nName: $name\nEmail: $email\nStatus: pending\n\nApprove this user in the dashboard.";
    $adminHeaders = "From: PSA Website <no-reply@psamalawi.org>";
    mail($adminEmail, $adminSubject, $adminMessage, $adminHeaders);

    // ✅ Send Welcome Email to Subscriber
    $userSubject = "Welcome to PSA Malawi Newsletter!";
    $userMessage = "Hi $name,\n\nThank you for subscribing to the Malawi Political Science Association newsletter. You’ll be notified once your subscription is approved.\n\nBest regards,\nPSA Malawi Team";
    $userHeaders = "From: PSA Malawi <no-reply@psamalawi.org>";
    mail($email, $userSubject, $userMessage, $userHeaders);

    echo "✅ Thank you for subscribing. Please wait for admin approval.";
}
?>
