
<?php 
$judul = 'Stok Kumulatif';
set_title($judul);
// to do : fix decimal
include 'include/date_managements.php';
$p = 'penerimaan'; // untuk navigasi
$cat= $_GET['cat'] ?? 'aks'; //default AKS
$get_csv= $_GET['get_csv'] ?? '';
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
  $filter_waktu!='all_time'||
  $filter_po!=''||
  $filter_id!=''||
  $filter_proyeksi!=''||
  $filter_ppic!='' 
)$clear_filter = "<a href='?$parameter&cat=$cat'>Clear</a>";

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


$where_po = $filter_po=='' ? '1' : "c.kode_po LIKE '%$filter_po%' ";
$where_id = $filter_id=='' ? '1' : "(d.kode LIKE '%$filter_id%' OR d.nama LIKE '%$filter_id%' OR d.keterangan LIKE '%$filter_id%' )";
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

# =====================================================
# CSV URL HANDLER
# =====================================================
$arr = explode('?',$_SERVER['REQUEST_URI']);
$href_get_csv = $arr[1] . '&get_csv=1';

# =====================================================
# FORM CARI / FILTER
# =====================================================
$form_cari = "
  <form method=post>
    <div class=flexy>
      <div class=kecil>$clear_filter</div>
      <div><input type='text' class='form-control form-control-sm $bg_po' placeholder='PO' name=filter_po value='$filter_po' ></div>
      <div><input type='text' class='form-control form-control-sm $bg_id' placeholder='ID' name=filter_id value='$filter_id' ></div>
      <div>
        <select class='form-control form-control-sm $bg_waktu' name=filter_waktu>$opt_waktu</select>
      </div>
      <div class=hideit><input type='text' class='form-control form-control-sm $bg_proyeksi' placeholder='proyeksi' name=filter_proyeksi value='$filter_proyeksi' ></div>
      <div class=hideit><input type='text' class='form-control form-control-sm $bg_ppic' placeholder='ppic' name=filter_ppic value='$filter_ppic' ></div>
      <div>
        <button class='btn btn-success btn-sm' name=btn_cari>Cari</button>
      </div>
      <div>
        <a href='?$href_get_csv' class='btn btn-info btn-sm'>Get CSV</a>
      </div>
    </div>
  </form>

";

$bread = "<li class='breadcrumb-item'><a href='?rekap_kumulatif&cat=fab'>Fabric</a></li><li class='breadcrumb-item active'>Aksesoris</li>";
if($cat=='fab')
$bread = "<li class='breadcrumb-item'><a href='?rekap_kumulatif&cat=aks'>Aksesoris</a></li><li class='breadcrumb-item active'>Fabric</li>";







































# ==================================================
# MAIN SELECT
# ==================================================
$s = "SELECT 
a.id as id_kumulatif,
a.no_lot,
a.kode_lokasi,
b.kode_sj,
a.tanggal_masuk,
a.tanggal_qc,
c.kode_po,
d.kode as kode_barang, 
d.nama as nama_barang, 
d.keterangan as keterangan_barang,
d.kode_lama,
d.satuan,
(
  SELECT sum(qty) FROM tb_roll 
  WHERE id_kumulatif=a.id 
  AND is_fs is null
  AND tanggal_qc is null) qty_transit,
(
  SELECT sum(qty) FROM tb_roll 
  WHERE id_kumulatif=a.id 
  AND is_fs is not null
  AND tanggal_qc is null) qty_tr_fs,
(
  SELECT sum(qty) FROM tb_roll 
  WHERE id_kumulatif=a.id 
  AND is_fs is null
  AND tanggal_qc is not null) qty_qc,
(
  SELECT sum(qty) FROM tb_roll 
  WHERE id_kumulatif=a.id 
  AND is_fs is not null
  AND tanggal_qc is not null) qty_qc_fs,
(
  SELECT sum(qty) FROM tb_retur 
  WHERE id_kumulatif=a.id) qty_retur,
(
  SELECT sum(p.qty) FROM tb_ganti p 
  JOIN tb_retur q ON p.id_retur=q.id  
  WHERE q.id_kumulatif=a.id) qty_ganti,
(
  SELECT count(1) FROM tb_roll 
  WHERE id_kumulatif=a.id ) count_roll,

  -- =================================
  -- PENGELUARAN
  -- =================================
(
  SELECT sum(qty) FROM tb_pick 
  WHERE id_kumulatif=a.id) qty_pick,
(
  SELECT sum(qty_allocate) FROM tb_pick 
  WHERE id_kumulatif=a.id) qty_allocate,
(
  SELECT sum(qty) FROM tb_retur_do 
  WHERE id_kumulatif=a.id) qty_retur_do,

1

FROM tb_sj_kumulatif a 
JOIN tb_sj_item b ON a.id_sj_item=b.id 
JOIN tb_sj c ON b.kode_sj=c.kode 
JOIN tb_barang d ON b.kode_barang=d.kode 

WHERE c.id_kategori = $id_kategori 
AND $where_date 
AND $where_po 
AND $where_id 

ORDER BY a.tanggal_masuk DESC 
";

$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$total_row = mysqli_num_rows($q);
$jumlah_row_limited = mysqli_num_rows($q);


