<?php
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/auth.php';

$catId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$cats = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

if ($catId) {
    $stmt = $pdo->prepare("SELECT * FROM films WHERE category_id = :id ORDER BY created_at DESC");
    $stmt->execute([':id' => $catId]);
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $currentCat = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $currentCat->execute([$catId]);
    $catName = $currentCat->fetchColumn();
} else {
    $films = $pdo->query("SELECT * FROM films ORDER BY created_at DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
    $catName = "All Movies";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Categories - WatchOne</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%2250%22 fill=%22%23ff0000%22/></svg>">
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include __DIR__ . '/inc/header.php'; ?>

<div class="container" style="padding-top: 100px;"> <div style="margin-bottom: 30px; display:flex; gap:10px; flex-wrap:wrap;">
        <a href="category.php" class="btn btn-glass" style="padding: 8px 20px; font-size:0.9rem; <?= !$catId ? 'background:var(--primary-red);' : '' ?>">All</a>
        <?php foreach($cats as $c): ?>
            <a href="category.php?id=<?= $c['id'] ?>" class="btn btn-glass" style="padding: 8px 20px; font-size:0.9rem; <?= $catId == $c['id'] ? 'background:var(--primary-red);' : '' ?>">
                <?= htmlspecialchars($c['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <h2 class="section-title"><?= htmlspecialchars($catName) ?></h2>

    <?php if ($films): ?>
        <div class="grid-movies">
            <?php foreach ($films as $f): ?>
                <a href="film.php?id=<?= $f['id'] ?>" class="movie-card">
                    <img src="<?= htmlspecialchars($f['poster_path']) ?>" alt="<?= htmlspecialchars($f['title']) ?>">
                    <div class="movie-info">
                        <h3><?= htmlspecialchars($f['title']) ?></h3>
                        <span><?= substr($f['created_at'], 0, 4) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No movies found in this category.</p>
    <?php endif; ?>

</div>

</body>
</html>