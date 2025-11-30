<?php
// inc/auth.php

// 1. Mulai Session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Load koneksi database
require_once __DIR__ . '/db.php';

/**
 * Mengambil data user yang sedang login
 */
function current_user() {
    return $_SESSION['user'] ?? null;
}

/**
 * Cek apakah user sedang login
 */
function is_logged_in() {
    return isset($_SESSION['user']);
}

/**
 * Fungsi Login
 */
function login_user($email, $password) {
    global $pdo;
    
    // Ambil user berdasarkan email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // PERBAIKAN DI SINI:
    // Gunakan $user['password_hash'] sesuai nama kolom di database kamu
    if ($user && password_verify($password, $user['password_hash'])) {
        
        // Hapus hash password dari session agar aman
        unset($user['password_hash']);
        
        $_SESSION['user'] = $user;
        return ['success' => true];
    }

    return ['success' => false, 'message' => 'Email atau password salah.'];
}

/**
 * Fungsi Register
 */
function register_user($name, $email, $password) {
    global $pdo;

    // Cek email duplikat
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Email sudah terdaftar.'];
    }

    // Hash password
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // PERBAIKAN DI SINI:

    $ins = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, created_at) VALUES (:n, :e, :p, 'user', NOW())");
    
    if ($ins->execute([':n' => $name, ':e' => $email, ':p' => $hash])) {
        return ['success' => true, 'message' => 'Registrasi berhasil.'];
    }

    return ['success' => false, 'message' => 'Gagal mendaftar.'];
}

/**
 * Fungsi Logout
 */
function logout_user() {
    unset($_SESSION['user']);
    session_destroy();
}
?>