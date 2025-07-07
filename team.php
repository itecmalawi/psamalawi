<?php
include 'includes/header.php';
include 'includes/db.php';

$categories = ['Patron', 'Members of the Executive', 'Editorial Team', 'Secretariate'];
$members = [];

foreach ($categories as $cat) {
  $stmt = $conn->prepare("SELECT * FROM team_members WHERE category = ? ORDER BY id ASC");
  $stmt->bind_param("s", $cat);
  $stmt->execute();
  $members[$cat] = $stmt->get_result();
}
?>

<!-- Styles -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.accordion-item {
  border: 1px solid #eee;
  border-radius: 6px;
  margin-bottom: 20px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}

.accordion-header {
  background: #117733;
  color: #fff;
  padding: 14px 20px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.accordion-body {
  display: none;
  padding: 20px;
  background: #fff;
}

.team-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 1.2rem;
  margin-top: 10px;
  justify-content: flex-start;
}

.team-card {
  background: white;
  border-radius: 10px;
  flex: 1 1 220px; /* minimum width */
  max-width: 240px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  transition: transform 0.3s;
  cursor: pointer;
  display: flex;
  flex-direction: column;
}

.team-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.08);
}

.team-card img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
}

.team-card .content {
  padding: 12px;
  text-align: center;
}

.team-card h5 {
  font-size: 16px;
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
  font-size: 15px;
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
  text-align: center;
  position: relative;
}

.modal-close {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 24px;
  color: #cc0000;
  cursor: pointer;
}

</style>

<!-- Page Title -->
<section class="page-title overlay" style="background-image: url(images/background/page-title.jpg);">
  <div class="container text-center">
    <h2 class="text-white font-weight-bold">Our Team</h2>
  </div>
</section>

<!-- Accordion Layout -->
<section class="section">
  <div class="container">

    <?php foreach ($members as $category => $group): ?>
      <div class="accordion-item">
        <div class="accordion-header" onclick="toggleAccordion(this)">
          <?= htmlspecialchars($category) ?>
          <i class="fas fa-chevron-down rotate-icon"></i>
        </div>
        <div class="accordion-body">
          <div class="team-grid">
            <?php while($m = $group->fetch_assoc()): ?>
              <div class="team-card" data-member='<?= json_encode($m) ?>'>
                <img src="uploads/team/<?= htmlspecialchars($m['image']) ?>" alt="<?= htmlspecialchars($m['name']) ?>">
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
            <?php endwhile; ?>
          </div>
        </div>
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

<!-- Scripts -->
<script>
  function toggleAccordion(el) {
    const body = el.nextElementSibling;
    const icon = el.querySelector('.rotate-icon');
    const allBodies = document.querySelectorAll('.accordion-body');
    const allIcons = document.querySelectorAll('.rotate-icon');

    allBodies.forEach(b => b.style.display = 'none');
    allIcons.forEach(i => i.style.transform = 'rotate(0deg)');

    if (body.style.display === 'block') {
      body.style.display = 'none';
      icon.style.transform = 'rotate(0deg)';
    } else {
      body.style.display = 'block';
      icon.style.transform = 'rotate(180deg)';
    }
  }

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
