<?php
require 'fungsi.php'; require 'koneksi.php'; wajib_role('pelanggan');
$daftar_khusus = ['Kemeja','Kaos','Celana','Rok','Jaket','Seprai','Handuk','Jas','Gaun / Kebaya','Batik','Seragam','Jaket tebal','Selimut / Bedcover','Sepatu','Boneka'];
$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_cek();
  $cat = trim($_POST['catatan']);
  $baris = [];
  foreach ((array)($_POST['baris'] ?? []) as $r) {
    if (!is_array($r)) continue;
    $l = (int)($r['layanan_id'] ?? 0); $b = (float)($r['berat'] ?? 0);
    if ($l <= 0 || $b <= 0) continue;
    $pilih = is_array($r['khusus'] ?? null) ? $r['khusus'] : [];
    $jml = is_array($r['jml'] ?? null) ? $r['jml'] : [];
    $bagian = [];
    foreach ($daftar_khusus as $nama) {
      if (in_array($nama, $pilih, true)) {
        $n = max(1, min(99, (int)($jml[$nama] ?? 1)));
        $bagian[] = $n.'x '.$nama;
      }
    }
    $lain = trim($r['lain'] ?? '');
    if ($lain !== '') $bagian[] = mb_substr($lain, 0, 100);
    $baris[] = [$l, $b, $bagian ? mb_substr(implode(', ', $bagian), 0, 255) : null];
  }
  if (!$baris) { $pesan = 'Pilih layanan dan isi perkiraan berat.'; }
  else {
    $db->begin_transaction();
    $st = $db->prepare("INSERT INTO pesanan (user_id,layanan_id,berat_perkiraan,catatan,pakaian_khusus) VALUES (?,?,?,?,?)");
    $gagal = !$st ? $db->error : ''; $kode = 0;
    foreach ($baris as $i => $br) {
      if ($gagal) break;
      $c = $i === 0 ? $cat : '';   // catatan umum disimpan di pesanan pertama
      $st->bind_param('iidss', $_SESSION['user_id'], $br[0], $br[1], $c, $br[2]);
      if (!$st->execute()) { $gagal = $st->error; $kode = $st->errno; }
    }
    if ($gagal) {
      $db->rollback();
      if ($kode === 1452) { session_destroy(); header('Location: login.php'); exit; }
      $pesan = 'Pesanan gagal disimpan: '.$gagal;
    } else { $db->commit(); header('Location: pesanan_saya.php'); exit; }
  }
}
$layanan = $db->query("SELECT * FROM layanan ORDER BY nama_layanan");
$opsi = '<option value="">Pilih layanan</option>';
while ($l = $layanan->fetch_assoc())
  $opsi .= '<option value="'.(int)$l['id'].'">'.e($l['nama_layanan']).' (Rp '.number_format($l['tarif_per_kg'],0,',','.').'/kg)</option>';

function baris_html($i, $opsi, $daftar) {
  ob_start(); ?>
  <div class="baris-kartu">
    <div class="baris-atas">
      <select name="baris[<?= $i ?>][layanan_id]" required><?= $opsi ?></select>
      <input name="baris[<?= $i ?>][berat]" type="number" step="0.1" min="0.1" placeholder="Berat (kg)" required>
      <button type="button" class="hapus hapus-baris" hidden aria-label="Hapus layanan ini">&times;</button>
    </div>
    <details open>
      <summary>Isi cucian untuk layanan ini (opsional)</summary>
      <div class="pilihan">
      <?php foreach ($daftar as $j => $nama): ?>
        <div class="opsi">
          <input class="cb" type="checkbox" id="k<?= $i ?>_<?= $j ?>" name="baris[<?= $i ?>][khusus][]" value="<?= e($nama) ?>">
          <label class="tile" for="k<?= $i ?>_<?= $j ?>"><span class="tanda"></span><span class="nm"><?= e($nama) ?></span></label>
          <input class="jml" type="number" name="baris[<?= $i ?>][jml][<?= e($nama) ?>]" min="1" max="99" value="1" aria-label="Jumlah <?= e($nama) ?>">
        </div>
      <?php endforeach; ?>
      </div>
      <input name="baris[<?= $i ?>][lain]" maxlength="100" placeholder="Isi lain? Tulis di sini (misalnya toga, mukena, sarung bantal)">
    </details>
  </div>
  <?php return ob_get_clean();
}
include 'header.php'; ?>
<style>

