<?php
set_title('Master Pengeluaran');
// to do : fix decimal
include 'include/date_managements.php';
include 'include/arr_brand.php';
include 'include/arr_apparel.php';
include 'include/arr_gender.php';
include 'include/arr_pic.php';
include 'include/arr_assign_pic.php';
$p = 'pengeluaran'; // untuk navigasi
$cat = $_GET['cat'] ?? 'aks'; //default AKS
$jenis_barang = $cat == 'aks' ? 'Aksesoris' : 'Fabric';
$belum_red = '<span class="red f12 miring">belum</span>';
$belum_abu = '<span class="abu f12 miring">belum</span>';
// allocate sangat penting untuk WH
$belum = $id_role == 3 ? $belum_red : $belum_abu;
$jumlah_item_valid = 0;

$arr_waktu = [
  'hari_ini' => 'Hari ini',
  'kemarin' => 'Kemarin',
  'minggu_ini' => 'Minggu ini',
  'bulan_ini' => 'Bulan ini',
  'tahun_ini' => 'Tahun ini',
  'all_time' => 'All Tanggal Pick',
];

$filter_waktu = $_GET['waktu'] ?? 'all_time';
$opt_waktu = '';
foreach ($arr_waktu as $waktu => $nama_waktu) {
  $selected = $filter_waktu == $waktu ? 'selected' : '';
  $opt_waktu .= "<option value=$waktu $selected>$nama_waktu</option>";
}

$keyword = $_GET['do'] ?? '';
$filter_id = $_GET['id'] ?? '';
$filter_ppic = $_GET['ppic'] ?? '';
if (isset($_POST['btn_cari'])) {
  jsurl("?$parameter&cat=$cat&do=$_POST[keyword]&id=$_POST[filter_id]&waktu=$_POST[filter_waktu]");
}

$clear_filter = 'Filter:';
if (
  $keyword != '' ||
  $filter_id != ''
) $clear_filter = "<a href='?$parameter&cat=$cat'>Clear Text Filter</a>";

$bg_waktu = $filter_waktu == 'all_time' ? '' : 'bg-hijau';
$bg_do = $keyword == '' ? '' : 'bg-hijau';
$bg_id = $filter_id == '' ? '' : 'bg-hijau';

$id_kategori = $cat == 'aks' ? 1 : 2;

if ($filter_waktu == 'all_time') {
  $where_date = '1';
} else 
if ($filter_waktu == 'hari_ini') {
  $where_date = "a.tanggal_pick >= '$today' ";
} else 
if ($filter_waktu == 'kemarin') {
  $where_date = "a.tanggal_pick >= '$kemarin' AND a.tanggal_pick < '$today' ";
} else 
if ($filter_waktu == 'minggu_ini') {
  $where_date = "a.tanggal_pick >= '$ahad_skg' AND a.tanggal_pick < '$ahad_depan' ";
} else 
if ($filter_waktu == 'bulan_ini') {
  $where_date = "a.tanggal_pick >= '$awal_bulan' ";
} else
if ($filter_waktu == 'tahun_ini') {
  $where_date = "a.tanggal_pick >= '$awal_tahun' ";
}


$where_keyword = $keyword == '' ? '1' : "(
  d.kode_po LIKE '%$keyword%' OR 
  i.kode_do LIKE '%$keyword%' OR 
  i.kode_artikel LIKE '%$keyword%' OR 
  f.kode_lama LIKE '%$keyword%' OR 
  f.kode LIKE '%$keyword%' OR 
  f.nama LIKE '%$keyword%' OR 
  f.keterangan LIKE '%$keyword%' )";



// $sql_from = "FROM tb_pengeluaran a 
// JOIN tb_barang b ON a.kode_barang=b.kode 
// ";

// $sql_where = "
// WHERE f.id_kategori=$id_kategori 
// AND $where_date 
// AND $where_keyword 
// AND $where_id 
// ";

// $s = "SELECT 1 $sql_from $sql_where ";

# ==============================================================
# MAIN SQL PENGELUARAN
# ==============================================================

include 'sql_pick.php';
$q = mysqli_query($cn, $sql_pick_one) or die(mysqli_error($cn));
$total_row = mysqli_num_rows($q);

$s = "$sql_pick
  -- AND i.kode_do='$-kode_do' -- All kode_do
  AND $where_date 
  AND $where_keyword 
  ORDER BY i.kode_do  
  ";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$count_pick = mysqli_num_rows($q);

