<?php
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/auth.php';


$query = isset($_GET['q']) ? trim($_GET['q']) : '';


$films = [];
if ($query !== '') {

    $stmt = $pdo->prepare("SELECT * FROM films WHERE title LIKE :q OR description LIKE :q ORDER BY created_at DESC");
    $stmt->execute([':q' => "%$query%"]);
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search: <?= htmlspecialchars($query) ?> - WatchOne</title>
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    
<?php include __DIR__ . '/inc/header.php'; ?>

<div class="container" style="padding-top: 120px; min-height: 80vh;">
    
    <h2 class="section-title">
        <?php if($query): ?>
            Search Results for: "<span style="color:var(--primary-red);"><?= htmlspecialchars($query) ?></span>"
        <?php else: ?>
            Search Movies
        <?php endif; ?>
    </h2>

    <?php if ($films): ?>
        <div class="grid-movies">
            <?php foreach ($films as $f): ?>
                <a href="film.php?id=<?= $f['id'] ?>" class="movie-card">
                    <img src="<?= htmlspecialchars($f['poster_path'] ?: 'assets/img/no-poster.png') ?>" 
                         alt="<?= htmlspecialchars($f['title']) ?>">
                    
                    <div class="movie-info">
                        <h3><?= htmlspecialchars($f['title']) ?></h3>
                        <span><?= substr($f['created_at'], 0, 4) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        
        <div style="text-align: center; margin-top: 50px; color: #888;">
            <?php if($query): ?>
                <i class="fas fa-film" style="font-size: 3rem; margin-bottom: 20px;"></i>
                <p>Oops! No movies found matching "<?= htmlspecialchars($query) ?>".</p>
                <a href="index.php" class="btn btn-primary" style="margin-top:20px;">Back to Home</a>
            <?php else: ?>
                <p>Please type something in the search box above.</p>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchForm  = document.querySelector('.search-form');
    const searchInput = document.querySelector('.search-input');
    const searchBtn   = document.querySelector('.search-btn');

    if (!searchForm || !searchInput) return;

    
    const loader = document.createElement('div');
    loader.className = 'search-loader';
    loader.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
    document.body.appendChild(loader);

    
    searchForm.addEventListener('submit', function (e) {
        const q = searchInput.value.trim();

        if (!q) {
            e.preventDefault();
            searchInput.focus();


            searchInput.classList.add('shake');
            setTimeout(() => searchInput.classList.remove('shake'), 300);

            return;
        }

        
        loader.classList.add('show');

        if (searchBtn) {
            searchBtn.disabled = true;
        }
    });

    
    searchInput.addEventListener('input', function () {
        if (searchBtn) {
            searchBtn.disabled = false;
        }
    });
});
</script>

</body>
</html>