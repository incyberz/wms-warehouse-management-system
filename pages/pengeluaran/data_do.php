<div class="pagetitle">
  <h1>Data Delivery Order</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?pengeluaran">Pengeluaran</a></li>
      <li class="breadcrumb-item active">Delivery Order</li>
    </ol>
  </nav>
</div>

<?php
$s = "SELECT 
a.*,
(SELECT COUNT(1) FROM tb_picking WHERE kode_do=a.kode_do) jumlah_pick 
FROM tb_do a 
WHERE 1  
ORDER BY tanggal_delivery DESC
LIMIT 10
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$tr = '';
$i = 0;
while($d=mysqli_fetch_assoc($q)){
  $i++;
  $kode_do = $d['kode_do'];
  $abu_items = $d['jumlah_pick'] ? 'abu' : 'tebal merah';
  $aksi_hapus = ($d['jumlah_pick'] || $d['kode_bbm']) ? '-' : "<span class='btn_aksi' id=sj__delete__$kode_do>$img_delete</span>";
  $tr .= "
    <tr id=source_do__$kode_do>
      <td>$i</td>
      <td>
        <a href='?pengeluaran&p=terima_do&kode_do=$d[kode_do]'>
          $d[kode_do]
          <div class='kecil $abu_items'>$d[jumlah_pick] picks</div>
        </a>
      </td>
      <td>
        $aksi_hapus
      </td>
    </tr>
  ";
}

$tambah_do_baru = "<a class='btn btn-sm btn-success' href='?pengeluaran&p=terima_do'>Terima DO Baru</a>";

echo $tr=='' ? div_alert('danger', "Belum ada data PO | $tambah_do_baru") : "
  <div class='mb2 kanan'>$tambah_do_baru</div>
  <table class=table>
    <thead>
      <th>NO</th>
      <th>NOMOR DO</th>
    </thead>
    $tr
  </table>
";