<?php
// logout.php
session_start();
unset($_SESSION['user']); // atau session_unset() kalau semua mau dihapus
session_destroy();
header('Location: index.php');
exit;
