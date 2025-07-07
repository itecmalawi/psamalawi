<?php
session_start();
if (!isset($_SESSION['admin'])) header("Location: auth/login.php");
include 'includes/db.php';

$members = $conn->query("SELECT * FROM team_members ORDER BY FIELD(category, 'Patron','Executive','Editorial Team','Secretariat'), id ASC");
?>
<!-- Minimal Manage Page -->
<html><body>
<h2>Manage Team Members</h2>
<table border="1" cellpadding="5">
<tr><th>Image</th><th>Name</th><th>Position</th><th>Category</th><th>Actions</th></tr>
<?php while($m = $members->fetch_assoc()): ?>
<tr>
  <td><img src="uploads/team/<?= htmlspecialchars($m['image']) ?>" width="50"></td>
  <td><?= htmlspecialchars($m['name']) ?></td>
  <td><?= htmlspecialchars($m['position']) ?></td>
  <td><?= htmlspecialchars($m['category']) ?></td>
  <td>
    <a href="edit_team_member.php?id=<?= $m['id'] ?>">Edit</a> |
    <a href="delete_team_member.php?id=<?= $m['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
  </td>
</tr>
<?php endwhile; ?>
</table>
</body></html>