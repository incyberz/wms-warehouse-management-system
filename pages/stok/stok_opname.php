
<?php
$judul = 'Stok Opname';
set_title($judul);
// to do : fix decimal
include 'include/date_managements.php';
$cat = $_GET['cat'] ?? 'aks'; //default AKS
$id_kategori = $cat == 'aks' ? 1 : 2;
$jenis_barang = $cat == 'aks' ? 'Aksesoris' : 'Fabric';

$get_csv = $_GET['get_csv'] ?? '';
$limit = $get_csv ? '99999' : '50';

$arr_waktu = [
  'hari_ini' => 'Hari ini',
  'kemarin' => 'Kemarin',
  'minggu_ini' => 'Minggu ini',
  'bulan_ini' => 'Bulan ini',
  'tahun_ini' => 'Tahun ini',
  'all_waktu' => 'All time',
];

$arr_item = [
  'all_item' => 'All item',
  'tr_item' => 'Transit',
  'tr_fs_item' => 'Transit FS',
  'qc_item' => 'After QC',
  'qc_fs_item' => 'After QC FS',
];

$filter_waktu = $_GET['waktu'] ?? 'all_waktu';
$opt_waktu = '';
foreach ($arr_waktu as $waktu => $nama_waktu) {
  $selected = $filter_waktu == $waktu ? 'selected' : '';
  $opt_waktu .= "<option value=$waktu $selected>$nama_waktu</option>";
}

$filter_item = $_GET['item'] ?? 'all_item';
$opt_item = '';
foreach ($arr_item as $item => $nama_item) {
  $selected = $filter_item == $item ? 'selected' : '';
  $opt_item .= "<option value=$item $selected>$nama_item</option>";
}

# ================================================
# GET / POST NAVIGATION
# ================================================
$keyword = $_GET['po'] ?? '';
$filter_proyeksi = $_GET['proyeksi'] ?? '';
$filter_ppic = $_GET['ppic'] ?? '';
$filter_item = $_GET['item'] ?? 'all_item';
$filter_waktu = $_GET['waktu'] ?? 'all_waktu';
if (isset($_POST['btn_cari'])) {
  jsurl("?$parameter&cat=$cat&po=$_POST[keyword]&waktu=$_POST[filter_waktu]&proyeksi=$_POST[filter_proyeksi]&ppic=$_POST[filter_ppic]&item=$_POST[filter_item]");
}

# ================================================
# MANAGE FILTER
# ================================================
$clear_filter = 'Filter:';
if (
  $filter_item != 'all_item' ||
  $filter_waktu != 'all_waktu' ||
  $keyword != '' ||
  $filter_proyeksi != '' ||
  $filter_ppic != ''
) $clear_filter = "<a href='?$parameter&cat=$cat'>Clear</a>";

$bg_item = $filter_item == 'all_item' ? '' : 'bg-hijau';
$bg_waktu = $filter_waktu == 'all_waktu' ? '' : 'bg-hijau';
$bg_po = $keyword == '' ? '' : 'bg-hijau';
$bg_proyeksi = $filter_proyeksi == '' ? '' : 'bg-hijau';
$bg_ppic = $filter_ppic == '' ? '' : 'bg-hijau';


if ($filter_item == 'all_item') {
  $where_item = '1';
} elseif ($filter_item == 'tr_item') {
  // bukan FS AND belum QC
  $where_item = "a.is_fs is null AND a.tanggal_qc is null";
} elseif ($filter_item == 'tr_fs_item') {
  // Item FS AND belum QC
  $where_item = "a.is_fs is not null AND a.tanggal_qc is null";
} elseif ($filter_item == 'qc_item') {
  // Bukan FS AND sudah QC
  $where_item = "a.is_fs is null AND a.tanggal_qc is not null";
} elseif ($filter_item == 'qc_fs_item') {
  // Item FS AND sudah QC
  $where_item = "a.is_fs is not null AND a.tanggal_qc is not null";
} else {
  die("Invalid value of filter_item: $filter_item");
}


