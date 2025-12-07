<?php
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/db.php';

$user = current_user();
if (!$user) { header('Location: login.php'); exit; }


if (isset($_POST['clear_history'])) {
    $pdo->prepare("DELETE FROM history WHERE user_id = ?")->execute([$user['id']]);
    header("Location: history.php"); 
    exit;
}

$stmt = $pdo->prepare("
    SELECT f.*, h.watched_at 
    FROM films f
    JOIN history h ON f.id = h.film_id
    WHERE h.user_id = :uid
    ORDER BY h.watched_at DESC
");
$stmt->execute([':uid' => $user['id']]);
$histFilms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Watch History - WatchOne</title>
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
      .hist-container {
          padding: 120px 50px 50px;
          max-width: 1400px;
          margin: 0 auto;
          min-height: 80vh;
      }
      @media (max-width: 768px) {
          .hist-container { padding: 100px 20px 20px; }
      }
      .watch-date {
          font-size: 0.75rem;
          color: #aaa;
          margin-top: 5px;
          display: block;
      }
  </style>
</head>
<body>

<?php include __DIR__ . '/inc/header.php'; ?>

<div class="hist-container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 class="section-title" style="margin-bottom:0;"><i class="fas fa-history" style="color:var(--primary-red);"></i> Watch History</h2>
        
        <?php if ($histFilms): ?>
            <form method="post" onsubmit="return confirm('Clear all history?');">
                <button type="submit" name="clear_history" class="btn btn-glass" style="font-size:0.8rem; padding:8px 15px; color:#ff4d4f;">
                    <i class="fas fa-trash-alt"></i> Clear History
                </button>
            </form>
        <?php endif; ?>
    </div>
    
    <?php if ($histFilms): ?>
        <div class="grid-movies">
            <?php foreach ($histFilms as $f): ?>
                <div class="movie-card">
                    <a href="film.php?id=<?= $f['id'] ?>">
                        <img src="<?= htmlspecialchars($f['poster_path'] ?: 'assets/img/no-poster.png') ?>" alt="<?= htmlspecialchars($f['title']) ?>">
                    </a>
                    <div class="movie-info">
                        <h3><?= htmlspecialchars($f['title']) ?></h3>
                        <span class="watch-date">
                            <i class="far fa-eye"></i> <?= date('d M Y, H:i', strtotime($f['watched_at'])) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="glass-panel" style="text-align: center; padding: 50px; margin-top:20px;">
            <i class="fas fa-history" style="font-size: 3rem; color: #555; margin-bottom: 20px;"></i>
            <h3 style="color:white; font-size:1.2rem;">No history yet</h3>
            <p style="color:#aaa; margin-bottom:20px; font-size:0.9rem;">Movies you watch will appear here.</p>
            <a href="index.php" class="btn btn-primary">Start Watching</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>