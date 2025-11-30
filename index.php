<?php
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/auth.php';

$user = current_user(); // Ambil data user

// 1. Ambil 1 Film Featured untuk Hero
$heroStmt = $pdo->query("SELECT f.*, c.name as category_name FROM films f LEFT JOIN categories c ON f.category_id = c.id WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 1");
$heroFilm = $heroStmt->fetch(PDO::FETCH_ASSOC);

// Jika tidak ada featured, ambil film terbaru random
if (!$heroFilm) {
    $heroStmt = $pdo->query("SELECT f.*, c.name as category_name FROM films f LEFT JOIN categories c ON f.category_id = c.id ORDER BY created_at DESC LIMIT 1");
    $heroFilm = $heroStmt->fetch(PDO::FETCH_ASSOC);
}

// 2. CEK STATUS FAVORITE (Untuk Hero Section)
$is_hero_fav = false;
if ($user && $heroFilm) {
    $stmt = $pdo->prepare("SELECT 1 FROM favorites WHERE user_id = :uid AND film_id = :fid");
    $stmt->execute([':uid' => $user['id'], ':fid' => $heroFilm['id']]);
    $is_hero_fav = (bool)$stmt->fetchColumn();
}

// 3. Ambil Film Terbaru (Grid)
$excludeId = $heroFilm ? $heroFilm['id'] : 0;
$latestStmt = $pdo->prepare("SELECT * FROM films WHERE id != :id ORDER BY created_at DESC LIMIT 12");
$latestStmt->execute([':id' => $excludeId]);
$latestFilms = $latestStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>WatchOne - Home</title>
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include __DIR__ . '/inc/header.php'; ?>

<?php if ($heroFilm): ?>
<div class="hero-wrapper" style="background-image: url('<?= htmlspecialchars($heroFilm['poster_path']) ?>');">
    <div class="hero-overlay">
        <div class="hero-content">
            <span class="tag-category"><?= htmlspecialchars($heroFilm['category_name'] ?? 'Movie') ?></span>
            
            <h1 class="hero-title"><?= htmlspecialchars($heroFilm['title']) ?></h1>
            
            <p class="hero-desc">
                <?= htmlspecialchars(mb_strimwidth($heroFilm['description'], 0, 200, '...')) ?>
            </p>

            <div class="btn-group">
                <a href="film.php?id=<?= $heroFilm['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-play"></i> Watch Now
                </a>
                
                <?php if ($user): ?>
                    <button id="heroFavBtn" class="btn btn-glass" style="<?= $is_hero_fav ? 'background:rgba(255,255,255,0.3); border-color:white;' : '' ?>">
                        <i class="<?= $is_hero_fav ? 'fas' : 'far' ?> fa-plus"></i> 
                        <span id="heroFavText"><?= $is_hero_fav ? 'Added to Favorites' : 'Add to Favorites' ?></span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="container">
    <h2 class="section-title">New Releases</h2>
    
    <?php if ($latestFilms): ?>
    <div class="grid-movies">
        <?php foreach ($latestFilms as $f): ?>
            <a href="film.php?id=<?= $f['id'] ?>" class="movie-card">
                <img src="<?= htmlspecialchars($f['poster_path'] ?: 'assets/img/no-poster.png') ?>" alt="<?= htmlspecialchars($f['title']) ?>">
                <div class="movie-info">
                    <h3><?= htmlspecialchars($f['title']) ?></h3>
                    <span><?= substr($f['created_at'], 0, 4) ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <p>Belum ada film lainnya.</p>
    <?php endif; ?>
</div>

<?php if ($user && $heroFilm): ?>
<script>
document.getElementById('heroFavBtn').addEventListener('click', async function() {
    const btn = this;
    const icon = btn.querySelector('i');
    const textSpan = document.getElementById('heroFavText');
    const filmId = <?= (int)$heroFilm['id'] ?>;

    // Efek loading sementara (opsional)
    btn.style.opacity = '0.7';

    try {
        const response = await fetch('api/toggle_favorite.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ film_id: filmId })
        });
        
        const result = await response.json();

        if (result.success) {
            if (result.favorited) {
                // Jika berhasil ditambahkan
                icon.classList.remove('far', 'fa-plus'); 
                icon.classList.add('fas', 'fa-check');    
                textSpan.textContent = 'Added to Favorites';
                btn.style.background = 'rgba(255, 255, 255, 0.3)';
                btn.style.borderColor = 'white';
            } else {
                // Jika berhasil dihapus
                icon.classList.remove('fas', 'fa-check');
                icon.classList.add('far', 'fa-plus');
                textSpan.textContent = 'Add to Favorites';
                btn.style.background = ''; // Balik ke transparan
                btn.style.borderColor = '';
            }
        } else {
            alert('Gagal: ' + (result.message || 'Terjadi kesalahan'));
        }
    } catch (error) {
        console.error('Error:', error);
    } finally {
        btn.style.opacity = '1';
    }
});
</script>
<?php endif; ?>

</body>
</html>