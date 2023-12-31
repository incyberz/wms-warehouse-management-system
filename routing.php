<?php
$page_tujuan = "pages/$parameter.php";
if(!isset($parameter)) die('Routing memerlukan parameter.');

switch($parameter){
  case '' : 
  case 'dashboard' : 
  case 'home' : include 'pages/dashboard/dashboard.php'; break;
  case 'stok_awal' : 
  case 'awal' : include 'pages/awal/awal.php'; break;
  case 'master' : include 'pages/master/master.php'; break;
  case 'po' : 
  case 'terima_po' : include 'pages/terima_po/po.php'; break;
  case 'laporan' : include 'pages/laporan/laporan.php'; break;
  case 'update_barang' : include 'pages/admin/update_barang.php'; break;
  case 'penerimaan' : include 'pages/penerimaan/penerimaan.php'; break;
  case 'pengeluaran' : include 'pages/pengeluaran/pengeluaran.php'; break;
  case 'master_pengeluaran' : include 'pages/pengeluaran/master_pengeluaran.php'; break;
  case 'stok' : include 'pages/stok/stok.php'; break;
  case 'retur' : include 'pages/retur/retur.php'; break;
  case 'terima_retur' : include 'pages/retur/terima_retur.php'; break;
  default:
    if(file_exists($page_tujuan)){
      include $page_tujuan;
    }else{
      include 'na.php';
    }
  ;
}
