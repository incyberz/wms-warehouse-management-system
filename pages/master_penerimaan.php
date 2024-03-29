
<?php 
set_title("Master Penerimaan");
// to do : fix decimal
include 'include/date_managements.php';
$p = 'penerimaan'; // untuk navigasi
$cat= $_GET['cat'] ?? 'aks'; //default AKS
$jenis_barang = $cat=='aks' ? 'Aksesoris' : 'Fabric';

$arr_waktu = [
  'hari_ini' => 'Hari ini',
  'kemarin' => 'Kemarin',
  'minggu_ini' => 'Minggu ini',
  'bulan_ini' => 'Bulan ini',
  'tahun_ini' => 'Tahun ini',
  'all_time' => 'All time',
];

$filter_waktu = $_GET['waktu'] ?? 'all_time';
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
if($filter_waktu=='hari_ini'){$where_date = "c.tanggal_masuk >= '$today' ";}else 
if($filter_waktu=='kemarin'){$where_date = "c.tanggal_masuk >= '$kemarin' AND c.tanggal_masuk < '$today' ";}else 
if($filter_waktu=='minggu_ini'){$where_date = "c.tanggal_masuk >= '$ahad_skg' AND c.tanggal_masuk < '$ahad_depan' ";}else 
if($filter_waktu=='bulan_ini'){$where_date = "c.tanggal_masuk >= '$awal_bulan' ";}else
if($filter_waktu=='tahun_ini'){$where_date = "c.tanggal_masuk >= '$awal_tahun' ";} 


$where_po = $filter_po=='' ? '1' : "b.kode_po LIKE '%$filter_po%' ";
$where_id = $filter_id=='' ? '1' : "(a.kode_barang LIKE '%$filter_id%' OR e.nama LIKE '%$filter_id%' OR e.keterangan LIKE '%$filter_id%' )";
$where_proyeksi = $filter_proyeksi=='' ? '1' : "a.proyeksi LIKE '%$filter_proyeksi%' ";
$where_ppic = $filter_ppic=='' ? '1' : "a.kode_ppic LIKE '%$filter_ppic%' ";


$sql_from = "FROM tb_penerimaan a 
JOIN tb_barang b ON a.kode_barang=b.kode 
";

$sql_where = "
WHERE e.id_kategori=$id_kategori 
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
a.id as id_sj_item,
a.qty,
a.proyeksi,
a.kode_ppic,
a.kode_sj,
b.kode_po,
b.awal_terima,
b.akhir_terima,
b.tanggal_terima,
d.kode as kode_supplier,
d.nama as nama_supplier,
e.kode as kode_barang,
e.nama as nama_barang,
e.keterangan as keterangan_barang,
e.satuan,
(
  SELECT SUM(qty) FROM tb_sj_kumulatif 
  WHERE id_sj_item=a.id 
  ) qty_datang,
(
  SELECT COUNT(1) FROM tb_sj_kumulatif 
  WHERE id_sj_item=a.id 
  ) jumlah_kumulatif_item,
-- ==================================================
-- GET REGULAR DATA
-- ==================================================
(
  SELECT SUM(p.tmp_qty) FROM tb_sj_kumulatif p 
  JOIN tb_retur q ON p.id=q.id 
  WHERE p.id_sj_item=a.id AND is_fs is null
  ) qty_diterima_with_qc_no_fs,
(
  SELECT count(1) FROM tb_sj_kumulatif 
  WHERE id_sj_item=a.id AND is_fs is null
  ) count_kumulatif_item_no_fs,
(
  SELECT sum(p.qty) FROM tb_retur p 
  JOIN tb_sj_kumulatif q ON p.id=q.id  
  WHERE q.id_sj_item=a.id AND q.is_fs is null 
  ) qty_retur,  
(
  SELECT count(1) FROM tb_retur p 
  JOIN tb_sj_kumulatif q ON p.id=q.id  
  WHERE q.id_sj_item=a.id  AND q.is_fs is null 
  ) count_retur,  
(
  SELECT sum(p.qty) FROM tb_ganti p 
  JOIN tb_retur q ON p.id=q.id 
  JOIN tb_sj_kumulatif r ON q.id=r.id  
  WHERE r.id_sj_item=a.id  AND r.is_fs is null 
  ) qty_balik,
