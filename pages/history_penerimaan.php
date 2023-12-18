
<?php 
// to do : fix decimal
include 'include/date_managements.php';
$p = 'penerimaan'; // untuk navigasi
$cat= $_GET['cat'] ?? 'aks'; //default AKS

$arr_waktu = [
  'hari_ini' => 'Hari ini',
  'kemarin' => 'Kemarin',
  'minggu_ini' => 'Minggu ini',
  'bulan_ini' => 'Bulan ini',
  'tahun_ini' => 'Tahun ini',
  'all_time' => 'All time',
];

$filter_waktu = $_GET['waktu'] ?? 'hari_ini';
$opt_waktu = '';
foreach ($arr_waktu as $waktu => $nama_waktu) {
  $selected = $filter_waktu==$waktu ? 'selected' : '';
  $opt_waktu.= "<option value=$waktu $selected>$nama_waktu</option>";
}

$filter_po = $_GET['po'] ?? '';
$filter_id = $_GET['id'] ?? '';
$filter_proyeksi = $_GET['proyeksi'] ?? '';
$filter_ppic = $_GET['ppic'] ?? '';
if(isset($_POST['btn_cari'])){
  jsurl("?$parameter&cat=$cat&po=$_POST[filter_po]&id=$_POST[filter_id]&waktu=$_POST[filter_waktu]&proyeksi=$_POST[filter_proyeksi]&ppic=$_POST[filter_ppic]");
}

$clear_filter = 'Filter:';
if(
  $filter_po!=''||
  $filter_id!=''||
  $filter_proyeksi!=''||
  $filter_ppic!='' 
)$clear_filter = "<a href='?$parameter&cat=$cat'>Clear Text Filter</a>";

$bg_waktu = $filter_waktu=='all_time' ? '' : 'bg-hijau';
$bg_po = $filter_po=='' ? '' : 'bg-hijau';
$bg_id = $filter_id=='' ? '' : 'bg-hijau';
$bg_proyeksi = $filter_proyeksi=='' ? '' : 'bg-hijau';
$bg_ppic = $filter_ppic=='' ? '' : 'bg-hijau';

$id_kategori = $cat=='aks' ? 1 : 2;

if($filter_waktu=='all_time'){$where_date = '1';}else 
if($filter_waktu=='hari_ini'){$where_date = "a.tanggal_masuk >= '$today' ";}else 
if($filter_waktu=='kemarin'){$where_date = "a.tanggal_masuk >= '$kemarin' AND a.tanggal_masuk < '$today' ";}else 
if($filter_waktu=='minggu_ini'){$where_date = "a.tanggal_masuk >= '$ahad_skg' AND a.tanggal_masuk < '$ahad_depan' ";}else 
if($filter_waktu=='bulan_ini'){$where_date = "a.tanggal_masuk >= '$awal_bulan' ";}else
if($filter_waktu=='tahun_ini'){$where_date = "a.tanggal_masuk >= '$awal_tahun' ";} 


$where_po = $filter_po=='' ? '1' : "a.kode_po LIKE '%$filter_po%' ";
$where_id = $filter_id=='' ? '1' : "(a.kode_barang LIKE '%$filter_id%' OR b.nama LIKE '%$filter_id%' OR b.keterangan LIKE '%$filter_id%' )";
$where_proyeksi = $filter_proyeksi=='' ? '1' : "a.proyeksi LIKE '%$filter_proyeksi%' ";
$where_ppic = $filter_ppic=='' ? '1' : "a.kode_ppic LIKE '%$filter_ppic%' ";


$sql_from = "FROM tb_penerimaan a 
JOIN tb_barang b ON a.kode_barang=b.kode 
";

$sql_where = "
WHERE b.id_kategori=$id_kategori 
AND $where_date 
AND $where_po 
AND $where_id 
AND $where_proyeksi 
AND $where_ppic 
";

$s = "SELECT 1 $sql_from $sql_where ";
// echo "<pre>$s</pre>";
// $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
// $total_row = mysqli_num_rows($q);

