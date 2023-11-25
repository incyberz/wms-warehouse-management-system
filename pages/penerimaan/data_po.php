<div class="pagetitle">
  <h1>Data PO</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?po">PO Home</a></li>
      <li class="breadcrumb-item active">Data PO</li>
    </ol>
  </nav>
</div>

<?php
$s = "SELECT 
a.id as id_po, 
b.kode as kode_supplier ,
b.nama as nama_supplier 
FROM tb_po a 
JOIN tb_supplier b ON a.id_supplier=b.id 
WHERE status=1";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$tr = '';
$i = 0;
while($d=mysqli_fetch_assoc($q)){
  $i++;
  // $id_po = $d['id_po'];
  // $nama_supplier = $d['nama_supplier'];
  $tr .= "
    <tr>
      <td>$i</td>
      <td>$d[nama_supplier]</td>
      <td>edit | hapus</td>
    </tr>
  ";
}

echo $tr=='' ? div_alert('danger', 'Belum ada data PO | <a href="?po&p=tambah_po">Buat PO baru</a>') : "
  <table class=table>
    <thead>
      <th>NO</th>
      <th>NAMA PO</th>
      <th>AKSI</th>
    </thead>
    $tr
  </table>
";