if ($filter_waktu == 'all_waktu') {
  $where_waktu = '1';
} else 
if ($filter_waktu == 'hari_ini') {
  $where_waktu = "a.tanggal_masuk >= '$today' ";
} else 
if ($filter_waktu == 'kemarin') {
  $where_waktu = "a.tanggal_masuk >= '$kemarin' AND a.tanggal_masuk < '$today' ";
} else 
if ($filter_waktu == 'minggu_ini') {
  $where_waktu = "a.tanggal_masuk >= '$ahad_skg' AND a.tanggal_masuk < '$ahad_depan' ";
} else 
if ($filter_waktu == 'bulan_ini') {
  $where_waktu = "a.tanggal_masuk >= '$awal_bulan' ";
} else
if ($filter_waktu == 'tahun_ini') {
  $where_waktu = "a.tanggal_masuk >= '$awal_tahun' ";
} else {
  die("Invalid value of filter_waktu: $filter_waktu");
}


$where_keyword = $keyword == '' ? '1' : "(
  a.kode_lokasi LIKE '%$keyword%' OR 
  c.kode_po LIKE '%$keyword%' OR 
  d.kode_lama LIKE '%$keyword%' OR 
  d.kode LIKE '%$keyword%' OR 
  d.nama LIKE '%$keyword%' OR 
  d.keterangan LIKE '%$keyword%' )";

$where_proyeksi = $filter_proyeksi == '' ? '1' : "a.proyeksi LIKE '%$filter_proyeksi%' ";
$where_ppic = $filter_ppic == '' ? '1' : "a.kode_ppic LIKE '%$filter_ppic%' ";


// $sql_from = "FROM tb_penerimaan a 
// JOIN tb_barang b ON a.kode_barang=b.kode 
// ";

// $sql_where = "
// WHERE e.id_kategori=$id_kategori 
// AND $where_waktu 
// AND $where_keyword 
// AND $where_proyeksi 
// AND $where_ppic 
// ";

# =====================================================
# CSV URL HANDLER
# =====================================================
$arr = explode('?', $_SERVER['REQUEST_URI']);
$href_get_csv = $arr[1] . '&get_csv=1';

# =====================================================
# FORM CARI / FILTER
# =====================================================
$form_cari = "
  <form method=post>
    <div class=flexy>
      <div class=kecil>$clear_filter</div>
      <div><input type='text' class='form-control form-control-sm $bg_po' placeholder='keyword...' name=keyword value='$keyword' ></div>
      <div>
        <select class='form-control form-control-sm $bg_waktu' name=filter_waktu>$opt_waktu</select>
      </div>
      <div>
        <select class='form-control form-control-sm $bg_item' name=filter_item>$opt_item</select>
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

$bread = "<li class='breadcrumb-item'><a href='?stok_opname&cat=fab'>Fabric</a></li><li class='breadcrumb-item active'>Aksesoris</li>";
if ($cat == 'fab')
  $bread = "<li class='breadcrumb-item'><a href='?stok_opname&cat=aks'>Aksesoris</a></li><li class='breadcrumb-item active'>Fabric</li>";







































# ==================================================
# MAIN SELECT
# ==================================================
include 'sql_opname.php';
$sql_opname_one = "SELECT 1 $FROM
  AND $where_waktu 
  AND $where_keyword 
  AND $where_item 
";
$q = mysqli_query($cn, $sql_opname_one) or die(mysqli_error($cn));
$total_row = mysqli_num_rows($q);

$s = "$sql_opname
  AND $where_waktu 
  AND $where_keyword 
  AND $where_item 
  ORDER BY a.tanggal_masuk DESC 
  LIMIT $limit  
  ";

$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_row_limited = mysqli_num_rows($q);

$tr_kumulatif = '';
$i = 0;
$tgl = date('d-m-Y');
$data_csv = "STOK OPNAME WMS\nTanggal: $tgl\n\n";

$download_csv = '';
if ($get_csv) {
  $tgl = date('ymd');
  $src = "csv/stok_opname_$cat-$tgl.csv";
  $download_csv = "<a class='btn btn-success btn-sm' href='$src' target=_blank onclick='setTimeout(function(){location.replace(\"?stok_opname\");},2000)'>Download CSV</a>";
  $file = fopen($src, 'w+');
}

