<?php
$s = "SELECT kode,nama FROM tb_pic";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$arr_pic = [];
while($d=mysqli_fetch_assoc($q)){
  $arr_pic[$d['kode']] = $d['nama'];
}