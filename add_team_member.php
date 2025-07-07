<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $position = $_POST['position'];
  $category = $_POST['category'];
  $facebook = $_POST['facebook'] ?? '';
  $twitter = $_POST['twitter'] ?? '';
  $linkedin = $_POST['linkedin'] ?? '';
  $google = $_POST['google'] ?? '';

  $imageName = '';
  if (!empty($_FILES['image']['name'])) {
    $imageName = time() . '_' . basename($_FILES['image']['name']);
    $target = 'uploads/team/' . $imageName;
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
  }

  $stmt = $conn->prepare("INSERT INTO team_members (name, position, category, image, facebook, twitter, linkedin, google)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssssss", $name, $position, $category, $imageName, $facebook, $twitter, $linkedin, $google);

  if ($stmt->execute()) $success = "Member added successfully!";
  else $error = "Failed to add member.";
}
?>
<!-- Minimal Form Output -->
<html><body>
<h2>Add Team Member</h2>
<?php if($success) echo "<p style='color:green'>$success</p>"; ?>
<?php if($error) echo "<p style='color:red'>$error</p>"; ?>
<form method="post" enctype="multipart/form-data">
  Name: <input type="text" name="name" required><br>
  Position: <input type="text" name="position" required><br>
  Category:
  <select name="category" required>
    <option value="Patron">Patron</option>
    <option value="Executive">Executive</option>
    <option value="Editorial Team">Editorial Team</option>
    <option value="Secretariat">Secretariat</option>
  </select><br>
  Image: <input type="file" name="image" accept="image/*"><br>
  Facebook: <input type="url" name="facebook"><br>
  Twitter: <input type="url" name="twitter"><br>
  LinkedIn: <input type="url" name="linkedin"><br>
  Google: <input type="url" name="google"><br>
  <button type="submit">Add Member</button>
</form>
</body></html>