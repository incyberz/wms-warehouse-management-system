<?php
if(isset($_POST['btn_verifikasi'])){

  ONLY('WHAS');

  $s = "UPDATE tb_bbm SET diverifikasi_oleh=$id_user, tanggal_verifikasi=CURRENT_TIMESTAMP WHERE id=$_POST[id_bbm]";
  // die($s);
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));



  echo div_alert('success',"Verifikasi sukses.");
  jsurl("?po&p=terima_barang&no_po=$_GET[no_po]&id_bbm=$_GET[id_bbm]");
  exit;
}