<?php
# ============================================================
# DATABASE CONNECTION
# ============================================================
$online_version = $_SERVER['SERVER_NAME'] == 'localhost' ? 0 : 1;

$dm_db = 0;
if ($online_version) {
  $db_server = "localhost";
  $db_user = "pesc7881_insho";
  $db_pass = "hq'qC3D}+Hzj@TT";
  $db_name = "pesc7881_wms";

  $db_user = "iotikain_insho";
  $db_pass = "hq'qC3D}+Hzj@TT";
  $db_name = "iotikain_wms";
} else {
  $db_server = "localhost";
  $db_user = "root";
  $db_pass = '';
  $db_name = "db_wms";
  if (1) {
    $dm_db = 1;
    $db_name = "db_online_wms";
  }
}

$cn = new mysqli($db_server, $db_user, $db_pass, $db_name);
if ($cn->connect_errno) {
  echo "Error Konfigurasi# Tidak dapat terhubung ke MySQL Server :: $db_name";
  exit();
}

date_default_timezone_set("Asia/Jakarta");

function erid($a)
{
  return "Error, index $a belum terdefinisi.";
}

function clean_sql($a)
{
  $a = str_replace('\'', '`', $a);
  // $a = str_replace('"','`',$a);
  $a = str_replace(';', ',', $a);
  return $a;
}

function div_alert($a, $b)
{
  return "<div class='alert alert-$a'>$b</div>";
}
