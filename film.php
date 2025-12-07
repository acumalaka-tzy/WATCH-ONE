<?php
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/auth.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: index.php"); exit; }

// 1. AMBIL DATA FILM
$stmt = $pdo->prepare("SELECT f.*, c.name AS category_name FROM films f LEFT JOIN categories c ON f.category_id = c.id WHERE f.id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$film = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$film) { echo "Film tidak ditemukan."; exit; }

// 2. AMBIL RELATED FILMS
$relStmt = $pdo->prepare("SELECT * FROM films WHERE category_id = :cat AND id != :id LIMIT 4");
$relStmt->execute([':cat' => $film['category_id'], ':id' => $film['id']]);
$related = $relStmt->fetchAll(PDO::FETCH_ASSOC);

$user = current_user();

// 3. FITUR HISTORY 
if ($user) {

    $pdo->prepare("DELETE FROM history WHERE user_id = ? AND film_id = ?")->execute([$user['id'], $film['id']]);

    $pdo->prepare("INSERT INTO history (user_id, film_id, watched_at) VALUES (?, ?, NOW())")->execute([$user['id'], $film['id']]);
}

// 4. CEK STATUS FAVORIT (Untuk tombol)
$is_fav = false;
if ($user) {
    $checkFav = $pdo->prepare("SELECT 1 FROM favorites WHERE user_id = :u AND film_id = :f LIMIT 1");
    $checkFav->execute([':u' => $user['id'], ':f' => $film['id']]);
    $is_fav = (bool)$checkFav->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($film['title']) ?></title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%2250%22 fill=%22%23ff0000%22/></svg>">
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include __DIR__ . '/inc/header.php'; ?>

<div class="hero-wrapper" style="background-image: url('<?= htmlspecialchars($film['poster_path']) ?>'); height: 100vh;">
    <div class="hero-overlay">
        <div class="hero-content">
            <span class="tag-category"><?= htmlspecialchars($film['category_name']) ?></span>
            <h1 class="hero-title"><?= htmlspecialchars($film['title']) ?></h1>
            <p class="hero-desc"><?= nl2br(htmlspecialchars($film['description'])) ?></p>
            
            <div class="btn-group">
                <button class="btn btn-primary" onclick="openModal()">
                    <i class="fas fa-play"></i> Play Movie
                </button>
                
                <?php if ($user): ?>
                    <button id="favBtn" class="btn btn-glass" style="<?= $is_fav ? 'background:rgba(255,255,255,0.3); border-color:white;' : '' ?>">
                        <i class="<?= $is_fav ? 'fas' : 'far' ?> fa-plus"></i> 
                        <span id="favText"><?= $is_fav ? 'Added to List' : 'Add to List' ?></span>
                    </button>
                <?php else: ?>
                    <a href="login.php" class="btn btn-glass">
                        <i class="fas fa-plus"></i> Add to List
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div id="video-modal">
    <div class="close-modal" onclick="closeModal()">&times;</div>
    <?php
        $url = $film['video_url'];
        if (preg_match('/(?:v=|youtu\\.be\\/|embed\\/)([A-Za-z0-9_-]{6,})/', $url, $m)) {
            $embedUrl = "https://www.youtube.com/embed/" . $m[1] . "?autoplay=1";
        } else {
            $embedUrl = $url;
        }
    ?>
    <iframe id="vidFrame" class="video-frame" src="" data-src="<?= $embedUrl ?>" allowfullscreen></iframe>
</div>

<script>
function openModal() {
    document.getElementById('video-modal').style.display = 'flex';
    var iframe = document.getElementById('vidFrame');
    iframe.src = iframe.getAttribute('data-src');
}
function closeModal() {
    document.getElementById('video-modal').style.display = 'none';
    document.getElementById('vidFrame').src = '';
}
</script>

<?php if ($user): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const favBtn = document.getElementById('favBtn');
    
    if (!favBtn) {
        console.error('Button #favBtn tidak ditemukan di DOM');
        return;
    }
    
    favBtn.addEventListener('click', async function() {
        console.log("Tombol ditekan!");
        
        const btn = this;
        const icon = btn.querySelector('i');
        const textSpan = document.getElementById('favText');
        const filmId = <?= (int)$film['id'] ?>;

        btn.style.opacity = '0.7';

        try {
            const response = await fetch('api/api_toggle_favorite.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ film_id: filmId })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                if (result.favorited) {
                    icon.classList.remove('far', 'fa-plus');
                    icon.classList.add('fas', 'fa-check');
                    textSpan.textContent = 'Added to List';
                    btn.style.background = 'rgba(255, 255, 255, 0.3)';
                    btn.style.borderColor = 'white';
                } else {
                    icon.classList.remove('fas', 'fa-check');
                    icon.classList.add('far', 'fa-plus');
                    textSpan.textContent = 'Add to List';
                    btn.style.background = ''; 
                    btn.style.borderColor = '';
                }
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengubah favorit');
        } finally {
            btn.style.opacity = '1';
        }
    });
});
</script>
<?php endif; ?>

</body>
</html>