<?php 
include '../../conn.php';
$id_kumulatif = $_GET['id_kumulatif'] ?? die(erid('id_kumulatif'));
$id_do = $_GET['id_do'] ?? die(erid('id_do'));

if(!$id_kumulatif) die(erid('id_kumulatif::empty'));
if(!$id_do) die(erid('id_do::empty'));

$s = "SELECT 1 FROM tb_pick WHERE id_kumulatif=$id_kumulatif AND id_do='$id_do' ";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(!mysqli_num_rows($q)){
  $s = "INSERT INTO tb_pick (id_kumulatif,id_do) VALUES ($id_kumulatif,'$id_do')";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
}
echo 'sukses';