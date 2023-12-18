<div class="pagetitle">
  <h1>Data Surat Jalan</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item active">Surat Jalan</li>
    </ol>
  </nav>
</div>

<?php
$s = "SELECT 
a.id as id_sj, 
a.kode as kode_sj ,
a.kode_po,
b.kode as kode_supplier ,
b.nama as nama_supplier 
FROM tb_sj a 
JOIN tb_supplier b ON a.id_supplier=b.id 
WHERE 1  
AND a.kode NOT LIKE 'STOCK%' 
ORDER BY tanggal_terima DESC
LIMIT 10
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$tr = '';
$i = 0;
while($d=mysqli_fetch_assoc($q)){
  $i++;
  // $id_sj = $d['id_sj'];
  // $nama_supplier = $d['nama_supplier'];
  $tr .= "
    <tr>
      <td>$i</td>
      <td>$d[kode_po]</td>
      <td><a href='?penerimaan&p=manage_sj&kode_sj=$d[kode_sj]'>$d[kode_sj]</a></td>
      <td>$d[nama_supplier]</td>
      <td>edit | hapus</td>
    </tr>
  ";
}

$tambah_sj_baru = "<a class='btn btn-sm btn-success' href='?penerimaan&p=terima_sj_baru'>Terima SJ Baru</a>";

echo $tr=='' ? div_alert('danger', "Belum ada data PO | $tambah_sj_baru") : "
  <div class='mb2 kanan'>$tambah_sj_baru</div>
  <table class=table>
    <thead>
      <th>NO</th>
      <th>NOMOR PO</th>
      <th>NOMOR SJ</th>
      <th>SUPPLIER</th>
      <th>AKSI</th>
    </thead>
    $tr
  </table>
";