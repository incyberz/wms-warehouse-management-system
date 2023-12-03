<?php
session_start();
$username = $_SESSION['wms_username'] ?? die('Silahkan Login terlebih dahulu.');
include '../conn.php';
include '../data_user.php';

function ONLY($kode_role){
  if($kode_role!=$_SESSION['wms_role']) die("<div class='alert alert-danger mt-2'><span class=red>Maaf Anda tidak berhak mengakses fitur ini.</span> Silahkan <a href='?logout' onclick='return confirm(\"Yakin untuk Logout?\")'>relogin sebagai $kode_role</a></div>");
}