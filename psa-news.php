<?php
include 'includes/db.php';
include 'includes/header.php';

$per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $per_page;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : null;
$newsQuery = "SELECT * FROM news WHERE status='published'";

if ($search) {
    $newsQuery .= " AND (title LIKE '%$search%' OR content LIKE '%$search%' OR tags LIKE '%$search%')";
}

$newsQuery .= " ORDER BY date_posted DESC LIMIT $start, $per_page";
$news = $conn->query($newsQuery);

// Count total for pagination
$countResult = $conn->query("SELECT COUNT(*) as total FROM news WHERE status='published'");
$total = $countResult->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);
?>

<section class="page-title overlay" style="background-image: url(images/background/page-title-3.jpg);">
  <div class="container">
    <div class="row">
      <div class="col-12 text-center">
        <h2 class="text-white font-weight-bold">Malawi PSA News</h2>
      </div>
    </div>
  </div>
</section>

<section class="bg-gray">
  <div class="container">
    <div class="row">
      <!-- News List -->
      <div class="col-lg-8 py-100">
        <?php while ($n = $news->fetch_assoc()): ?>
          <article class="bg-white rounded mb-40">
            <div class="d-flex align-items-center border-bottom">
              <div class="text-center border-right p-4">
                <h3 class="text-primary mb-0">
                  <?= date('d', strtotime($n['date_posted'])) ?>
                  <span class="d-block paragraph"><?= date('M Y', strtotime($n['date_posted'])) ?></span>
                </h3>
              </div>
              <div class="px-4">
                <a class="h4 d-block mb-10" href="news-details.php?id=<?= $n['id'] ?>"><?= $n['title'] ?></a>
                <ul class="list-inline">
                  <li class="list-inline-item paragraph mr-5">By <a href="#" class="paragraph"><?= $n['author'] ?></a></li>
                  <li class="list-inline-item paragraph"><?= $n['category'] ?></li>
                </ul>
              </div>
            </div>
            <div class="p-4">
              <p><?= substr(strip_tags($n['content']), 0, 300) ?>...</p>
              <a href="news-details.php?id=<?= $n['id'] ?>" class="btn btn-sm btn-primary">Read More</a>
            </div>
          </article>
        <?php endwhile; ?>

        <!-- Pagination -->
        <nav class="mb-md-50">
          <ul class="pagination justify-content-center align-items-center">
            <?php if ($page > 1): ?>
              <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">Prev</a></li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
              <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
              <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">Next</a></li>
            <?php endif; ?>
          </ul>
        </nav>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <div class="bg-white px-4 py-100 sidebar-box-shadow">

          <!-- Search -->
          <div class="mb-50">
            <h4 class="mb-3">Search Here</h4>
            <form method="get">
              <div class="search-wrapper">
                <input type="text" class="form-control" name="search" placeholder="Type Here...">
              </div>
            </form>
          </div>

          <!-- Categories -->
          <div class="mb-50">
            <h4 class="mb-3">Categories</h4>
            <ul class="pl-0 mb-0">
              <?php
              $categories = $conn->query("SELECT DISTINCT category FROM news WHERE category IS NOT NULL AND category != ''");
              while ($cat = $categories->fetch_assoc()):
                $catName = $cat['category'];
              ?>
                <li class="border-bottom">
                  <a href="?search=<?= urlencode($catName ?? '') ?>" class="d-block text-color py-10"><?= $catName ?></a>
                </li>
              <?php endwhile; ?>
            </ul>
          </div>

          <!-- Recent Posts -->
          <div class="mb-50">
            <h4 class="mb-3">Recent News</h4>
            <?php
            $recent = $conn->query("SELECT id, title, date_posted FROM news ORDER BY date_posted DESC LIMIT 3");
            while ($r = $recent->fetch_assoc()):
            ?>
              <div class="d-flex py-3 border-bottom">
                <div class="content">
                  <h6 class="mb-3"><a class="text-dark" href="news-details.php?id=<?= $r['id'] ?>"><?= $r['title'] ?></a></h6>
                  <p class="meta"><?= date('d M, Y', strtotime($r['date_posted'])) ?></p>
                </div>
              </div>
            <?php endwhile; ?>
          </div>

          <!-- Tags -->
          <div class="mb-50">
            <h4 class="mb-3">Tags</h4>
            <ul class="list-inline tag-list">
              <?php
              $tagsResult = $conn->query("SELECT tags FROM news WHERE tags IS NOT NULL AND tags != ''");
              $tagArray = [];
              while ($row = $tagsResult->fetch_assoc()) {
                $tags = explode(',', $row['tags']);
                foreach ($tags as $tag) {
                  $cleanTag = trim($tag);
                  if (!in_array($cleanTag, $tagArray)) {
                    $tagArray[] = $cleanTag;
                  }
                }
              }
              foreach ($tagArray as $tag):
              ?>
                <li class="list-inline-item"><a href="?search=<?= urlencode($tag) ?>"><?= htmlspecialchars($tag) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>

          <!-- Newsletter -->
          <div class="newsletter">
            <h4 class="mb-3">Stay Updated</h4>
            <form id="subscribeForm">
                <input type="text" name="name" id="name" class="form-control mb-2" placeholder="Name" required>
                <input type="email" name="email" id="email" class="form-control mb-2" placeholder="Email" required>
                <button type="submit" class="btn btn-primary btn-sm">Subscribe</button>
                <div id="subscribeMsg" class="mt-2 text-success"></div>
            </form>
        </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
