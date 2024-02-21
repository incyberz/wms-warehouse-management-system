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
set_title('Data Surat Jalan');
if(isset($_POST['keyword'])){
  $keyword = clean_sql($_POST['keyword']);
  jsurl("?penerimaan&p=data_sj&keyword=$keyword");
  exit;
}
$keyword = $_GET['keyword'] ?? '';
$bg_keyword = $keyword ? 'style="background:#0f0"' : '';
$hide_clear = $keyword ? '' : 'hideit';


$sql_filter = $keyword ? "
  (
    a.kode LIKE '%$keyword%' 
  )
" : '1';

$s = "SELECT 
a.id as id_sj, 
a.kode as kode_sj ,
a.tanggal_terima,
a.kode_po,
b.kode as kode_supplier ,
b.nama as nama_supplier,
(SELECT COUNT(1) FROM tb_sj_item WHERE kode_sj=a.kode) jumlah_item,
(SELECT kode FROM tb_bbm WHERE kode_sj=a.kode) kode_bbm
FROM tb_sj a 
JOIN tb_supplier b ON a.id_supplier=b.id 
WHERE $sql_filter  
AND a.kode NOT LIKE 'STOCK%' 
ORDER BY a.tanggal_terima DESC
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_records = mysqli_num_rows($q);

$s .= "LIMIT 100";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_tampil = mysqli_num_rows($q);

$tr = '';
$i = 0;
while($d=mysqli_fetch_assoc($q)){
  $i++;
  $id = $d['id_sj'];
  // $nama_supplier = $d['nama_supplier'];
  $abu_items = $d['jumlah_item'] ? 'abu' : 'tebal merah';
  $abu_bbm = $d['kode_bbm'] ? 'abu' : 'tebal merah';
  $aksi_hapus = ($d['jumlah_item'] || $d['kode_bbm']) ? '-' : "<span class='btn_aksi' id=sj__delete__$id>$img_delete</span>";
  $kode_bbm_show = $d['kode_bbm'] ? "<a href='?penerimaan&p=bbm&kode_sj=$d[kode_sj]'>$d[kode_bbm]</a>" : $unset;
  $tgl = date('d M y',strtotime($d['tanggal_terima']));
  $tr .= "
    <tr id=source_sj__$id>
      <td>$i</td>
      <td>
        <a href='?penerimaan&p=manage_sj&kode_sj=$d[kode_sj]'>
          $d[kode_sj]
          <div class='kecil $abu_items'>$tgl | $d[jumlah_item] items</div>
        </a>
      </td>
      <td>
        $d[kode_po]
        <div class='kecil $abu_bbm'>BBM: $kode_bbm_show</div>
      </td>
      <td>$d[nama_supplier]</td>
      <td>
        $aksi_hapus
      </td>
    </tr>
  ";
}

$tambah_sj_baru = "<a class='btn btn-sm btn-success' href='?penerimaan&p=terima_sj_baru'>Terima SJ Baru</a>";

if(!$tr) $tr = "<tr><td colspan=100%><div class='alert alert-danger'>Data tidak ditemukan</div></td></tr>";

echo  "
  <div class='flexy flex-between mb2'>
    <div class=flexy>
      <div>
        <form method=post>
          <input class='form-control form-control-sm' placeholder='Filter ...' name=keyword id=keyword value='$keyword' maxlength=15 $bg_keyword>
          <button class=hideit>Filter</button>
        </form>
      </div>
      <div class='$hide_clear'><a href='?penerimaan&p=data_sj' class=kecil>Clear<span class=f18> </span></a></div>
      <div class='kecil abu'>Tampil <span class='darkblue f18'>$jumlah_tampil</span> data of $jumlah_records records</div>
    </div>
    <div>$tambah_sj_baru</div>
  </div>  

  <table class=table>
    <thead>
      <th>NO</th>
      <th>NOMOR SJ</th>
      <th>NOMOR PO</th>
      <th>SUPPLIER</th>
      <th>AKSI</th>
    </thead>
    $tr
  </table>
";