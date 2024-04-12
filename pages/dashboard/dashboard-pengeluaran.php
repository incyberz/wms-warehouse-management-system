<?php
$s = "SELECT 
-- ==============================================
-- AKS HARI INI
-- ==============================================
(
  SELECT count(1) FROM tb_do 
  WHERE id_kategori=1 
  AND date_created>='$today') jumlah_do_hari_ini_aks,
(
  SELECT sum(qty_apparel) FROM tb_do 
  WHERE id_kategori=1 
  AND date_created>='$today') jumlah_apparel_hari_ini_aks,
(
  SELECT count(1) FROM tb_pick a 
  JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id
  JOIN tb_sj_item c ON b.id_sj_item=c.id
  JOIN tb_barang d ON c.kode_barang=d.kode
  WHERE d.id_kategori=1 
  AND a.tanggal_pick>='$today') picked_hari_ini_aks,
(
  SELECT count(1) FROM tb_pick a 
  JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id
  JOIN tb_sj_item c ON b.id_sj_item=c.id
  JOIN tb_barang d ON c.kode_barang=d.kode
  WHERE d.id_kategori=1 
  AND a.tanggal_pick>='$today' 
  AND tanggal_allocate is not null) allocate_hari_ini_aks,
(
  SELECT count(1) FROM tb_retur_do a 
  JOIN tb_pick b ON a.id_pick=b.id 
  JOIN tb_sj_kumulatif c ON b.id_kumulatif=c.id 
  JOIN tb_sj_item d ON c.id_sj_item=d.id 
  JOIN tb_barang e ON d.kode_barang=e.kode 
  WHERE a.tanggal_retur >='$today'
  AND e.id_kategori=1) picked_retur_hari_ini_aks,

-- ==============================================
-- AKS ALL TIME
-- ==============================================
(
  SELECT count(1) FROM tb_do 
  WHERE id_kategori=1) jumlah_do_aks,
(
  SELECT sum(qty_apparel) FROM tb_do 
  WHERE id_kategori=1) jumlah_apparel_aks,
(
  SELECT count(1) FROM tb_pick a 
  JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id
  JOIN tb_sj_item c ON b.id_sj_item=c.id
  JOIN tb_barang d ON c.kode_barang=d.kode
  WHERE d.id_kategori=1) picked_aks,
(
  SELECT count(1) FROM tb_pick a 
  JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id
  JOIN tb_sj_item c ON b.id_sj_item=c.id
  JOIN tb_barang d ON c.kode_barang=d.kode
  WHERE d.id_kategori=1 
  AND tanggal_allocate is not null) allocate_aks,
(
  SELECT count(1) FROM tb_retur_do a 
  JOIN tb_pick b ON a.id_pick=b.id 
  JOIN tb_sj_kumulatif c ON b.id_kumulatif=c.id 
  JOIN tb_sj_item d ON c.id_sj_item=d.id 
  JOIN tb_barang e ON d.kode_barang=e.kode
  AND e.id_kategori=1) picked_retur_aks,



-- ==============================================
-- FAB HARI INI
-- ==============================================
(
  SELECT count(1) FROM tb_do 
  WHERE id_kategori=2 
  AND date_created>='$today') jumlah_do_hari_ini_fab,
(
  SELECT sum(qty_apparel) FROM tb_do 
  WHERE id_kategori=2 
  AND date_created>='$today') jumlah_apparel_hari_ini_fab,
(
  SELECT count(1) FROM tb_pick a 
  JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id
  JOIN tb_sj_item c ON b.id_sj_item=c.id
  JOIN tb_barang d ON c.kode_barang=d.kode
  WHERE d.id_kategori=2 
  AND a.tanggal_pick>='$today') picked_hari_ini_fab,
(
  SELECT count(1) FROM tb_pick a 
  JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id
  JOIN tb_sj_item c ON b.id_sj_item=c.id
  JOIN tb_barang d ON c.kode_barang=d.kode
  WHERE d.id_kategori=2 
  AND a.tanggal_pick>='$today' 
  AND tanggal_allocate is not null) allocate_hari_ini_fab,
