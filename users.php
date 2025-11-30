<?php
session_start();
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/db.php';

$user = current_user();
if (!$user || ($user['role'] ?? '') !== 'admin') { header('Location: login.php'); exit; }

function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf'];

// ... (Logic PHP Change Role & Delete Sama Seperti Sebelumnya) ...
// Saya singkat logic PHP-nya biar muat, tapi pastikan kamu pakai logic yg lengkap dari kode sebelumnya 
// atau copy bagian HTML di bawah ini saja.

// --- Bagian Penting HTML ---
$action = $_POST['action'] ?? null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {
     // (Paste logic PHP user dari jawaban sebelumnya disini)
     // Untuk mempersingkat, saya asumsikan logic PHP-nya sudah ada.
      if (!isset($_POST['csrf']) || !hash_equals($csrf, $_POST['csrf'])) {
        $error = "Invalid CSRF token.";
    } else {
        if ($action === 'change_role') {
            $target = (int)($_POST['user_id'] ?? 0);
            $newrole = ($_POST['role'] === 'admin') ? 'admin' : 'user';
             if ($target !== $user['id']) {
                 $pdo->prepare("UPDATE users SET role=:r WHERE id=:id")->execute([':r'=>$newrole, ':id'=>$target]);
                 $success = "Role updated.";
             }
        }
        if ($action === 'delete_user') {
            $target = (int)($_POST['user_id'] ?? 0);
            if ($target !== $user['id']) {
                $pdo->prepare("DELETE FROM users WHERE id=:id")->execute([':id'=>$target]);
                $success = "User deleted.";
            }
        }
    }
}

// Search Logic
$q = trim($_GET['q'] ?? '');
$listStmt = $pdo->prepare("SELECT * FROM users WHERE name LIKE :q OR email LIKE :q ORDER BY id DESC LIMIT 20");
$listStmt->execute([':q' => "%$q%"]);
$usersList = $listStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Manage Users - Admin</title>
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include __DIR__ . '/inc/header.php'; ?>

<main class="admin-wrapper">
  <h2 class="page-title">Manage Users</h2>

  <div class="glass-panel">
      <div style="margin-bottom:20px;">
        <form method="get" style="display:flex; gap:10px;">
            <input type="text" name="q" value="<?= e($q) ?>" class="form-dark" placeholder="Search..." style="flex:1;">
            <button class="btn btn-primary"><i class="fas fa-search"></i></button>
        </form>
      </div>

      <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Joined</th>
                <th class="text-right">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($usersList as $u): ?>
              <tr>
                <td style="font-weight:bold;"><?= e($u['name']) ?></td>
                <td style="color:#ccc;"><?= e($u['email']) ?></td>
                <td>
                  <span class="badge <?= $u['role']==='admin'?'badge-admin':'badge-user' ?>">
                      <?= strtoupper($u['role']) ?>
                  </span>
                </td>
                <td style="white-space:nowrap;"><?= substr($u['created_at'],0,10) ?></td>
                <td class="text-right" style="white-space:nowrap;">
                  <?php if ($u['id'] !== $user['id']): ?>
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                      <input type="hidden" name="action" value="change_role">
                      <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                      <input type="hidden" name="role" value="<?= $u['role']==='admin'?'user':'admin' ?>">
                      <button class="btn btn-glass" style="font-size:0.7rem; padding:5px 8px;">
                          <?= $u['role']==='admin'?'Demote':'Promote' ?>
                      </button>
                    </form>
                    <form method="post" style="display:inline;" onsubmit="return confirm('Hapus?');">
                      <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                      <input type="hidden" name="action" value="delete_user">
                      <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                      <button class="btn" style="color:#ff4d4f; padding:5px;"><i class="fas fa-trash"></i></button>
                    </form>
                  <?php else: ?>
                    <span class="badge" style="border:1px solid #555;">You</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
      </div>
  </div>
</main>
</body>
</html>