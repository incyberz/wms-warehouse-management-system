<div class="pagetitle">
  <h1>Data DO</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?do">DO Home</a></li>
      <li class="breadcrumb-item active">Data DO</li>
    </ol>
  </nav>
</div>

<?php
$s = "SELECT 
a.id as id_do, 
b.kode as kode_buyer ,
b.nama as nama_buyer 
FROM tb_do a 
JOIN tb_buyer b ON a.id_buyer=b.id 
WHERE status=1";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$tr = '';
$i = 0;
while($d=mysqli_fetch_assoc($q)){
  $i++;
  // $id_do = $d['id_do'];
  // $nama_buyer = $d['nama_buyer'];
  $tr .= "
    <tr>
      <td>$i</td>
      <td>$d[nama_buyer]</td>
      <td>edit | hapus</td>
    </tr>
  ";
}

echo $tr=='' ? div_alert('danger', 'Belum ada data DO | <a href="?do&p=tambah_do">Buat DO baru</a>') : "
  <table class=table>
    <thead>
      <th>NO</th>
      <th>NAMA DO</th>
      <th>AKSI</th>
    </thead>
    $tr
  </table>
";