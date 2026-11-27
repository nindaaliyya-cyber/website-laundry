<?php
require 'fungsi.php'; require 'koneksi.php'; wajib_role('admin');
$pesan = ''; $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_cek();
  if ($_POST['aksi'] === 'tambah') {
    $nama = trim($_POST['nama']); $email = strtolower(trim($_POST['email'])); $pass = $_POST['password'];
    if ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 8) {
      $pesan = 'Isi nama, email yang valid, dan kata sandi minimal 8 karakter.';
    } else {
      $hash = password_hash($pass, PASSWORD_DEFAULT);
      $st = $db->prepare("INSERT INTO users (nama,email,password,role) VALUES (?,?,?,'karyawan')");
      $st->bind_param('sss', $nama, $email, $hash);
      if ($st->execute()) $ok = 'Akun karyawan dibuat.'; else $pesan = 'Email sudah terdaftar.';
    }
  } elseif ($_POST['aksi'] === 'hapus') {
    $st = $db->prepare("DELETE FROM users WHERE id=? AND role='karyawan'");
    $st->bind_param('i', $_POST['id']); $st->execute();
  }
}
$daftar = $db->query("SELECT id,nama,email,role FROM users ORDER BY FIELD(role,'admin','karyawan','pelanggan'), nama");
include 'header.php'; ?>
<div class="kotak"><h2>Tambah Karyawan</h2>
<?php if ($pesan): ?><p class="salah"><?= e($pesan) ?></p><?php endif; ?>
<?php if ($ok): ?><p class="benar"><?= e($ok) ?></p><?php endif; ?>
<form method="post"><?= csrf_field() ?><input type="hidden" name="aksi" value="tambah">
  Nama <input name="nama" required>
  Email <input name="email" type="email" required>
  Password awal <input name="password" type="password" required>
  <button>Buat akun karyawan</button>
</form></div>
<table><tr><th>Nama</th><th>Email</th><th>Peran</th><th></th></tr>
<?php while ($u = $daftar->fetch_assoc()): ?>
<tr><td><?= e($u['nama']) ?></td><td><?= e($u['email']) ?></td><td><?= e(ucfirst($u['role'])) ?></td>
<td><?php if ($u['role'] === 'karyawan'): ?>
<form method="post" onsubmit="return confirm('Hapus akun karyawan ini?')"><?= csrf_field() ?><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><button class="hapus">Hapus</button></form>
<?php endif; ?></td></tr>
<?php endwhile; ?></table>
<?php include 'footer.php'; ?>
