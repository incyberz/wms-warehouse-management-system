<?php
if(isset($_POST['btn_verifikasi'])){

  ONLY('WHAS');
  $kode_sj = $_GET['kode_sj'] ?? die(erid('kode_sj'));
  if($kode_sj=='') die(erid('kode_sj::null'));

  $kode_sj = clean_sql($kode_sj);

  $s = "UPDATE tb_bbm SET diverifikasi_oleh=$id_user, tanggal_verifikasi=CURRENT_TIMESTAMP WHERE kode_sj='$kode_sj'";
  // die($s);
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));



  echo div_alert('success',"Verifikasi sukses. | <a href='?penerimaan&p=bbm&kode_sj=$kode_sj'>Kembali ke BBM</a>");
  exit;
}