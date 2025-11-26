<?php
// admin/films_list.php
session_start();
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/db.php';
$user = current_user();
if (!$user || ($user['role'] ?? '') !== 'admin') { header('Location: ../login.php'); exit; }

// fetch films
$films = $pdo->query("SELECT f.id,f.title,f.created_at,c.name AS category_name FROM films f LEFT JOIN categories c ON f.category_id=c.id ORDER BY f.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin — Films</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include __DIR__ . '/../inc/header.php'; ?>
<main class="container" style="padding:20px;">
  <h2>Daftar Film</h2>
  <p><a class="btn" href="films_create.php">+ Tambah Film</a> <a class="btn btn-ghost" href="index.php">Dashboard</a></p>
  <table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Judul</th>
            <th>Kategori</th>
            <th>Dibuat Pada</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
      <?php foreach($films as $f): ?>
        <tr>
          <td><?= (int)$f['id'] ?></td>
          <td><?= htmlspecialchars($f['title']) ?></td>
          <td><?= htmlspecialchars($f['category_name'] ?? '-') ?></td>
          <td><?= htmlspecialchars($f['created_at']) ?></td>
          <td>
            <a class="btn-ghost" href="films_edit.php?id=<?= (int)$f['id'] ?>">Edit</a>
            <form method="post" action="films_delete.php" style="display:inline;" onsubmit="return confirm('Hapus film?');">
              <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
              <button class="btn-ghost" type="submit" style="background:#fff;border:1px solid #ff4d4f;color:#ff4d4f;">Hapus</button>
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