<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

/**
 * 
 */
function current_user() {
    return $_SESSION['user'] ?? null;
}

/**
 * 
 */
function is_logged_in() {
    return isset($_SESSION['user']);
}

/**
 * 
 */
function login_user($email, $password) {
    global $pdo;
    
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($user && password_verify($password, $user['password_hash'])) {
        

        unset($user['password_hash']);
        
        $_SESSION['user'] = $user;
        return ['success' => true];
    }

    return ['success' => false, 'message' => 'Email atau password salah.'];
}

/**
 * 
 */
function register_user($name, $email, $password) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Email sudah terdaftar.'];
    }

    
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    

    $ins = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, created_at) VALUES (:n, :e, :p, 'user', NOW())");
    
    if ($ins->execute([':n' => $name, ':e' => $email, ':p' => $hash])) {
        return ['success' => true, 'message' => 'Registrasi berhasil.'];
    }

    return ['success' => false, 'message' => 'Gagal mendaftar.'];
}

/**
 * 
 */
function logout_user() {
    unset($_SESSION['user']);
    session_destroy();
}
?>