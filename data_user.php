<?php
$profile_na = "assets/img/profile_na.jpg";
if($username==''){
  // belum login
  $id_user = '';
  $is_login = '';
  $id_role = 0;
  $sebagai = 'Pengunjung';
  $nama_user = '';
  $src_profile = $profile_na;
}else{
  //telah login
  $s = "SELECT a.*, 
  a.kode as username,   
  a.nama as nama_user,  
  b.kode as sebagai,
  b.nama as jabatan  
  FROM tb_user a 
  JOIN tb_role b ON a.id_role=b.id 
  WHERE a.kode='$username'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0) die('Data username tidak ada.');

  $d = mysqli_fetch_assoc($q);

  $id_user = $d['id'];
  $is_login = 1;
  $id_role = $d['id_role'];
  $sebagai = $d['sebagai'];
  $nama_user = $d['nama_user'];
  $jabatan = $d['jabatan'];

  $_SESSION['wms_role'] = $sebagai;

  $nama_user = ucwords(strtolower($nama_user));
  $jabatan = ucwords(strtolower($jabatan));

  $src_profile = "assets/img/user/$id_user.jpg";
  if(!file_exists($src_profile)) $src_profile = $profile_na;
}