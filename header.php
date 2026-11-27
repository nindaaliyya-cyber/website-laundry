<?php
/* di-include setelah koneksi.php */
if (!function_exists('lencana')) {
  function lencana($s) {
    $k = ['menunggu'=>'s-menunggu','diterima'=>'s-diterima','dicuci'=>'s-dicuci','disetrika'=>'s-disetrika','siap diambil'=>'s-siap','selesai'=>'s-selesai'];
    return '<span class="lencana '.($k[$s] ?? '').'">'.e($s).'</span>';
  }
}
$halaman = basename($_SERVER['SCRIPT_NAME']);
$masuk = isset($_SESSION['user_id']);
function menu($file, $teks) {
  global $halaman;
  return '<a href="'.$file.'"'.($halaman === $file ? ' class="aktif"' : '').'>'.$teks.'</a>';
}
$logo = '<svg viewBox="0 0 32 32" aria-hidden="true"><rect x="3" y="3" width="26" height="26" rx="7" fill="#1b7f93"/><circle cx="16" cy="17" r="8" fill="#fff"/><path d="M9.5 17c2.2-2 4.3-2 6.5 0s4.3 2 6.5 0v3a6.5 6.5 0 0 1-13 0z" fill="#9fd8e1"/><circle cx="8" cy="7.5" r="1.2" fill="#fff"/><circle cx="12" cy="7.5" r="1.2" fill="#fff"/></svg>';
?>
<!DOCTYPE html>
<html lang="id" translate="no"><head>
<meta name="google" content="notranslate">
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(NAMA_USAHA) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head><body class="<?= $masuk ? 'peran-'.e($_SESSION['role']) : 'tamu' ?>">
<?php if ($masuk): ?>
<nav>
  <span class="merek"><?= $logo ?> <?= e(NAMA_USAHA) ?></span>
  <?= menu('dashboard.php', 'Dashboard') ?>
  <?php if ($_SESSION['role'] === 'admin'): ?>
    <?= menu('layanan.php', 'Layanan') ?>
    <?= menu('pengguna.php', 'Pengguna') ?>
    <?= menu('pesanan_admin.php', 'Semua Pesanan') ?>
  <?php elseif ($_SESSION['role'] === 'karyawan'): ?>
    <?= menu('pesanan_admin.php', 'Kelola Pesanan') ?>
  <?php else: ?>
    <?= menu('pesanan_buat.php', 'Buat Pesanan') ?>
    <?= menu('pesanan_saya.php', 'Pesanan Saya') ?>
  <?php endif; ?>
  <?= menu('profil.php', 'Profil') ?>
  <span class="kanan"><?= e($_SESSION['nama']) ?> <em class="peran"><?= e(ucfirst($_SESSION['role'])) ?></em>
    <form method="post" action="logout.php" class="kecil"><?= csrf_field() ?><button class="tautan">Keluar</button></form></span>
</nav>
<?php endif; ?>
<main>
<?php if (!$masuk): ?>
<div class="sambutan"><div class="merek"><?= $logo ?> <?= e(NAMA_USAHA) ?></div><p>Pesan cucian, pantau statusnya.</p></div>
<?php endif; ?>
