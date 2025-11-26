<?php
require_once __DIR__ . '/inc/auth.php';

// Jika user sudah login, jangan biarkan buka login lagi
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $res = login_user($email, $password);

    if ($res['success']) {
        header('Location: index.php');
        exit;
    } else {
        $errors[] = $res['message'];
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - WatchOne</title>
  <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__ . '/style.css') ?>">
</head>
<body>

<?php include __DIR__ . '/inc/header.php'; ?>

<main class="container">
 <div class="form-card" style="max-width:520px; margin:32px auto;">
  <h2 style="text-align:center; margin-bottom:12px;">Login</h2>

  <?php if ($errors): ?>
    <div class="alert alert-danger" style="padding:10px; background:#f8d7da; color:#721c24; border-radius:6px; margin-bottom:15px;">
      <?php foreach ($errors as $err): ?>
        <div><?php echo htmlspecialchars($err); ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form action="" method="post">
    <label>Email</label>
    <input type="email" name="email" required style="width:100%; padding:8px; margin-bottom:12px;">

    <label>Password</label>
    <input type="password" name="password" required style="width:100%; padding:8px; margin-bottom:18px;">

    <button type="submit" style="width:100%; padding:10px; background:#0b2f3a; color:white; border:none; border-radius:6px;">
      Login
    </button>
  </form>

  <p style="margin-top:14px;">Belum punya akun? <a href="register.php">Daftar di sini</a></p>

</main>

<?php include __DIR__ . '/inc/footer.php'; ?>

</body>
</html>
