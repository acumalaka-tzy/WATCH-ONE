<?php
// admin/users.php
session_start();
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/db.php';

$user = current_user();
if (!$user || ($user['role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}

function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

// CSRF simple
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf'];

$action = $_POST['action'] ?? null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {
    if (!isset($_POST['csrf']) || !hash_equals($csrf, $_POST['csrf'])) {
        $error = "Invalid CSRF token.";
    } else {
        if ($action === 'change_role') {
            $target = (int)($_POST['user_id'] ?? 0);
            $newrole = ($_POST['role'] === 'admin') ? 'admin' : 'user';

            if ($target === $user['id']) {
                $error = "Tidak bisa mengubah role diri sendiri.";
            } else {
                if ($newrole !== 'admin') {
                    $countAdmin = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();

                    $stmt = $pdo->prepare("SELECT role FROM users WHERE id=:id");
                    $stmt->execute([':id'=>$target]);
                    $targetRole = $stmt->fetchColumn();

                    if ($targetRole === 'admin' && $countAdmin <= 1) {
                        $error = "Tidak bisa menurunkan admin terakhir.";
                    } else {
                        $upd = $pdo->prepare("UPDATE users SET role=:r WHERE id=:id");
                        $upd->execute([':r'=>$newrole, ':id'=>$target]);
                        $success = "Role berhasil diperbarui.";
                    }
                } else {
                    $upd = $pdo->prepare("UPDATE users SET role=:r WHERE id=:id");
                    $upd->execute([':r'=>$newrole, ':id'=>$target]);
                    $success = "Role berhasil diperbarui.";
                }
            }
        }

        if ($action === 'delete_user') {
            $target = (int)($_POST['user_id'] ?? 0);

            if ($target === $user['id']) {
                $error = "Tidak bisa menghapus akun sendiri.";
            } else {
                $stmt = $pdo->prepare("SELECT role FROM users WHERE id=:id");
                $stmt->execute([':id'=>$target]);
                $roleTarget = $stmt->fetchColumn();

                if ($roleTarget === 'admin') {
                    $countAdmin = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
                    if ($countAdmin <= 1) {
                        $error = "Tidak bisa menghapus admin terakhir.";
                    }
                }

                if (empty($error)) {
                    $pdo->prepare("DELETE FROM users WHERE id=:id")->execute([':id'=>$target]);
                    $success = "User berhasil dihapus.";
                }
            }
        }
    }
}

// search & pagination
$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$where = "1";
$params = [];
if ($q !== '') {
    $where = "(name LIKE :q OR email LIKE :q)";
    $params[':q'] = "%$q%";
}

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE $where");
$totalStmt->execute($params);
$total = (int)$totalStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));

$listStmt = $pdo->prepare("SELECT id,name,email,role,created_at FROM users WHERE $where ORDER BY id DESC LIMIT :lim OFFSET :off");
foreach ($params as $k=>$v) $listStmt->bindValue($k, $v);
$listStmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
$listStmt->bindValue(':off', $offset, PDO::PARAM_INT);
$listStmt->execute();
$users = $listStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin — Users</title>
  <link rel="stylesheet" href="../style.css?v=<?= filemtime(__DIR__ . '/../style.css') ?>">
  <style>
    .user-table { width:100%; border-collapse:collapse; }
    .user-table th, .user-table td {
      padding:10px; border-bottom:1px solid #f1f3f5; text-align:left;
    }
    .role-badge {
      padding:6px 8px; border-radius:8px; font-weight:700; font-size:13px; display:inline-block;
    }
    .role-admin { background:#fdecec; color:#b71c1c; border:1px solid #f7c7c7; }
    .role-user  { background:#e9f6fa; color:#0b5a63; border:1px solid #c8e7ef; }
  </style>
</head>
<body>

<?php include __DIR__ . '/../inc/header.php'; ?>

<main class="container" style="padding:20px;">
  <h1>Kelola User</h1>

  <?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
  <?php if (!empty($success)): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

  <div style="display:flex; gap:12px; align-items:center; margin-bottom:12px;">
    <form method="get" style="flex:1;">
      <input type="text" name="q" placeholder="Cari nama atau email..." value="<?= e($q) ?>" style="padding:8px 10px; width:100%; border-radius:8px; border:1px solid #e6eef0;">
    </form>
  </div>

  <div class="card">
    <table class="user-table">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Email</th>
          <th>Role</th>
          <th>Joined</th>
          <th>Aksi</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach($users as $u): ?>
        <tr>
          <td><strong><?= e($u['name']) ?></strong></td>
          <td><?= e($u['email']) ?></td>
          <td>
            <span class="role-badge <?= $u['role'] === 'admin' ? 'role-admin' : 'role-user' ?>">
              <?= ucfirst($u['role']) ?>
            </span>
          </td>
          <td class="muted"><?= e($u['created_at']) ?></td>
          <td>
            <?php if ($u['id'] !== $user['id']): ?>
              <form method="post" style="display:inline;">
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                <input type="hidden" name="action" value="change_role">
                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                <input type="hidden" name="role" value="<?= $u['role']==='admin'?'user':'admin' ?>">
                <button class="btn-ghost" type="submit"><?= $u['role']==='admin'?'Demote':'Promote' ?></button>
              </form>

              <form method="post" style="display:inline;" onsubmit="return confirm('Hapus user ini?');">
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                <button class="btn-ghost" type="submit" style="color:#ff4d4f;border-color:#ffbdbd">Hapus</button>
              </form>
            <?php else: ?>
              <span class="muted">Ini kamu</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div style="text-align:center; margin-top:16px;">
    <?php if ($page > 1): ?>
      <a class="btn-ghost" href="?page=<?= $page-1 ?>&q=<?= urlencode($q) ?>">&laquo; Prev</a>
    <?php endif; ?>
    <span style="margin:0 8px;">Hal <?= $page ?> / <?= $totalPages ?></span>
    <?php if ($page < $totalPages): ?>
      <a class="btn-ghost" href="?page=<?= $page+1 ?>&q=<?= urlencode($q) ?>">Next &raquo;</a>
    <?php endif; ?>
  </div>

</main>

<?php include __DIR__ . '/../inc/footer.php'; ?>

</body>
</html>
