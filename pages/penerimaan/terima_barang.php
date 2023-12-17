<?php
// $kode_po = $_GET['kode_po'] ?? '';
$kode_po = isset($_GET['kode_po']) ? $_GET['kode_po'] : '';
if($kode_po==''){
  include 'terima_barang_cari_nomor_po.php';
}else{
  include 'bbm.php';
}