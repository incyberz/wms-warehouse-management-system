
<?php 
$judul = 'Rekap Item';
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
if($filter_waktu=='hari_ini'){$where_date = "d.tanggal_terima >= '$today' ";}else 
if($filter_waktu=='kemarin'){$where_date = "d.tanggal_terima >= '$kemarin' AND d.tanggal_terima < '$today' ";}else 
if($filter_waktu=='minggu_ini'){$where_date = "d.tanggal_terima >= '$ahad_skg' AND d.tanggal_terima < '$ahad_depan' ";}else 
if($filter_waktu=='bulan_ini'){$where_date = "d.tanggal_terima >= '$awal_bulan' ";}else
if($filter_waktu=='tahun_ini'){$where_date = "d.tanggal_terima >= '$awal_tahun' ";} 


$where_po = $filter_po=='' ? '1' : "d.kode_po LIKE '%$filter_po%' ";
$where_id = $filter_id=='' ? '1' : "(b.kode LIKE '%$filter_id%' OR b.nama LIKE '%$filter_id%' OR b.keterangan LIKE '%$filter_id%' )";
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

$bread = "<li class='breadcrumb-item'><a href='?rekap_item&cat=fab'>Fabric</a></li><li class='breadcrumb-item active'>Aksesoris</li>";
if($cat=='fab')
$bread = "<li class='breadcrumb-item'><a href='?rekap_item&cat=aks'>Aksesoris</a></li><li class='breadcrumb-item active'>Fabric</li>";







































# ==================================================
# MAIN SELECT
# ==================================================
$s = "SELECT 
a.id as id_sj_item,
a.qty_po,
a.qty,
a.kode_sj,
b.satuan,
b.keterangan,
b.id as id_barang,  
b.kode as kode_barang,  
b.nama as nama_barang,
b.keterangan as keterangan_barang,
b.kode_lama,
c.step,
d.kode_po,
d.tanggal_terima,
e.nama as supplier,
(
  SELECT SUM(p.qty) FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
  WHERE q.id_sj_item=a.id 
  AND q.is_fs is null
  AND q.tanggal_qc is null) qty_transit,   
  -- BUKAN FS BELUM QC
(
  SELECT SUM(p.qty) FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
  WHERE q.id_sj_item=a.id 
  AND q.is_fs is not null
  AND q.tanggal_qc is null) qty_tr_fs,
  -- FS BELUM QC 
(
  SELECT SUM(p.qty) FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
  WHERE q.id_sj_item=a.id 
  AND q.is_fs is null
  AND q.tanggal_qc is not null) qty_qc,
  -- BUKAN FS SUDAH QC
(
  SELECT SUM(p.qty) FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
  WHERE q.id_sj_item=a.id 
  AND q.is_fs is not null
  AND q.tanggal_qc is not null) qty_qc_fs, 
  -- FS SUDAH QC  


(
  SELECT SUM(p.qty) FROM tb_retur p 
  JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
  WHERE q.id_sj_item=a.id 
  -- AND q.is_fs is not null
  -- AND q.tanggal_qc is not null
  ) qty_retur, 
  -- ALL RETUR = RETUR REG + RETUR FS  

(
  SELECT SUM(p.qty) FROM tb_ganti p
  JOIN tb_retur q ON p.id_retur=q.id  
  JOIN tb_sj_kumulatif r ON q.id_kumulatif=r.id 
  WHERE r.id_sj_item=a.id 
  -- AND r.is_fs is not null
  -- AND r.tanggal_qc is not null
  ) qty_ganti, 
  -- ALL GANTI = GANTI REG + GANTI FS  






(
  SELECT SUM(p.qty) FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
  JOIN tb_sj_item r ON q.id_sj_item=r.id 
  WHERE q.id_sj_item!=a.id 
  AND q.is_fs is null 
  AND r.kode_barang=a.kode_barang) qty_parsial,
    -- QTY Parsial adalah qty_datang pada penerimaan lain 
(
  SELECT stok 
  FROM tb_trx 
  WHERE id_barang=b.id 
  ORDER BY tanggal DESC LIMIT 1) stok,   
(
  SELECT tanggal 
  FROM tb_trx 
  WHERE id_barang=b.id 
  ORDER BY tanggal DESC LIMIT 1) last_trx


FROM tb_sj_item a 
JOIN tb_barang b ON a.kode_barang=b.kode 
JOIN tb_satuan c ON b.satuan=c.satuan  
JOIN tb_sj d ON a.kode_sj=d.kode 
JOIN tb_supplier e ON d.kode_supplier=e.kode 
WHERE d.id_kategori=$id_kategori 
AND $where_date 
AND $where_po 
AND $where_id 

ORDER BY d.tanggal_terima DESC
";

$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$total_row = mysqli_num_rows($q);
$jumlah_row_limited = mysqli_num_rows($q);


