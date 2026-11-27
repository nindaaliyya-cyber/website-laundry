<?php
require 'fungsi.php'; require 'koneksi.php';
$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_cek();
  $nama = trim($_POST['nama']); $email = strtolower(trim($_POST['email'])); $pass = $_POST['password'];
  if ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 8) {
    $pesan = 'Isi nama, email yang valid, dan kata sandi minimal 8 karakter.';
  } else {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $st = $db->prepare("INSERT INTO users (nama,email,password,role) VALUES (?,?,?,'pelanggan')");
    $st->bind_param('sss', $nama, $email, $hash);
    if ($st->execute()) { header('Location: login.php?daftar=ok'); exit; }
    $pesan = 'Email sudah terdaftar.';
  }
}
include 'header.php'; ?>
<div class="kotak"><h2>Daftar Pelanggan</h2>
<?php if ($pesan): ?><p class="salah"><?= e($pesan) ?></p><?php endif; ?>
<form method="post"><?= csrf_field() ?>
  Nama <input name="nama" required>
  Email <input name="email" type="email" required>
  Password <input name="password" type="password" required>
  <button>Daftar</button>
</form><p>Sudah punya akun? <a href="login.php">Masuk</a></p></div>
<?php include 'footer.php'; ?>