-- ==================================================
-- GET FS DATA
-- ==================================================
(
  SELECT SUM(p.tmp_qty) FROM tb_sj_kumulatif p 
  JOIN tb_retur q ON p.id=q.id 
  WHERE p.id_sj_item=a.id AND is_fs is not null
  ) qty_diterima_with_qc_fs,
(
  SELECT count(1) FROM tb_sj_kumulatif 
  WHERE id_sj_item=a.id AND is_fs is not null
  ) count_kumulatif_item_fs,
(
  SELECT sum(p.qty) FROM tb_retur p 
  JOIN tb_sj_kumulatif q ON p.id=q.id  
  WHERE q.id_sj_item=a.id AND q.is_fs is not null 
  ) qty_retur_fs,  
(
  SELECT count(1) FROM tb_retur p 
  JOIN tb_sj_kumulatif q ON p.id=q.id  
  WHERE q.id_sj_item=a.id  AND q.is_fs is not null 
  ) count_retur_fs,  
(
  SELECT sum(p.qty) FROM tb_ganti p 
  JOIN tb_retur q ON p.id=q.id 
  JOIN tb_sj_kumulatif r ON q.id=r.id  
  WHERE r.id_sj_item=a.id  AND r.is_fs is not null 
  ) qty_balik_fs

FROM tb_sj_item a  
JOIN tb_sj b ON a.kode_sj=b.kode
JOIN tb_supplier d ON b.kode_supplier=d.kode 
JOIN tb_barang e ON a.kode_barang=e.kode 

$sql_where
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$total_row = mysqli_num_rows($q);
$jumlah_row = mysqli_num_rows($q);


