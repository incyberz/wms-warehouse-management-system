<div class="pagetitle">
  <h1>Data PO</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item active">Data PO</li>
    </ol>
  </nav>
</div>

<?php
$s = "SELECT 
a.id as id_po, 
a.kode as kode_po ,
b.kode as kode_supplier ,
b.nama as nama_supplier 
FROM tb_sj a 
JOIN tb_supplier b ON a.kode_supplier=b.kode 
WHERE 1  
AND a.kode NOT LIKE 'STOCK%'
LIMIT 10
";
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
      <td><a href='?penerimaan&p=manage_sj&kode_sj=$d[kode_po]'>$d[kode_po]</a></td>
      <td>$d[nama_supplier]</td>
      <td>edit | hapus</td>
    </tr>
  ";
}

echo $tr=='' ? div_alert('danger', 'Belum ada data PO | <a href="?penerimaan&p=manage_sj&aksi=tambah">Buat PO baru</a>') : "
  <div class='mb2 kanan'><a class='btn btn-success btn-sm' href='?penerimaan&p=manage_sj&aksi=tambah'>Buat PO baru</a></div>
  <table class=table>
    <thead>
      <th>NO</th>
      <th>NOMOR PO</th>
      <th>SUPPLIER</th>
      <th>AKSI</th>
    </thead>
    $tr
  </table>
";