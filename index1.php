<?php

session_start();
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/db.php';

$user = current_user();
if (!$user || ($user['role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}


function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }


$totalFilms = (int)$pdo->query("SELECT COUNT(*) FROM films")->fetchColumn();
$totalCats  = (int)$pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Dashboard — WatchOne</title>
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include __DIR__ . '/inc/header.php'; ?>

<main class="admin-wrapper">
  <h2 class="page-title">Manage Films</h2>
  <p class="muted">Halo, <?= e($user['name']) ?> — ini panel cepat untuk mengelola situs.</p>

  <div class="admin-hero" style="margin-top:18px;">
    <div class="stat">
      <h2><?= $totalFilms ?></h2>
      <div class="muted">Total Film</div>
    </div>
    <div class="stat">
      <h2><?= $totalCats ?></h2>
      <div class="muted">Total Kategori</div>
    </div>
    <div class="stat">
      <h2><?= $totalUsers ?></h2>
      <div class="muted">Total Users</div>
    </div>
  </div>

  <div style="margin-top:18px;">
    <h3>Quick actions</h3>
    <div class="quick-links"> 
      <a href="films_create.php">+ Tambah Film</a>
      <a href="films_list.php">Kelola Film</a>
      <a href="categories.php">Kelola Kategori</a>
      <a href="index.php">Lihat Situs</a>
      <a href="users.php">Kelola Users</a>
    </div>
  </div>

  <div style="margin-top:22px;">
    <h3>Recent films</h3>
    <div style="margin-top:10px;">
      <?php
        $recent = $pdo->query("SELECT id,title,poster_path FROM films ORDER BY created_at DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
        if (!$recent) {
          echo '<div class="card">Belum ada film.</div>';
        } else {
          echo '<div class="grid auto">';
          foreach ($recent as $r) {
            $poster = $r['poster_path'] ?: '/ppw/TUBES/assets/img/no-poster.png';
            echo '<article class="card" style="padding:8px;">';
            echo '  <a href="../TUBES/film.php?id='.(int)$r['id'].'" style="text-decoration:none; color:inherit;">';
            echo '    <img src="'.e($poster).'" alt="'.e($r['title']).'" style="height:110px; object-fit:cover; border-radius:8px;">';
            echo '    <h4 style="margin-top:8px; font-size:14px;">'.e($r['title']).'</h4>';
            echo '  </a>';
            echo '</article>';
          }
          echo '</div>';
        }
      ?>
    </div>
  </div>

</main>



</body>
</html>
