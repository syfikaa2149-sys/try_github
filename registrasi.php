<?php
// registrasi.php — pendaftaran akun baru. Role SELALU 'santri'.
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/koneksi.php';

if (is_logged_in()) {
    redirect(url_dashboard($_SESSION['role'] ?? 'santri'));
}

const USERNAME_MIN = 3;
const USERNAME_MAX = 30;
const PASSWORD_MIN = 8;
const PASSWORD_MAX = 72;   // batas byte algoritma bcrypt

$error    = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(post_string('username'));
    $password = post_string('password');

    if (!verify_csrf()) {
        http_response_code(403);
        $error = 'Sesi formulir tidak valid atau sudah kedaluwarsa. Muat ulang halaman lalu coba lagi.';
    } elseif ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi!';
    } elseif (strlen($username) < USERNAME_MIN || strlen($username) > USERNAME_MAX) {
        $error = 'Username harus ' . USERNAME_MIN . '-' . USERNAME_MAX . ' karakter.';
    } elseif (!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
        $error = 'Username hanya boleh berisi huruf, angka, dan garis bawah (_).';
    } elseif (strlen($password) < PASSWORD_MIN) {
        $error = 'Password minimal harus ' . PASSWORD_MIN . ' karakter!';
    } elseif (strlen($password) > PASSWORD_MAX) {
        $error = 'Password maksimal ' . PASSWORD_MAX . ' karakter.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Role dikunci langsung menjadi 'santri' di dalam query SQL
            $stmt = $pdo->prepare(
                "INSERT INTO users (username, password, role) VALUES (:username, :password, 'santri')"
            );
            $stmt->execute([
                ':username' => $username,
                ':password' => $hash,
            ]);

            redirect('login.php?registered=1');
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null) == 1062) {
                $error = 'Username sudah terdaftar, silakan pilih username lain.';
            } else {
                error_log('Registrasi gagal: ' . $e->getMessage());
                $error = 'Terjadi kesalahan sistem saat mendaftar.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun Baru - Aplikasi Santri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container">
    <div class="row justify-content-center align-items-center min-vh-100 py-4">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 text-center mb-1">Registrasi Santri</h1>
                    <p class="text-center text-muted small mb-4">Buat akun baru untuk Aplikasi Santri</p>

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
                    <?php endif; ?>

                    <form action="registrasi.php" method="POST" novalidate>
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                   value="<?= e($username) ?>" required maxlength="<?= USERNAME_MAX ?>"
                                   autocomplete="username">
                            <div class="form-text">3-30 karakter: huruf, angka, atau garis bawah (_).</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   required minlength="<?= PASSWORD_MIN ?>" maxlength="<?= PASSWORD_MAX ?>"
                                   autocomplete="new-password">
                            <div class="form-text">Minimal <?= PASSWORD_MIN ?> karakter.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Daftar Akun</button>
                    </form>

                    <p class="text-center small mt-3 mb-0">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>
