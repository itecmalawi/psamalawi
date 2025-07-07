<?php
session_start();
include 'includes/db.php';

$members = $conn->query("SELECT * FROM team_members ORDER BY FIELD(category, 'Patron', 'Executive', 'Secretariate'), name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PSA Team</title>
  <link rel="icon" href="images/favicon.png">
  <link rel="stylesheet" href="plugins/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="plugins/themify-icons/themify-icons.css">
  <link rel="stylesheet" href="plugins/animate/animate.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .team-grid {
      display: grid;
      gap: 1rem;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    }
    .team-card {
      break-inside: avoid;
      cursor: pointer;
    }
    .org-row { position: relative; margin-bottom: 2rem; }
    .org-row::after {
      content: '';
      position: absolute;
      left: 50%;
      bottom: -20px;
      width: 2px; height: 20px;
      background: #ccc;
    }
  </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="page-title overlay" style="background-image: url(images/background/page-title-3.jpg);">
  <div class="container text-center">
    <h2 class="text-white font-weight-bold">Our Team</h2>
  </div>
</section>

<section class="section py-5">
  <div class="container">
    <?php 
    $currentCategory = '';
    while ($m = $members->fetch_assoc()):
      if ($m['category'] !== $currentCategory):
        if ($currentCategory) echo '</div>'; // close previous grid
        $currentCategory = $m['category'];
    ?>
      <h3 class="mb-3"><?= htmlspecialchars($currentCategory) ?></h3>
      <div class="team-grid org-row">
    <?php endif; ?>

      <div class="team-card card animate__animated animate__fadeInUp" data-img="uploads/team/<?= htmlspecialchars($m['image']) ?>"
        data-name="<?= htmlspecialchars($m['name']) ?>"
        data-position="<?= htmlspecialchars($m['position']) ?>"
        data-bio="<?= htmlspecialchars($m['bio']) ?>">
        <img class="card-img-top" src="uploads/team/<?= htmlspecialchars($m['image']) ?>" alt="<?= htmlspecialchars($m['name']) ?>">
        <div class="card-body text-center">
          <h5 class="card-title"><?= htmlspecialchars($m['name']) ?></h5>
          <p class="text-color mb-2"><?= htmlspecialchars($m['position']) ?></p>
          <div class="list-inline">
            <?php foreach (['facebook','twitter','linkedin','google'] as $social):
              if (!empty($m[$social])): ?>
            <a class="text-color p-2" href="<?= htmlspecialchars($m[$social]) ?>" target="_blank">
              <i class="ti-<?= $social === 'google' ? 'google' : ($social === 'linkedin' ? 'linkedin' : ($social)) ?>"></i>
            </a>
            <?php endif; endforeach; ?>
          </div>
        </div>
      </div>

    <?php endwhile;
    if ($currentCategory) echo '</div>';
    ?>
  </div>
</section>

<!-- Member Profile Modal -->
<div class="modal fade" id="memberModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content p-4">
      <div class="text-center">
        <img id="modal-img" class="rounded-circle mb-3" width="150">
        <h4 id="modal-name"></h4>
        <p id="modal-position" class="text-muted"></p>
        <p id="modal-bio"></p>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="plugins/bootstrap/bootstrap.bundle.min.js"></script>
<script>
  document.querySelectorAll('.team-card').forEach(card => {
    card.addEventListener('click', () => {
      document.getElementById('modal-img').src = card.dataset.img;
      document.getElementById('modal-name').innerText = card.dataset.name;
      document.getElementById('modal-position').innerText = card.dataset.position;
      document.getElementById('modal-bio').innerText = card.dataset.bio;
      new bootstrap.Modal(document.getElementById('memberModal')).show();
    });
  });
</script>
</body>
</html>
