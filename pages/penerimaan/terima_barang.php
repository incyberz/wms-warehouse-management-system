<?php
$no_po = $_GET['no_po'] ?? '';
if($no_po==''){
  include 'terima_barang_cari_nomor_po.php';
}else{
  include 'bukti_barang_masuk.php';
}