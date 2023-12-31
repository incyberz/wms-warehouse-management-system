<?php 
include '../../conn.php';
$id_sj_subitem = $_GET['id_sj_subitem'] ?? die(erid('id_sj_subitem'));
$kode_do_cat = $_GET['kode_do_cat'] ?? die(erid('kode_do_cat'));

if(!$id_sj_subitem) die(erid('id_sj_subitem::empty'));
if(!$kode_do_cat) die(erid('kode_do_cat::empty'));

$s = "SELECT 1 FROM tb_picking WHERE id_sj_subitem=$id_sj_subitem AND kode_do_cat='$kode_do_cat' ";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(!mysqli_num_rows($q)){
  $s = "INSERT INTO tb_picking (id_sj_subitem,kode_do_cat) VALUES ($id_sj_subitem,'$kode_do_cat')";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
}
echo 'sukses';