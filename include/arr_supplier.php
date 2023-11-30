<?php
$s = "SELECT id,nama FROM tb_supplier";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$arr_supplier = [];
while($d=mysqli_fetch_assoc($q)){
  $arr_supplier[$d['id']] = $d['nama'];
}