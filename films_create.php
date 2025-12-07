<?php
session_start();
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/db.php';
$user = current_user();
if (!$user || ($user['role'] ?? '') !== 'admin') { header('Location: login.php'); exit; }

$cats = $pdo->query('SELECT id,name FROM categories ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $video_url = trim($_POST['video_url'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0) ?: null;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    if ($title === '' || $video_url === '') $errors[] = 'Judul dan Video URL wajib diisi.';
    
    if (empty($errors)) {
        // Auto fetch thumbnail
        $poster = null;
        if (preg_match('/(?:v=|youtu\\.be\\/|embed\\/)([A-Za-z0-9_-]{6,})/', $video_url, $m)) {
            $poster = "https://i.ytimg.com/vi/{$m[1]}/hqdefault.jpg";
        } else {
            $o = @file_get_contents('https://noembed.com/embed?url=' . urlencode($video_url));
            if ($o) { $j = json_decode($o, true); if (!empty($j['thumbnail_url'])) $poster = $j['thumbnail_url']; }
        }
        $slug = strtolower(preg_replace('/[^a-z0-9]+/','-', $title));
        $stmt = $pdo->prepare('INSERT INTO films (title,slug,description,poster_path,video_url,category_id,is_featured) VALUES (:t,:s,:d,:p,:v,:c,:f)');
        $stmt->execute([':t'=>$title,':s'=>$slug,':d'=>$description,':p'=>$poster,':v'=>$video_url,':c'=>$category_id,':f'=>$is_featured]);
        header('Location: films_list.php'); exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Film - Admin</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%2250%22 fill=%22%23ff0000%22/></svg>">
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>

<main class="admin-wrapper">
  <div style="max-width: 700px; margin: 0 auto;">
      <h2 class="page-title">Add New Film</h2>
      
      <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $err) echo '<div><i class="fas fa-exclamation-triangle"></i> '.$err.'</div>'; ?>
        </div>
      <?php endif; ?>

      <div class="glass-panel">
          <form method="post">
            <div style="margin-bottom:15px;">
                <label style="color:#ccc; display:block; margin-bottom:5px;">Judul Film</label>
                <input type="text" name="title" class="form-dark" required placeholder="Judul film...">
            </div>

            <div style="margin-bottom:15px;">
                <label style="color:#ccc; display:block; margin-bottom:5px;">Video URL (YouTube)</label>
                <input type="text" name="video_url" class="form-dark" required placeholder="https://youtube.com/...">
            </div>

            <div style="margin-bottom:15px;">
                <label style="color:#ccc; display:block; margin-bottom:5px;">Kategori</label>
                <select name="category_id" class="form-dark">
                  <option value="">-- Pilih Kategori --</option>
                  <?php foreach($cats as $c): ?>
                    <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                  <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom:15px;">
                <label style="color:#ccc; display:block; margin-bottom:5px;">Deskripsi</label>
                <textarea name="description" class="form-dark" rows="5" placeholder="Sinopsis..."></textarea>
            </div>

            <div style="margin-bottom:20px;">
                <label style="cursor:pointer; display:flex; align-items:center; gap:10px; color:white;">
                    <input type="checkbox" name="is_featured" style="width:18px; height:18px;"> 
                    Tampilkan di Header Utama (Featured)
                </label>
            </div>

            <div style="display:flex; gap:10px; flex-wrap: wrap;">
                <button class="btn btn-primary" type="submit" style="flex:1;">
                    <i class="fas fa-save"></i> Publish Film
                </button> 
                <a class="btn btn-glass" href="films_list.php" style="flex:1; text-align:center;">Cancel</a>
            </div>
          </form>
      </div>
  </div>
</main>
</body>
</html>