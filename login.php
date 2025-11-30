<?php
require_once __DIR__ . '/inc/auth.php';
if (is_logged_in()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $res = login_user($email, $password); // Asumsi fungsi ini ada di auth.php
    if ($res['success']) { header('Location: index.php'); exit; }
    else { $error = $res['message']; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Login - WatchOne</title>
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include __DIR__ . '/inc/header.php'; ?>

<div class="auth-wrapper">
    <div class="form-glass">
        <h2 style="text-align:center; margin-bottom:20px;">Welcome Back</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter password">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Sign In</button>
        </form>
        
        <p style="text-align:center; margin-top:15px; font-size:0.9rem;">
            New to WatchOne? <a href="register.php" style="color:var(--primary-red); font-weight:bold;">Register now</a>
        </p>
    </div>
</div>

</body>
</html>