(
  SELECT count(1) FROM tb_retur_do a 
  JOIN tb_pick b ON a.id_pick=b.id 
  JOIN tb_sj_kumulatif c ON b.id_kumulatif=c.id 
  JOIN tb_sj_item d ON c.id_sj_item=d.id 
  JOIN tb_barang e ON d.kode_barang=e.kode 
  WHERE a.tanggal_retur >='$today'
  AND e.id_kategori=2) picked_retur_hari_ini_fab,

-- ==============================================
-- FAB ALL TIME
-- ==============================================
(
  SELECT count(1) FROM tb_do 
  WHERE id_kategori=2) jumlah_do_fab,
(
  SELECT sum(qty_apparel) FROM tb_do 
  WHERE id_kategori=2) jumlah_apparel_fab,
(
  SELECT count(1) FROM tb_pick a 
  JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id
  JOIN tb_sj_item c ON b.id_sj_item=c.id
  JOIN tb_barang d ON c.kode_barang=d.kode
  WHERE d.id_kategori=2) picked_fab,
(
  SELECT count(1) FROM tb_pick a 
  JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id
  JOIN tb_sj_item c ON b.id_sj_item=c.id
  JOIN tb_barang d ON c.kode_barang=d.kode
  WHERE d.id_kategori=2 
  AND tanggal_allocate is not null) allocate_fab,
(
  SELECT count(1) FROM tb_retur_do a 
  JOIN tb_pick b ON a.id_pick=b.id 
  JOIN tb_sj_kumulatif c ON b.id_kumulatif=c.id 
  JOIN tb_sj_item d ON c.id_sj_item=d.id 
  JOIN tb_barang e ON d.kode_barang=e.kode
  AND e.id_kategori=2) picked_retur_fab



";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);

$jumlah_do_hari_ini_aks = $d['jumlah_do_hari_ini_aks'];
$jumlah_apparel_hari_ini_aks = $d['jumlah_apparel_hari_ini_aks'];
$picked_hari_ini_aks = $d['picked_hari_ini_aks'];
$allocate_hari_ini_aks = $d['allocate_hari_ini_aks'];
$picked_retur_hari_ini_aks = $d['picked_retur_hari_ini_aks'];
$jumlah_do_aks = $d['jumlah_do_aks'];
$jumlah_apparel_aks = $d['jumlah_apparel_aks'];
$picked_aks = $d['picked_aks'];
$allocate_aks = $d['allocate_aks'];
$picked_retur_aks = $d['picked_retur_aks'];

$jumlah_do_hari_ini_fab = $d['jumlah_do_hari_ini_fab'];
$jumlah_apparel_hari_ini_fab = $d['jumlah_apparel_hari_ini_fab'];
$picked_hari_ini_fab = $d['picked_hari_ini_fab'];
$allocate_hari_ini_fab = $d['allocate_hari_ini_fab'];
$picked_retur_hari_ini_fab = $d['picked_retur_hari_ini_fab'];
$jumlah_do_fab = $d['jumlah_do_fab'];
$jumlah_apparel_fab = $d['jumlah_apparel_fab'];
$picked_fab = $d['picked_fab'];
$allocate_fab = $d['allocate_fab'];
$picked_retur_fab = $d['picked_retur_fab'];

$arr = [];
$arr['aks'] =
  [
    ['Count DO hari ini', $jumlah_do_hari_ini_aks],
    ['Jumlah Apparel hari ini', intval($jumlah_apparel_hari_ini_aks)],
    ['Count Picked hari ini', $picked_hari_ini_aks],
    ['Count Allocate hari ini', $allocate_hari_ini_aks],
    ['Count Picked Retur hari ini', $picked_retur_hari_ini_aks],
    ['Count DO All time', $jumlah_do_aks],
    ['Jumlah Apparel All time', $jumlah_apparel_aks],
    ['Count Picked Item All time', $picked_aks],
    ['Count Allocate Item All time', $allocate_aks],
    ['Count Picked Retur All time', $picked_retur_aks]
  ];

