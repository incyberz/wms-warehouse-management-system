<?php
$s = "SELECT 
a.id as id_buyer, 
a.nama as nama_buyer 
FROM tb_buyer a 
WHERE status=1";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$tr = '';
$i = 0;
while($d=mysqli_fetch_assoc($q)){
  $i++;
  // $id_buyer = $d['id_buyer'];
  // $nama_buyer = $d['nama_buyer'];
  $tr .= "
    <tr>
      <td>$i</td>
      <td>$d[nama_buyer]</td>
      <td>edit | hapus</td>
    </tr>
  ";
}

echo $tr=='' ? div_alert('danger', 'Belum ada data buyer.') : "
  <table class=table>
    <thead>
      <th>NO</th>
      <th>NAMA BUYER</th>
      <th>AKSI</th>
    </thead>
    $tr
  </table>
";