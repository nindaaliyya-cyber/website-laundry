<?php
require 'fungsi.php'; require 'koneksi.php';
if (isset($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }
$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_cek();
  if (($_SESSION['kunci'] ?? 0) > time()) {
    $pesan = 'Terlalu banyak percobaan gagal. Coba lagi dalam ' . ($_SESSION['kunci'] - time()) . ' detik.';
  } else {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $st = $db->prepare("SELECT id,nama,password,role FROM users WHERE email=?");
    $st->bind_param('s', $email);
    $st->execute();
    $u = $st->get_result()->fetch_assoc();
    if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
      session_regenerate_id(true);
      $_SESSION['user_id'] = $u['id'];
      $_SESSION['nama'] = $u['nama'];
      $_SESSION['role'] = $u['role'];
      unset($_SESSION['gagal'], $_SESSION['kunci']);
      header('Location: dashboard.php'); exit;
    }
    $_SESSION['gagal'] = ($_SESSION['gagal'] ?? 0) + 1;
    if ($_SESSION['gagal'] >= 5) { $_SESSION['kunci'] = time() + 60; $_SESSION['gagal'] = 0; }
    $pesan = 'Email atau kata sandi salah.';
  }
}
include 'header.php'; ?>
<div class="kotak"><h2>Masuk</h2>
<?php if (isset($_GET['daftar'])): ?><p class="benar">Akun dibuat. Silakan masuk.</p><?php endif; ?>
<?php if ($pesan): ?><p class="salah"><?= e($pesan) ?></p><?php endif; ?>
<form method="post"><?= csrf_field() ?>
  Email <input name="email" type="email" required>
  Kata sandi <input name="password" type="password" required>
  <button>Masuk</button>
</form><p>Belum punya akun? <a href="register.php">Daftar</a></p></div>
<?php include 'footer.php'; ?>
