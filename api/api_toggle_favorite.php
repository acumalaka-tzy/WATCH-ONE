<?php
// File: api/toggle_favorite.php

// Matikan display error agar HTML error tidak merusak JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    $root = dirname(__DIR__); 
    
    if (!file_exists($root . '/inc/auth.php')) {
        throw new Exception("File auth.php tidak ditemukan di: " . $root . '/inc/auth.php');
    }

    require_once $root . '/inc/auth.php';
    require_once $root . '/inc/db.php';

    // Cek User Login
    $user = current_user();
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
        exit;
    }

    // Ambil Data JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $film_id = (int)($data['film_id'] ?? 0);

    if ($film_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID Film tidak valid']);
        exit;
    }

    // Cek Database
    $check = $pdo->prepare("SELECT 1 FROM favorites WHERE user_id = :uid AND film_id = :fid LIMIT 1");
    $check->execute([':uid' => $user['id'], ':fid' => $film_id]);
    
    if ($check->fetchColumn()) {
        // UNLIKE
        $del = $pdo->prepare("DELETE FROM favorites WHERE user_id = :uid AND film_id = :fid");
        $del->execute([':uid' => $user['id'], ':fid' => $film_id]);
        echo json_encode(['success' => true, 'favorited' => false]);
    } else {
        // LIKE
        $ins = $pdo->prepare("INSERT INTO favorites (user_id, film_id, created_at) VALUES (:uid, :fid, NOW())");
        $ins->execute([':uid' => $user['id'], ':fid' => $film_id]);
        echo json_encode(['success' => true, 'favorited' => true]);
    }

} catch (Exception $e) {
    // Tangkap error dan kirim sebagai JSON, BUKAN HTML
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>