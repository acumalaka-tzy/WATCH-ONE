<?php
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/db.php';

$user = current_user();
if (!$user) { header('Location: login.php'); exit; }

$stmt = $pdo->prepare("
    SELECT f.* FROM films f
    JOIN favorites fav ON f.id = fav.film_id
    WHERE fav.user_id = :uid
    ORDER BY fav.created_at DESC
");
$stmt->execute([':uid' => $user['id']]);
$favFilms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>My Favorites - WatchOne</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%2250%22 fill=%22%23ff0000%22/></svg>">
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
      
      .fav-container {
          padding: 120px 50px 50px;
          max-width: 1400px;
          margin: 0 auto;
          min-height: 80vh;
      }
      @media (max-width: 768px) {
          .fav-container {
              padding: 100px 20px 20px; 
          }
      }
  </style>
</head>
<body>

<?php include __DIR__ . '/inc/header.php'; ?>

<div class="fav-container">
    <h2 class="section-title"><i class="fas fa-heart" style="color:var(--primary-red);"></i> My List</h2>
    
    <?php if ($favFilms): ?>
        <div class="grid-movies">
            <?php foreach ($favFilms as $f): ?>
                <div class="movie-card">
                    <a href="film.php?id=<?= $f['id'] ?>">
                        <img src="<?= htmlspecialchars($f['poster_path'] ?: 'assets/img/no-poster.png') ?>" alt="<?= htmlspecialchars($f['title']) ?>">
                    </a>
                    <div class="movie-info">
                        <h3><?= htmlspecialchars($f['title']) ?></h3>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:5px;">
                            <span><?= substr($f['created_at'], 0, 4) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="glass-panel" style="text-align: center; padding: 50px; margin-top:20px;">
            <i class="far fa-sad-tear" style="font-size: 3rem; color: #555; margin-bottom: 20px;"></i>
            <h3 style="color:white; font-size:1.2rem;">Your list is empty</h3>
            <p style="color:#aaa; margin-bottom:20px; font-size:0.9rem;">Go find some movies you like and click the "Add to List" button.</p>
            <a href="index.php" class="btn btn-primary">Browse Movies</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>