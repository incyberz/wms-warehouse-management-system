<?php
echo '<pre>';
var_dump($_POST);
echo '</pre>';

foreach ($_POST as $key => $value) $_POST[$key] = clean_sql($value);

if(isset($_POST['btn_simpan_dan_tambahkan'])){
  $s = "INSERT INTO tb_barang (kode,nama) VALUES ('$_POST[kode]','$_POST[nama]') ON DUPLICATE KEY UPDATE nama='$_POST[nama]'";
  echo $s;
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $kode_brg = $_POST['kode'];
}else{
  $kode_brg = $_POST['btn_add_sj_item'];
}

$s = "INSERT INTO tb_sj_item (kode_barang,kode_sj) VALUES ('$kode_brg','$kode_sj') ";
echo $s;
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

echo div_alert('success','Tambah barang dan tambah PO item berhasil.');
// jsurl("?penerimaan&p=manage_sj&kode_sj=$kode_sj");
exit;
