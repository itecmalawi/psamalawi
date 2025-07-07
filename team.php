<?php
include 'includes/header.php';
include 'includes/db.php';

// Fetch team members grouped by category
$categories = ['Patron', 'Executive', 'Editorial Team', 'Secretariat'];
$members = [];

foreach ($categories as $cat) {
  $stmt = $conn->prepare("SELECT * FROM team_members WHERE category = ? ORDER BY id ASC");
  $stmt->bind_param("s", $cat);
  $stmt->execute();
  $members[$cat] = $stmt->get_result();
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js">
<style>
  .team-section h3 {
    font-weight: bold;
    color: #117733;
    border-left: 4px solid #cc0000;
    padding-left: 12px;
    margin-top: 50px;
  }


  @media (min-width: 768px) {
    .team-grid {
      column-count: 2;
    }
  }
  @media (min-width: 992px) {
    .team-grid {
      column-count: 3;
    }
  }

  .team-card {
    background: #fff;
    border-radius: 10px;
    margin: 0 0 1.5rem;
    display: inline-block;
    width: 100%;
    cursor: pointer;
    transition: transform .3s;
  }
  .team-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  }

  .team-card img {
    width: 100%;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
  }

  .team-card .content {
    padding: 15px;
    text-align: center;
  }

  .team-card h5 {
    font-size: 18px;
    margin-bottom: 5px;
    color: #000;
  }

  .team-card small {
    color: #117733;
    font-weight: 600;
  }

  .team-card .social a {
    color: #444;
    margin: 0 6px;
    font-size: 16px;
  }

  .modal-member {
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.6);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
  }

  .modal-content {
    background: #fff;
    max-width: 400px;
    padding: 25px;
    border-radius: 10px;
    position: relative;
    text-align: center;
  }

  .modal-content h4 {
    margin-bottom: 10px;
  }

  .modal-close {
    position: absolute;
    top: 10px;
    right: 15px;
    cursor: pointer;
    font-size: 22px;
    color: #cc0000;
  }
</style>

<!-- Page Title -->
<section class="page-title overlay" style="background-image: url(images/background/page-title.jpg);">
  <div class="container text-center">
    <h2 class="text-white font-weight-bold">Our Team</h2>
  </div>
</section>

<section class="section team-section">
  <div class="container">
    <?php foreach ($members as $category => $group): ?>
      <h3><?= htmlspecialchars($category) ?></h3>
        <div class="row">
  <?php while($m = $group->fetch_assoc()): ?>
    <div class="col-lg-4 col-md-6 mb-4">
      <div class="team-card h-100" data-member='<?= json_encode($m) ?>'>
        <img src="uploads/team/<?= htmlspecialchars($m['image']) ?>" alt="<?= $m['name'] ?>">
        <div class="content">
          <h5><?= htmlspecialchars($m['name']) ?></h5>
          <small><?= htmlspecialchars($m['position']) ?></small>
          <div class="social mt-2">
            <?php if ($m['facebook']): ?><a href="<?= $m['facebook'] ?>"><i class="fab fa-facebook"></i></a><?php endif; ?>
            <?php if ($m['twitter']): ?><a href="<?= $m['twitter'] ?>"><i class="fab fa-twitter"></i></a><?php endif; ?>
            <?php if ($m['linkedin']): ?><a href="<?= $m['linkedin'] ?>"><i class="fab fa-linkedin"></i></a><?php endif; ?>
            <?php if ($m['google']): ?><a href="<?= $m['google'] ?>"><i class="fab fa-google"></i></a><?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
</div>

    <?php endforeach; ?>
  </div>
</section>

<!-- Modal -->
<div class="modal-member" id="memberModal">
  <div class="modal-content">
    <span class="modal-close" onclick="document.getElementById('memberModal').style.display='none'">&times;</span>
    <img src="" id="modalImg" class="rounded mb-3" style="width: 100px; border-radius: 50%;">
    <h4 id="modalName"></h4>
    <p class="text-muted mb-1" id="modalPosition"></p>
    <div id="modalLinks" class="mb-2"></div>
  </div>
</div>

<script>
  // Handle modal open
  document.querySelectorAll('.team-card').forEach(card => {
    card.addEventListener('click', () => {
      const data = JSON.parse(card.getAttribute('data-member'));
      document.getElementById('modalImg').src = 'uploads/team/' + data.image;
      document.getElementById('modalName').innerText = data.name;
      document.getElementById('modalPosition').innerText = data.position;

      const links = [];
      if (data.facebook) links.push(`<a href="${data.facebook}" target="_blank"><i class="fab fa-facebook"></i></a>`);
      if (data.twitter) links.push(`<a href="${data.twitter}" target="_blank"><i class="fab fa-twitter"></i></a>`);
      if (data.linkedin) links.push(`<a href="${data.linkedin}" target="_blank"><i class="fab fa-linkedin"></i></a>`);
      if (data.google) links.push(`<a href="${data.google}" target="_blank"><i class="fab fa-google"></i></a>`);
      document.getElementById('modalLinks').innerHTML = links.join(' ');

      document.getElementById('memberModal').style.display = 'flex';
    });
  });
</script>

<?php include 'includes/footer.php'; ?>
