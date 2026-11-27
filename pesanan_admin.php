<?php
require 'fungsi.php'; require 'koneksi.php'; wajib_login();
if (!in_array($_SESSION['role'], ['admin','karyawan'], true)) { http_response_code(403); die('Akses ditolak.'); }
$admin = $_SESSION['role'] === 'admin';
$status_ok = ['menunggu','diterima','dicuci','disetrika','siap diambil','selesai'];
$perlu_berat = ['dicuci','disetrika','siap diambil','selesai'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_cek();
  $id = (int)($_POST['id'] ?? 0);
  $aksi = $_POST['aksi'] ?? 'simpan';
  if ($aksi === 'hapus' && $admin) {
    $st = $db->prepare("DELETE FROM pesanan WHERE id=?");
    $st->bind_param('i', $id); $st->execute();
    flash_set('Pesanan dihapus.');
  } elseif ($aksi === 'simpan') {
    $status = $_POST['status'] ?? '';
    $berat = round((float)($_POST['berat_final'] ?? 0), 1);
    if (!in_array($status, $status_ok, true)) {
      flash_set('Status tidak valid.', 'salah');
    } elseif ($berat < 0 || $berat > 999) {
      flash_set('Berat final harus antara 0 dan 999 kg.', 'salah');
    } else {
      $st = $db->prepare("SELECT berat_final FROM pesanan WHERE id=?");
      $st->bind_param('i', $id); $st->execute();
      $lama = $st->get_result()->fetch_assoc();
      if (!$lama) {
        flash_set('Pesanan tidak ditemukan.', 'salah');
      } elseif ($berat <= 0 && $lama['berat_final'] === null && in_array($status, $perlu_berat, true)) {
        flash_set('Isi berat final terlebih dahulu sebelum status diubah ke "' . $status . '".', 'salah');
      } else {
        if ($berat > 0) {
          $st = $db->prepare("UPDATE pesanan p JOIN layanan l ON l.id=p.layanan_id
            SET p.status=?, p.berat_final=?, p.total_biaya=ROUND(? * l.tarif_per_kg) WHERE p.id=?");
          $st->bind_param('sddi', $status, $berat, $berat, $id);
        } else {
          $st = $db->prepare("UPDATE pesanan SET status=? WHERE id=?");
          $st->bind_param('si', $status, $id);
        }
        $st->execute();
        flash_set('Pesanan diperbarui.');
      }
    }
  }
  header('Location: pesanan_admin.php?' . http_build_query(['status' => $_POST['f_status'] ?? '', 'q' => $_POST['f_q'] ?? '']));
  exit;
}

$fs = $_GET['status'] ?? ''; $q = trim($_GET['q'] ?? '');
$w = []; $t = ''; $par = [];
if ($fs !== '' && in_array($fs, $status_ok, true)) { $w[] = 'p.status=?'; $t .= 's'; $par[] = $fs; } else { $fs = ''; }
if ($q !== '') { $w[] = 'u.nama LIKE ?'; $t .= 's'; $par[] = '%' . $q . '%'; }
$sql = "SELECT p.*, u.nama, l.nama_layanan FROM pesanan p JOIN users u ON u.id=p.user_id JOIN layanan l ON l.id=p.layanan_id"
     . ($w ? ' WHERE ' . implode(' AND ', $w) : '') . " ORDER BY p.id DESC";
$st = $db->prepare($sql);
if ($par) $st->bind_param($t, ...$par);
$st->execute();
$hasil = $st->get_result();
include 'header.php'; ?>
<h2><?= $admin ? 'Semua Pesanan' : 'Kelola Pesanan' ?></h2>
<?= flash_tampil() ?>
<form method="get" class="filter">
  <select name="status" aria-label="Filter status"><option value="">Semua status</option>
  <?php foreach ($status_ok as $s): ?><option <?= $s === $fs ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select>
  <input name="q" value="<?= e($q) ?>" placeholder="Cari nama pelanggan" aria-label="Cari nama pelanggan">
  <button>Cari</button> <a href="pesanan_admin.php">Atur ulang</a>
</form>
<table><tr><th>Pelanggan</th><th>Layanan</th><th>Catatan</th><th>Berat final (kg)</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
<?php if ($hasil->num_rows === 0): ?><tr><td colspan="7">Tidak ada pesanan.</td></tr><?php endif; ?>
<?php while ($p = $hasil->fetch_assoc()): $f = 'f' . (int)$p['id']; ?>
<tr>
<td><form id="<?= $f ?>" method="post"></form>
  <input form="<?= $f ?>" type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <input form="<?= $f ?>" type="hidden" name="id" value="<?= (int)$p['id'] ?>">
  <input form="<?= $f ?>" type="hidden" name="f_status" value="<?= e($fs) ?>">
  <input form="<?= $f ?>" type="hidden" name="f_q" value="<?= e($q) ?>">
  <?= e($p['nama']) ?></td>
<td><?= e($p['nama_layanan']) ?><?php if ($p['pakaian_khusus']): ?><br><?php foreach (explode(', ', $p['pakaian_khusus']) as $k): ?><span class="khusus"><?= e($k) ?></span> <?php endforeach; ?><?php endif; ?></td>
<td><?= e($p['catatan']) ?> <small>(perkiraan <?= number_format((float)$p['berat_perkiraan'],1,'.','') ?> kg)</small></td>
<td><input form="<?= $f ?>" type="number" name="berat_final" step="0.1" min="0" max="999" style="width:85px" value="<?= $p['berat_final'] !== null ? number_format((float)$p['berat_final'],1,'.','') : '' ?>"></td>
<td><?= $p['total_biaya'] !== null ? rupiah($p['total_biaya']) : '-' ?></td>
<td><select form="<?= $f ?>" name="status"><?php foreach ($status_ok as $s): ?>
<option <?= $s === $p['status'] ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select></td>
<td class="aksi"><button form="<?= $f ?>" name="aksi" value="simpan">Simpan</button>
<a href="nota.php?id=<?= (int)$p['id'] ?>">Nota</a>
<?php if ($admin): ?><button form="<?= $f ?>" name="aksi" value="hapus" class="hapus" formnovalidate onclick="return confirm('Hapus pesanan ini?')">Hapus</button><?php endif; ?></td>
</tr>
<?php endwhile; ?></table>
<?php include 'footer.php'; ?>
