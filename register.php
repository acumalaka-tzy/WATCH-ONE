<?php
require_once __DIR__ . '/inc/auth.php';


if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $errors[] = "Semua kolom wajib diisi.";
    } elseif ($password !== $confirm) {
        $errors[] = "Konfirmasi password tidak cocok.";
    } else {
        $res = register_user($name, $email, $password);

        if ($res['success']) {

            header('Location: login.php?registered=1');
            exit;
        } else {
            $errors[] = $res['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - WatchOne</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%2250%22 fill=%22%23ff0000%22/></svg>">
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include __DIR__ . '/inc/header.php'; ?>

    <div class="auth-wrapper">
        
        <div class="form-glass">
            <h2 style="text-align:center; margin-bottom:10px; font-weight:700;">Join Us</h2>
            <p style="text-align:center; color:#ccc; font-size:0.9rem; margin-bottom:25px;">Create an account to start watching</p>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $err): ?>
                        <div><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($err) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label>Full Name</label>
                    <div style="position:relative;">
                        <input type="text" name="name" required placeholder="Ex: Eren Yeager" value="<?= htmlspecialchars($name ?? '') ?>">
                        <i class="fas fa-user" style="position:absolute; right:15px; top:12px; color:#666;"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <div style="position:relative;">
                        <input type="email" name="email" required placeholder="name@example.com" value="<?= htmlspecialchars($email ?? '') ?>">
                        <i class="fas fa-envelope" style="position:absolute; right:15px; top:12px; color:#666;"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" required placeholder="Create a password">
                        <i class="fas fa-lock" style="position:absolute; right:15px; top:12px; color:#666;"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div style="position:relative;">
                        <input type="password" name="confirm_password" required placeholder="Repeat password">
                        <i class="fas fa-check-circle" style="position:absolute; right:15px; top:12px; color:#666;"></i>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; margin-top:10px;">
                    Sign Up Now
                </button>
            </form>

            <div style="text-align:center; margin-top:20px; font-size:0.9rem; color:#ccc;">
                Already have an account? 
                <a href="login.php" style="color:var(--primary-red); font-weight:600; text-decoration:none;">
                    Sign In here
                </a>
            </div>
        </div>
    </div>

</body>
</html>