$tr = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $id_pick = $d['id_pick'];
  $no_lot = $d['no_lot'];
  $kode_lokasi = $d['kode_lokasi'];
  $brand = $d['brand'];
  $is_hutangan = $d['is_hutangan'];
  $count_roll = $d['count_roll'];
  $is_fs = $d['is_fs'];
  $satuan = $d['satuan'];
  $step = $d['step'];
  $allocator = $d['allocator'];
  $picker = $d['picker'];

  $allocator = ucwords(strtolower($allocator));
  $picker = ucwords(strtolower($picker));

  // tanggal
  $tanggal_pick = $d['tanggal_pick'];
  $tanggal_allocate = $d['tanggal_pick'];

  // pemasukan
  $qty_transit = floatval($d['qty_transit']);
  $qty_tr_fs = floatval($d['qty_tr_fs']);
  $qty_qc = floatval($d['qty_qc']);
  $qty_qc_fs = floatval($d['qty_qc_fs']);
  $qty_retur = floatval($d['qty_retur']);
  $qty_ganti = floatval($d['qty_ganti']);

  //pengeluaran
  $qty_pick = floatval($d['qty_pick']);
  $qty_allocate = floatval($d['qty_allocate']);
  $qty_pick_by_other = floatval($d['qty_pick_by_other']);
  $qty_allocate_by_other = floatval($d['qty_allocate_by_other']);











  # =======================================================
  # QTY CALCULATION
  # =======================================================
  // exception for hutangan
  if ($is_hutangan) {
    $qty_hutangan = $qty_pick;
    $qty_pick = 0;
  }

  $qty_datang = $qty_transit + $qty_tr_fs + $qty_qc + $qty_qc_fs - $qty_retur + $qty_ganti;
  $stok_available = $qty_qc + $qty_qc_fs - $qty_pick_by_other - $qty_pick - $qty_retur + $qty_ganti;

  // qty stok akhir
  if ($sebagai = 'WH') { // id_role=3
    $qty_pick_or_allocate = $qty_allocate;
    $stok_akhir = $qty_datang - $qty_allocate - $qty_allocate_by_other;
  } else { // id_role=7 PPIC
    $qty_pick_or_allocate = $qty_pick;
    $stok_akhir = $qty_datang - $qty_pick_or_allocate - $qty_pick_by_other;
  }

  // set max
  $qty_set_max_pick = $stok_available + $qty_pick;
  $max_pick = $is_hutangan ? '' : $qty_set_max_pick;
  $qty_set_max_allocate = $qty_pick;
  $max_allocate = $is_hutangan ? '' : $qty_set_max_allocate;
  # =======================================================
  # END QTY CALCULATION
  # =======================================================





  // tanggal_show
  $tanggal_pick_show = date('d-M H:i', strtotime($tanggal_pick));
  $tanggal_allocate_show = date('d-M H:i', strtotime($tanggal_allocate));

  // other show
  $no_lot_show = $no_lot ? $no_lot : $null;
  $brand_show = $brand ? $brand : '';

  // repeat_show
  $repeat_show = $d['is_repeat'] ? '<span class="tebal consolas miring abu bg-yellow">repeat item</span>' : '';


  if ($qty_pick) $jumlah_item_valid++;

  // qty pick
  $qty_pick = $qty_pick ? $qty_pick : '';
  $qty_input_allocate = $qty_allocate ? $qty_allocate : '';

  // qty for input exception hutangan
  $qty_pick = $is_hutangan ? $qty_hutangan : $qty_pick;

  $gradasi = $qty_pick ? '' : 'merah';
  $gradasi = !$is_hutangan ? $gradasi : 'kuning';
  $hutangan_show = $is_hutangan ? "<span class='badge bg-red mb1 bold'>HUTANGAN</span>" : '';
  $fs_icon_show = $is_fs ? $fs_icon : '';





  if ($is_hutangan) {
    // exception for hutangan view PPIC
    $qty_allocate_show = '-';
  } elseif ($qty_allocate) {
    $qty_allocate_show = "
        $qty_allocate
        <div class='f12 abu'>by: $allocator</div>
        <div class='f10 abu'>$tanggal_allocate_show</div>
      ";
  } else {
    if ($id_role == 3) {
      if ($qty_pick) {
        //link allocate
        $qty_allocate_show = "<a href='?pengeluaran&p=buat_do&kode_do=$d[kode_do]&cat=$cat' class='btn btn-danger btn-sm'>Belum Allocate</a>";
      } else {
        $qty_allocate_show = '-'; // gapapa blm allocate krn pick masih nol
      }
    } else { // for PPIC
      if ($qty_pick) {
        //link allocate
        $qty_allocate_show = "<span class='red consolas miring f12'>Belum Allocate</span>";
      } else {
        $qty_allocate_show = '-'; // gapapa blm allocate krn pick masih nol
      }
    }
  }

  $qty_pick_class = $is_hutangan ? 'qty_hutangan' : 'qty_pick';
  if ($qty_pick) {
    $qty_pick_show = "
      $qty_pick
      <div class='miring abu f12'>Picked by: </div>
      <div class='f12'>$picker</div>
      <div class='abu f10'>$tanggal_pick_show</div>
    ";
  } else {
    if ($id_role == 7) {
      $qty_pick_show = "<a href='?pengeluaran&p=buat_do&kode_do=$d[kode_do]&cat=$cat' class='btn btn-danger btn-sm'>Belum Pick</a>";
    } else {
      $qty_pick_show = "<span class='red consolas f12 miring'>Belum Pick</span>";
    }
  }

  $tanggal_do_show = date('d-m-y H:i', strtotime($d['tanggal_do']));




  # =======================================================
  # FINAL TR LOOP
  # =======================================================
  $tr .= "
    <tr class='gradasi-$gradasi'>
      <td>$i</td>
      <td>
        $d[kode_do]
        <div class='f12 abu'>Artikel: $d[kode_artikel]</div>
        <div class='f12 abu'>$tanggal_do_show</div>
      </td>
      <td>
        $d[kode_po]
        <div class='f12 abu'>Lot: $no_lot_show</div>
        <div class='f12 abu'>Lokasi: $kode_lokasi $brand_show</div>
        <div class='f12 abu'>Roll: $count_roll</div>
      </td>
      <td>
        <div>$d[kode_barang] $repeat_show </div>
        <div class='f12 abu'>
          <div>Kode lama: $d[kode_lama]</div>
          <div>$d[nama_barang]</div>
          <div>$d[keterangan_barang]</div>
        </div>
      </td>
      <td class=''>
        <span id=stok_available__$id_pick>$stok_available</span> 
        <span class=btn_aksi id=stok_available_info$id_pick" . "__toggle>$img_detail</span> $fs_icon_show
        <div id=stok_available_info$id_pick class='hideit wadah f12 mt1'>
          <div class=darkred>Transit: $qty_transit</div>
          <div class=darkred>Tr-FS: $qty_tr_fs</div>
          <div class=abu>Retur: $qty_retur</div>
          <div class=abu>Ganti: $qty_ganti</div>
          <div class=green>QTY QC: $qty_qc</div>
          <div class=green>QTY QC-FS: $qty_qc_fs</div>
        </div> 
      </td>
      <td class=' darkred' id=qty_pick_by_other__$id_pick>$qty_pick_by_other</td>
      <td width=100px>
        $qty_pick_show $hutangan_show
      </td>
      <td class=>
        <div class=darkblue id=qty_datang__$id_pick>$qty_datang</div>
        <div class='darkred f12' id=qty_allocate_by_other__$id_pick>-$qty_allocate_by_other</div>
      </td>
      <td width=100px>
        $qty_allocate_show
      </td>
      <td><span id=stok_akhir__$id_pick>$stok_akhir</span></td>
      <td class=f10>$satuan</td>
    </tr>
  ";
} // end while