while ($d = mysqli_fetch_assoc($q)) {
  $i++;

  $id_kumulatif = $d['id_kumulatif'];
  $kode_sj = $d['kode_sj'];

  // qty pemasukan
  $qty_transit = $d['qty_transit'];
  $qty_tr_fs = $d['qty_tr_fs'];
  $qty_qc = $d['qty_qc'];
  $qty_qc_fs = $d['qty_qc_fs'];
  $qty_retur = $d['qty_retur'];
  $qty_ganti = $d['qty_ganti'];

  // qty pengeluaran
  $qty_pick = $d['qty_pick'];
  $qty_allocate = $d['qty_allocate'];
  $qty_retur_do = $d['qty_retur_do'];

  // tanggal jam
  $tanggal_masuk = $d['tanggal_masuk'];
  $tanggal_qc = $d['tanggal_qc'];



  // qty calculation
  $qty_datang = $qty_transit + $qty_tr_fs + $qty_qc_fs + $qty_qc - $qty_retur + $qty_ganti;

  if (!$qty_datang and strpos($d['kode_sj'], '-999')) {
    // $qty_datang = $d['tmp_qty'];
    $qty_qc = $qty_datang;
  }

  $qty_available = $qty_qc_fs + $qty_qc - $qty_retur + $qty_ganti;
  $qty_stok = $qty_available - $qty_allocate + $qty_retur_do;

  // qty show pemasukan
  $qty_transit_show = $qty_transit ? floatval($qty_transit) : '-';
  $qty_tr_fs_show = $qty_tr_fs ? floatval($qty_tr_fs) . " $img_fs" : '-';
  $qty_qc_show = $qty_qc ? floatval($qty_qc) : '-';
  $qty_qc_fs_show = $qty_qc_fs ? floatval($qty_qc_fs) : '-';
  $qty_retur_show = $qty_retur ? floatval($qty_retur) : '-';
  $qty_ganti_show = $qty_ganti ? floatval($qty_ganti) : '-';

  // Opsi Belum QC
  if (!$tanggal_qc) $qty_retur_show = '<span class="red tebal f14">Belum QC</span>';

  // qty show pengeluaran
  $link_pick_by = "<a href='?picked_by_do&id_kumulatif=$id_kumulatif'>$img_next</a>";
  $qty_pick_show = $qty_pick ? floatval($qty_pick) . " $link_pick_by" : '-';
  $qty_allocate_show = $qty_allocate ? floatval($qty_allocate) : '-';
  $link_retur_do = $qty_pick ? "<a href='?picked_by_do&id_kumulatif=$id_kumulatif'>$img_next</a>" : '';
  $qty_retur_do_show = $qty_retur_do ? floatval($qty_retur_do) : '-';
  $qty_retur_do_show .= " $link_retur_do";

  // tanggal jam show
  $tanggal_masuk_show = date('d-M-y', strtotime($tanggal_masuk));
  $jam_masuk_show = date('H:i', strtotime($tanggal_masuk));
  $tanggal_masuk_show = "$tanggal_masuk_show<div class='f14 abu'>$jam_masuk_show</div>";


  // qty calculation show
  $qty_datang_show = $qty_datang ? floatval($qty_datang) : '-';
  $qty_available_show = $qty_available ? floatval($qty_available) : '-';
  $qty_stok_show = $qty_stok ? floatval($qty_stok) : '-';


  $parsial_icon = strpos($kode_sj, '-001') ? '' : '<span class="f12 abu consolas bg-yellow">PARSIAL</span>';
  $parsial_icon = strpos($d['kode_sj'], '-999') ? '<span class="f12 abu consolas bg-yellow">STOK AWAL</span>' : $parsial_icon;

  // add for csv
  $d['stok_akhir'] = $qty_stok;





















  // csv loop
  if ($get_csv) {
    # =====================================================
    # LOOP CSV
    # =====================================================
    if ($i == 1) {
      // HEADER CSV
      $arr = [];
      foreach ($d as $key => $value) {
        $kolom = strtoupper(str_replace('_', ' ', $key));
        if ($kolom == 'TMP QTY') $kolom = 'QTY DATANG'; //exception
        array_push($arr, $kolom);
      }
      fputcsv($file, $arr);
    }

    // ISI CSV
    fputcsv($file, $d);
  } else {
    # =====================================================
    # FINAL TR KUMULATIF
    # =====================================================
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
        <td class=green>$qty_available_show</td>
        <td class=darkred>$qty_pick_show</td>
        <td class=darkred>$qty_allocate_show</td>
        <td class=darkred>$qty_retur_do_show</td>
        <td>$qty_stok_show</td>
        
      </tr>
    ";
  }
}

if ($get_csv) fclose($file);









































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
    <th class=green>AVAILABLE</th>
    <th class=darkred>PICK</th>
    <th class=darkred>ALLOCATE</th>
    <th class=darkred>RETUR-DO</th>
    <th>STOK</th>
  </thead>
  $tr_kumulatif
</table>
";
