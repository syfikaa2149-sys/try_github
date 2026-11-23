<?php
// verifikasi_password.php — memeriksa kecocokan password dengan hash di database.
const PESAN_LOGIN_GAGAL = 'Username atau kata sandi yang Anda masukkan salah!';
// Hash tiruan dipakai ketika username tidak ditemukan supaya waktu proses kira-kira sama.
const HASH_TIRUAN = '$2y$10$SXx8h/xC43cItmWeZ5lmSO/jGJ.RWgsT4IzKLi/3dI7vsg.C6ksg6';

function verifikasi_password(?array $user, string $passwordInput): bool
{
    $hash = $user['password'] ?? HASH_TIRUAN;
    $cocok = password_verify($passwordInput, $hash);

    return $user !== null && $cocok;
}
