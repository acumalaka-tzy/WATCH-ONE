<?php
// kategori.php
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/auth.php';

// helper escape
function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

// get list of categories
$catsStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
$categories = $catsStmt->fetchAll(PDO::FETCH_ASSOC);

// get selected category id (optional)
$catId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

// if category selected -> fetch films in that category, else show message/option
$total = 0;
$films = [];

if ($catId) {
    // count total
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM films WHERE category_id = :cid");
    $countStmt->execute([':cid' => $catId]);
    $total = (int)$countStmt->fetchColumn();

    // fetch films with limit/offset
    $stmt = $pdo->prepare("SELECT id, title, slug, poster_path, description FROM films WHERE category_id = :cid ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':cid', $catId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // fetch current category name for title
    $cStmt = $pdo->prepare("SELECT name FROM categories WHERE id = :id LIMIT 1");
    $cStmt->execute([':id' => $catId]);
    $catRow = $cStmt->fetch(PDO::FETCH_ASSOC);
    $catName = $catRow ? $catRow['name'] : 'Kategori';
} else {
    $catName = 'Semua Kategori';
}

$totalPages = $total ? (int)ceil($total / $limit) : 1;

?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Kategori — <?php echo e($catName); ?> — WatchOne</title>
  <link rel="stylesheet" href="style.css?v=<?= @file_exists(__DIR__ . '/style.css') ? filemtime(__DIR__ . '/style.css') : time() ?>">
</head>
<body>

<?php include __DIR__ . '/inc/header.php'; ?>

<main class="container" style="padding-top:24px;">
  <div class="form-card" style="padding:18px;">
    <h2 style="margin-top:0;">Kategori: <?php echo e($catName); ?></h2>

    <!-- categories list -->
    <div style="margin-bottom:16px;">
      <strong>Filter kategori:</strong>
      <div style="margin-top:8px; display:flex; gap:8px; flex-wrap:wrap;">
        <a href="category.php" class="chip" style="text-decoration:none; padding:6px 10px; border-radius:6px; background:<?php echo $catId? 'transparent':'#0b2f3a'; ?>; color:<?php echo $catId? '#0b2f3a':'#fff'; ?>; border:1px solid #0b2f3a;">Semua</a>
        <?php foreach($categories as $c): ?>
          <?php $active = ($catId && $catId == $c['id']); ?>
          <a href="category.php?id=<?php echo (int)$c['id']; ?>" class="chip" style="text-decoration:none; padding:6px 10px; border-radius:6px; background:<?php echo $active? '#0b2f3a':'transparent'; ?>; color:<?php echo $active? '#fff':'#0b2f3a'; ?>; border:1px solid #0b2f3a;"><?php echo e($c['name']); ?></a>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if (!$catId): ?>
      <!-- Jika tidak ada kategori yang dipilih: tampilkan preview per kategori -->
      <?php foreach ($categories as $c): ?>
        <?php
          // ambil sampai 6 film terbaru untuk setiap kategori
          $stmt = $pdo->prepare("SELECT id, title, poster_path, description FROM films WHERE category_id = :cid ORDER BY created_at DESC LIMIT 6");
          $stmt->execute([':cid' => $c['id']]);
          $group = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <section style="margin-bottom:22px;">
          <h3 style="margin-bottom:8px;"><?php echo e($c['name']); ?></h3>

          <?php if (empty($group)): ?>
            <div class="muted">Belum ada film di kategori ini.</div>
          <?php else: ?>
            <div class="grid" style="grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;">
              <?php foreach ($group as $f): ?>
                <article class="card">
                  <a href="/ppw/TUBES/film.php?id=<?= $f['id'] ?>">
                    <img src="<?php echo e($f['poster_path'] ?: 'assets/img/no-poster.png'); ?>" alt="<?php echo e($f['title']); ?>" style="width:100%; height:140px; object-fit:cover; border-radius:6px;">
                    <h4 style="font-size:14px; margin:8px 0 4px;"><?php echo e($f['title']); ?></h4>
                    <p style="font-size:12px; color:#666; margin:0;"><?php echo e(mb_strimwidth($f['description'] ?? '', 0, 70, '...')); ?></p>
                  </a>
                </article>
              <?php endforeach; ?>
            </div>
            <div style="margin-top:8px;">
              <a class="btn btn-ghost" href="/ppw/TUBES/kategori.php?id=<?php echo (int)$c['id']; ?>">Lihat semua di "<?php echo e($c['name']); ?>"</a>
            </div>
          <?php endif; ?>
        </section>
      <?php endforeach; ?>

    <?php else: ?>

      <?php if (empty($films)): ?>
        <div class="alert alert-danger">Belum ada film di kategori ini.</div>
      <?php else: ?>
        <!-- films grid -->
        <div class="grid" style="margin-top:12px;">
          <?php foreach($films as $f): ?>
            <article class="card">
              <a href="film.php?id=<?= $f['id'] ?>" style="text-decoration:none; color:inherit;">
                <img src="<?php echo e($f['poster_path'] ?: 'assets/img/no-poster.png'); ?>" alt="<?php echo e($f['title']); ?>" style="width:100%; height:160px; object-fit:cover; border-radius:6px;">
                <h3 style="font-size:15px; margin:8px 0 4px;"><?php echo e($f['title']); ?></h3>
                <p style="font-size:13px; color:#666; margin:0;"><?php echo e(mb_strimwidth($f['description'] ?? '', 0, 80, '...')); ?></p>
              </a>
            </article>
          <?php endforeach; ?>
        </div>

        <!-- pagination -->
        <?php if ($totalPages > 1): ?>
        <nav aria-label="Pagination" style="margin-top:18px; text-align:center;">
          <?php if ($page > 1): ?>
            <a href="category.php?id=<?php echo $catId; ?>&page=<?php echo $page-1; ?>" class="btn" style="display:inline-block; margin-right:6px; width:auto; padding:8px 12px;">&laquo; Prev</a>
          <?php endif; ?>

          <span style="margin:0 8px;">Halaman <?php echo $page; ?> dari <?php echo $totalPages; ?></span>

          <?php if ($page < $totalPages): ?>
            <a href="category.php?id=<?php echo $catId; ?>&page=<?php echo $page+1; ?>" class="btn" style="display:inline-block; margin-left:6px; width:auto; padding:8px 12px;">Next &raquo;</a>
          <?php endif; ?>
        </nav>
        <?php endif; ?>

      <?php endif; ?>

    <?php endif; ?>

  </div>
</main>

<?php include __DIR__ . '/inc/footer.php'; ?>

</body>
</html>