$tr_kumulatif = '';
$i=0;
while($d=mysqli_fetch_assoc($q)){
  $i++;
  $id_kumulatif=$d['id_kumulatif'];
  $kode_sj=$d['kode_sj'];

  // qty pemasukan
  $qty_transit=$d['qty_transit'];
  $qty_tr_fs=$d['qty_tr_fs'];
  $qty_qc=$d['qty_qc'];
  $qty_qc_fs=$d['qty_qc_fs'];
  $qty_retur=$d['qty_retur'];
  $qty_ganti=$d['qty_ganti'];

  // qty pengeluaran
  $qty_pick=$d['qty_pick'];
  $qty_allocate=$d['qty_allocate'];
  $qty_retur_do=$d['qty_retur_do'];
  
  // tanggal jam
  $tanggal_masuk=$d['tanggal_masuk'];
  $tanggal_qc=$d['tanggal_qc'];


  // qty show pemasukan
  $qty_transit_show = $qty_transit ? floatval($qty_transit) : '-';
  $qty_tr_fs_show = $qty_tr_fs ? floatval($qty_tr_fs)." $img_fs" : '-';
  $qty_qc_show = $qty_qc ? floatval($qty_qc) : '-';
  $qty_qc_fs_show = $qty_qc_fs ? floatval($qty_qc_fs) : '-';
  $qty_retur_show = $qty_retur ? floatval($qty_retur) : '-';
  $qty_ganti_show = $qty_ganti ? floatval($qty_ganti) : '-';

  // Opsi Belum QC
  if(!$tanggal_qc) $qty_retur_show = '<span class="red tebal f14">Belum QC</span>';

  // qty show pengeluaran
  $qty_pick_show = $qty_pick ? floatval($qty_pick) : '-';
  $qty_allocate_show = $qty_allocate ? floatval($qty_allocate) : '-';
  $qty_retur_do_show = $qty_retur_do ? floatval($qty_retur_do) : '-';

  // tanggal jam show
  $tanggal_masuk_show = date('d-M-y',strtotime($tanggal_masuk));
  $jam_masuk_show = date('H:i',strtotime($tanggal_masuk));
  $tanggal_masuk_show = "$tanggal_masuk_show<div class='f14 abu'>$jam_masuk_show</div>";

  // qty calculation
  $qty_datang = $qty_transit+$qty_tr_fs+$qty_qc_fs+$qty_qc -$qty_retur+$qty_ganti;
  $qty_stok = $qty_datang - $qty_allocate+$qty_retur_do;
  
  // qty calculation show
  $qty_datang_show = $qty_datang ? floatval($qty_datang) : '-';
  $qty_stok_show = $qty_stok ? floatval($qty_stok) : '-';


  $parsial_icon = strpos($kode_sj,'-001') ? '' : '<span class="f12 abu consolas bg-yellow">PARSIAL</span>';

  $tr_kumulatif .= "
    <tr id=tr__$id_kumulatif>
      <td>
        <div class='f12 abu'>$i</div>
        <div class='mt1 f10 abu miring'>id: $id_kumulatif</div>
      </td>
      <td>$tanggal_masuk_show</td>
      <td>
        <div>$d[kode_barang]</div>
        <div class='f12 abu'>Kode lama: $d[kode_lama]</div>
        <div class='f12 abu'>$d[nama_barang]</div>
        <div class='f12 abu'>$d[keterangan_barang]</div>
      </td>
      <td>
        $d[kode_po]
        <div class='f12 abu'>SJ: $d[kode_sj]</div>
        $parsial_icon
      </td>
      <td>$d[no_lot]</td>
      <td>$d[kode_lokasi]</td>
      <td>$d[count_roll]</td>
      <td class=darkred>$qty_transit_show</td>
      <td class=darkred>$qty_tr_fs_show</td>
      <td class=green>$qty_qc_show</td>
      <td class=green>$qty_qc_fs_show</td>
      <td>$qty_retur_show <a href='?retur&id_kumulatif=$id_kumulatif'>$img_next</a></td>
      <td>$qty_ganti_show</td>
      <td class='abu f12 center'>$d[satuan]</td>
      <td class=green>$qty_datang_show</td>
      <td class=darkred>$qty_pick_show</td>
      <td class=darkred>$qty_allocate_show</td>
      <td class=darkred>$qty_retur_do_show</td>
      <td>$qty_stok_show</td>
      
    </tr>
  ";
}

$download_csv = $get_csv ? "<a class='btn btn-success btn-sm' href='#'>Download CSV</a>" : '';

# =====================================================
# FINAL ECHO
# =====================================================
echo "
<div class='pagetitle'>
  <h1>$judul $jenis_barang</h1>
  <nav>
    <ol class='breadcrumb'>
      <li class='breadcrumb-item'><a href='?penerimaan'>Penerimaan</a></li>
      $bread
    </ol>
  </nav>
</div>

$form_cari
<div class=flexy>
  <div>
    <span class='darkblue'>$jumlah_row_limited</span> 
    <span class='abu kecil'>
      data dari <b>$total_row </b> records
    </span>
  </div>
  <div>$download_csv</div>
</div>
<table class='table' style='width: 2500px;'>
  <thead class='gradasi-hijau'>
    <th>NO</th>
    <th>TGL MASUK</th>
    <th>ID</th>
    <th>PO</th>
    <th>LOT</th>
    <th>LOKASI</th>
    <th>ROLL</th>
    <th class=darkred>TRANSIT</th>
    <th class=darkred>TR-FS</th>
    <th class=green>QC</th>
    <th class=green>QC-FS</th>
    <th>RETUR</th>
    <th>GANTI</th>
    <th class='abu f12 center'>UOM</th>
    <th class=green>DATANG</th>
    <th class=darkred>PICK</th>
    <th class=darkred>ALLOCATE</th>
    <th class=darkred>RETUR-DO</th>
    <th>STOK</th>
  </thead>
  $tr_kumulatif
</table>
";