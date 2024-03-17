<?php
$judul = 'Super Delete Importer';
set_title($judul);
echo "
<div class='pagetitle'>
  <h1>$judul</h1>
</div>
";


$s = "SELECT kode as kode_sj FROM tb_sj WHERE kode LIKE '%-999'";
echolog($s, false);
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
while ($d = mysqli_fetch_assoc($q)) {
  $kode_sj = $d['kode_sj'];
  $s2 = "SELECT id as id_sj_item FROM tb_sj_item WHERE kode_sj='$d[kode_sj]'";
  echolog('-- ' . $s2);
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  while ($d2 = mysqli_fetch_assoc($q2)) {
    $id_sj_item = $d2['id_sj_item'];
    $s3 = "SELECT id as id_kumulatif FROM tb_sj_kumulatif WHERE id_sj_item=$d2[id_sj_item]";
    echolog('-- -- ' . $s3);
    $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
    while ($d3 = mysqli_fetch_assoc($q3)) {
      $id_kumulatif = $d3['id_kumulatif'];
      $s4 = "DELETE FROM tb_pick WHERE id_kumulatif=$d3[id_kumulatif]";
      echolog('-- -- -- ' . $s4, false);
      $q4 = mysqli_query($cn, $s4) or die(mysqli_error($cn));
      echolog('sukses');
      $s4 = "DELETE FROM tb_roll WHERE id_kumulatif=$d3[id_kumulatif]";
      echolog('-- -- -- ' . $s4, false);
      $q4 = mysqli_query($cn, $s4) or die(mysqli_error($cn));
      echolog('sukses');
    }

    $s3 = "DELETE FROM tb_sj_kumulatif WHERE id_sj_item=$d2[id_sj_item]";
    echolog('-- -- ' . $s3);
    $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
  }


  $s2 = "DELETE FROM tb_sj_item WHERE kode_sj='$d[kode_sj]'";
  echolog('-- ' . $s2);
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
}

$s = "DELETE FROM tb_sj WHERE kode LIKE '%-999'";
echolog($s, false);
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
echolog('sukses');
