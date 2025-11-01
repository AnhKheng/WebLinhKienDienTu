<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xóa tất cả SESSION
session_unset();
session_destroy();

// Xóa cookie Google hoặc token
if (isset($_COOKIE['g_state'])) {
    setcookie('g_state', '', time() - 3600, '/');
}
if (isset($_COOKIE['token'])) {
    setcookie('token', '', time() - 3600, '/');
}

// Quay về trang chủ
header("Location: ../Index.php");
exit();
?>
