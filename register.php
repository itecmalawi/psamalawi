<?php include 'includes/header.php'; ?>
<main>
  <h2>Register</h2>
  <form action="admin/register_action.php" method="post">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Register</button>
  </form>
</main>
<?php include 'includes/footer.php'; ?>
