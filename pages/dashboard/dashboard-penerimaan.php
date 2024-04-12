<?php

$s = "SELECT 
(
  SELECT count(1) FROM tb_sj_kumulatif a 
  JOIN tb_sj_item b ON a.id_sj_item=b.id 
  JOIN tb_barang c ON b.kode_barang=c.kode
  WHERE a.tanggal_masuk >= '$today' AND c.id_kategori=1) count_aks,
(
  SELECT count(1) FROM tb_sj_kumulatif a 
  JOIN tb_sj_item b ON a.id_sj_item=b.id 
  JOIN tb_barang c ON b.kode_barang=c.kode
  WHERE a.tanggal_masuk >= '$today' AND c.id_kategori=2) count_fab,
(
  SELECT count(1) FROM tb_sj_kumulatif a 
  JOIN tb_sj_item b ON a.id_sj_item=b.id 
  JOIN tb_barang c ON b.kode_barang=c.kode
  WHERE c.id_kategori=1) count_aks_all,
(
  SELECT count(1) FROM tb_sj_kumulatif a 
  JOIN tb_sj_item b ON a.id_sj_item=b.id 
  JOIN tb_barang c ON b.kode_barang=c.kode
  WHERE c.id_kategori=2) count_fab_all,
(
  SELECT count(1) FROM tb_lokasi a 
  JOIN tb_blok b ON a.blok=b.blok 
  WHERE b.id_kategori=1) lokasi_aks,
(
  SELECT count(1) FROM tb_blok a 
  WHERE a.id_kategori=1) blok_aks,
(
  SELECT count(1) FROM tb_lokasi a 
  JOIN tb_blok b ON a.blok=b.blok 
  WHERE b.id_kategori=2) lokasi_fab,
(
  SELECT count(1) FROM tb_blok a 
  WHERE a.id_kategori=2) blok_fab
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$count_aks = $d['count_aks'];
$count_fab = $d['count_fab'];
$count_aks_all = $d['count_aks_all'];
$count_fab_all = $d['count_fab_all'];
$lokasi_aks = $d['lokasi_aks'];
$lokasi_fab = $d['lokasi_fab'];
$blok_aks = $d['blok_aks'];
$blok_fab = $d['blok_fab'];


$count['Aksesoris'] = [$count_aks, $count_aks_all, $lokasi_aks, $blok_aks];
$count['Fabric'] = [$count_fab, $count_fab_all, $lokasi_fab, $blok_fab];

$div = '';
foreach ($count as $key => $arr) {
  $cat = $key == 'Aksesoris' ? 'aks' : 'fab';
  $id_kategori = $key == 'Aksesoris' ? 1 : 2;
  $div .= "
    <div class='col-lg-6'>
      <div class='wadah'>
        <h2>$key Count</h2>
        <table class='table table-hover'>
          <tr>
            <td>Hari ini</td>
            <td>:</td>
            <td>$arr[0] item</td>
          </tr>
          <tr>
            <td>All time</td>
            <td>:</td>
            <td><a href='?rekap_kumulatif&cat=$cat'>$arr[1] item</a></td>
          </tr>
          <tr>
            <td>Lokasi</td>
            <td>:</td>
            <td><a href='?manage_lokasi&id_kategori=$id_kategori'>$arr[2] Lokasi</a></td>
          </tr>
          <tr>
            <td>Blok Lokasi</td>
            <td>:</td>
            <td><a href='?manage_lokasi&id_kategori=$id_kategori'>$arr[3] Blok</a></td>
          </tr>
        </table>
      </div>
    </div>
  ";
}

echo "<div class=row>$div</div>";
