<?php
session_start();
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/db.php';
$user = current_user();
if (!$user || ($user['role'] ?? '') !== 'admin') { header('Location: login.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: films_list.php'); exit; }

$cats = $pdo->query('SELECT id,name FROM categories ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$errors = [];

$stm = $pdo->prepare('SELECT * FROM films WHERE id=:id LIMIT 1'); $stm->execute([':id'=>$id]);
$film = $stm->fetch(PDO::FETCH_ASSOC);
if (!$film) { header('Location: films_list.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $video_url = trim($_POST['video_url'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0) ?: null;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    if ($title === '' || $video_url === '') $errors[] = 'Judul dan video URL wajib.';
    
    if (empty($errors)) {
        $poster = trim($_POST['poster_path'] ?? '') ?: $film['poster_path'];
        $slug = strtolower(preg_replace('/[^a-z0-9]+/','-', $title));
        $u = $pdo->prepare('UPDATE films SET title=:t,slug=:s,description=:d,poster_path=:p,video_url=:v,category_id=:c,is_featured=:f WHERE id=:id');
        $u->execute([':t'=>$title,':s'=>$slug,':d'=>$description,':p'=>$poster,':v'=>$video_url,':c'=>$category_id,':f'=>$is_featured,':id'=>$id]);
        header('Location: films_list.php'); exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Film - Admin</title>
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>

<main class="admin-wrapper">
  <div style="max-width: 700px; margin: 0 auto;">
      <h2 class="page-title">Edit Film</h2>

      <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $err) echo '<div>'.$err.'</div>'; ?>
        </div>
      <?php endif; ?>

      <div class="glass-panel">
          <form method="post">
            <div style="margin-bottom:15px;">
                <label style="color:#ccc; display:block; margin-bottom:5px;">Judul</label>
                <input type="text" name="title" class="form-dark" value="<?= htmlspecialchars($film['title']) ?>" required>
            </div>
            
            <div style="margin-bottom:15px;">
                <label style="color:#ccc; display:block; margin-bottom:5px;">Video URL</label>
                <input type="text" name="video_url" class="form-dark" value="<?= htmlspecialchars($film['video_url']) ?>" required>
            </div>
            
            <div style="margin-bottom:15px;">
                <label style="color:#ccc; display:block; margin-bottom:5px;">Poster URL (Manual)</label>
                <input type="text" name="poster_path" class="form-dark" value="<?= htmlspecialchars($film['poster_path']) ?>">
                <small style="color:#666; font-size:0.8rem;">Biarkan kosong jika tidak ingin mengubah poster</small>
            </div>

            <div style="margin-bottom:15px;">
                <label style="color:#ccc; display:block; margin-bottom:5px;">Kategori</label>
                <select name="category_id" class="form-dark">
                  <option value="">-- Pilih --</option>
                  <?php foreach($cats as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= ($film['category_id']==$c['id'])? 'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
                  <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom:15px;">
                <label style="color:#ccc; display:block; margin-bottom:5px;">Deskripsi</label>
                <textarea name="description" class="form-dark" rows="5"><?= htmlspecialchars($film['description']) ?></textarea>
            </div>

            <div style="margin-bottom:20px;">
                <label style="cursor:pointer; display:flex; align-items:center; gap:10px; color:white;">
                    <input type="checkbox" name="is_featured" style="width:18px; height:18px;" <?= $film['is_featured']? 'checked':'' ?>> 
                    Tampilkan di Featured
                </label>
            </div>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button class="btn btn-primary" type="submit" style="flex:1;">
                    <i class="fas fa-save"></i> Save Changes
                </button> 
                <a class="btn btn-glass" href="films_list.php" style="flex:1; text-align:center;">Cancel</a>
            </div>
          </form>
      </div>
  </div>
</main>
</body>
</html>