<?php
$id_kategori = $_GET['id_kategori'] ?? die(erid('id_kategori'));
$judul = 'Import Item Surat Jalan ' . $arr_kategori[$id_kategori];
set_title($judul);
$today = date('Y-m-d');

if (isset($_POST['btn_insert_item'])) {
  $s = "SELECT * FROM tb_importer_po";
  echo "<div class='debuga f30 red'>$s</div>";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  while ($d = mysqli_fetch_assoc($q)) {
    $kode_po = $d['kode_po'];
    $kode_sj = "$kode_po-999";
    $kode = $kode_sj;
    $kode_supplier = 'OWNSUPPLY';

    // select kode barang at importer where kode_po
    $s2 = "SELECT ID_BARU,SISA_STOCK FROM tb_importer WHERE PO='$kode_po'";
    echo "<div class='debuga f20 biru'>LOOP $s2</div>";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    while ($d2 = mysqli_fetch_assoc($q2)) {
      $ID_BARU = $d2['ID_BARU'];
      $kode_barang = $ID_BARU;
      $qty_po = $d2['SISA_STOCK'];
      // remove dot
      $qty_po = str_replace('.', '', $qty_po);
      //ganti koma dengan titik
      $qty_po = str_replace(',', '.', $qty_po);
      if (!$qty_po || $qty_po <= 0) {
        die("Invalid nilai QTY PO: $qty_po");
      }
      $qty = $qty_po;


      $s3 = "SELECT 1 FROM tb_sj_item WHERE kode_sj='$kode_sj' AND kode_barang='$kode_barang'";
      echo "<div class='debuga f14 merah'>LOOP LOOP $s3</div>";
      $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
      if (!mysqli_num_rows($q3)) {
        $s4 = "INSERT INTO tb_sj_item (
          kode_sj,
          kode_barang,
          qty_po,
          qty
        ) VALUES (
          '$kode_sj',
          '$kode_barang',
          '$qty_po',
          '$qty'
        )";
        echo "<div class='debuga f14 merah'>LOOP LOOP INSERT $s4</div>";
        // die($s4);
        $q4 = mysqli_query($cn, $s4) or die(mysqli_error($cn));
      } else {
        $s4 = "UPDATE tb_sj_item SET 
          kode_sj = '$kode_sj',
          kode_barang = '$kode_barang',
          qty_po = '$qty_po',
          qty = '$qty' 
        WHERE kode_sj = '$kode_sj' AND kode_barang = '$kode_barang'
        ";
        echo "<div class='debuga f14 merah'>LOOP LOOP UPDATE $s4</div>";
        $q4 = mysqli_query($cn, $s4) or die(mysqli_error($cn));
      }
    }
  }
  echo div_alert('success', 'Semua Item Surat Jalan berhasil dibuat.');
  echo "<hr><a class='btn btn-success' href='?insert_item_kumulatif&id_kategori=$id_kategori'>Next Insert Item Kumulatif</a>";
  exit;
}


$s = "SELECT 1 FROM tb_importer";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_item = mysqli_num_rows($q);

?>
<div class="pagetitle">
  <h1><?= $judul ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?importer">Import</a></li>
      <li class="breadcrumb-item"><a href="?import_data_barang">Import Barang</a></li>
      <li class="breadcrumb-item"><a href="?import_data_po">Import PO</a></li>
      <li class="breadcrumb-item"><a href="?import_data_sj">Import SJ</a></li>
      <li class="breadcrumb-item active"><?= $judul ?></li>
    </ol>
  </nav>
</div>

<div class="wadah">
  <div class=" miring f14 mb1">Kode Surat Jalan System</div>
  <input type="text" class="form-control" disabled value="NOMOR_PO-999">
  <div class="abu miring f12 mt1 mb3">Format Kode Surat Jalan System adalah KODE_PO + COUNTER</div>

  <div class=" miring f14 mb1">List Kode Barang (Item)</div>
  <input type="text" class="form-control" disabled value="AUTO-LOOKUP-IMPORTER">
  <div class="abu miring f12 mt1 mb3">Diambil dari tabel importer dengan PO yang sama</div>

  <form method="post">
    <button class="btn btn-primary" name=btn_insert_item>Insert <?= $jumlah_item ?> Item Surat Jalan</button>
  </form>
</div>