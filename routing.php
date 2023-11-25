<?php
$page_tujuan = "pages/$parameter.php";
if(!isset($parameter)) die('Routing memerlukan parameter.');

switch($parameter){
  case '' : 
  case 'dashboard' : 
  case 'home' : include 'pages/dashboard/dashboard.php'; break;
  case 'master' : include 'pages/master/master.php'; break;
  case 'po_home' : include 'pages/penerimaan/po_home.php'; break;
  default:
    if(file_exists($page_tujuan)){
      include $page_tujuan;
    }else{
      include 'na.php';
    }
  ;
}
