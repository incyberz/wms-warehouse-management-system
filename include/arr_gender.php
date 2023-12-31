<?php
$s = "SELECT kode,gender FROM tb_gender";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$arr_gender = [];
while($d=mysqli_fetch_assoc($q)){
  $arr_gender[$d['kode']] = $d['gender'];
}