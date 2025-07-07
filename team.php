<?php
include 'includes/db.php';
include 'includes/header.php';

// Fetch team members grouped by category
$categories = [
  'Patron' => [],
  'Executive' => [],
  'Editorial Team' => [],
  'Secretariat' => []
];

$result = $conn->query("SELECT * FROM team_members ORDER BY FIELD(category, 'Patron','Executive','Editorial Team','Secretariat'), id ASC");
while ($member = $result->fetch_assoc()) {
  $cat = $member['category'] ?? 'Executive';
  $categories[$cat][] = $member;
}
?>

<!-- Page Title -->
<section class="page-title overlay" style="background-image: url(images/background/page-title.jpg);">
  <div class="container">
    <div class="row">
      <div class="col-12 text-center">
        <h2 class="text-white font-weight-bold">Our Team</h2>
      </div>
    </div>
  </div>
</section>

<!-- Team Sections -->
<section class="section">
  <div class="container">
    <?php foreach ($categories as $category => $members): ?>
      <?php if (count($members)): ?>
        <div class="row justify-content-center mb-5">
          <div class="col-12 text-center">
            <h5 class="section-title-sm"><?= htmlspecialchars($category == 'Patron' ? 'Our' : 'Members of the') ?></h5>
            <h2 class="section-title section-title-border-half"><?= htmlspecialchars($category) ?></h2>
          </div>

          <?php foreach ($members as $m): ?>
            <div class="col-lg-4 col-sm-6 mb-4">
              <div class="card text-center h-100">
                <img class="card-img-top" src="uploads/team/<?= htmlspecialchars($m['image'] ?? 'default.png') ?>" alt="<?= htmlspecialchars($m['name'] ?? '') ?>">
                <div class="card-body card-body-2 pb-0 px-4">
                  <h5 class="card-title"><?= htmlspecialchars($m['name'] ?? '') ?></h5>
                  <h6 class="text-color mb-30"><?= htmlspecialchars($m['position'] ?? '') ?></h6>
                  <ul class="list-inline border-top d-inline-block">
                    <?php if (!empty($m['facebook'])): ?>
                      <li class="list-inline-item">
                        <a href="<?= htmlspecialchars($m['facebook']) ?>" class="text-color d-inline-block p-3" target="_blank">
                          <i class="ti-facebook"></i>
                        </a>
                      </li>
                    <?php endif; ?>
                    <?php if (!empty($m['twitter'])): ?>
                      <li class="list-inline-item">
                        <a href="<?= htmlspecialchars($m['twitter']) ?>" class="text-color d-inline-block p-3" target="_blank">
                          <i class="ti-twitter-alt"></i>
                        </a>
                      </li>
                    <?php endif; ?>
                    <?php if (!empty($m['linkedin'])): ?>
                      <li class="list-inline-item">
                        <a href="<?= htmlspecialchars($m['linkedin']) ?>" class="text-color d-inline-block p-3" target="_blank">
                          <i class="ti-linkedin"></i>
                        </a>
                      </li>
                    <?php endif; ?>
                    <?php if (!empty($m['google'])): ?>
                      <li class="list-inline-item">
                        <a href="<?= htmlspecialchars($m['google']) ?>" class="text-color d-inline-block p-3" target="_blank">
                          <i class="ti-google"></i>
                        </a>
                      </li>
                    <?php endif; ?>
                  </ul>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
