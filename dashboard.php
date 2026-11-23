<?php
// dashboard.php — halaman untuk semua pengguna yang sudah login (santri maupun admin).
require_once __DIR__ . '/auth.php';
require_login();   // belum login -> dialihkan ke login.php

$username = $_SESSION['username'] ?? 'Santri';
$role     = $_SESSION['role'] ?? 'santri';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Santri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Aplikasi Santri</a>
        <div class="d-flex align-items-center gap-2">
            <?php if ($role === 'admin'): ?>
                <a href="admin.php" class="btn btn-warning btn-sm">Halaman Admin</a>
            <?php endif; ?>
            <form action="logout.php" method="POST" class="m-0">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-light btn-sm">Keluar</button>
            </form>
        </div>
    </div>
</nav>

<main class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h1 class="h3">Selamat Datang, <?= e($username) ?>!</h1>
            <p class="text-muted">Ini adalah halaman yang hanya bisa dibuka setelah Anda berhasil login.</p>
            <table class="table table-borderless w-auto mb-0">
                <tr>
                    <th scope="row" class="pe-4">Username</th>
                    <td><?= e($username) ?></td>
                </tr>
                <tr>
                    <th scope="row" class="pe-4">Role</th>
                    <td>
                        <span class="badge <?= $role === 'admin' ? 'bg-danger' : 'bg-success' ?>"><?= e($role) ?></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</main>
</body>
</html>