$form_cari = "
  <form method=post>
    <div class=flexy>
      <div class=kecil>$clear_filter</div>
      <div><input type='text' class='form-control form-control-sm $bg_do' placeholder='keyword...' name=keyword value='$keyword' ></div>
      <div>
        <select class='form-control form-control-sm $bg_waktu' name=filter_waktu>$opt_waktu</select>
      </div>
      <div>
        <button class='btn btn-success btn-sm' name=btn_cari>Cari</button>
      </div>
    </div>
  </form>

";

$tb = "
  $form_cari
  <table class='table'>
    <thead>
      <th>No</th>
      <th>DO/Artikel</th>
      <th>PO</th>
      <th>ITEM</th>
      <th class=''>Stok Available</th>
      <th class='darkred '>Picked <div class='f10 abu'>by other DO</div>
      </th>
      <th>QTY Pick</th>
      <th class=' darkblue'>
        QTY Datang
        <div class='darkred f11'>Allocate di Line lain</div>
      </th>
      <th>Allocate</th>
      <th>Stok Akhir</th>
      <th class=f10>UOM</th>
    </thead>
    $tr
  </table>
";






































$bread = "<li class='breadcrumb-item'><a href='?master_pengeluaran&cat=fab'>Fabric</a></li><li class='breadcrumb-item active'>Aksesoris</li>";
if ($cat == 'fab')
  $bread = "<li class='breadcrumb-item'><a href='?master_pengeluaran&cat=aks'>Aksesoris</a></li><li class='breadcrumb-item active'>Fabric</li>";


echo "
<div class='pagetitle'>
  <h1>Master Pengeluaran $jenis_barang</h1>
  <nav>
    <ol class='breadcrumb'>
      <li class='breadcrumb-item'><a href='?pengeluaran'>Pengeluaran</a></li>
      $bread
    </ol>
  </nav>
</div>

$tb
";
