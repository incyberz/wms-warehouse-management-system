<?php
$s = "SELECT 
a.id as id_barang, 
a.kode as kode_barang, 
a.nama as nama_barang 
FROM tb_barang a 
WHERE 1";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$tr = '';
$i = 0;
while($d=mysqli_fetch_assoc($q)){
  $i++;
  // $id_barang = $d['id_barang'];
  // $nama_barang = $d['nama_barang'];
  $tr .= "
    <tr>
      <td>$i</td>
      <td>$d[kode_barang]</td>
      <td>$d[nama_barang]</td>
      <td>edit | hapus</td>
    </tr>
  ";
}

echo $tr=='' ? div_alert('danger', 'Belum ada data barang.') : "
  <table class=table>
    <thead>
      <th>NO</th>
      <th>KODE</th>
      <th>NAMA BARANG</th>
      <th>AKSI</th>
    </thead>
    $tr
  </table>
";