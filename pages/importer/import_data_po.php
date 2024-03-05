<?php
$id_kategori = $_GET['id_kategori'] ?? die(erid('id_kategori'));
$judul = 'Import Data PO ' . $arr_kategori[$id_kategori];
set_title($judul);
?>
<div class="pagetitle">
  <h1><?= $judul ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?import_data">Import Data</a></li>
      <li class="breadcrumb-item"><a href="?import_data_barang">Import Data Barang</a></li>
      <li class="breadcrumb-item active"><?= $judul ?></li>
    </ol>
  </nav>
</div>
<?php
echo div_alert('info', 'Sedang mengecek kode PO pada importer kumulatif... harap bersabar.');
$s = "SELECT * FROM tb_importer";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_importer = mysqli_num_rows($q);
while ($d = mysqli_fetch_assoc($q)) {

  $id_importer = $d['id_auto'];
  // $kode_barang = $d['ID_BARU'];
  $kode_po = $d['PO'];
  // $no_lot = $d['LOT'];
  // $kode_lokasi = $d['LOC'];
  // $is_fs = '';

  // $no_lot_or_null = $no_lot ? "'$no_lot'" : 'NULL';
  // $is_fs_or_null = $is_fs ? "'$is_fs'" : 'NULL';

  // $kode_kumulatif = "$kode_barang~$kode_po~$no_lot~$kode_lokasi~$is_fs";

  // $kode_sj = "$kode_po-001";

  $s2 = "SELECT 1 FROM tb_importer_po WHERE kode_po='$kode_po'";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  if (!mysqli_num_rows($q2)) {
    $s2 = "INSERT INTO tb_importer_po (
      kode_po,
      id_importer
    ) VALUES (
      '$kode_po',
      '$id_importer'
    )";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  }
}

$s = "SELECT 1 FROM tb_importer_po";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_po_importer = mysqli_num_rows($q);

echo div_alert('success', "Terdapat $jumlah_po_importer PO yang siap diproses.");
echo "<hr><a class='btn btn-success' href='?import_data_sj&id_kategori=$id_kategori'>Next Import Data Surat Jalan</a>";
