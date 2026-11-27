<?php
require 'fungsi.php'; require 'koneksi.php'; wajib_login();
$uid = (int)$_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_cek();
  $aksi = $_POST['aksi'] ?? '';
  if ($aksi === 'nama') {
    $nama = trim($_POST['nama'] ?? '');
    if ($nama === '' || mb_strlen($nama) > 100) {
      flash_set('Nama wajib diisi dan maksimal 100 karakter.', 'salah');
    } else {
      $st = $db->prepare("UPDATE users SET nama=? WHERE id=?");
      $st->bind_param('si', $nama, $uid); $st->execute();
      $_SESSION['nama'] = $nama;
      flash_set('Nama diperbarui.');
    }
  } elseif ($aksi === 'sandi') {
    $lama = $_POST['lama'] ?? ''; $baru = $_POST['baru'] ?? ''; $ulang = $_POST['ulang'] ?? '';
    $st = $db->prepare("SELECT password FROM users WHERE id=?");
    $st->bind_param('i', $uid); $st->execute();
    $u = $st->get_result()->fetch_assoc();
    if (!$u || !password_verify($lama, $u['password'])) {
      flash_set('Kata sandi lama salah.', 'salah');
    } elseif (strlen($baru) < 8) {
      flash_set('Kata sandi baru minimal 8 karakter.', 'salah');
    } elseif ($baru !== $ulang) {
      flash_set('Konfirmasi kata sandi tidak sama.', 'salah');
    } else {
      $hash = password_hash($baru, PASSWORD_DEFAULT);
      $st = $db->prepare("UPDATE users SET password=? WHERE id=?");
      $st->bind_param('si', $hash, $uid); $st->execute();
      session_regenerate_id(true);
      flash_set('Kata sandi diperbarui.');
    }
  }
  header('Location: profil.php'); exit;
}
$st = $db->prepare("SELECT nama,email,role FROM users WHERE id=?");
$st->bind_param('i', $uid); $st->execute();
$u = $st->get_result()->fetch_assoc();
if (!$u) { session_destroy(); header('Location: login.php'); exit; }
include 'header.php'; ?>
<h2>Profil</h2>
<?= flash_tampil() ?>
<div class="kotak"><h2>Data akun</h2>
<p>Email: <strong><?= e($u['email']) ?></strong> &middot; Peran: <strong><?= e(ucfirst($u['role'])) ?></strong></p>
<form method="post"><?= csrf_field() ?><input type="hidden" name="aksi" value="nama">
  Nama <input name="nama" value="<?= e($u['nama']) ?>" maxlength="100" required>
  <button>Simpan nama</button>
</form></div>
<div class="kotak"><h2>Ubah kata sandi</h2>
<form method="post"><?= csrf_field() ?><input type="hidden" name="aksi" value="sandi">
  Kata sandi lama <input name="lama" type="password" required>
  Kata sandi baru (minimal 8 karakter) <input name="baru" type="password" minlength="8" required>
  Ulangi kata sandi baru <input name="ulang" type="password" minlength="8" required>
  <button>Ubah kata sandi</button>
</form></div>
<?php include 'footer.php'; ?>
