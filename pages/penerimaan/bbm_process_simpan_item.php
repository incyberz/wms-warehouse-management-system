<?php
if(isset($_POST['btn_simpan'])){
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';

  foreach ($_POST as $key => $qty_datang) {
    if(strpos("salt$key",'qty_diterima__')){
      $arr = explode('__',$key);
      $id_sj_item = $arr[1];

      $s = "UPDATE tb_sj_item SET qty_datang=$qty_datang WHERE id=$id_sj_item";
      echo $s;
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    }
  }

  $s = "UPDATE tb_bbm SET tanggal_masuk=CURRENT_TIMESTAMP WHERE kode_sj='$kode_sj'";
  echo $s;
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  jsurl("?penerimaan&p=bbm&kode_sj=$kode_sj");
  exit;
}