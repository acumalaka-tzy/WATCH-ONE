<?php
session_start();
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/db.php';

$user = current_user();
if (!$user || ($user['role'] ?? '') !== 'admin') { header('Location: login.php'); exit; }

// Create & Delete
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $name = trim($_POST['name'] ?? '');
        if ($name === '') $errors[] = 'Nama kategori kosong.';
        else {
            $stmt = $pdo->prepare('SELECT id FROM categories WHERE name=:n LIMIT 1'); 
            $stmt->execute([':n'=>$name]);
            if ($stmt->fetch()) $errors[] = 'Kategori sudah ada.';
            else { 
                $pdo->prepare('INSERT INTO categories (name) VALUES (:n)')->execute([':n'=>$name]); 
                header("Location: categories.php"); exit;
            }
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
                header("Location: categories.php"); exit;
            } catch (Exception $e) { 
                $pdo->rollBack(); 
                $errors[] = 'Gagal menghapus kategori.'; 
            }
        }
    }
}
$cats = $pdo->query('SELECT id,name FROM categories ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Categories - Admin</title>
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
      .cat-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
  </style>
</head>
<body>

<?php include __DIR__ . '/inc/header.php'; ?>

<main class="admin-wrapper">
  <h2 class="page-title">Manage Categories</h2>
  
  <?php if ($errors): ?>
    <div class="alert alert-danger">
        <?php foreach($errors as $err) echo '<div><i class="fas fa-exclamation-circle"></i> '.$err.'</div>'; ?>
    </div>
  <?php endif; ?>

  <div class="cat-grid">
      <div class="glass-panel" style="height: fit-content;">
          <h3 style="margin-bottom:15px; color:white; border:none; padding:0;">Add New</h3>
          <form method="post">
            <div style="margin-bottom:15px;">
                <input type="text" name="name" class="form-dark" placeholder="Category Name..." required>
            </div>
            <button class="btn btn-primary" style="width:100%;" name="create" type="submit">
                <i class="fas fa-plus"></i> Add
            </button>
          </form>
      </div>

      <div class="glass-panel">
          <div class="table-responsive">
              <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                  <?php if($cats): ?>
                      <?php foreach($cats as $c): ?>
                        <tr>
                          <td style="color:#888; font-weight:bold;">#<?= (int)$c['id'] ?></td>
                          <td style="font-weight:600; font-size:1rem; color:white;">
                              <?= htmlspecialchars($c['name']) ?>
                          </td>
                          <td class="text-right">
                            <form method="post" style="display:inline;">
                              <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                              <button class="btn btn-glass" name="delete" type="submit" onclick="return confirm('Hapus kategori?');" style="color:#ff4d4f; border-color:rgba(255, 77, 79, 0.3);">
                                  <i class="fas fa-trash"></i>
                              </button>
                            </form>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                  <?php else: ?>
                      <tr><td colspan="3" style="text-align:center;">No categories found.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
          </div>
      </div>
  </div>
</main>
</body>

</html>
