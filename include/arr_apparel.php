<?php
$s = "SELECT kode,apparel FROM tb_apparel";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$arr_apparel = [];
while($d=mysqli_fetch_assoc($q)){
  $arr_apparel[$d['kode']] = $d['apparel'];
}