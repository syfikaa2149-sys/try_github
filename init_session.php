<?php
// init_session.php — DINONAKTIFKAN.
// Pada kode awal, membuka file ini langsung membuat login otomatis sebagai user_id 1 tanpa password.
// Karena berbahaya, sekarang file ini langsung menolak akses (HTTP 403).

http_response_code(403);
exit('File demonstrasi ini sudah dinonaktifkan. Silakan login melalui login.php.');