/* pilihan pakaian khusus */
.blok-khusus{background:var(--busa);border:1px solid var(--garis);border-radius:14px;padding:16px;margin:6px 0 16px}
.blok-khusus .bantu{margin:2px 0 12px;color:var(--redup);font-size:.88rem}
.blok-khusus small{font-weight:400;color:var(--redup)}
.pilihan{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:10px;margin:0 0 14px}
.opsi{position:relative}
.opsi .cb{position:absolute;opacity:0;width:1px;height:1px;margin:0;pointer-events:none}
.tile{display:flex;align-items:center;gap:12px;min-height:52px;padding:10px 74px 10px 14px;background:#fff;border:1.5px solid var(--garis);border-radius:12px;cursor:pointer;transition:border-color .15s,background .15s}
.tile:hover{border-color:#9ccbd4}
.tanda{flex:none;width:22px;height:22px;border:2px solid #b7cdd3;border-radius:50%;background:#fff;position:relative;transition:all .15s}
.tanda::after{content:"";position:absolute;left:6px;top:2px;width:6px;height:11px;border:solid #fff;border-width:0 2.5px 2.5px 0;transform:rotate(45deg) scale(0);transition:transform .15s}
.nm{font-weight:700;font-size:.92rem}
.cb:checked + .tile{border-color:var(--laut);background:var(--embun)}
.cb:checked + .tile .tanda{background:var(--laut);border-color:var(--laut)}
.cb:checked + .tile .tanda::after{transform:rotate(45deg) scale(1)}
.cb:focus-visible + .tile{outline:2px solid var(--laut);outline-offset:2px}
.opsi .jml{display:none;position:absolute;right:10px;top:50%;transform:translateY(-50%);width:56px;margin:0;padding:5px 6px;text-align:center}
.cb:checked ~ .jml{display:block}

.bantu{margin:2px 0 10px;color:var(--redup);font-size:.88rem}
.baris-atas{display:flex;gap:8px;align-items:center;margin-bottom:8px}
.baris-atas select{flex:1;max-width:none;margin:0}
.baris-atas input{width:130px;margin:0}
button.sekunder{background:#fff;color:var(--laut-tua);border:1px solid var(--laut);margin:4px 0 18px}
button.sekunder:hover{background:var(--embun)}

.baris-kartu{background:#fff;border:1px solid var(--garis);border-radius:14px;padding:14px;margin-bottom:12px}
.baris-kartu details{margin-top:4px}
.baris-kartu summary{cursor:pointer;font-weight:700;color:var(--laut-tua);font-size:.9rem;padding:6px 0}
.baris-kartu .pilihan{margin-top:10px}
.baris-kartu details input[name$="[lain]"]{max-width:none}
</style>
<div class="kotak"><h2>Buat Pesanan</h2>
<?php if ($pesan): ?><p class="salah"><?= e($pesan) ?></p><?php endif; ?>
<form method="post"><?= csrf_field() ?>
  <strong>Layanan dan perkiraan berat</strong>
  <p class="bantu">Pisahkan cucianmu menurut layanannya. Satu kotak = satu layanan, lalu pilih isi cucian yang masuk ke layanan itu. Contoh: kotak 1 Cuci Setrika (celana, seprai), kotak 2 Setrika Saja (kemeja). Pakaian biasa maupun khusus boleh dipilih.</p>
  <div id="daftar-baris"><?= baris_html(0, $opsi, $daftar_khusus) ?></div>
  <button type="button" id="tambah-baris" class="sekunder">+ Tambah layanan lain</button>

  Catatan (alamat jemput, permintaan lain) <textarea name="catatan" rows="3"></textarea>
  <button>Kirim pesanan</button>
</form></div>
<template id="tpl"><?= baris_html('__I__', $opsi, $daftar_khusus) ?></template>
<script>
(function(){
  var daftar=document.getElementById('daftar-baris'), tpl=document.getElementById('tpl'), n=1;
  function atur(){
    var b=daftar.querySelectorAll('.baris-kartu');
    b.forEach(function(x){x.querySelector('.hapus-baris').hidden=b.length<2;});
  }
  document.getElementById('tambah-baris').addEventListener('click',function(){
    var w=document.createElement('div');
    w.innerHTML=tpl.innerHTML.replace(/__I__/g,n++);
    daftar.appendChild(w.firstElementChild); atur();
  });
  daftar.addEventListener('click',function(e){
    var t=e.target.closest('.hapus-baris');
    if(t&&daftar.children.length>1){t.closest('.baris-kartu').remove();atur();}
  });
})();
</script>
<?php include 'footer.php'; ?>
