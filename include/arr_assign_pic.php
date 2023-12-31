<?php
$s = "SELECT a.kode_unik,b.nama FROM tb_assign_pic a JOIN tb_pic b ON a.kode_pic=b.kode";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$arr_assign_pic = [];
while($d=mysqli_fetch_assoc($q)){
  $arr_assign_pic[$d['kode_unik']] = $d['nama'];
}