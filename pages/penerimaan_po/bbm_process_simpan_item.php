<?php
if(isset($_POST['btn_simpan'])){
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';
  $id_bbm = $_POST['id_bbm'];

  foreach ($_POST as $key => $qty_diterima) {
    if(strpos("salt$key",'qty_diterima__')){
      $arr = explode('__',$key);
      $id_po_item = $arr[1];

      $s = "SELECT id as id_bbm_item FROM tb_bbm_item WHERE id_bbm=$id_bbm AND id_po_item=$id_po_item";
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      if(mysqli_num_rows($q)>1){
        die("Tidak boleh 2 id_bbm_item untuk 1 id_bbm<hr>id_bbm: $id_bbm | id_po_item: $id_po_item ");
      }else{
        if(mysqli_num_rows($q)){
          $d = mysqli_fetch_assoc($q);
          $s = "UPDATE tb_bbm_item SET qty_diterima=$qty_diterima WHERE id=$d[id_bbm_item]";
        }else{
          $s = "INSERT INTO tb_bbm_item (id_bbm,id_po_item,qty_diterima) VALUES ($id_bbm,$id_po_item,$qty_diterima)";
        }
        $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      }
    }
  }

  $s = "UPDATE tb_bbm SET tanggal_terima=CURRENT_TIMESTAMP WHERE id=$id_bbm";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  jsurl("?po&p=terima_barang&no_po=$no_po");
  exit;
}