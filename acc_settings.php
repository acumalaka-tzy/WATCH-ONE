<?php
session_start();
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/db.php';

$user = current_user();
if (!$user) { header('Location: login.php'); exit; }

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $conf = $_POST['confirm_password'] ?? '';

    if (!$name || !$email) $errors[] = "Nama dan Email wajib diisi.";

    if (!empty($pass)) {
        if ($pass !== $conf) $errors[] = "Konfirmasi password tidak cocok.";
        elseif (strlen($pass) < 6) $errors[] = "Password minimal 6 karakter.";
    }

    if (empty($errors)) {
        try {
            if (!empty($pass)) {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET name=:n, email=:e, password_hash=:p WHERE id=:id");
                $stmt->execute([':n'=>$name, ':e'=>$email, ':p'=>$hash, ':id'=>$user['id']]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET name=:n, email=:e WHERE id=:id");
                $stmt->execute([':n'=>$name, ':e'=>$email, ':id'=>$user['id']]);
            }
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
            $user = $_SESSION['user']; 
            $success = "Profil berhasil diperbarui!";
        } catch (PDOException $e) {
            $errors[] = "Gagal update (Email mungkin sudah dipakai).";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Account - WatchOne</title>
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
      /* CSS Khusus Halaman Settings */
      .settings-wrapper {
          padding-top: 120px;
          max-width: 800px;
          margin: 0 auto;
          padding-left: 20px;
          padding-right: 20px;
      }
      
      /* Grid Form: Desktop 2 Kolom, HP 1 Kolom */
      .form-grid {
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: 20px;
      }
      
      @media (max-width: 768px) {
          .settings-wrapper {
             padding-top: 100px; /* Jarak navbar di HP */
          }
          .form-grid {
              grid-template-columns: 1fr; /* Jadi 1 kolom di HP */
              gap: 0;
          }
      }
  </style>
</head>
<body>

<?php include __DIR__ . '/inc/header.php'; ?>

<div class="settings-wrapper">
    <h2 class="section-title">Account</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $e) echo "<div>$e</div>"; ?>
        </div>
    <?php endif; ?>

    <div class="glass-panel">
        <form method="post">
            <div style="margin-bottom: 20px; text-align: center;">
                 <img src="assets/img/profil.png" style=" width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 2px solid transparent;">
            </div>

            <div class="form-grid">
                <div style="margin-bottom:15px;">
                    <label style="color:#ccc; display:block; margin-bottom:5px;">Full Name</label>
                    <input type="text" name="name" class="form-dark" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="color:#ccc; display:block; margin-bottom:5px;">Email Address</label>
                    <input type="email" name="email" class="form-dark" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 20px 0;">
            <h4 style="color: white; margin-bottom: 15px;">Change Password <small style="color:#666; font-weight:normal; font-size:0.8rem;">(Optional)</small></h4>

            <div class="form-grid">
                <div style="margin-bottom:15px;">
                    <label style="color:#ccc; display:block; margin-bottom:5px;">New Password</label>
                    <input type="password" name="password" class="form-dark" placeholder="New Password">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="color:#ccc; display:block; margin-bottom:5px;">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-dark" placeholder="Repeat Password">
                </div>
            </div>

            <div style="margin-top: 20px; display:flex; gap:10px; flex-wrap:wrap;">
                <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">Save Changes</button>
                <a href="index.php" class="btn btn-glass" style="flex:1; justify-content:center;">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>