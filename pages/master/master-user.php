<?php
$s = "SELECT 
a.id as id_user, 
a.nama as nama_user,
b.sebagai  
FROM tb_user a 
JOIN tb_role b ON a.id_role=b.id 
WHERE status=1";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$tr = '';
$i = 0;
while($d=mysqli_fetch_assoc($q)){
  $i++;
  // $id_user = $d['id_user'];
  // $nama_user = $d['nama_user'];
  $tr .= "
    <tr>
      <td>$i</td>
      <td>$d[nama_user]</td>
      <td>$d[sebagai]</td>
      <td>edit | hapus</td>
    </tr>
  ";
}

echo $tr=='' ? div_alert('danger', 'Belum ada data user.') : "
  <table class=table>
    <thead>
      <th>NO</th>
      <th>NAMA USER</th>
      <th>ROLE</th>
      <th>AKSI</th>
    </thead>
    $tr
  </table>
";