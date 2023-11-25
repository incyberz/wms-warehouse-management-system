<?php
if($username==''){
  // belum login
  $id_user = '';
  $is_login = '';
  $id_role = 0;
  $sebagai = 'Pengunjung';
  $nama_user = '';
}else{
  //telah login
  $s = "SELECT a.* 
  FROM tb_user a WHERE a.username='$username'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0) die('Data username tidak ada.');

  $d = mysqli_fetch_assoc($q);

  $id_user = $d['id'];
  $is_login = 1;
  $id_role = $d['id_role'];
  $sebagai = $d['id_role']==2 ? 'Admin' : 'Pembeli';
  $nama_user = $d['nama_user'];
}