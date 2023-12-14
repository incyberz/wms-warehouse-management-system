
<?php 
include 'include/date_managements.php';
$p = 'pengeluaran'; // untuk navigasi
$cat= $_GET['cat'] ?? 'aks'; //default AKS
$jumlah_row = 0;

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

$filter_lokasi = $_GET['lokasi'] ?? '';
$filter_po = $_GET['po'] ?? '';
$filter_id = $_GET['id'] ?? '';
$filter_proyeksi = $_GET['proyeksi'] ?? '';
$filter_ppic = $_GET['ppic'] ?? '';
if(isset($_POST['btn_cari'])){
  jsurl("?$parameter&cat=$cat&lokasi=$_POST[filter_lokasi]&po=$_POST[filter_po]&id=$_POST[filter_id]&waktu=$_POST[filter_waktu]&proyeksi=$_POST[filter_proyeksi]&ppic=$_POST[filter_ppic]");
}

$clear_filter = 'Filter:';
if(
  $filter_lokasi!=''||
  $filter_po!=''||
  $filter_id!=''||
  $filter_proyeksi!=''||
  $filter_ppic!='' 
)$clear_filter = "<a href='?$parameter&cat=$cat'>Clear Text Filter</a>";

$bg_waktu = $filter_waktu=='all_time' ? '' : 'bg-hijau';
$bg_lokasi = $filter_lokasi=='' ? '' : 'bg-hijau';
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


$where_lokasi = $filter_lokasi=='' ? '1' : "a.kode_lokasi LIKE '%$filter_lokasi%' ";
$where_po = $filter_po=='' ? '1' : "a.kode_po LIKE '%$filter_po%' ";
$where_id = $filter_id=='' ? '1' : "(a.kode_barang LIKE '%$filter_id%' OR b.nama LIKE '%$filter_id%' OR b.keterangan LIKE '%$filter_id%' )";
$where_proyeksi = $filter_proyeksi=='' ? '1' : "a.proyeksi LIKE '%$filter_proyeksi%' ";
$where_ppic = $filter_ppic=='' ? '1' : "a.kode_ppic LIKE '%$filter_ppic%' ";


$sql_from = "FROM tb_pengeluaran a 
JOIN tb_barang b ON a.kode_barang=b.kode 
";

$sql_where = "
WHERE b.id_kategori=$id_kategori 
AND $where_date 
AND $where_lokasi 
AND $where_po 
AND $where_id 
AND $where_proyeksi 
AND $where_ppic 
";

$s = "SELECT 1 $sql_from $sql_where ";
// echo "<pre>$s</pre>";
// $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
// $total_row = mysqli_num_rows($q);

// $s = "SELECT 
// a.*,
// c.*,
// b.satuan,
// b.nama as nama_barang,  
// b.keterangan as ket_barang 
// FROM tb_pengeluaran a 
// JOIN tb_barang b ON a.kode_barang=b.kode 
// JOIN tb_lokasi c ON a.kode_lokasi=c.kode 

// $sql_where

// LIMIT 10 

// ";


// $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
// $jumlah_row = mysqli_num_rows($q);

$tr_hasil = $jumlah_row==0 ? "<tr><td colspan=100%><div class='alert alert-danger'>Data tidak ditemukan..</div></td></tr>" : '';
// while($d=mysqli_fetch_assoc($q)){
//   $id=$d['id'];
//   $satuan=$d['satuan'];
//   $kode_ppic=$d['kode_ppic'] ?? $unset;
//   $brand=$d['brand'];


//   $tgl = date('d M Y',strtotime($d['tanggal_masuk']));
//   $awal_datang = $d['awal_datang'] ?? '';
//   $akhir_datang = $d['akhir_datang'] ?? '';
//   $awal_masuk = $d['awal_masuk'] ?? '';
//   $akhir_masuk = $d['akhir_masuk'] ?? '';

