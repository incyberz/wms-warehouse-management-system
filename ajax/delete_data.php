<?php
session_start();
$username = $_SESSION['wms_username'] ?? die('Silahkan Login terlebih dahulu.');
include '../conn.php';
include '../data_user.php';

$p = $_GET['p'] ?? die(erid('p'));
$id = $_GET['id'] ?? die(erid('id'));

$p = str_replace('edit_','',$p);

$s = "DELETE FROM tb_$p WHERE id=$id";
// die($s);
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

?>
sukses