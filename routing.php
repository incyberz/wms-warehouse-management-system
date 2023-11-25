<?php
$page_tujuan = "pages/$parameter.php";
if(!isset($parameter)) die('Routing memerlukan parameter.');

switch($parameter){
  case '' : 
  case 'dashboard' : 
  case 'home' : include 'pages/dashboard/dashboard.php'; break;
  case 'master' : include 'pages/master/master.php'; break;
  case 'po' : include 'pages/penerimaan/po.php'; break;
  case 'do' : include 'pages/pengiriman/do.php'; break;
  case 'laporan' : include 'pages/laporan/laporan.php'; break;
  default:
    if(file_exists($page_tujuan)){
      include $page_tujuan;
    }else{
      include 'na.php';
    }
  ;
}
