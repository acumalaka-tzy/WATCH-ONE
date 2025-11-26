<?php
session_start();
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/db.php';
$user = current_user();
if (!$user || ($user['role'] ?? '') !== 'admin') { header('Location: ../login.php'); exit; }

$cats = $pdo->query('SELECT id,name FROM categories ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $video_url = trim($_POST['video_url'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0) ?: null;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    if ($title === '' || $video_url === '') $errors[] = 'Judul dan video URL wajib.';
    if (empty($errors)) {
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
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tambah Film — Admin</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include __DIR__ . '/../inc/header.php'; ?>
<main class="container" style="padding:20px;">
  <h2>Tambah Film</h2>
  <?php if ($errors): ?><div class="card" style="background:#fff0f0; border:1px solid #ffd6d6; color:#9b2a2a;"><?php foreach($errors as $err) echo '<div>'.htmlspecialchars($err).'</div>'; ?></div><?php endif; ?>
  <form method="post">
    <label>Judul</label>
    <input type="text" name="title" required style="width:100%; padding:8px; margin-bottom:8px;">
    <label>Video URL</label>
    <input type="text" name="video_url" required style="width:100%; padding:8px; margin-bottom:8px;">
    <label>Kategori</label>
    <select name="category_id" style="width:100%; padding:8px; margin-bottom:8px;">
      <option value="">-- Pilih --</option>
      <?php foreach($cats as $c): ?>
        <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <label>Deskripsi</label>
    <textarea name="description" style="width:100%; padding:8px; margin-bottom:8px;"></textarea>
    <label><input type="checkbox" name="is_featured"> Tampilkan di Featured</label>
    <div style="margin-top:10px;"><button class="btn" type="submit">Simpan</button> <a class="btn btn-ghost" href="films_list.php">Batal</a></div>
  </form>
</main>
<?php include __DIR__ . '/../inc/footer.php'; ?>
</body>
</html>