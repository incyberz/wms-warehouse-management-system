<?php
echo '<pre>';
var_dump($_POST);
echo '</pre>';

foreach ($_POST as $key => $value) $_POST[$key] = clean_sql($value);

$s = "INSERT INTO tb_barang (kode,nama) VALUES ('$_POST[kode]','$_POST[nama]') ";
echo $s;
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));


$s = "INSERT INTO tb_po_item (kode_barang,kode_po) VALUES ('$_POST[kode]','$kode_po') ";
echo $s;
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

echo div_alert('success','Tambah barang dan tambah PO item berhasil.');
jsurl("?po&p=po_manage&kode_po=$kode_po");
exit;
