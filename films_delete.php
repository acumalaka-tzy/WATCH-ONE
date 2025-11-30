<?php
session_start();
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/db.php';
$user = current_user();
if (!$user || ($user['role'] ?? '') !== 'admin') { header('Location: ../login.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: films_list.php'); exit; }
$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) { header('Location: films_list.php'); exit; }
$pdo->prepare('DELETE FROM films WHERE id=:id')->execute([':id'=>$id]);
header('Location: films_list.php'); exit;
?>
