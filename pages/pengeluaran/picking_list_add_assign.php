<?php 
include '../../conn.php';
$id_sj_subitem = $_GET['id_sj_subitem'] ?? die(erid('id_sj_subitem'));
$id_do = $_GET['id_do'] ?? die(erid('id_do'));

if(!$id_sj_subitem) die(erid('id_sj_subitem::empty'));
if(!$id_do) die(erid('id_do::empty'));

$s = "SELECT 1 FROM tb_picking WHERE id_sj_subitem=$id_sj_subitem AND id_do='$id_do' ";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(!mysqli_num_rows($q)){
  $s = "INSERT INTO tb_picking (id_sj_subitem,id_do) VALUES ($id_sj_subitem,'$id_do')";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
}
echo 'sukses';