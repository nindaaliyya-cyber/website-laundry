<?php
require 'fungsi.php'; require 'koneksi.php'; wajib_login();
include 'header.php';
$peran = $_SESSION['role'];
$uid = (int)$_SESSION['user_id'];
$keterangan = ['admin' => 'Setiap langkah pengelolaan yang baik menjadi awal dari pelayanan
    yang luar biasa. Terus kembangkan Laundry Ni-Ki menjadi lebih baik!',
  'karyawan' => 'Setiap pakaian yang dirawat dengan sepenuh hati adalah bentuk
    pelayanan terbaik. Tetap semangat dan berikan hasil terbaik hari ini!',
  'pelanggan' => ' Percayakan kebutuhan laundry Anda kepada kami.
    Kami siap memberikan pelayanan terbaik dengan penuh perhatian
    dan sepenuh hati.'][$peran];
?>
<section class="hero">
  <div>
    <p class="salam"><span id="salam">Selamat datang</span>, <?= e($_SESSION['nama']) ?></p>
    <p class="ket"><?= e($keterangan) ?></p>
  </div>
  <div class="jam"><div id="tanggal"></div><div id="waktu"></div></div>
</section>
<script>
(function(){
  function tik(){
    var d=new Date(), h=d.getHours();
    var s=h<11?'Selamat pagi':h<15?'Selamat siang':h<18?'Selamat sore':'Selamat malam';
    document.getElementById('salam').textContent=s;
    document.getElementById('tanggal').textContent=d.toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
    document.getElementById('waktu').textContent=d.toLocaleTimeString('id-ID',{hour12:false}).replace(/\./g,':');
  }
  tik(); setInterval(tik,1000);
})();
</script>
<?php
function hitung_status($db) {
  $j = []; $r = $db->query("SELECT status, COUNT(*) jml FROM pesanan GROUP BY status");
  while ($b = $r->fetch_assoc()) $j[$b['status']] = (int)$b['jml'];
  return $j;
}
function satu($db, $sql) { $r = $db->query($sql)->fetch_row(); return (int)$r[0]; }

if ($peran === 'admin') {
  $j = hitung_status($db);
  echo '<h2>Ringkasan pesanan</h2><div class="deret">';
  foreach (['menunggu','diterima','dicuci','disetrika','siap diambil','selesai'] as $s)
    echo '<div class="angka">'.lencana($s).'<strong>'.($j[$s] ?? 0).'</strong></div>';
  echo '</div><h2>Usaha</h2><div class="deret">';
  echo '<div class="angka">Pelanggan<strong>'.satu($db,"SELECT COUNT(*) FROM users WHERE role='pelanggan'").'</strong></div>';
  echo '<div class="angka">Karyawan<strong>'.satu($db,"SELECT COUNT(*) FROM users WHERE role='karyawan'").'</strong></div>';
  echo '<div class="angka">Pendapatan selesai<strong>Rp '.number_format(satu($db,"SELECT COALESCE(SUM(total_biaya),0) FROM pesanan WHERE status='selesai'"),0,',','.').'</strong></div></div>';
} elseif ($peran === 'karyawan') {
  $j = hitung_status($db);
  echo '<h2>Antrian kerja</h2><div class="deret">';
  foreach (['menunggu','diterima','dicuci','disetrika','siap diambil'] as $s)
    echo '<div class="angka">'.lencana($s).'<strong>'.($j[$s] ?? 0).'</strong></div>';
  echo '</div><h2>Perlu ditangani</h2><table><tr><th>Pelanggan</th><th>Layanan</th><th>Status</th></tr>';
  $r = $db->query("SELECT u.nama, l.nama_layanan, p.status FROM pesanan p JOIN users u ON u.id=p.user_id JOIN layanan l ON l.id=p.layanan_id WHERE p.status<>'selesai' ORDER BY p.id DESC LIMIT 5");
  if ($r->num_rows === 0) echo '<tr><td colspan="3">Tidak ada pesanan yang perlu ditangani.</td></tr>';
  while ($p = $r->fetch_assoc()) echo '<tr><td>'.e($p['nama']).'</td><td>'.e($p['nama_layanan']).'</td><td>'.lencana($p['status']).'</td></tr>';
  echo '</table><a href="pesanan_admin.php">Buka semua pesanan</a>';
} else {
  $st = $db->prepare("SELECT COUNT(*) FROM pesanan WHERE user_id=? AND status<>'selesai'");
  $st->bind_param('i', $uid); $st->execute(); $st->bind_result($aktif); $st->fetch(); $st->close();
  echo '<div class="deret"><div class="angka">Pesanan diproses<strong>'.(int)$aktif.'</strong></div></div>';
  echo '<h2>Pesanan terakhir</h2><table><tr><th>Tanggal</th><th>Layanan</th><th>Status</th></tr>';
  $st = $db->prepare("SELECT p.tanggal, l.nama_layanan, p.status FROM pesanan p JOIN layanan l ON l.id=p.layanan_id WHERE p.user_id=? ORDER BY p.id DESC LIMIT 3");
  $st->bind_param('i', $uid); $st->execute(); $r = $st->get_result();
  if ($r->num_rows === 0) echo '<tr><td colspan="3">Belum ada pesanan.</td></tr>';
  while ($p = $r->fetch_assoc()) echo '<tr><td>'.e($p['tanggal']).'</td><td>'.e($p['nama_layanan']).'</td><td>'.lencana($p['status']).'</td></tr>';
  echo '</table><a href="pesanan_buat.php">Buat pesanan baru</a>';
}
include 'footer.php';
