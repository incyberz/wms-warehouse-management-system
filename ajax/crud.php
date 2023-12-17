<?php
include 'harus_login.php';

$tb = $_GET['tb'] ?? die(erid('tb'));
$aksi = $_GET['aksi'] ?? die(erid('aksi'));
$id = $_GET['id'] ?? die(erid('id'));

$tb = str_replace('edit_','',$tb);

if($aksi=='update'){
  $kolom = $_GET['kolom'] ?? die(erid('kolom'));
  $value = $_GET['value'] ?? die(erid('value'));
  
  $value = strip_tags(clean_sql($value));
  $value = $value=='' ? 'NULL' : "'$value'";
  $s = "UPDATE tb_$tb SET $kolom = $value WHERE id = $id";
}elseif($aksi=='insert'){
  $koloms = 'kode,nama';
  $values = "'AAA-NEW','NEW $tb'";
  $s = "INSERT INTO tb_$tb ($koloms) VALUES ($values) ";

}elseif($aksi=='insert_item'){
  $ids = $_GET['ids'] ?? die(erid('ids'));
  $qtys = $_GET['qtys'] ?? die(erid('qtys'));
  $hargas = $_GET['hargas'] ?? die(erid('hargas'));

  $rid = explode(';',$ids);
  $rqty = explode(';',$qtys);
  $rharga = explode(';',$hargas);

  foreach ($rid as $key => $id) {
    if(strlen($id)>0){
      $harga_manual = $rharga[$key] ?? 'NULL';
      $s = "UPDATE tb_$tb SET qty=$rqty[$key], harga_manual=$harga_manual WHERE id=$rid[$key] ";
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));


    }
  }

  die('sukses');


}elseif($aksi=='delete'){
  $s = "DELETE FROM tb_$tb WHERE id = $id";

}else{
  die("Handler untuk aksi: $aksi, belum ditentukan. ");
}
// die($s);
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

?>
sukses