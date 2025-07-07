<?php
ob_start();
session_start();
include 'includes/db.php';

// Download full journal logic
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
$search = '';
$where = '1';
$filter = [];

if (!empty($_GET['q'])) {
  $q = $conn->real_escape_string($_GET['q']);
  $search = $q;
  $filter[] = "(title LIKE '%$q%' OR author LIKE '%$q%' OR tags LIKE '%$q%')";
}
if (!empty($_GET['author'])) {
  $author = $conn->real_escape_string($_GET['author']);
  $filter[] = "author = '$author'";
}
if (!empty($_GET['year'])) {
  $year = (int)$_GET['year'];
  $filter[] = "YEAR(created_at) = $year";
}
if (!empty($_GET['topic'])) {
  $topic = $conn->real_escape_string($_GET['topic']);
  $filter[] = "tags LIKE '%$topic%'";
}
if ($filter) {
  $where = implode(' AND ', $filter);
}

// Pagination
$perPage = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $perPage;
$totalRows = $conn->query("SELECT COUNT(*) AS total FROM journals WHERE $where")->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $perPage);

$journals = $conn->query("SELECT * FROM journals WHERE $where ORDER BY created_at DESC LIMIT $offset, $perPage");
$recent = $conn->query("SELECT * FROM journals ORDER BY created_at DESC LIMIT 5");

include 'includes/header.php';
?>

<style>
  .journal-summary {
    overflow-wrap: break-word;
    word-break: break-word;
    max-height: 140px;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>

<section class="page-title overlay" style="background-image: url(images/background/page-title-3.jpg);">
  <div class="container">
    <div class="row">
      <div class="col-12 text-center">
        <h2 class="text-white font-weight-bold">PSA Journals</h2>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row">
      <!-- Journal List -->
      <div class="col-lg-8">
        <?php while($j = $journals->fetch_assoc()): ?>
          <div class="bg-white rounded shadow-sm mb-4 p-4 wow fadeIn" data-wow-delay="0.2s">
            <div class="d-flex align-items-center mb-2">
              <img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" width="36" alt="PDF" class="mr-3">
              <div>
                <h5 class="mb-0 text-dark"><?= htmlspecialchars($j['title']) ?></h5>
                <small class="text-muted">By <?= htmlspecialchars($j['author'] ?? 'Unknown') ?> | <?= date('d M Y', strtotime($j['created_at'])) ?></small>
              </div>
            </div>
            <p class="mt-2 mb-3 journal-summary"><?= nl2br(htmlspecialchars($j['summary'] ?? 'No summary available.')) ?></p>

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

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
          <nav>
            <ul class="pagination">
              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>&q=<?= urlencode($search) ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>
            </ul>
          </nav>
        <?php endif; ?>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <div class="bg-white px-4 py-4 shadow-sm wow fadeInRight">
          <form class="mb-4" method="GET">
            <h5>Search & Filter</h5>
            <input type="text" name="q" class="form-control mb-2" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
            <input type="text" name="author" class="form-control mb-2" placeholder="Author">
            <input type="text" name="topic" class="form-control mb-2" placeholder="Topic">
            <input type="number" name="year" class="form-control mb-2" placeholder="Year">
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
          </form>

          <h5 class="mb-3">Recent Journals</h5>
          <?php while($r = $recent->fetch_assoc()): ?>
            <div class="mb-3 border-bottom pb-2">
              <h6 class="mb-1"><?= htmlspecialchars($r['title']) ?></h6>
              <small class="text-muted d-block">By <?= htmlspecialchars($r['author'] ?? 'Unknown') ?> | <?= date('M d, Y', strtotime($r['created_at'])) ?></small>
              <?php if (!empty($r['file']) && file_exists('uploads/journals/' . $r['file'])): ?>
                <a href="journal.php?download_journal_id=<?= $r['id'] ?>" class="text-primary small">
                  <i class="ti-download"></i> Download
                </a>
              <?php endif; ?>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
