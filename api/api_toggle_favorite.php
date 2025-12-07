<?php

ob_start();

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    $root = dirname(__DIR__); 
    
    if (!file_exists($root . '/inc/auth.php') || !file_exists($root . '/inc/db.php')) {
        throw new Exception("File konfigurasi (auth/db) tidak ditemukan.");
    }

    require_once $root . '/inc/auth.php';
    require_once $root . '/inc/db.php';

    $user = current_user();
    if (!$user) {
        throw new Exception("Silakan login terlebih dahulu.");
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $film_id = (int)($data['film_id'] ?? 0);

    if ($film_id <= 0) {
        throw new Exception("ID Film tidak valid.");
    }

    $check = $pdo->prepare("SELECT 1 FROM favorites WHERE user_id = :uid AND film_id = :fid LIMIT 1");
    $check->execute([':uid' => $user['id'], ':fid' => $film_id]);
    
    $response = [];

    if ($check->fetchColumn()) {
        $del = $pdo->prepare("DELETE FROM favorites WHERE user_id = :uid AND film_id = :fid");
        $del->execute([':uid' => $user['id'], ':fid' => $film_id]);
        $response = ['success' => true, 'favorited' => false];
    } else {
        $ins = $pdo->prepare("INSERT INTO favorites (user_id, film_id, created_at) VALUES (:uid, :fid, NOW())");
        $ins->execute([':uid' => $user['id'], ':fid' => $film_id]);
        $response = ['success' => true, 'favorited' => true];
    }

    ob_end_clean(); 
    echo json_encode($response);

} catch (Exception $e) {
    ob_end_clean(); 
    http_response_code(500); 
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>