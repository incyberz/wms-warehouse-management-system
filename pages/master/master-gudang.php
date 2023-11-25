<?php
$s = "SELECT 
a.id as id_gudang, 
a.kode as kode_gudang, 
a.nama as nama_gudang 
FROM tb_gudang a 
WHERE 1";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$tr = '';
$i = 0;
while($d=mysqli_fetch_assoc($q)){
  $i++;
  // $id_gudang = $d['id_gudang'];
  // $nama_gudang = $d['nama_gudang'];
  $tr .= "
    <tr>
      <td>$i</td>
      <td>$d[kode_gudang]</td>
      <td>$d[nama_gudang]</td>
      <td>edit | hapus</td>
    </tr>
  ";
}

echo $tr=='' ? div_alert('danger', 'Belum ada data gudang.') : "
  <table class=table>
    <thead>
      <th>NO</th>
      <th>KODE</th>
      <th>NAMA GUDANG</th>
      <th>AKSI</th>
    </thead>
    $tr
  </table>
";