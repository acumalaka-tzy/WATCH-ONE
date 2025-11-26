<?php
// inc/auth.php
// Helper simple untuk auth: register, login, cek user

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php'; // pastikan path benar

// Cek apakah user sudah login
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

// Ambil data user (array) atau null
function current_user(): ?array {
    global $pdo;
    if (!is_logged_in()) return null;
    $stmt = $pdo->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    return $u ?: null;
}

// Protect page (redirect ke login kalau belum)
function require_login($redirect = '/TUBES/login.php') {
    if (!is_logged_in()) {
        header("Location: $redirect");
        exit;
    }
}

// Register user (returns array [success => bool, message => string])
function register_user(string $name, string $email, string $password): array {
    global $pdo;
    // basic validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return ['success'=>false, 'message'=>'Email tidak valid'];
    if (strlen($password) < 6) return ['success'=>false, 'message'=>'Password minimal 6 karakter'];
    // check duplicate
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email'=>$email]);
    if ($stmt->fetch()) return ['success'=>false, 'message'=>'Email sudah terdaftar'];
    // insert
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :hash, 'user')");
    $stmt->execute([':name'=>$name, ':email'=>$email, ':hash'=>$hash]);
    return ['success'=>true, 'message'=>'Registrasi berhasil'];
}

// Try login (returns array [success, message])
function login_user(string $email, string $password): array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email'=>$email]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$u) return ['success'=>false, 'message'=>'Email tidak ditemukan'];
    if (!password_verify($password, $u['password_hash'])) return ['success'=>false, 'message'=>'Password salah'];
    // login success
    session_regenerate_id(true);
    $_SESSION['user_id'] = $u['id'];
    return ['success'=>true, 'message'=>'Login berhasil'];
}

// Logout
function logout_user() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
