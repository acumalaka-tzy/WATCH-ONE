<?php
require_once __DIR__ . '/inc/auth.php';

logout_user(); // hapus session & cookie
header('Location: index.php'); 
exit;
?>