$tr_kumulatif = '';
$i=0;
while($d=mysqli_fetch_assoc($q)){

  // skip jika belum ada QTY Transit atau QTY QC Pass
  if(!$d['qty_transit'] AND !$d['qty_tr_fs'] AND !$d['qty_qc'] AND !$d['qty_qc_fs']) continue;

  // normal looping
  $i++;
  $id_sj_item=$d['id_sj_item'];
  $kode_sj=$d['kode_sj'];
  $tanggal_terima=$d['tanggal_terima'];
  
  $qty_po = $d['qty_po'];
  $qty_parsial = $d['qty_parsial'];
  $qty_transit = $d['qty_transit'];
  $qty_tr_fs = $d['qty_tr_fs'];
  $qty_qc = $d['qty_qc'];
  $qty_qc_fs = $d['qty_qc_fs'];
  $qty_ganti = $d['qty_ganti'];
  $qty_retur = $d['qty_retur'];

  $qty_po_show = $qty_po ? floatval($qty_po) : '-';
  $qty_parsial_show = $qty_parsial ? floatval($qty_parsial) : '-';
  $qty_transit_show = $qty_transit ? floatval($qty_transit) : '-';
  $qty_retur_show = $qty_retur ? floatval($qty_retur) : '-';
  $qty_ganti_show = $qty_ganti ? floatval($qty_ganti) : '-';
  $qty_tr_fs_show = $qty_tr_fs ? floatval($qty_tr_fs)." $img_fs" : '-';

  $qty_qc_show = $qty_qc ? floatval($qty_qc) : '-';
  $qty_qc_fs_show = $qty_qc_fs ? floatval($qty_qc_fs) : '-';

  // datang dan selisih
  $qty_datang = $qty_parsial  + $qty_transit  + $qty_qc - $qty_retur + $qty_ganti;
  $qty_selisih = $qty_datang - $qty_po;
  $qty_datang_show = $qty_datang;
  $qty_selisih_show = $qty_selisih;
  $persen = $qty_po ? number_format($qty_selisih/$qty_po*100,2) : 0;
  $ket = $qty_selisih>0 ? 'LEBIH' : 'KURANG'; 
  $ket = $qty_selisih==0 ? 'PAS' : $ket; 
  
  // persen coloring
  if($persen<-3 || $persen>3){
    $bg_persen = 'background:#f88';
  }elseif($persen>0){
    $bg_persen = 'background:#cfc';
  }elseif($persen<0){
    $bg_persen = 'background:#ffc';
  }else{
    $bg_persen = 'background:#5f5';
  }

  $tanggal_terima_show = date('d-M-y',strtotime($tanggal_terima));
  $jam_masuk_show = date('H:i',strtotime($tanggal_terima));
  $tanggal_terima_show = "$tanggal_terima_show<div class='f14 abu'>$jam_masuk_show</div>";
  $tiga_persen = floatval(.03*$qty_po);
  $selisih_kedatangan = floatval($tiga_persen - $qty_selisih);

  $parsial_icon = strpos($kode_sj,'-001') ? '' : '<span class="f12 abu consolas bg-yellow">PARSIAL</span>';

  $tr_kumulatif .= "
    <tr id=tr__$id_sj_item>
      <td>
        <div class='f12 abu'>$i</div>
        <div class='mt1 f10 abu miring'>id: $id_sj_item</div>
      </td>
      <td>$tanggal_terima_show</td>
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
      <td>$qty_po_show</td>
      <td>$qty_parsial_show</td>
      <td class=darkred>$qty_transit_show</td>
      <td class=darkred>$qty_tr_fs_show</td>
      <td class=green>$qty_qc_show</td>
      <td class=green>$qty_qc_fs_show</td>
      <td>$qty_retur_show</td>
      <td>$qty_ganti_show</td>
      <td class=abu>$d[satuan]</td>
      <td>$qty_datang_show</td>
      <td>$qty_selisih_show</td>
      <td style='$bg_persen' class='tengah f14'>$persen%</td>
      <td>$ket</td>
      <td>$d[supplier]</td>
      <td>TGL KONFM</td>
      <td>FEEDBACK PROC</td>
      <td>$tiga_persen</td>
      <td>$selisih_kedatangan</td>
      <td>KET</td>
      
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
<table class='table' style=min-width:3000px>
  <thead class='gradasi-hijau '>
    <th>NO</th>
    <th>TGL TERIMA</th>
    <th>ID</th>
    <th>PO</th>
    <th>QTY PO</th>
    <th>PARSIAL</th>
    <th class=darkred>TRANSIT</th>
    <th class=darkred>TR-FS</th>
    <th class=green>QC</th>
    <th class=green>QC-FS</th>
    <th>RETUR</th>
    <th>GANTI</th>
    <th class=abu>UOM</th>
    <th>DATANG</th>
    <th>SELISIH</th>
    <th class=tengah>%</th>
    <th>KET</th>
    <th>SUPPLIER</th>
    <th>TGL KONFM</th>
    <th>FEEDBACK PROC</th>
    <th>3% QTY PO</th>
    <th>SELISIH KEDATANGAN</th>
    <th>KET</th>
  </thead>
  $tr_kumulatif
</table>
";