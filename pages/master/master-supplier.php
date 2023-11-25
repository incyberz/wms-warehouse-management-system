<?php
$s = "SELECT 
a.id as id_supplier, 
a.kode as kode_supplier, 
a.nama as nama_supplier 
FROM tb_supplier a 
WHERE status=1";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$tr = '';
$i = 0;
while($d=mysqli_fetch_assoc($q)){
  $i++;
  // $id_supplier = $d['id_supplier'];
  // $nama_supplier = $d['nama_supplier'];
  $tr .= "
    <tr>
      <td>$i</td>
      <td>$d[kode_supplier]</td>
      <td>$d[nama_supplier]</td>
      <td>edit | hapus</td>
    </tr>
  ";
}

echo $tr=='' ? div_alert('danger', 'Belum ada data supplier.') : "
  <table class=table>
    <thead>
      <th>NO</th>
      <th>KODE</th>
      <th>NAMA SUPPLIER</th>
      <th>AKSI</th>
    </thead>
    $tr
  </table>
";