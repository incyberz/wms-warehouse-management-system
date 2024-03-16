<?php
$id_kategori = $_GET['id_kategori'] ?? die(erid('id_kategori'));
$judul = 'Import Data Barang ' . $arr_kategori[$id_kategori];
set_title($judul);
?>
<style>
  .log {
    font-family: consolas;
    font-size: 12px;
    background: yellow;
  }
</style>
<div class="pagetitle">
  <h1><?= $judul ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?importer">Import Data</a></li>
      <li class="breadcrumb-item active"><?= $judul ?></li>
    </ol>
  </nav>
</div>
<?php
$s = "SELECT ID,ID_BARU,ITEM,DESKRIPSI,SATUAN FROM tb_importer ";
echolog('select all data barang at tabel importer');
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

echolog('start looping... checking data barang');
while ($d = mysqli_fetch_assoc($q)) {
  $ID = $d['ID'];
  $ID_BARU = $d['ID_BARU'];
  $ITEM = $d['ITEM'];
  $DESKRIPSI = $d['DESKRIPSI'];
  $SATUAN = $d['SATUAN'];
  $s2 = "SELECT 1 FROM tb_barang WHERE kode='$ID_BARU'";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  if (mysqli_num_rows($q2) == 0) {
    $ID_or_null = $ID ? "'$ID'" : 'NULL';
    $s2 = "INSERT INTO tb_barang (
      id_kategori,
      kode,
      nama,
      keterangan,
      satuan,
      kode_lama
    ) VALUES (
      '$id_kategori',
      '$ID_BARU',
      '$ITEM',
      '$DESKRIPSI',
      '$SATUAN',
      $ID_or_null
    )";
    echolog('inserting new data');
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    echolog('insert sukses', false);
  }
}

echo div_alert('success', 'Semua Data barang berhasil diimport!');
echo "<hr><a class='btn btn-success' href='?cek_duplikat_kumulatif&id_kategori=$id_kategori'>Next Cek Duplikat Kumulatif</a>";
