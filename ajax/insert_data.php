<?php
session_start();
$username = $_SESSION['wms_username'] ?? die('Silahkan Login terlebih dahulu.');
include '../conn.php';
include '../data_user.php';

$p = $_GET['p'];
$p_upper = strtoupper($p);
$koloms = 'kode,nama';
$values = "'AAA-NEW','NEW $p'";

$s = "INSERT INTO tb_$p ($koloms) VALUES ($values) ";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

?>
sukses