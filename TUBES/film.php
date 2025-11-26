<?php
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/auth.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// ambil film
$stmt = $pdo->prepare("
    SELECT f.*, c.name AS category_name 
    FROM films f 
    LEFT JOIN categories c ON f.category_id = c.id
    WHERE f.id = :id
    LIMIT 1
");
$stmt->execute([':id' => $id]);
$film = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$film) {
    echo "<h2>Film tidak ditemukan.</h2>";
    exit;
}

// user login?
$user = current_user();
$is_fav = false;

if ($user) {
    $fav = $pdo->prepare("SELECT 1 FROM favorites WHERE user_id = :u AND film_id = :f LIMIT 1");
    $fav->execute([':u' => $user['id'], ':f' => $film['id']]);
    $is_fav = (bool)$fav->fetchColumn();
}

// related films
$related = $pdo->prepare("
    SELECT id, title, poster_path 
    FROM films 
    WHERE category_id = :cat AND id != :id
    ORDER BY created_at DESC
    LIMIT 6
");
$related->execute([':cat' => $film['category_id'], ':id' => $film['id']]);
$related = $related->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($film['title']) ?> — WatchOne</title>
  <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__.'/style.css') ?>">
  
</head>
<body>

<?php include __DIR__ . "/inc/header.php"; ?>

<div class="film-container">

    <h1 class="film-title"><?= htmlspecialchars($film['title']) ?></h1>

    <div class="film-meta">
        Kategori: <?= htmlspecialchars($film['category_name'] ?? '-') ?>
    </div>

    <div class="video-frame">
        <?php
        // deteksi YouTube
        $url = $film['video_url'];
        if (preg_match('/(?:v=|youtu\\.be\\/|embed\\/)([A-Za-z0-9_-]{6,})/', $url, $m)) {
            $yt_id = $m[1];
            echo '<iframe src="https://www.youtube.com/embed/'.$yt_id.'" allowfullscreen></iframe>';
        } else {
            // selain youtube → embed langsung
            echo '<iframe src="'.htmlspecialchars($url).'" allowfullscreen></iframe>';
        }
        ?>
    </div>

    <?php if ($user): ?>
    <div class="action-row">
        <button id="favBtn" class="btn-fav <?= $is_fav ? 'active':'' ?>">
            <?= $is_fav ? '★ Favorit' : '☆ Tambah ke Favorit' ?>
        </button>
    </div>
    <?php else: ?>
        <p><a href="login.php">Login</a> untuk menambahkan ke favorit.</p>
    <?php endif; ?>


    <?php if (!empty($film['description'])): ?>
        <h3>Deskripsi</h3>
        <p><?= nl2br(htmlspecialchars($film['description'])) ?></p>
    <?php endif; ?>


    <h3 style="margin-top:30px;">Film Terkait</h3>
    <div class="related-grid">
        <?php foreach ($related as $r): ?>
            <a class="related-item" href="film.php?id=<?= $r['id'] ?>">
                <img src="<?= htmlspecialchars($r['poster_path'] ?: 'noimg.png') ?>">
                <div class="related-item-title"><?= htmlspecialchars($r['title']) ?></div>
            </a>
        <?php endforeach; ?>
    </div>

</div>

<?php include __DIR__ . "/inc/footer.php"; ?>

<?php if ($user): ?>
<script>
document.getElementById('favBtn').addEventListener('click', async function(){
    const btn = this;
    const res = await fetch('api/toggle_favorite.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({ film_id: <?= $film['id'] ?> })
    });
    const j = await res.json();
    if (j.success) {
        if (j.favorited) {
            btn.classList.add('active');
            btn.textContent = '★ Favorit';
        } else {
            btn.classList.remove('active');
            btn.textContent = '☆ Tambah ke Favorit';
        }
    }
});
</script>
<?php endif; ?>

</body>
</html>
