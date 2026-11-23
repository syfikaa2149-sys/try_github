<?php
// logout.php — hanya menerima metode POST dengan token CSRF yang valid.
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Metode tidak diizinkan. Gunakan tombol "Keluar" di dashboard. <a href="login.php">Ke halaman login</a>');
}

if (!verify_csrf()) {
    http_response_code(403);
    exit('Permintaan logout tidak valid (token CSRF salah). <a href="login.php">Ke halaman login</a>');
}

// 1. Kosongkan semua data session
$_SESSION = [];

// 2. Hapus cookie session di browser
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// 3. Hancurkan session di server
session_destroy();
// 4. Kembali ke halaman login
redirect('login.php?logout=1');
