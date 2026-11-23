<?php
// admin.php — halaman khusus admin. Pemeriksaan role dilakukan di SERVER oleh require_role().
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/koneksi.php';
require_role('admin');   // belum login -> login.php ; bukan admin -> HTTP 403

$username = $_SESSION['username'] ?? 'Admin';

// Daftar pengguna (hanya baca). Kolom password TIDAK pernah diambil.
$daftarUser = [];
$errorDb    = '';
try {
    $daftarUser = $pdo->query('SELECT id, username, role, created_at FROM users ORDER BY id ASC LIMIT 100')->fetchAll();
} catch (PDOException $e) {
    error_log('Admin gagal memuat daftar pengguna: ' . $e->getMessage());
    $errorDb = 'Daftar pengguna tidak dapat dimuat.';
}

$jumlahAdmin  = count(array_filter($daftarUser, fn($u) => $u['role'] === 'admin'));
$jumlahSantri = count($daftarUser) - $jumlahAdmin;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="admin.php">Aplikasi Santri <span class="badge bg-danger ms-1">Admin</span></a>
        <div class="d-flex align-items-center gap-2">
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
            <form action="logout.php" method="POST" class="m-0">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-light btn-sm">Keluar</button>
            </form>
        </div>
    </div>
</nav>

<main class="container py-4">
    <div class="alert alert-warning" role="alert">
        Halaman ini <strong>hanya dapat diakses oleh admin</strong>. Pemeriksaan hak akses dilakukan di server.
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <h1 class="h3">Dashboard Admin</h1>
            <p class="text-muted mb-0">Selamat datang, <?= e($username) ?>. Anda masuk sebagai <span class="badge bg-danger">admin</span>.</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <strong>Daftar Pengguna</strong>
            <span class="text-muted small ms-2"><?= $jumlahAdmin ?> admin, <?= $jumlahSantri ?> santri</span>
        </div>
        <div class="card-body p-0">
            <?php if ($errorDb !== ''): ?>
                <div class="alert alert-danger m-3" role="alert"><?= e($errorDb) ?></div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr><th>ID</th><th>Username</th><th>Role</th><th>Terdaftar</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($daftarUser as $u): ?>
                            <tr>
                                <td><?= e($u['id']) ?></td>
                                <td><?= e($u['username']) ?></td>
                                <td><span class="badge <?= $u['role'] === 'admin' ? 'bg-danger' : 'bg-success' ?>"><?= e($u['role']) ?></span></td>
                                <td><?= e($u['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
</body>
</html>