$s = "SELECT 
a.*,
c.*,
b.satuan,
b.nama as nama_barang,  
b.keterangan as keterangan_barang 
FROM tb_penerimaan a 
JOIN tb_barang b ON a.kode_barang=b.kode 
JOIN tb_lokasi c ON a.kode_lokasi=c.kode 

$sql_where

LIMIT 10 

";


// $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
// $jumlah_row = mysqli_num_rows($q);

$s = "SELECT 
a.id,
a.qty,
a.proyeksi,
a.kode_ppic,
a.qty_reject,
b.kode_po,
b.awal_terima,
b.akhir_terima,
c.tanggal_masuk,
c.awal_masuk,
c.akhir_masuk,
d.kode as kode_supplier,
d.nama as nama_supplier,
e.kode as kode_barang,
e.nama as nama_barang,
e.keterangan as keterangan_barang,
e.satuan,
(
  SELECT SUM(qty) FROM tb_sj_subitem 
  WHERE id_sj_item=a.id 
  ) qty_diterima,
(
  SELECT COUNT(1) FROM tb_sj_subitem 
  WHERE id_sj_item=a.id 
  ) jumlah_subitem  
FROM tb_sj_item a  
JOIN tb_sj b ON a.kode_sj=b.kode
JOIN tb_bbm c ON b.kode=c.kode_sj
JOIN tb_supplier d ON b.id_supplier=d.id 
JOIN tb_barang e ON a.kode_barang=e.kode 
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$total_row = mysqli_num_rows($q);
$jumlah_row = mysqli_num_rows($q);


$tr_hasil = $jumlah_row==0 ? "<tr><td colspan=100%><div class='alert alert-danger'>Data tidak ditemukan..</div></td></tr>" : '';
while($d=mysqli_fetch_assoc($q)){
  $id=$d['id'];
  $satuan=$d['satuan'];
  $kode_ppic=$d['kode_ppic'] ?? $unset;
  $proyeksi=$d['proyeksi'] ?? $unset;


  $tgl = date('d M Y',strtotime($d['tanggal_masuk']));
  $awal_terima = $d['awal_terima'] ?? '';
  $akhir_terima = $d['akhir_terima'] ?? '';
  $awal_masuk = $d['awal_masuk'] ?? '';
  $akhir_masuk = $d['akhir_masuk'] ?? '';

  $datang = $awal_terima=='' ? '' : '<img src=assets/img/icons/wms/datang2.png height=14px> '. date('h:i',strtotime($awal_terima));
  $datang = $akhir_terima=='' ? $datang : "$datang s.d ". date('h:i',strtotime($akhir_terima));

  $masuk = $awal_masuk=='' ? '' : '<img src=assets/img/icons/wms/masuk.png height=14px> '. date('h:i',strtotime($awal_masuk));
  $masuk = $akhir_masuk=='' ? $masuk : "$masuk s.d ". date('h:i',strtotime($akhir_masuk));



  $qty = floatval($d['qty']);
  $qty_diterima = floatval($d['qty_diterima']);
  $qty_reject = $d['qty_reject']>0 ? '<img src=assets/img/icons/wms/qty_reject.png height=14px> '.floatval($d['qty_reject']) : '';



  $qty_lebih = $qty_diterima - $qty;
  $qty_kurang = $qty - $qty_diterima;
  
  $qty_pas_show = $qty==$qty_diterima ? "diterima: $qty $satuan <img src=assets/img/icons/check.png height=14px>" : '';
  $qty_lebih_show = $qty_lebih>0 ? "<img src=assets/img/icons/wms/qty_lebih.png height=14px> $qty_lebih" : '';
  $qty_kurang_show = $qty_kurang>0 ? "<img src=assets/img/icons/wms/qty_kurang.png height=14px> $qty_kurang" : '';
  
  $qty_reject_show = ''; //zzz sementara


  if($d['jumlah_subitem']){
    $s2 = "SELECT a.kode_lokasi, b.brand  
    FROM tb_sj_subitem a 
    JOIN tb_lokasi b ON a.kode_lokasi=b.kode 
    WHERE a.id_sj_item=$id";
    $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));

    $arr = [];
    while($d2=mysqli_fetch_assoc($q2)){
      // $arr.= "$d2[kode_lokasi] ";
      if(in_array($d2['kode_lokasi'],$arr)) continue;
      $arr[$d2['kode_lokasi']] = $d2['brand'] ?? 'no-brand';
    }

    $li = '';
    foreach ($arr as $kode_lokasi => $brand) {
      $li.= "<li>$kode_lokasi - $brand</li>";
    }

    $locations = "<ul style='padding-left:15px'>$li</ul>";
  }else{

    $locations = $img_warning. ' <span class=red>Belum ada subitem</span>';
  }
  

  $tr_hasil .= "
    <tr id=tr__$id>
      <td>
        <div>$locations</div>
      </td>
      <td>
        <div>$d[kode_po]</div>
        <div class='kecil abu'>Supplier: $d[nama_supplier]</div>
      </td>
      <td>
        <div>$d[kode_barang]</div>
        <div class='kecil abu'>$d[nama_barang]</div>
        <div class='kecil abu'>$d[keterangan_barang]</div>
      </td>
      <td>
        <div>$tgl</div>
        <div class='kecil abu'>$datang</div>
        <div class='kecil abu'>$masuk</div>
      </td>
      <td>
        <div>$qty $satuan</div>
        <div class='kecil abu'>$qty_pas_show</div>
        <div class='kecil abu'>$qty_lebih_show</div>
        <div class='kecil abu'>$qty_kurang_show</div>
        <div class='kecil abu'>$qty_reject_show</div>
      </td>
      <td>
        <div class=kecil>$proyeksi</div>
      </td>
      <td>
        <div class=kecil>$kode_ppic</div>
      </td>
    </tr>
  ";
}






