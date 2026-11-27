<?php
// Salin file ini menjadi koneksi.php, lalu sesuaikan nilainya.
$host = '127.0.0.1';
$user = 'root';
$pass = 'Ninda-12';
$name = 'laundry_db';
$port = 3306;

mysqli_report(MYSQLI_REPORT_OFF);
$db = new mysqli($host, $user, $pass, $name, $port);
if ($db->connect_errno) {
    http_response_code(500);
    exit('Koneksi database gagal. Periksa konfigurasi koneksi.php dan pastikan MySQL aktif.');
}
$db->set_charset('utf8mb4');
