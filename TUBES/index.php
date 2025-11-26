<?php
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/auth.php';
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>WatchOne — Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__ . '/style.css') ?>">
</head>

<body>

<?php include __DIR__ . '/inc/header.php'; ?>

<main class="container" style="padding-top:25px;">

  <!-- SECTION: Featured -->
  <h2>Featured</h2>
  <?php
  $feat = $pdo->query("SELECT id,title,poster_path FROM films WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 6")->fetchAll();
  ?>

  <?php if ($feat): ?>
  <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap:16px;">
    <?php foreach ($feat as $f): ?>
      <article class="card">
        <a href="film.php?id=<?= $f['id'] ?>" style="text-decoration:none; color:inherit;">
          <img src="<?= htmlspecialchars($f['poster_path'] ?: 'assets/img/no-poster.png') ?>"
               style="width:100%; height:200px; object-fit:cover; border-radius:6px;">
          <h3 style="margin-top:8px; font-size:16px;"><?= htmlspecialchars($f['title']) ?></h3>
        </a>
      </article>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <p>Belum ada film featured.</p>
  <?php endif; ?>


  <hr style="margin:30px 0; opacity:.2;">



  <!-- SECTION: FILM TERBARU -->
  <h2>Film Terbaru</h2>

  <?php
  $latest = $pdo->query("SELECT id,title,poster_path FROM films ORDER BY created_at DESC LIMIT 12")->fetchAll();
  ?>

  <?php if ($latest): ?>
  <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap:16px;">
    <?php foreach ($latest as $f): ?>
      <article class="card">
        <a href="/ppw/TUBES/film.php?id=<?= $f['id'] ?>">
          <img src="<?= htmlspecialchars($f['poster_path'] ?: 'assets/img/no-poster.png') ?>"
               style="width:100%; height:200px; object-fit:cover; border-radius:6px;">
          <h3 style="margin-top:8px; font-size:16px;"><?= htmlspecialchars($f['title']) ?></h3>
        </a>
      </article>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <p>Belum ada film yang ditambahkan.</p>
  <?php endif; ?>


  <hr style="margin:30px 0; opacity:.2;">



  <!-- SECTION: KATEGORI -->
  <h2>Kategori</h2>

  <?php
  $cats = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
  ?>

  <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:12px;">
    <?php foreach ($cats as $c): ?>
      <a href="kategori.php?id=<?= $c['id'] ?>"
         style="padding:8px 14px; background:#0b2f3a; color:#fff; border-radius:8px; text-decoration:none;">
        <?= htmlspecialchars($c['name']) ?>
      </a>
    <?php endforeach; ?>
  </div>

</main>

<?php include __DIR__ . '/inc/footer.php'; ?>

</body>
</html>
