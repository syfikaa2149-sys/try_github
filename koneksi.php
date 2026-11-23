<?php
// koneksi.php — koneksi database memakai PDO.
$dbHost    = '127.0.0.1';
$dbName    = 'latihan_santri';
$dbUser    = 'root';
$dbPass    = '';
$dbCharset = 'utf8mb4';

// Susun string DSN (Data Source Name)
$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=$dbCharset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // error database menjadi exception
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // hasil query berupa array asosiatif
    PDO::ATTR_EMULATE_PREPARES   => false,                    // prepared statement asli dari MySQL
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    // Detail error hanya ditulis ke log server, TIDAK ditampilkan ke pengunjung.
    error_log('Koneksi database gagal: ' . $e->getMessage());
    http_response_code(500);
    die('Koneksi basis data gagal: Silakan hubungi administrator.');
}

// Hapus kredensial dari memori agar tidak ikut terbawa ke file lain.
unset($dbHost, $dbName, $dbUser, $dbPass, $dbCharset, $dsn, $options);
