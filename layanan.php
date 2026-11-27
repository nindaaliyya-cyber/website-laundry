<?php
require 'fungsi.php'; require 'koneksi.php'; wajib_role('admin');
$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_cek();
  $aksi = $_POST['aksi'];
  if ($aksi === 'tambah' || $aksi === 'ubah') {
    $nama = trim($_POST['nama_layanan']); $tarif = (int)$_POST['tarif_per_kg'];
    if ($nama === '' || $tarif <= 0) { $pesan = 'Nama dan tarif wajib diisi.'; }
    elseif ($aksi === 'tambah') {
      $st = $db->prepare("INSERT INTO layanan (nama_layanan,tarif_per_kg) VALUES (?,?)");
      $st->bind_param('si', $nama, $tarif); $st->execute();
    } else {
      $st = $db->prepare("UPDATE layanan SET nama_layanan=?, tarif_per_kg=? WHERE id=?");
      $st->bind_param('sii', $nama, $tarif, $_POST['id']); $st->execute();
    }
  } elseif ($aksi === 'hapus') {
    $st = $db->prepare("DELETE FROM layanan WHERE id=?");
    $st->bind_param('i', $_POST['id']);
    if (!$st->execute()) $pesan = 'Layanan sudah dipakai pesanan, tidak bisa dihapus.';
  }
}
$edit = null;
if (isset($_GET['edit'])) {
  $st = $db->prepare("SELECT * FROM layanan WHERE id=?");
  $st->bind_param('i', $_GET['edit']); $st->execute();
  $edit = $st->get_result()->fetch_assoc();
}
$daftar = $db->query("SELECT * FROM layanan ORDER BY id");
include 'header.php'; ?>
<div class="kotak"><h2><?= $edit ? 'Ubah' : 'Tambah' ?> Layanan</h2>
<?php if ($pesan): ?><p class="salah"><?= e($pesan) ?></p><?php endif; ?>
<form method="post"><?= csrf_field() ?>
  <input type="hidden" name="aksi" value="<?= $edit ? 'ubah' : 'tambah' ?>">
  <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
  Nama layanan <input name="nama_layanan" value="<?= e($edit['nama_layanan'] ?? '') ?>" required>
  Tarif per kg (Rp) <input name="tarif_per_kg" type="number" min="1" value="<?= e($edit['tarif_per_kg'] ?? '') ?>" required>
  <button>Simpan</button>
</form></div>
<table><tr><th>Layanan</th><th>Tarif/kg</th><th>Aksi</th></tr>
<?php while ($l = $daftar->fetch_assoc()): ?>
<tr><td><?= e($l['nama_layanan']) ?></td><td>Rp <?= number_format($l['tarif_per_kg'],0,',','.') ?></td>
<td><a href="layanan.php?edit=<?= (int)$l['id'] ?>">Ubah</a>
<form method="post" style="display:inline" onsubmit="return confirm('Hapus layanan ini?')"><?= csrf_field() ?>
<input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
<button class="hapus">Hapus</button></form></td></tr>
<?php endwhile; ?></table>
<?php include 'footer.php'; ?>
