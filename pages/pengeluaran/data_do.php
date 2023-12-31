<div class="pagetitle">
  <h1>Data Delivery Order</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?pengeluaran">Pengeluaran</a></li>
      <li class="breadcrumb-item"><a href="?pengeluaran&p=terima_do">Terima DO Baru</a></li>
      <li class="breadcrumb-item active">Data DO</li>
    </ol>
  </nav>
</div>

<?php
set_title('Data DO');
if(isset($_POST['keyword'])){
  $keyword = clean_sql($_POST['keyword']);
  jsurl("?pengeluaran&p=data_do&keyword=$keyword");
  exit;
}
$keyword = $_GET['keyword'] ?? '';
$bg_keyword = $keyword ? 'style="background:#0f0"' : '';
$hide_clear = $keyword ? '' : 'hideit';


$sql_filter = $keyword ? "
  (
    a.kode_do LIKE '%$keyword%' OR  
    a.kode_artikel LIKE '%$keyword%' 
  )
" : '1';

$s = "SELECT 
a.*,
(SELECT COUNT(1) FROM tb_picking WHERE kode_do_cat=a.kode) jumlah_pick 
FROM tb_terima_do a 
WHERE $sql_filter  
ORDER BY tanggal_delivery DESC
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_records = mysqli_num_rows($q);

$s .= "LIMIT 10";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_tampil = mysqli_num_rows($q);

$tr = '';
$i = 0;
while($d=mysqli_fetch_assoc($q)){
  $i++;
  $kode_do = $d['kode_do'];
  $abu_items = $d['jumlah_pick'] ? 'abu' : 'tebal merah';
  $aksi_hapus = $d['jumlah_pick'] ? '-' : "<span class='btn_aksi' id=sj__delete__$kode_do>$img_delete</span>";
  $untuk = $d['id_kategori']==1 ? 'Aksesoris' : 'Fabric';
  $cat = $d['id_kategori']==1 ? 'aks' : 'fab';

  $tr .= "
    <tr id=source_do__$kode_do>
      <td>$i</td>
      <td>
        <a href='?pengeluaran&p=terima_do&kode_do=$d[kode_do]&cat=$cat'>
          $d[kode_do]
          <div class='kecil $abu_items'>$d[jumlah_pick] picks</div>
        </a>
      </td>
      <td>$untuk</td>
      <td>
        $aksi_hapus
      </td>
    </tr>
  ";
}

$tambah_do_baru = "<a class='btn btn-sm btn-success' href='?pengeluaran&p=terima_do'>Terima DO Baru</a>";
if(!$tr) $tr = "<tr><td colspan=100%><div class='alert alert-danger'>Data tidak ditemukan</div></td></tr>";

echo 
"
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
    <div class='mb2 kanan'>$tambah_do_baru</div>
  </div>  

  <table class=table>
    <thead>
      <th>NO</th>
      <th>NOMOR DO</th>
      <th>OTP</th>
    </thead>
    $tr
  </table>
";