$tr_hasil = $jumlah_row==0 ? "<tr><td colspan=100%><div class='alert alert-danger'>Data tidak ditemukan..</div></td></tr>" : '';
while($d=mysqli_fetch_assoc($q)){
  $id=$d['id'];
  $satuan=$d['satuan'];
  $kode_sj=$d['kode_sj'];
  $count_kumulatif_item_no_fs=$d['count_kumulatif_item_no_fs'];
  $count_kumulatif_item_fs=$d['count_kumulatif_item_fs'];
  $count_retur=$d['count_retur'];
  $count_retur_fs=$d['count_retur_fs'];
  $kode_ppic=$d['kode_ppic'] ?? "PIC: $unset";
  $proyeksi=$d['proyeksi'] ?? "Proyeksi: $unset";

  $id_sj_item = $id;

  $tanggal_terima = date('d M Y',strtotime($d['tanggal_terima']));
  $awal_terima = $d['awal_terima'] ?? '';
  $akhir_terima = $d['akhir_terima'] ?? '';
  $awal_masuk = $d['awal_masuk'] ?? '';
  $akhir_masuk = $d['akhir_masuk'] ?? '';

  $datang = $awal_terima=='' ? '' : '<img src=assets/img/icons/wms/datang2.png height=14px> '. date('h:i',strtotime($awal_terima));
  $datang = $akhir_terima=='' ? $datang : "$datang s.d ". date('h:i',strtotime($akhir_terima));

  $masuk = $awal_masuk=='' ? '' : '<img src=assets/img/icons/wms/masuk.png height=14px> '. date('h:i',strtotime($awal_masuk));
  $masuk = $akhir_masuk=='' ? $masuk : "$masuk s.d ". date('h:i',strtotime($akhir_masuk));

  $datang = $datang=='' ? '' : "<a href='?penerimaan&p=manage_sj&kode_sj=$d[kode_sj]'>$datang</a>";


  $qty = floatval($d['qty']);
  $qty_retur = floatval($d['qty_retur']);
  $qty_balik_fs = floatval($d['qty_balik_fs']);
  $qty_retur_fs = floatval($d['qty_retur_fs']);
  $qty_balik = floatval($d['qty_balik']);
  $qty_datang = floatval($d['qty_datang']);
  $qty_diterima_with_qc_no_fs = floatval($d['qty_diterima_with_qc_no_fs']);
  $qty_diterima_with_qc_fs = floatval($d['qty_diterima_with_qc_fs']);



  $qty_lebih = $qty_datang - $qty;
  $qty_kurang = $qty - $qty_datang;
  $qty_tr_fs = $qty_lebih>0 ? $qty_lebih : 0;
  
  $pas = $qty==$qty_datang ? '<img src=assets/img/icons/check.png height=14px>' : '';
  $qty_diterima_show = "diterima: $qty_datang $satuan $pas";
  $qty_lebih_show = $qty_lebih>0 ? "<img src=assets/img/icons/wms/qty_lebih.png height=14px> $qty_lebih" : '';
  $qty_kurang_show = $qty_kurang>0 ? "<img src=assets/img/icons/wms/qty_kurang.png height=14px> $qty_kurang" : '';
  


  if($d['jumlah_kumulatif_item']){
    $s2 = "SELECT a.kode_lokasi, b.brand  
    FROM tb_sj_kumulatif a 
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
      $li.= "<li>$kode_lokasi  <div class='f14 abu'>$brand</div></li>";
    }

    $locations = "<ul style='padding-left:15px'>$li</ul>";
  }else{

    $locations = "<a href='?penerimaan&p=manage_sj_kumulatif&kode_sj=$kode_sj&id_sj_item=$id'>$img_warning  <span class='red kecil'>Belum ada subitem</span></a>";
  }

  $count_kumulatif_item_with_fs = $count_kumulatif_item_no_fs+$count_kumulatif_item_fs;

  $fs_count_show = $count_kumulatif_item_fs ? " | <span class='kecil tebal biru'>$count_kumulatif_item_fs FS</span>" : '';
  if($count_kumulatif_item_with_fs!=$count_retur){
    if($count_retur){
      $retur_show = "<span class=red>QC Sebagian</span>";
    }else{
      $retur_show = "<span class=red>Belum QC</span>";
    }
  }else{
    if($qty_retur){
      $retur_show = "Retur: $qty_retur";
    }else{
      $retur_show = $count_kumulatif_item_no_fs ? "<span class=green>No Retur $img_check</span>" : '-';
    }
  }

  if($qty_retur){
    if($qty_retur==$qty_balik && $count_retur==$count_kumulatif_item_no_fs){
      $terima_retur_show = "<div><a href='?retur&id_sj_item=$id_sj_item'>Balik: $qty_balik $img_check</a></div>";
    }else{
      $terima_retur_show = "<div><a href='?retur&id_sj_item=$id_sj_item'><span class=red>Balik: $qty_balik</span> $img_warning</a></div>";
    }
  }else{
    if($qty_retur){ // belum balik ada retur
      $terima_retur_show = "<div class=red>Belum Balik</div>";
    }else{ // belum balik tak ada retur
      $terima_retur_show = '';
    }
  }

  $count_retur_with_fs = $count_retur + $count_retur_fs;

  $qc_show = $count_retur_with_fs==$count_kumulatif_item_with_fs ? "<div class=green>QC $count_retur_with_fs of $count_kumulatif_item_with_fs $img_check</div>" : "<div>QC $count_retur_with_fs of $count_kumulatif_item_with_fs</div>";
  $qc_show = $count_kumulatif_item_no_fs ? $qc_show : '-';


  $qty_stok = $qty_diterima_with_qc_no_fs - $qty_retur + $qty_balik;
  $qty_stok_fs = $qty_diterima_with_qc_fs - $qty_retur_fs + $qty_balik_fs;
  $qty_transit = $qty_datang - $qty_stok -$qty_tr_fs -$qty_retur + $qty_balik;

  $fs_show = $count_kumulatif_item_fs ? ' <b class="f14 ml1 mr1 biru p1 pr2 br5" style="display:inline-block;background:green;color:white">FS</b>' : '';
  $qty_stok_fs_show = $qty_stok_fs ? "$qty_stok_fs $fs_show" : '';
  
  // echo "<h1>qty_transit:$qty_transit = qty_datang:$qty_datang - qty_stok:$qty_stok -qty_retur:$qty_retur + qty_balik:$qty_balik;</h1>";


  $tr_hasil .= "
    <tr id=tr__$id>
      <td>
        <div>$locations</div>
      </td>
      <td>
        <div><a href='?penerimaan&p=manage_sj&kode_sj=$d[kode_sj]'>$d[kode_sj]</a></div>
        <div class='kecil abu'>$d[nama_supplier]</div>
      </td>
      <td>
        <div>
          <a href='?penerimaan&p=manage_sj_kumulatif&kode_sj=$d[kode_sj]&id_sj_item=$d[id_sj_item]'>
            $d[kode_barang] | 
            <span class=kecil>$count_kumulatif_item_no_fs subitems</span> $fs_count_show
          </a>
        </div>
        <div class='kecil abu'>$d[nama_barang]</div>
        <div class='kecil abu'>$d[keterangan_barang]</div>
      </td>
      <td>
        <div>$tanggal_terima</div>
        <div class='kecil abu'>$datang</div>
        <div class='kecil abu'>$masuk</div>
      </td>
      <td>
        <div><a href='?penerimaan&p=manage_sj&kode_sj=$d[kode_sj]'>$qty $satuan</a></div>
        <div class='kecil abu'>$qty_diterima_show</div>
        <div class='kecil abu'>$qty_lebih_show</div>
        <div class='kecil abu'>$qty_kurang_show</div>
      </td>
      <td class=hideit>
        <div class=kecil>$proyeksi</div>
        <div class=kecil>$kode_ppic</div>
      </td>
      <td class=kecil>
        $qc_show
        <div><a href='?retur&id_sj_item=$id_sj_item'>$retur_show</a></div>
        $terima_retur_show
      </td>
      <td>
        $qty_transit 
      </td>
      <td>
        <div>$qty_stok $satuan</div>
        $qty_stok_fs_show
      </td>
    </tr>
  ";
}






