<?php
ob_start();
session_start();
include 'includes/db.php';

// Handle download
if (isset($_GET['download_journal_id'])) {
  $jid = (int) $_GET['download_journal_id'];
  $file = $conn->query("SELECT file FROM journals WHERE id = $jid")->fetch_assoc();
  $filename = $file['file'] ?? '';
  if ($filename && file_exists("uploads/journals/$filename")) {
    $conn->query("UPDATE journals SET downloads = downloads + 1 WHERE id = $jid");
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Length: ' . filesize("uploads/journals/$filename"));
    readfile("uploads/journals/$filename");
    exit;
  } else {
    die("File not found.");
  }
}

// Filters
$search = $_GET['q'] ?? '';
$author = $_GET['author'] ?? '';
$year = $_GET['year'] ?? '';
$topic = $_GET['topic'] ?? '';

$where = "1";
if ($search) {
  $q = $conn->real_escape_string($search);
  $where .= " AND (title LIKE '%$q%' OR author LIKE '%$q%' OR tags LIKE '%$q%')";
}
if ($author) $where .= " AND author = '" . $conn->real_escape_string($author) . "'";
if ($year) $where .= " AND YEAR(created_at) = '" . intval($year) . "'";
if ($topic) $where .= " AND tags LIKE '%" . $conn->real_escape_string($topic) . "%'";

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$journals = $conn->query("SELECT * FROM journals WHERE $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
$recent = $conn->query("SELECT * FROM journals ORDER BY created_at DESC LIMIT 5");
$authors = $conn->query("SELECT DISTINCT author FROM journals");
$years = $conn->query("SELECT DISTINCT YEAR(created_at) as year FROM journals ORDER BY year DESC");
$topics = $conn->query("SELECT DISTINCT tags FROM journals");

include 'includes/header.php';
?>

<section class="page-title overlay" style="background-image: url(images/background/page-title-3.jpg);">
  <div class="container">
    <h2 class="text-white font-weight-bold text-center">PSA Journals</h2>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row">
      <!-- Main -->
      <div class="col-lg-8" id="journal-container">
        <?php while($j = $journals->fetch_assoc()): ?>
        <div class="bg-white rounded shadow-sm mb-4 p-4 wow fadeIn" data-wow-delay="0.2s">
          <div class="d-flex align-items-center mb-2">
            <img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" width="36" class="mr-3">
            <div>
              <h5 class="mb-0"><?= htmlspecialchars($j['title']) ?></h5>
              <small class="text-muted">By <?= htmlspecialchars($j['author']) ?> | <?= date('d M Y', strtotime($j['created_at'])) ?></small>
            </div>
          </div>
          <p class="mt-2 mb-3"><?= nl2br(htmlspecialchars(mb_strimwidth($j['summary'], 0, 400, '...'))) ?></p>
          <?php if (!empty($j['tags'])): ?>
            <p>
              <?php foreach(explode(',', $j['tags']) as $tag): ?>
                <span class="badge badge-secondary"><?= trim($tag) ?></span>
              <?php endforeach; ?>
            </p>
          <?php endif; ?>
          <?php if (!empty($j['file']) && file_exists('uploads/journals/' . $j['file'])): ?>
            <a href="journal.php?download_journal_id=<?= $j['id'] ?>" class="btn btn-sm btn-outline-primary">
              <i class="ti-download"></i> Download
            </a>
            <span class="text-muted small ml-2"><?= (int)$j['downloads'] ?> downloads</span>
          <?php else: ?>
            <span class="text-danger small">[PDF not available]</span>
          <?php endif; ?>
        </div>
        <?php endwhile; ?>

        <!-- Load More -->
        <div class="text-center mt-4">
          <a href="?page=<?= $page + 1 ?>&q=<?= urlencode($search) ?>&author=<?= urlencode($author) ?>&year=<?= urlencode($year) ?>&topic=<?= urlencode($topic) ?>" class="btn btn-outline-dark">Load More</a>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <div class="bg-white p-4 shadow-sm">
          <form class="mb-4" method="GET">
            <h5>Search Journals</h5>
            <input type="text" name="q" class="form-control mb-2" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">

            <select name="author" class="form-control mb-2">
              <option value="">-- Filter by Author --</option>
              <?php while($a = $authors->fetch_assoc()): ?>
                <option value="<?= $a['author'] ?>" <?= ($a['author'] == $author) ? 'selected' : '' ?>><?= $a['author'] ?></option>
              <?php endwhile; ?>
            </select>

            <select name="year" class="form-control mb-2">
              <option value="">-- Filter by Year --</option>
              <?php while($y = $years->fetch_assoc()): ?>
                <option value="<?= $y['year'] ?>" <?= ($y['year'] == $year) ? 'selected' : '' ?>><?= $y['year'] ?></option>
              <?php endwhile; ?>
            </select>

            <select name="topic" class="form-control mb-3">
              <option value="">-- Filter by Topic --</option>
              <?php while($t = $topics->fetch_assoc()):
                foreach (explode(',', $t['tags']) as $tg): ?>
                  <option value="<?= trim($tg) ?>" <?= ($topic == trim($tg)) ? 'selected' : '' ?>><?= trim($tg) ?></option>
              <?php endforeach; endwhile; ?>
            </select>

            <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
          </form>

          <h5 class="mb-3">Recent Journals</h5>
          <?php while($r = $recent->fetch_assoc()): ?>
            <div class="mb-3 border-bottom pb-2">
              <h6 class="mb-1"><?= htmlspecialchars($r['title']) ?></h6>
              <small class="text-muted d-block"><?= htmlspecialchars($r['author'] ?? 'Unknown') ?> | <?= date('M d, Y', strtotime($r['created_at'])) ?></small>
              <a href="journal.php?download_journal_id=<?= $r['id'] ?>" class="text-primary small"><i class="ti-download"></i> Download</a>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
