<?php
session_start();
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/db.php';
$user = current_user();
if (!$user || ($user['role'] ?? '') !== 'admin') { header('Location: ../login.php'); exit; }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $name = trim($_POST['name'] ?? '');
        if ($name === '') $errors[] = 'Nama kategori kosong.';
        else {
            $stmt = $pdo->prepare('SELECT id FROM categories WHERE name=:n LIMIT 1'); $stmt->execute([':n'=>$name]);
            if ($stmt->fetch()) $errors[] = 'Kategori sudah ada.';
            else { $pdo->prepare('INSERT INTO categories (name) VALUES (:n)')->execute([':n'=>$name]); }
        }
    }
    if (isset($_POST['delete'])) {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $pdo->beginTransaction();
            try {
                $pdo->prepare('UPDATE films SET category_id=NULL WHERE category_id=:id')->execute([':id'=>$id]);
                $pdo->prepare('DELETE FROM categories WHERE id=:id')->execute([':id'=>$id]);
                $pdo->commit();
            } catch (Exception $e) { $pdo->rollBack(); $errors[] = 'Gagal menghapus kategori.'; }
        }
    }
}
$cats = $pdo->query('SELECT id,name FROM categories ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin — Kategori</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include __DIR__ . '/../inc/header.php'; ?>
<main class="container" style="padding:20px;">
  <h2>Kelola Kategori</h2>
  <?php if ($errors): ?><div class="card" style="background:#fff0f0; border:1px solid #ffd6d6; color:#9b2a2a;"><?php foreach($errors as $err) echo '<div>'.htmlspecialchars($err).'</div>'; ?></div><?php endif; ?>
  <form method="post" style="margin-bottom:12px;">
    <input type="text" name="name" placeholder="Nama kategori" required style="padding:8px; width:60%;">
    <button class="btn" name="create" type="submit">Tambah</button>
  </form>
  <table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
      <?php foreach($cats as $c): ?>
        <tr>
          <td><?= (int)$c['id'] ?></td>
          <td><?= htmlspecialchars($c['name']) ?></td>
          <td>
            <form method="post" style="display:inline;">
              <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
              <button class="btn-ghost" name="delete" type="submit" onclick="return confirm('Hapus kategori?');">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>
<?php include __DIR__ . '/../inc/footer.php'; ?>
</body>
</html>