$form_cari = "
  <form method=post>
    <tr>
      <td class=kecil>$clear_filter</td>
      <td><input type='text' class='form-control form-control-sm $bg_po' placeholder='PO' name=filter_po value='$filter_po' ></td>
      <td><input type='text' class='form-control form-control-sm $bg_id' placeholder='ID' name=filter_id value='$filter_id' ></td>
      <td>
        <select class='form-control form-control-sm $bg_waktu' name=filter_waktu>$opt_waktu</select>
      </td>
      <td class=hideit><input type='text' class='form-control form-control-sm $bg_proyeksi' placeholder='proyeksi' name=filter_proyeksi value='$filter_proyeksi' ></td>
      <td class=hideit><input type='text' class='form-control form-control-sm $bg_ppic' placeholder='ppic' name=filter_ppic value='$filter_ppic' ></td>
      <td>
        <button class='btn btn-success btn-sm' name=btn_cari>Cari</button>
      </td>
    </tr>
  </form>

";

$bread = "<li class='breadcrumb-item'><a href='?master_penerimaan&cat=fab'>Fabric</a></li><li class='breadcrumb-item active'>Aksesoris</li>";
if($cat=='fab')
$bread = "<li class='breadcrumb-item'><a href='?master_penerimaan&cat=aks'>Aksesoris</a></li><li class='breadcrumb-item active'>Fabric</li>";
?>

<div class="pagetitle">
  <h1>Master Penerimaan <?=$jenis_barang?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <?=$bread?>
    </ol>
  </nav>
</div>


<section class='section'>
  <div id="blok_penerimaan" class='mt2'>

    <table class='table'>
      <?=$form_cari?>
      <tr>
        <td colspan=100%><span class="darkblue"><?=$jumlah_row?></span> <span class="abu kecil">data dari <b><?=$total_row ?></b> records</span></td>
      </tr>
      <tr class='tebal gradasi-hijau '>
        <td>Lokasi</td>
        <td>PO / Supplier</td>
        <td>ID / Item / Keterangan</td>
        <td>Tanggal</td>
        <td>QTY </td>
        <td class=hideit>Info</td>
        <td>QC</td>
        <td>Transit</td>
        <td>Stok Terima</td>
      </tr>
      <?=$tr_hasil?>



    </table>

  </div>
</section>