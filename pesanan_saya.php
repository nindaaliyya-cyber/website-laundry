<?php
require 'fungsi.php'; require 'koneksi.php'; wajib_role('pelanggan');
$uid = (int)$_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_cek();
  if (($_POST['aksi'] ?? '') === 'batal') {
    // hanya pesanan milik sendiri yang belum diproses
    $st = $db->prepare("DELETE FROM pesanan WHERE id=? AND user_id=? AND status='menunggu'");
    $st->bind_param('ii', $_POST['id'], $uid);
    $st->execute();
    if ($st->affected_rows > 0) flash_set('Pesanan dibatalkan.');
    else flash_set('Pesanan tidak dapat dibatalkan karena sudah diproses.', 'salah');
  }
  header('Location: pesanan_saya.php'); exit;
}
// user_id selalu dari session, bukan dari URL
$st = $db->prepare("SELECT p.*, l.nama_layanan FROM pesanan p JOIN layanan l ON l.id=p.layanan_id WHERE p.user_id=? ORDER BY p.id DESC");
$st->bind_param('i', $uid); $st->execute();
$hasil = $st->get_result();
include 'header.php'; ?>
<h2>Pesanan Saya</h2>
<?= flash_tampil() ?>
<table><tr><th>Tanggal</th><th>Layanan</th><th>Berat</th><th>Total</th><th>Status</th><th></th></tr>
<?php if ($hasil->num_rows === 0): ?><tr><td colspan="6">Belum ada pesanan. <a href="pesanan_buat.php">Buat pesanan</a></td></tr><?php endif; ?>
<?php while ($p = $hasil->fetch_assoc()): ?>
<tr><td><?= e($p['tanggal']) ?></td>
<td><?= e($p['nama_layanan']) ?><?php if ($p['pakaian_khusus']): ?><br><?php foreach (explode(', ', $p['pakaian_khusus']) as $k): ?><span class="khusus"><?= e($k) ?></span> <?php endforeach; ?><?php endif; ?></td>
<td><?= $p['berat_final'] !== null ? number_format((float)$p['berat_final'],1,'.','').' kg' : number_format((float)$p['berat_perkiraan'],1,'.','').' kg (perkiraan)' ?></td>
<td><?= $p['total_biaya'] !== null ? rupiah($p['total_biaya']) : '-' ?></td>
<td><?= lencana($p['status']) ?></td>
<td><a href="nota.php?id=<?= (int)$p['id'] ?>">Nota</a>
<?php if ($p['status'] === 'menunggu'): ?>
<form method="post" class="kecil" onsubmit="return confirm('Batalkan pesanan ini?')"><?= csrf_field() ?>
<input type="hidden" name="aksi" value="batal"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><button class="hapus">Batalkan</button></form>
<?php endif; ?></td></tr>
<?php endwhile; ?></table>
<?php include 'footer.php'; ?>
