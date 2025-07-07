<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

// Get the slug from the URL
$slug = basename($_SERVER['REQUEST_URI']);
$stmt = $conn->prepare("SELECT * FROM news WHERE slug = ? AND status = 'published' LIMIT 1");
$stmt->bind_param("s", $slug);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();

if (!$article) {
  echo "<h2 class='text-center text-red-600 mt-10'>Article not found.</h2>";
  include 'includes/footer.php';
  exit;
}
?>

<section class="page-title overlay" style="background-image: url('images/background/page-title-3.jpg');">
  <div class="container">
    <div class="row">
      <div class="col-12 text-center">
        <h2 class="text-white font-weight-bold"><?= htmlspecialchars($article['title']) ?></h2>
      </div>
    </div>
  </div>
</section>

<section class="bg-gray py-5">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <article class="bg-white p-4 rounded shadow">
          <h1 class="mb-2 text-2xl font-bold text-green-900"><?= htmlspecialchars($article['title']) ?></h1>
          <p class="text-sm text-gray-600 mb-4">By <b><?= htmlspecialchars($article['author']) ?></b> | <?= date('F d, Y', strtotime($article['date_posted'])) ?></p>

          <div class="article-content mb-4">
            <?= $article['content'] ?>
          </div>

          <p><b>Tags:</b> <?= htmlspecialchars($article['tags']) ?></p>

          <!-- Share Buttons -->
          <div class="mt-4">
            <h5>Share this article:</h5>
            <a href="https://facebook.com/sharer/sharer.php?u=<?= urlencode($_SERVER['REQUEST_URI']) ?>" target="_blank">Facebook</a> |
            <a href="https://twitter.com/intent/tweet?url=<?= urlencode($_SERVER['REQUEST_URI']) ?>" target="_blank">Twitter</a>
          </div>
        </article>

        <!-- Comments Section -->
        <?php
        $comment_stmt = $conn->prepare("SELECT * FROM comments WHERE news_id = ? AND approved = 1 ORDER BY created_at DESC");
        $comment_stmt->bind_param("i", $article['id']);
        $comment_stmt->execute();
        $comments = $comment_stmt->get_result();
        ?>

        <div class="mt-5">
          <h4 class="text-xl font-bold mb-3">Comments (<?= $comments->num_rows ?>)</h4>
          <?php while ($c = $comments->fetch_assoc()): ?>
            <div class="bg-white p-3 border rounded mb-2">
              <b><?= htmlspecialchars($c['name']) ?>:</b>
              <p><?= htmlspecialchars($c['comment']) ?></p>
              <small class="text-muted">Posted on <?= date('d M Y', strtotime($c['created_at'])) ?></small>
            </div>
          <?php endwhile; ?>

          <!-- Comment Form -->
          <?php if (isset($_SESSION['subscriber_approved']) && $_SESSION['subscriber_approved']): ?>
          <form action="submit_comment.php" method="POST" class="mt-4">
            <input type="hidden" name="news_id" value="<?= $article['id'] ?>">
            <textarea name="comment" class="form-control mb-2" required placeholder="Write your comment..."></textarea>
            <button type="submit" class="btn btn-primary">Submit Comment</button>
          </form>
          <?php else: ?>
            <p class="text-warning mt-4">Only approved subscribers can comment. <a href="#subscribe">Subscribe now</a>.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <div class="bg-white p-4 rounded shadow">
          <h5 class="mb-3">Recent News</h5>
          <?php
          $recent = $conn->query("SELECT slug, title, date_posted FROM news WHERE status='published' ORDER BY date_posted DESC LIMIT 5");
          while ($r = $recent->fetch_assoc()): ?>
            <div class="mb-3">
              <a href="<?= htmlspecialchars($r['slug']) ?>" class="text-dark font-semibold"><?= htmlspecialchars($r['title']) ?></a>
              <div class="text-muted small"><?= date('d M Y', strtotime($r['date_posted'])) ?></div>
            </div>
          <?php endwhile; ?>

          <!-- Subscription -->
          <h5 class="mt-4">Stay Updated</h5>
          <form action="subscribe.php" method="POST">
            <input name="name" type="text" class="form-control mb-2" placeholder="Your Name" required>
            <input name="email" type="email" class="form-control mb-2" placeholder="Your Email" required>
            <button class="btn btn-success btn-sm" type="submit">Subscribe</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