$form_cari = "
  <form method=post>
    <tr>
      <td>Filter:</td>
      <td><input type='text' class='form-control form-control-sm $bg_po' placeholder='PO' name=filter_po value='$filter_po' ></td>
      <td><input type='text' class='form-control form-control-sm $bg_id' placeholder='ID' name=filter_id value='$filter_id' ></td>
      <td>
        <select class='form-control form-control-sm $bg_waktu' name=filter_waktu>$opt_waktu</select>
      </td>
      <td><input type='text' class='form-control form-control-sm $bg_proyeksi' placeholder='proyeksi' name=filter_proyeksi value='$filter_proyeksi' ></td>
      <td><input type='text' class='form-control form-control-sm $bg_ppic' placeholder='ppic' name=filter_ppic value='$filter_ppic' ></td>
      <td>
        <button class='btn btn-success btn-sm' name=btn_cari>Cari</button>
      </td>
    </tr>
  </form>

";
?>

<div class="pagetitle">
  <h1>History Penerimaan</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item active">History</li>
    </ol>
  </nav>
</div>


<section class='section'>
  <?php //include 'pages/sheet_nav.php'; ?>
  <div id="blok_penerimaan" class='mt2'>

    <table class='table'>
      <tr>
        <td colspan=100% class='kecil abu'><?=$clear_filter?></td>
      </tr>
      <?=$form_cari?>
      <tr>
        <td colspan=100%><span class="darkblue"><?=$jumlah_row?></span> <span class="abu kecil">data dari <b><?=$total_row ?></b> records</span></td>
      </tr>
      <tr class='tebal gradasi-hijau '>
        <td>Lokasi</td>
        <td>PO / Supplier</td>
        <td>ID / Item / Keterangan</td>
        <td>Tanggal Masuk</td>
        <td>QTY / Data Lebih</td>
        <td>Proyeksi</td>
        <td>PPIC</td>
      </tr>
      <?=$tr_hasil?>
      <tr>
        <td colspan=100% class='kecil abu'>Tambah Penerimaan:</td>
      </tr>
      <?php //include 'penerimaan-new.php'; ?>



    </table>

  </div>
</section>