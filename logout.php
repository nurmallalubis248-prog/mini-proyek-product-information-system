<?php
require_once 'config.php';

// Pastikan session aktif sebelum dibersihkan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kosongkan semua data session
$_SESSION = array();

// Hapus cookie session dari browser jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Redirect bersih ke login.php
header('Location: login.php');
exit;