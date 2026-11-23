<?php
// login.php — form dan proses login.
// Alur: CSRF -> cari_user() -> verifikasi_password() -> buat session -> arahkan sesuai role.
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/cari_user.php';
require_once __DIR__ . '/verifikasi_password.php';

if (is_logged_in()) {
    redirect(url_dashboard($_SESSION['role'] ?? 'santri'));
}

$error    = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(post_string('username'));
    $password = post_string('password');

    if (!verify_csrf()) {
        http_response_code(403);
        $error = 'Sesi formulir tidak valid atau sudah kedaluwarsa. Muat ulang halaman lalu coba lagi.';
    } elseif ($username === '' || $password === '') {
        $error = 'Username dan kata sandi wajib diisi.';
    } else {
        try {
            $user = cari_user($pdo, $username);

            if (verifikasi_password($user, $password)) {
                session_regenerate_id(true);        // ganti ID session baru saat berhasil login
                unset($_SESSION['csrf_token']);     // buat ulang token CSRF

                $role = ($user['role'] ?? '') === 'admin' ? 'admin' : 'santri';

                $_SESSION['user_id']      = (int) $user['id'];
                $_SESSION['username']     = $user['username'];
                $_SESSION['role']         = $role;
                $_SESSION['is_logged_in'] = true;

                redirect(url_dashboard($role));
            }

            $error = PESAN_LOGIN_GAGAL;
        } catch (PDOException $e) {
            error_log('Login gagal karena error database: ' . $e->getMessage());
            $error = 'Terjadi kesalahan sistem. Silakan coba lagi nanti.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Aplikasi Santri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container">
    <div class="row justify-content-center align-items-center min-vh-100 py-4">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 text-center mb-1">Aplikasi Santri</h1>
                    <p class="text-center text-muted small mb-4">Masuk ke akun Anda</p>

                    <?php if (isset($_GET['registered'])): ?>
                        <div class="alert alert-success" role="alert">Registrasi berhasil! Silakan masuk dengan akun baru Anda.</div>
                    <?php endif; ?>
                    <?php if (isset($_GET['logout'])): ?>
                        <div class="alert alert-info" role="alert">Anda telah keluar dari aplikasi.</div>
                    <?php endif; ?>
                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
                    <?php endif; ?>

                    <form action="login.php" method="POST" novalidate>
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="username" class="form-label">Nama Pengguna</label>
                            <input type="text" class="form-control" id="username" name="username"
                                   value="<?= e($username) ?>" required autocomplete="username">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Kata Sandi</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   required autocomplete="current-password">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Masuk</button>
                    </form>

                    <p class="text-center small mt-3 mb-0">Belum punya akun? <a href="registrasi.php">Daftar di sini</a></p>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>
