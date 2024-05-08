<?php
$id_kategori = $_GET['id_kategori'] ?? die(erid('id_kategori'));
$judul = 'Import Data Surat Jalan ' . $arr_kategori[$id_kategori];
set_title($judul);
$today = date('Y-m-d');

if (isset($_POST['btn_buat_sj'])) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';
  $s = "SELECT * FROM tb_importer_po";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  while ($d = mysqli_fetch_assoc($q)) {
    $kode_po = $d['kode_po'];
    $kode_sj = "$kode_po-999";
    $kode = $kode_sj;
    $kode_supplier = 'OWNSUPPLY';

    $s2 = "SELECT 1 FROM tb_sj WHERE kode='$kode'";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    if (!mysqli_num_rows($q2)) {
      $s2 = "INSERT INTO tb_sj (
        id_kategori,
        kode,
        kode_po,
        kode_supplier
      ) VALUES (
        '$id_kategori',
        '$kode',
        '$kode_po',
        '$kode_supplier'
      )";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      echo "<br>$s2";
    } else {
      $s2 = "UPDATE tb_sj SET 
        id_kategori = '$id_kategori',
        kode = '$kode',
        kode_po = '$kode_po',
        kode_supplier = '$kode_supplier'
      WHERE kode = '$kode'
      ";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      echo "<div class='debuga f12 green'>$s2</div>";
    }
  }
  echo div_alert('success', 'Semua Surat Jalan berhasil dibuat.');
  echo "<hr><a class='btn btn-success' href='?import_data_item&id_kategori=$id_kategori'>Next Import Data Item Surat Jalan</a>";
  exit;
}


$s = "SELECT 1 FROM tb_importer_po";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_po = mysqli_num_rows($q);

?>
<div class="pagetitle">
  <h1><?= $judul ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?importer">Import</a></li>
      <li class="breadcrumb-item"><a href="?import_data_barang">Import Barang</a></li>
      <li class="breadcrumb-item"><a href="?import_data_po">Import PO</a></li>
      <li class="breadcrumb-item active"><?= $judul ?></li>
    </ol>
  </nav>
</div>

<div class="wadah">
  <div class=" miring f14 mb1">Supplier</div>
  <input type="text" class="form-control" disabled value="OWNSUPPLY">
  <div class="abu miring f12 mt1 mb3">OWNSUPPLY adalah kode untuk gudang sendiri</div>

  <div class=" miring f14 mb1">Tanggal Terima (Tanggal Import)</div>
  <input type="text" class="form-control" disabled value="<?= $today ?>">
  <div class="abu miring f12 mt1 mb3">Default adalah hari ini. Tanggal PO, tanggal terima, dan tanggal masuk barang pada system di set ke hari ini.</div>

  <div class=" miring f14 mb1">Kode Surat Jalan</div>
  <input type="text" class="form-control" disabled value="NOMOR_PO-999">
  <div class="abu miring f12 mt1 mb3">Format Kode Surat Jalan System adalah KODE_PO + COUNTER</div>

  <form method="post">
    <button class="btn btn-primary" name=btn_buat_sj>Buat <?= $jumlah_po ?> Surat Jalan</button>
  </form>
</div>