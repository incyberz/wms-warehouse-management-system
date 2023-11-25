<?php
$s = "SELECT * FROM tb_user";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$arr_user = [];
while($d=mysqli_fetch_assoc($q)){
  $arr_user[$d['id']] = $d['username'];
  $arr_nama_user[$d['id']] = $d['nama'];
}