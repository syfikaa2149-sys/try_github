<?php
// auth.php — fungsi bersama untuk session, CSRF, autentikasi, dan otorisasi.
// Cara pakai: tulis  require_once __DIR__ . '/auth.php';  di baris awal setiap halaman.

// 1. Session (dimulai sekali, dengan pengaturan cookie yang lebih aman)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');   // tolak session ID yang tidak dikenal server
    ini_set('session.use_only_cookies', '1');  // session ID tidak boleh lewat URL

    session_name('SANTRI_SESSID');

    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $https,
        'httponly' => true,     // tidak bisa dibaca oleh JavaScript
        'samesite' => 'Lax',    // menahan pengiriman palsu lintas situs
    ]);
    session_start();
}

// 2. Fungsi bantu umum
function e($teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function post_string(string $kunci): string
{
    $nilai = $_POST[$kunci] ?? '';
    return is_string($nilai) ? $nilai : '';
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function tidak_boleh_di_cache(): void
{
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

// 3. CSRF (Tiket Pengaman Formulir)
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): bool
{
    $dikirim = $_POST['csrf_token'] ?? '';
    $asli    = $_SESSION['csrf_token'] ?? '';

    return is_string($dikirim)
        && is_string($asli)
        && $asli !== ''
        && hash_equals($asli, $dikirim);
}

// 4. Pemeriksaan Login dan Hak Akses (Role)
function is_logged_in(): bool
{
    return !empty($_SESSION['is_logged_in']) && !empty($_SESSION['user_id']);
}

function url_dashboard(string $role): string
{
    return $role === 'admin' ? 'admin.php' : 'dashboard.php';
}

function require_login(): void
{
    tidak_boleh_di_cache();
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function require_role(string $role): void
{
    require_login();
    if (($_SESSION['role'] ?? '') !== $role) {
        tolak_akses();
    }
}

function tolak_akses(): void
{
    http_response_code(403);
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-11 col-sm-8 col-md-6 col-lg-5">
            <div class="card shadow-sm text-center">
                <div class="card-body p-4">
                    <h1 class="display-4 text-danger">403</h1>
                    <h2 class="h5">Akses Ditolak</h2>
                    <p class="text-muted">Halaman ini hanya dapat diakses oleh admin.</p>
                    <a href="dashboard.php" class="btn btn-primary">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>
    <?php
    exit;
}
