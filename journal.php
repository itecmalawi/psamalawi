<?php
ob_start();
session_start();
include 'includes/db.php';

// --- DOWNLOAD LOGIC ---
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

// --- FILTERS ---
$search = $_GET['q'] ?? '';
$authorFilter = $_GET['author'] ?? '';
$yearFilter = $_GET['year'] ?? '';
$where = '1';

if ($search) {
  $q = $conn->real_escape_string($search);
  $where .= " AND (title LIKE '%$q%' OR tags LIKE '%$q%' OR author LIKE '%$q%')";
}
if ($authorFilter) {
  $where .= " AND author = '" . $conn->real_escape_string($authorFilter) . "'";
}
if ($yearFilter) {
  $where .= " AND YEAR(created_at) = " . (int)$yearFilter;
}

// --- PAGINATION ---
$limit = 5;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

$total = $conn->query("SELECT COUNT(*) as total FROM journals WHERE $where")->fetch_assoc()['total'];
$journals = $conn->query("SELECT * FROM journals WHERE $where ORDER BY created_at DESC LIMIT $offset, $limit");

// --- EXTRA DATA FOR FILTERS ---
$authors = $conn->query("SELECT DISTINCT author FROM journals WHERE author IS NOT NULL AND author != '' ORDER BY author");
$years = $conn->query("SELECT DISTINCT YEAR(created_at) as yr FROM journals ORDER BY yr DESC");

include 'includes/header.php';
?>

<section class="page-title overlay" style="background-image: url(images/background/page-title-3.jpg);">
  <div class="container"><div class="row"><div class="col-12 text-center">
    <h2 class="text-white font-weight-bold">PSA Journals</h2>
  </div></div></div>
</section>

<section class="section">
  <div class="container">
    <div class="row">
      <!-- MAIN CONTENT -->
      <div class="col-lg-8">
        <form class="mb-4" method="get">
          <div class="form-row align-items-end">
            <div class="col-md-4 mb-2">
              <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search journals..." class="form-control">
            </div>
            <div class="col-md-3 mb-2">
              <select name="author" class="form-control">
                <option value="">All Authors</option>
                <?php while($a = $authors->fetch_assoc()): ?>
                  <option value="<?= $a['author'] ?>" <?= ($a['author'] == $authorFilter) ? 'selected' : '' ?>><?= $a['author'] ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="col-md-3 mb-2">
              <select name="year" class="form-control">
                <option value="">All Years</option>
                <?php while($y = $years->fetch_assoc()): ?>
                  <option value="<?= $y['yr'] ?>" <?= ($y['yr'] == $yearFilter) ? 'selected' : '' ?>><?= $y['yr'] ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="col-md-2 mb-2">
              <button type="submit" class="btn btn-sm btn-primary btn-block">Filter</button>
            </div>
          </div>
        </form>

        <div id="journal-list">
          <?php while($j = $journals->fetch_assoc()): ?>
            <div class="bg-white rounded shadow-sm mb-4 p-4 wow fadeIn" data-wow-delay="0.1s">
              <div class="d-flex align-items-center mb-2">
                <img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" width="34" class="mr-3" alt="PDF">
                <div>
                  <h5 class="mb-0"><?= htmlspecialchars($j['title']) ?></h5>
                  <small class="text-muted">By <?= htmlspecialchars($j['author']) ?> | <?= date('M d, Y', strtotime($j['created_at'])) ?></small>
                </div>
              </div>
              <p class="text-justify"><?= nl2br(htmlspecialchars(mb_strimwidth($j['summary'], 0, 300, "..."))) ?></p>

              <?php
                $topics = $conn->query("SELECT * FROM journal_topics WHERE journal_id = " . (int)$j['id']);
                if ($topics->num_rows > 0):
              ?>
                <div class="mb-2">
                  <strong>Topics:</strong>
                  <?php while($t = $topics->fetch_assoc()): ?>
                    <a href="downloads/<?= urlencode($t['file']) ?>" target="_blank" class="badge badge-info mr-1">
                      <?= htmlspecialchars($t['topic']) ?> (<?= $t['downloads'] ?>)
                    </a>
                  <?php endwhile; ?>
                </div>
              <?php endif; ?>

              <?php if (!empty($j['file']) && file_exists('uploads/journals/' . $j['file'])): ?>
                <a href="journal.php?download_journal_id=<?= $j['id'] ?>" class="btn btn-sm btn-outline-primary">
                  <i class="ti-download"></i>
                </a>
                <small class="ml-2 text-muted"><?= (int)$j['downloads'] ?> downloads</small>
              <?php else: ?>
                <span class="text-danger">[No PDF]</span>
              <?php endif; ?>
            </div>
          <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total > $limit): ?>
        <nav>
          <ul class="pagination">
            <?php for ($p = 1; $p <= ceil($total / $limit); $p++): ?>
              <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $p ?>&q=<?= urlencode($search) ?>&author=<?= urlencode($authorFilter) ?>&year=<?= urlencode($yearFilter) ?>"><?= $p ?></a>
              </li>
            <?php endfor; ?>
          </ul>
        </nav>
        <?php endif; ?>
      </div>

      <!-- SIDEBAR -->
      <div class="col-lg-4">
        <div class="bg-white px-4 py-4 shadow-sm">
          <h5 class="mb-3">Recent Journals</h5>
          <?php
            $recent = $conn->query("SELECT * FROM journals ORDER BY created_at DESC LIMIT 5");
            while($r = $recent->fetch_assoc()):
          ?>
          <div class="mb-3 border-bottom pb-2">
            <strong class="d-block"><?= htmlspecialchars($r['title']) ?></strong>
            <small class="text-muted"><?= htmlspecialchars($r['author']) ?> | <?= date('M d, Y', strtotime($r['created_at'])) ?></small>
            <?php if (!empty($r['file'])): ?>
              <br><a href="journal.php?download_journal_id=<?= $r['id'] ?>" class="text-primary"><i class="ti-download"></i> Download</a>
            <?php endif; ?>
          </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