//   $datang = $awal_datang=='' ? '' : '<img src=assets/img/icons/wms/datang2.png height=14px> '. date('h:i',strtotime($awal_datang));
//   $datang = $akhir_datang=='' ? $datang : "$datang s.d ". date('h:i',strtotime($akhir_datang));

//   $masuk = $awal_masuk=='' ? '' : '<img src=assets/img/icons/wms/masuk.png height=14px> '. date('h:i',strtotime($awal_masuk));
//   $masuk = $akhir_masuk=='' ? $masuk : "$masuk s.d ". date('h:i',strtotime($akhir_masuk));



//   $qty = floatval($d['qty']);
//   $qty_lebih = $d['qty_lebih']>0 ? '<img src=assets/img/icons/wms/qty_lebih.png height=14px> '.floatval($d['qty_lebih']) : '';
//   $qty_kurang = $d['qty_kurang']>0 ? '<img src=assets/img/icons/wms/qty_kurang.png height=14px> '.floatval($d['qty_kurang']) : '';
//   $qty_reject = $d['qty_reject']>0 ? '<img src=assets/img/icons/wms/qty_reject.png height=14px> '.floatval($d['qty_reject']) : '';


//   $tr_hasil .= "
//     <tr id=tr__$id>
//       <td>
//         <div>$d[kode_lokasi]</div>
//         <div class='kecil abu'>$brand</div>
//       </td>
//       <td>
//         <div>$d[kode_po]</div>
//         <div class='kecil abu'>Supplier: $unset</div>
//       </td>
//       <td>
//         <div>$d[kode_barang]</div>
//         <div class='kecil abu'>$d[nama_barang]</div>
//         <div class='kecil abu'>$d[ket_barang]</div>
//       </td>
//       <td>
//         <div>$tgl</div>
//         <div class='kecil abu'>$datang</div>
//         <div class='kecil abu'>$masuk</div>
//       </td>
//       <td>
//         <div>$qty $satuan</div>
//         <div class='kecil abu'>$qty_lebih</div>
//         <div class='kecil abu'>$qty_kurang</div>
//         <div class='kecil abu'>$qty_reject</div>
//       </td>
//       <td>
//         <div class=kecil>$d[proyeksi]</div>
//       </td>
//       <td>
//         <div class=kecil>$kode_ppic</div>
//       </td>
//     </tr>
//   ";
// }


$input_select = "<select class='form-control form-control-sm'><option>--pilih--</option></select>";
$input_text = "<input type=text class='form-control form-control-sm' />";
$input_date = "<input type=date class='form-control form-control-sm' />";
$input_number = "<input type=number class='form-control form-control-sm' />";

$new_lokasi = $input_select;
$new_po = $input_text;
$new_id = $input_text;
$new_tgl = $input_date;
$new_qty = $input_number;
$new_proyeksi = $input_text;


$tr_new = "
  <tr>
    <td>$new_lokasi</td>
    <td>$new_po</td>
    <td>$new_id</td>
    <td>$new_tgl</td>
    <td>$new_qty</td>
    <td>$new_proyeksi</td>
  </tr>
";

$form_cari = "
  <form method=post>
    <tr>
      <td><input type='text' class='form-control form-control-sm $bg_lokasi' placeholder='lokasi' name=filter_lokasi value='$filter_lokasi' ></td>
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
<section class='section'>
  <?php include 'pages/sheet_nav.php'; ?>
  <div id="blok_pengeluaran" class='mt2'>

    <div class="alert alert-danger">Page ini sedang dalam tahap migrasi database.</div>

    <table class='table hideit'>
      <tr>
        <td colspan=100% class='kecil abu'><?=$clear_filter?></td>
      </tr>
      <?=$form_cari?>
      <!-- <tr>
        <td colspan=100%><span class="darkblue"><?=$jumlah_row?></span> <span class="abu kecil">data dari <b><?=$total_row ?></b> records</span></td>
      </tr> -->
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
        <td colspan=100% class='kecil abu'>Tambah pengeluaran:</td>
      </tr>
      <?=$tr_new?>



    </table>

  </div>
</section>