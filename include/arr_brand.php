<?php
$s = "SELECT kode,brand FROM tb_brand";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$arr_brand = [];
while($d=mysqli_fetch_assoc($q)){
  $arr_brand[$d['kode']] = $d['brand'];
}