<?php
require 'fungsi.php'; require 'koneksi.php'; wajib_login();
$id = (int)($_GET['id'] ?? 0);
$sql = "SELECT p.*, u.nama, l.nama_layanan, l.tarif_per_kg FROM pesanan p
        JOIN users u ON u.id=p.user_id JOIN layanan l ON l.id=p.layanan_id WHERE p.id=?";
if ($_SESSION['role'] === 'pelanggan') {
  // pelanggan hanya boleh melihat nota miliknya sendiri
  $st = $db->prepare($sql . " AND p.user_id=?");
  $st->bind_param('ii', $id, $_SESSION['user_id']);
} else {
  $st = $db->prepare($sql);
  $st->bind_param('i', $id);
}
$st->execute();
$p = $st->get_result()->fetch_assoc();
if (!$p) { http_response_code(404); die('Nota tidak ditemukan.'); }
$kembali = $_SESSION['role'] === 'pelanggan' ? 'pesanan_saya.php' : 'pesanan_admin.php';
include 'header.php'; ?>
<div class="kotak nota">
  <h2><?= e(NAMA_USAHA) ?></h2>
  <p class="bantu">Nota pesanan No. <?= e('LNK-' . str_pad($p['id'], 5, '0', STR_PAD_LEFT)) ?> &middot; <?= e($p['tanggal']) ?></p>
  <table class="tabel-nota">
    <tr><th>Pelanggan</th><td><?= e($p['nama']) ?></td></tr>
    <tr><th>Layanan</th><td><?= e($p['nama_layanan']) ?> (<?= rupiah($p['tarif_per_kg']) ?>/kg)</td></tr>
    <tr><th>Berat</th><td><?= $p['berat_final'] !== null ? number_format((float)$p['berat_final'],1,'.','') . ' kg' : number_format((float)$p['berat_perkiraan'],1,'.','') . ' kg (perkiraan)' ?></td></tr>
    <?php if ($p['pakaian_khusus']): ?><tr><th>Isi cucian</th><td><?= e($p['pakaian_khusus']) ?></td></tr><?php endif; ?>
    <?php if ($p['catatan']): ?><tr><th>Catatan</th><td><?= e($p['catatan']) ?></td></tr><?php endif; ?>
    <tr><th>Status</th><td><?= lencana($p['status']) ?></td></tr>
    <tr class="total"><th>Total biaya</th><td><?= $p['total_biaya'] !== null ? rupiah($p['total_biaya']) : 'Dihitung setelah cucian ditimbang' ?></td></tr>
  </table>
  <p class="noprint"><button type="button" onclick="window.print()">Cetak nota</button> <a href="<?= $kembali ?>">Kembali</a></p>
</div>
<?php include 'footer.php'; ?>
