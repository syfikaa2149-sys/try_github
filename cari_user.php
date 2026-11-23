<?php
// cari_user.php — mencari pengguna berdasarkan username.
function cari_user(PDO $pdo, string $username): ?array
{
    $stmt = $pdo->prepare(
        'SELECT id, username, password, role FROM users WHERE username = :username LIMIT 1'
    );
    $stmt->execute([':username' => $username]);

    $user = $stmt->fetch();
    return $user === false ? null : $user;
}
