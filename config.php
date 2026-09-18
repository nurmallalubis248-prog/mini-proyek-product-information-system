<?php
// Pastikan session selalu berjalan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Akun Utama (Bawaan System / Super Admin)
define('ADMIN_USERNAME', 'nurmala');
define('ADMIN_PASSWORD', 'nurmala123');

// Inisialisasi wadah penampung users di dalam Session jika belum ada
if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [];
}