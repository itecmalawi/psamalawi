<?php include 'includes/header.php'; ?>
<main>
  <h2>Login</h2>
  <form action="admin/dashboard.php" method="post">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
  </form>
</main>
<?php include 'includes/footer.php'; ?>
