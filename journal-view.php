<?php
include 'includes/header.php';
include 'includes/db.php';

if (!isset($_GET['slug'])) {
  echo "<div class='container py-5 text-center'><h2>Journal not found.</h2></div>";
  include 'includes/footer.php';
  exit;
}

$slug = $conn->real_escape_string($_GET['slug']);
$journal = $conn->query("SELECT * FROM journals WHERE slug = '$slug'")->fetch_assoc();

if (!$journal) {
  echo "<div class='container py-5 text-center'><h2>Journal not found.</h2></div>";
  include 'includes/footer.php';
  exit;
}

// Topics
$topics = $conn->query("SELECT * FROM journal_topics WHERE journal_id = " . $journal['id']);
?>

<!-- Page Title -->
<section class="page-title overlay" style="background-image: url(images/background/page-title-3.jpg);">
  <div class="container">
    <div class="row">
      <div class="col-12 text-center">
        <h2 class="text-white font-weight-bold">Journal Details</h2>
      </div>
    </div>
  </div>
</section>

<section class="section bg-light">
  <div class="container">
    <div class="row">

      <!-- Main Journal Content -->
      <div class="col-lg-8">
        <div class="bg-white p-4 rounded shadow-sm">
          <h2 class="mb-2 text-dark"><?= htmlspecialchars($journal['title']) ?></h2>
          <p class="text-muted">By <?= htmlspecialchars($journal['author'] ?? 'Unknown') ?> |
            <?= date('d M Y', strtotime($journal['created_at'])) ?></p>

          <?php if (!empty($journal['summary'])): ?>
            <p class="lead mt-3"><?= nl2br(htmlspecialchars($journal['summary'])) ?></p>
          <?php endif; ?>

          <?php if (!empty($journal['file'])): ?>
            <a href="downloads/<?= urlencode($journal['file']) ?>" class="btn btn-danger my-3" download>
              <i class="fas fa-file-pdf"></i> Download Full Journal
            </a>
          <?php endif; ?>

          <hr>
          <h5 class="mb-3">Journal Topics</h5>
          <?php if ($topics->num_rows): ?>
            <ul class="list-group mb-3">
              <?php while ($topic = $topics->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <?= htmlspecialchars($topic['topic']) ?>
                  <a href="downloads/<?= urlencode($topic['file']) ?>" class="btn btn-sm btn-outline-primary" download>
                    <i class="fas fa-download"></i> <?= (int)$topic['downloads'] ?> downloads
                  </a>
                </li>
              <?php endwhile; ?>
            </ul>
          <?php else: ?>
            <p class="text-muted">No topics listed for this journal.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <div class="bg-white p-4 shadow-sm">
          <h5 class="mb-3">Search Journals</h5>
          <form method="GET" action="journal.php">
            <input type="text" name="q" class="form-control mb-2" placeholder="Search by topic, author">
            <button class="btn btn-sm btn-primary w-100">Search</button>
          </form>

          <hr>

          <h5 class="mb-3">Recent Journals</h5>
          <?php
          $recent = $conn->query("SELECT title, slug, created_at FROM journals ORDER BY created_at DESC LIMIT 5");
          while ($r = $recent->fetch_assoc()): ?>
            <div class="mb-3 border-bottom pb-2">
              <a href="journal.php?slug=<?= urlencode($r['slug']) ?>" class="text-dark font-weight-bold">
                <?= htmlspecialchars($r['title']) ?>
              </a>
              <div class="small text-muted"><?= date('M d, Y', strtotime($r['created_at'])) ?></div>
            </div>
          <?php endwhile; ?>

          <hr>

          <h5 class="mb-3">Stay Updated</h5>
          <form method="post" action="subscribe.php">
            <input type="text" name="name" class="form-control mb-2" placeholder="Your Name" required>
            <input type="email" name="email" class="form-control mb-2" placeholder="Your Email" required>
            <button class="btn btn-sm btn-success w-100">Subscribe</button>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
