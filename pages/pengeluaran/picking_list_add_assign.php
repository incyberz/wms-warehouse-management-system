<?php 
include '../../conn.php';
$id_sj_subitem = $_GET['id_sj_subitem'] ?? die(erid('id_sj_subitem'));
$kode_do = $_GET['kode_do'] ?? die(erid('kode_do'));

if(!$id_sj_subitem) die(erid('id_sj_subitem::empty'));
if(!$kode_do) die(erid('kode_do::empty'));

$s = "SELECT 1 FROM tb_picking WHERE id_sj_subitem=$id_sj_subitem AND kode_do='$kode_do' ";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(!mysqli_num_rows($q)){
  $s = "INSERT INTO tb_picking (id_sj_subitem,kode_do) VALUES ($id_sj_subitem,'$kode_do')";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
}
echo 'sukses';