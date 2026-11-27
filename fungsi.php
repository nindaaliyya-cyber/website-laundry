<?php
// File ini harus dimuat sebelum koneksi.php agar pengaturan sesi diterapkan lebih dahulu.
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? '1' : '0');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

const NAMA_USAHA = 'Laundry Ni-Ki';

function e($nilai): string {
    return htmlspecialchars((string)($nilai ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function wajib_login(): void {
    if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
        header('Location: login.php');
        exit;
    }
}

function wajib_role(string $role): void {
    wajib_login();
    if (!hash_equals($role, (string)$_SESSION['role'])) {
        http_response_code(403);
        exit('Akses ditolak. Anda tidak memiliki izin untuk membuka halaman ini.');
    }
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_cek(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $kirim = (string)($_POST['csrf'] ?? '');
        if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $kirim)) {
            http_response_code(419);
            exit('Formulir kedaluwarsa atau tidak valid. Muat ulang halaman, lalu coba lagi.');
        }
    }
}

function rupiah($nilai): string {
    return 'Rp ' . number_format((int)$nilai, 0, ',', '.');
}

function flash_set(string $teks, string $jenis = 'benar'): void {
    $_SESSION['flash'] = [$teks, $jenis];
}

function flash_tampil(): string {
    if (empty($_SESSION['flash']) || !is_array($_SESSION['flash'])) return '';
    [$teks, $jenis] = $_SESSION['flash'];
    unset($_SESSION['flash']);
    $kelas = in_array($jenis, ['benar', 'salah'], true) ? $jenis : 'benar';
    return '<p class="' . $kelas . '">' . e($teks) . '</p>';
}
