<?php
$s = "SELECT 
a.id as id_kategori, 
a.kode as kode_kategori, 
a.kategori as nama_kategori 
FROM tb_kategori a 
WHERE 1";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$tr = '';
$i = 0;
while($d=mysqli_fetch_assoc($q)){
  $i++;
  // $id_kategori = $d['id_kategori'];
  // $nama_kategori = $d['nama_kategori'];
  $tr .= "
    <tr>
      <td>$i</td>
      <td>$d[kode_kategori]</td>
      <td>$d[nama_kategori]</td>
      <td>edit | hapus</td>
    </tr>
  ";
}

echo $tr=='' ? div_alert('danger', 'Belum ada data kategori.') : "
  <table class=table>
    <thead>
      <th>NO</th>
      <th>KODE</th>
      <th>KATEGORI</th>
      <th>AKSI</th>
    </thead>
    $tr
  </table>
";