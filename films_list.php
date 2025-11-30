<?php
session_start();
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/db.php';
$user = current_user();
if (!$user || ($user['role'] ?? '') !== 'admin') { header('Location: login.php'); exit; }

$films = $pdo->query("SELECT f.id,f.title,f.created_at,c.name AS category_name, f.poster_path FROM films f LEFT JOIN categories c ON f.category_id=c.id ORDER BY f.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Manage Films - Admin</title>
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include __DIR__ . '/inc/header.php'; ?>

<main class="admin-wrapper">
  
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <h2 class="page-title" style="margin:0;">Manage Films</h2>
    <a class="btn btn-primary" href="films_create.php" style="font-size:0.9rem;">
        <i class="fas fa-plus"></i> Add New
    </a>
  </div>

  <div class="glass-panel">
      <div class="table-responsive">
          <table class="admin-table">
            <thead>
                <tr>
                    <th width="50">Img</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Added Date</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
              <?php if($films): ?>
                  <?php foreach($films as $f): ?>
                    <tr>
                      <td>
                          <img src="<?= htmlspecialchars($f['poster_path'] ?: 'assets/img/no-poster.png') ?>" style="width:40px; height:60px; object-fit:cover; border-radius:4px;">
                      </td>
                      <td style="font-weight:600; font-size:1rem;">
                          <?= htmlspecialchars($f['title']) ?>
                      </td>
                      <td>
                          <span class="badge" style="background:rgba(255,255,255,0.1); white-space:nowrap;">
                              <?= htmlspecialchars($f['category_name'] ?? 'Uncategorized') ?>
                          </span>
                      </td>
                      <td style="color:#888; white-space:nowrap;">
                          <?= htmlspecialchars(substr($f['created_at'], 0, 10)) ?>
                      </td>
                      <td class="text-right" style="white-space:nowrap;">
                        <a class="btn btn-glass" style="padding:5px 10px;" href="films_edit.php?id=<?= (int)$f['id'] ?>">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="post" action="films_delete.php" style="display:inline;" onsubmit="return confirm('Delete this film?');">
                          <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
                          <button class="btn" type="submit" style="background:transparent; color:#ff4d4f; padding:5px 10px;">
                              <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
              <?php else: ?>
                  <tr><td colspan="5" style="text-align:center;">No films found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
      </div>
  </div>
</main>
</body>
</html>