$arr['fab'] =
  [
    ['Count DO hari ini', $jumlah_do_hari_ini_fab],
    ['Jumlah Apparel hari ini', intval($jumlah_apparel_hari_ini_fab)],
    ['Count Picked hari ini', $picked_hari_ini_fab],
    ['Count Allocate hari ini', $allocate_hari_ini_fab],
    ['Count Picked Retur hari ini', $picked_retur_hari_ini_fab],
    ['Count DO All time', $jumlah_do_fab],
    ['Jumlah Apparel All time', $jumlah_apparel_fab],
    ['Count Picked Item All time', $picked_fab],
    ['Count Allocate Item All time', $allocate_fab],
    ['Count Picked Retur All time', $picked_retur_fab]
  ];

$div = '';
foreach ($arr as $key => $arr_value) {
  $kategori = $key == 'aks' ? 'Aksesoris' : 'Fabric';

  $tr_hari_ini = '';
  $tr_all_time = '';
  foreach ($arr_value as $key => $value) {
    if (strpos($value[0], 'hari ini')) {
      $tr_hari_ini .= "
        <tr>
          <td>$value[0]</td>
          <td>:</td>
          <td>$value[1]</td>
        </tr>
      ";
    } else {
      $tr_all_time .= "
        <tr>
          <td>$value[0]</td>
          <td>:</td>
          <td>$value[1]</td>
        </tr>
      ";
    }
  }

  $div .= "
    <div class='col-lg-6'>
      <div class='wadah gradasi-hijau'>
        <h2>Pengeluaran $kategori</h2>

        <div class='wadah bg-white'>
          <div>Hari ini</div>
          <table class=table>$tr_hari_ini</table>
        </div>

        <div class='wadah bg-white'>
          <div>All time</div>
          <table class=table>$tr_all_time</table>
        </div>

      </div>
    </div>

  ";
}
echo "<div class='row'>$div</div>";


$s = "SELECT 
d.kode as kode_barang,
e.kode_do,
e.id_kategori 
FROM tb_pick a 
JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id 
JOIN tb_sj_item c ON b.id_sj_item=c.id 
JOIN tb_barang d ON c.kode_barang=d.kode 
JOIN tb_do e ON a.id_do=e.id 
WHERE a.boleh_allocate is null AND a.qty>0 
ORDER BY e.date_created DESC 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$li_unlock = '';
while ($d = mysqli_fetch_assoc($q)) {
  $cat = $d['id_kategori'] == 1 ? 'aks' : 'fab';
  $li_unlock .= "<li>$d[kode_barang] ~ DO: <a href='?pengeluaran&p=buat_do&kode_do=$d[kode_do]&cat=$cat'>$d[kode_do]</a></li>";
}

$s = "SELECT 
d.kode as kode_barang,
e.kode_do,
e.id_kategori 
FROM tb_pick a 
JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id 
JOIN tb_sj_item c ON b.id_sj_item=c.id 
JOIN tb_barang d ON c.kode_barang=d.kode 
JOIN tb_do e ON a.id_do=e.id 
WHERE a.boleh_allocate =1 AND tanggal_allocate is null 
ORDER BY e.date_created DESC 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$li_allocate = '';
while ($d = mysqli_fetch_assoc($q)) {
  $cat = $d['id_kategori'] == 1 ? 'aks' : 'fab';
  $li_allocate .= "<li>$d[kode_barang] ~ DO: <a href='?pengeluaran&p=buat_do&kode_do=$d[kode_do]&cat=$cat'>$d[kode_do]</a></li>";
}



?>
<div class="row">
  <div class="col-lg-6">
    <div class="wadah">
      <h2>Item Perlu di Unlock</h2>
      <ol><?= $li_unlock ?></ol>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="wadah">
      <h2>Item Perlu di Allocate</h2>
      <ol><?= $li_allocate ?></ol>
    </div>
  </div>
</div>