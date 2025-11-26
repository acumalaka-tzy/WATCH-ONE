<?php
require_once __DIR__ . '/inc/auth.php'; // sesuaikan: naik 1 level ke folder ppw/inc

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $res = register_user($name, $email, $password);
        if ($res['success']) {
            $success = $res['message'];
            login_user($email, $password);
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
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register — WatchOne</title>
  <!-- sesuaikan path style.css jika kamu taruh di folder lain -->
  <link rel="stylesheet" href="style.css?v=<?= @file_exists(__DIR__ . '/style.css') ? filemtime(__DIR__ . '/style.css') : time() ?>">
</head>
<body>

<?php include __DIR__ . '/inc/header.php'; // panggil header yang di inc ?>

<main class="container">
  <div class="form-card" style="max-width:520px; margin:32px auto;">
    <h1 style="text-align:center; margin-bottom:12px;">Register</h1>

    <!-- ALERTS -->
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <?php foreach($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="post" action="">
      <div class="form-group">
        <label for="name">Nama</label>
        <input id="name" type="text" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
      </div>

      <!-- Password with strength + eye SVG -->
      <div class="form-group">
        <label for="pw">Password</label>
        <div class="pw-wrapper">
          <input id="pw" type="password" name="password" required autocomplete="new-password">
          <button type="button" class="pw-toggle" id="pw-toggle" aria-label="Toggle password visibility">
            <svg id="pw-icon-show" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <circle cx="12" cy="12" r="3" stroke="#ffffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <svg id="pw-icon-hide" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true" style="display:none;">
              <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.38 21.38 0 0 1 5.06-6.94M9.88 4.12A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a21.12 21.12 0 0 1-4.06 5.94M1 1l22 22" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>

        <div class="pw-strength" aria-hidden="true">
          <div class="pw-bar" id="pw-bar"></div>
        </div>
        <div id="pw-strength-text" class="pw-strength-text" aria-live="polite"></div>
      </div>

      <div class="form-group">
        <label for="cpw">Confirm Password</label>
        <div class="pw-wrapper">
          <input id="cpw" type="password" name="confirm_password" required autocomplete="new-password">
          <button type="button" class="pw-toggle" id="cpw-toggle" aria-label="Toggle confirm password visibility">
            <svg id="cpw-icon-show" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <circle cx="12" cy="12" r="3" stroke="#ffffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <svg id="cpw-icon-hide" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true" style="display:none;">
              <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.38 21.38 0 0 1 5.06-6.94M9.88 4.12A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a21.12 21.12 0 0 1-4.06 5.94M1 1l22 22" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
        <div id="pw-msg" class="alert-msg" aria-live="polite"></div>
      </div>

      <button class="btn" type="submit" id="btn-submit" disabled>Daftar</button>
    </form>

    <div class="form-meta">Sudah punya akun? <a href="login.php">Login di sini</a></div>
  </div>
</main>

<?php include __DIR__ . '/inc/footer.php'; // footer ?>

<!-- JS: strength, match, toggle -->
<script>
(function(){
  const pw = document.getElementById('pw');
  const cpw = document.getElementById('cpw');
  const btn = document.getElementById('btn-submit');
  const msg = document.getElementById('pw-msg');
  const bar = document.getElementById('pw-bar');
  const txt = document.getElementById('pw-strength-text');
  const pwToggle = document.getElementById('pw-toggle');
  const cpwToggle = document.getElementById('cpw-toggle');

  function scorePassword(p) {
    let score = 0;
    if (!p) return 0;
    if (p.length >= 8) score++;
    if (p.length >= 12) score++;
    if (/[0-9]/.test(p)) score++;
    if (/[A-Z]/.test(p) && /[a-z]/.test(p)) score++;
    if (/[^A-Za-z0-9]/.test(p)) score++;
    if (score >= 5) return 4;
    if (score >= 4) return 3;
    if (score >= 3) return 2;
    if (score >= 2) return 1;
    return 0;
  }

  function updateStrength() {
    const p = pw.value;
    const s = scorePassword(p);
    const widths = [8, 30, 55, 80, 100];
    const colors = ['#ff4d4f', '#ff7a45', '#ffd666', '#73d13d', '#389e0d'];
    const texts = ['Very weak', 'Weak', 'Medium', 'Strong', 'Very strong'];
    bar.style.width = widths[s] + '%';
    bar.style.background = colors[s];
    txt.textContent = p ? texts[s] : '';
  }

  function checkMatch() {
    if (!pw.value && !cpw.value) {
      msg.textContent = '';
      cpw.classList.remove('input-ok','input-bad');
      btn.disabled = true;
      return;
    }
    if (pw.value === cpw.value) {
      msg.textContent = '';
      cpw.classList.remove('input-bad');
      cpw.classList.add('input-ok');
      btn.disabled = (scorePassword(pw.value) < 1); // require at least minimal strength
    } else {
      msg.textContent = 'Password dan konfirmasi tidak sama.';
      cpw.classList.remove('input-ok');
      cpw.classList.add('input-bad');
      btn.disabled = true;
    }
  }

  function togglePassword(input, svgShow, svgHide) {
    if (input.type === "password") {
      input.type = "text";
      svgShow.style.display = "none";
      svgHide.style.display = "block";
    } else {
      input.type = "password";
      svgShow.style.display = "block";
      svgHide.style.display = "none";
    }
  }

  pw.addEventListener('input', function(){
    updateStrength();
    checkMatch();
  });
  cpw.addEventListener('input', checkMatch);

  pwToggle.addEventListener('click', function(){
    togglePassword(pw, document.getElementById('pw-icon-show'), document.getElementById('pw-icon-hide'));
  });
  cpwToggle.addEventListener('click', function(){
    togglePassword(cpw, document.getElementById('cpw-icon-show'), document.getElementById('cpw-icon-hide'));
  });

  // initial
  updateStrength();
  checkMatch();
})();
</script>

</body>
</html>
