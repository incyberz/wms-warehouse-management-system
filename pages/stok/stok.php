<?php
if (isset($_POST['btn_filter']) || isset($_POST['btn_get_csv'])) {
  $keyword = clean_sql($_POST['keyword']);
  $get_csv = $_POST['btn_get_csv'] ?? '';
  jsurl("?stok&cat=$_POST[cat]&keyword=$keyword&tipe_stok=$_POST[tipe_stok]&get_csv=$get_csv");
  exit;
}
$keyword = $_GET['keyword'] ?? '';
$tipe_stok = $_GET['tipe_stok'] ?? 'all';
$get_csv = $_GET['get_csv'] ?? '';
$cat = $_GET['cat'] ?? 'aks';
$id_kategori = $cat == 'aks' ? 1 : 2;
$cat_lainnya = $cat == 'aks' ? 'fab' : 'aks';
$jenis_barang_lainnya = $cat == 'aks' ? 'Fabric' : 'Aksesoris';
$jenis_barang = $cat == 'aks' ? 'Aksesoris' : 'Fabric';
$bg_keyword = $keyword ? 'style="background:#0f0"' : '';
$hide_clear = ($keyword || $tipe_stok != 'all') ? '' : 'hideit';

$judul = "Stok Opname $jenis_barang";
set_title($judul);
echo "
<div class='pagetitle'>
  <h1>$judul</h1>
  <nav>
    <ol class='breadcrumb'>
      <li class='breadcrumb-item'><a href='?'>Dashboard</a></li>
      <li class='breadcrumb-item'><a href='?stok&cat=$cat_lainnya'>Stok Opname $jenis_barang_lainnya</a></li>
      <li class='breadcrumb-item active'>$judul</li>
    </ol>
  </nav>
</div>
";


$sql_filter = $keyword ? "
  (
    c.kode LIKE '%$keyword%' OR  
    c.keterangan LIKE '%$keyword%' OR  
    c.nama LIKE '%$keyword%' OR 
    g.kode_po LIKE '%$keyword%'
  )
" : '1';

$join_tb_retur_e = '';
$left_join_tb_retur_e = '';
$sql_tipe_stok = '1';
$left_join_where = '1';
if ($tipe_stok == 'qc' || $tipe_stok == 'qcfs') {
  $join_tb_retur_e = "JOIN tb_retur e ON a.id=e.id";
  if ($tipe_stok == 'qc') {
    $sql_tipe_stok = "a.is_fs is null ";
  } else {
    $sql_tipe_stok = "a.is_fs is not null ";
  }
} elseif ($tipe_stok == 'tr' || $tipe_stok == 'trfs') {
  $left_join_tb_retur_e = "LEFT JOIN tb_retur e ON a.id=e.id";
  $left_join_where = "e.id is null";
  if ($tipe_stok == 'tr') {
    $sql_tipe_stok = "a.is_fs is null ";
  } else {
    $sql_tipe_stok = "a.is_fs is not null ";
  }
}


$arr_tipe_stok = [
  'all' => 'All Stock',
  'tr' => 'Transit PO',
  'trfs' => 'Transit FS',
  'qc' => 'After QC PO',
  'qcfs' => 'After QC FS',
];

$data_csv = '';
if ($get_csv) {
  $data_csv .= "EXPORT STOCK OPNAME\n\n";
  $data_csv .= "Tanggal," . date('Y-m-d H:i:s') . "\n";
  $data_csv .= "Filter by keyword:,$keyword\n";
  $data_csv .= "Stock Type:,$arr_tipe_stok[$tipe_stok]\n";
  $data_csv .= "Operator:,$nama_user / $jabatan\n\n";
}



$select = '';
foreach ($arr_tipe_stok as $key => $value) {
  $selected = $tipe_stok == $key ? 'selected' : '';
  $select .= "<option value='$key' $selected>$value</option>";
}
$bg_select = $tipe_stok == 'all' ? '' : 'style="background:#0f0"';
$select = "<select class='form-control form-control-sm' name=tipe_stok $bg_select>$select</select>";

$s = "SELECT 
a.tmp_qty as qty,
a.no_lot,
a.is_fs,
a.kode_lokasi,
a.id as id_kumulatif,
a.tanggal_masuk,
b.kode_sj,
c.id_kategori,
c.kode as kode_barang,
c.nama as nama_barang,
c.keterangan as keterangan_barang,
c.kode_lama,
c.satuan,
d.brand,
g.kode_po,
(
  SELECT tanggal_retur FROM tb_retur 
  WHERE id=a.id) tanggal_retur,
(
  SELECT qty FROM tb_retur 
  WHERE id=a.id) qty_retur,
(
  SELECT p.qty FROM tb_ganti p 
  JOIN tb_retur q ON p.id=q.id  
  WHERE q.id=a.id) qty_balik,
(
  SELECT sum(p.qty) FROM tb_pick p 
  WHERE p.id_kumulatif=a.id) qty_pick,
(
  SELECT count(1) FROM tb_roll p 
  WHERE p.id_kumulatif=a.id) count_roll

FROM tb_sj_kumulatif a 
JOIN tb_sj_item b ON a.id_sj_item=b.id 
JOIN tb_barang c ON b.kode_barang=c.kode 
JOIN tb_lokasi d ON a.kode_lokasi=d.kode 
$join_tb_retur_e
$left_join_tb_retur_e 
JOIN tb_sj g ON b.kode_sj=g.kode 

WHERE $sql_filter 
AND $sql_tipe_stok 
AND $left_join_where 
AND c.id_kategori = $id_kategori 
ORDER BY a.tanggal_masuk DESC, c.kode 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_records = mysqli_num_rows($q);

if ($get_csv) {
  $jumlah_tampil = $jumlah_records;
} else {
  $s .= "LIMIT 100";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $jumlah_tampil = mysqli_num_rows($q);
}

$tr = '';
$tr_csv = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $id = $d['id_kumulatif'];
  $id_kumulatif = $d['id_kumulatif'];
  $kode_po = $d['kode_po'];
  $tanggal_masuk = $d['tanggal_masuk'];

  $tanggal_masuk = date('d-m-y', strtotime($tanggal_masuk));

  $satuan = $d['satuan'];
  $idz = "<span class='abu f12'>id-</span>$id";
  $lot = $d['no_lot'] ? "<span class='abu f12'>Lot-</span>$d[no_lot]" : '';

  $is_fs = $d['is_fs'];
  $qty_retur = floatval($d['qty_retur']);
  $qty_ganti = floatval($d['qty_balik']);
  $qty_retur_balik = floatval($d['qty']) - $qty_retur + $qty_ganti;
  $qty_pick = floatval($d['qty_pick']);

  $fs_show = $is_fs ? ' <b class="f14 ml1 mr1 biru p1 pr2 br5" style="display:inline-block;background:green;color:white">FS</b>' : '';

  // transit or after QC
  $qty_transit = 0;
  $qty_transit_fs = 0;
  $qty_qc = 0;
  $qty_qc_fs = 0;
  if ($d['tanggal_retur'] == '') { //belum QC
    if ($is_fs) {
      $qty_transit_fs = $qty_retur_balik;
    } else {
      $qty_transit = $qty_retur_balik;
    }
  } else { //sudah QC
    if ($is_fs) {
      $qty_qc_fs = $qty_retur_balik;
    } else {
      $qty_qc = $qty_retur_balik;
    }
  }

  //stok akhir
  $stok_akhir = $qty_qc + $qty_qc_fs - $qty_pick;

  if ($get_csv) {
    $fs_text = $is_fs ? "FREE SUPPLIER" : "NO-FS";
    $info_text = "$d[nama_barang] / $d[keterangan_barang]";
    $info_text = str_replace(',', ';', $info_text);
    $tr_csv .= "$i,$d[kode_po],$d[kode_barang],$info_text,$d[no_lot],$d[no_roll],$fs_text,$qty_retur_balik,$qty_retur,$qty_ganti,$qty_transit,$qty_transit_fs,$qty_qc,$qty_qc_fs,$qty_pick,$stok_akhir,$satuan,$d[kode_lokasi],$d[brand]\n";
  } else {
    $nol = '<span class="abu miring kecil">0</span>';
    $qty_transit_show = $qty_transit ? "<span class='tebal red'>$qty_transit</span>" : $nol;
    $qty_transit_fs_show = $qty_transit_fs ? "<span class='tebal purple'>$qty_transit_fs</span>" : $nol;
    $qty_qc_show = $qty_qc ? "<span class='tebal darkblue'>$qty_qc</span>" : $nol;
    $qty_qc_fs_show = $qty_qc_fs ? "<span class='tebal hijau'>$qty_qc_fs</span>" : $nol;
    $stok_akhir_show = $stok_akhir ? "<span class='tebal biru'>$stok_akhir $satuan</span>" : $nol;
    $qty_pick_show = $qty_pick ? "<span class='tebal darkred'>$qty_pick $satuan</span>" : $nol;

    $kode_lokasi_brand = "$d[kode_lokasi] <div class='abu f12'>$d[brand]</div>";
    $lokasi_show = ($qty_pick || $stok_akhir) ? "<span class='darkblue f16'>$kode_lokasi_brand</span>" : "<span class=abu>$kode_lokasi_brand</span>";

    $jam_terima = date('H:i', strtotime($d['tanggal_masuk']));
    $tanggal_terima_show = "$tanggal_masuk<div class='f12 abu'>$jam_terima</div>";

    $qty_retur_do_show = '0'; // zzz debug

    # ===================================================
    # FINAL TR LOOP
    # ===================================================
    $tr .= "
      <tr>
        <td>$i</td>
        
        <td>$tanggal_terima_show</td>
        <td>
          <div>$d[kode_po]</div>
          <div class='f12 abu'>SJ: $d[kode_sj]</div>
        </td>
        <td>
          $d[kode_barang]
          <div class='abu f12'>Kode lama: $d[kode_lama]</div>
          <div class='abu f12'>$d[nama_barang]</div>
          <div class='abu f12'>$d[keterangan_barang]</div>
        </td>
        <td>$d[no_lot]</td>
        <td>$d[count_roll]</td>
        <td>$lokasi_show</td>
        <td>$qty_transit_show</td>
        <td>$qty_transit_fs_show</td>
        <td>$qty_retur</td>
        <td>$qty_ganti</td>
        <td>$qty_qc_show</td>
        <td>$qty_qc_fs_show</td>
        <td>$qty_pick_show</td>
        <td>$qty_retur_do_show</td>
        <td>$stok_akhir_show</td>
      </tr>
    ";
  }
}

if (!$tr) $tr = "<tr><td colspan=100%><div class='alert alert-danger'>Data tidak ditemukan</div></td></tr>";

if ($get_csv) {
  // $tr_csv.= "$i,$d[kode_barang],$info_text,$qty_transit,$qty_transit_fs,$qty_qc,$qty_qc_fs,$qty_pick,$stok_akhir,$satuan,$stok_akhir,$d[kode_lokasi],$d[brand]\n";
  $data_csv .= "No,KODE BARANG,INFO,NO.LOT,NO.ROLL,FS,QTY SUBITEM,RETUR,BALIK,TRANSIT PO,TRANSIT FS,QC PO,QC FS,PICK,STOK AKHIR,SATUAN,LOKASI,BRAND\n$tr_csv";

  $ymd = date('ymd');
  $tipe_stok_ = $tipe_stok ? "_$tipe_stok" : '';
  $filtered_ = $keyword ? "_filtered" : '';
  $cat_ = $cat . '_';
  $path_csv = "csv/stok_opname_$cat_$ymd$tipe_stok_$filtered_.csv";
  $fcsv = fopen("$path_csv", "w+") or die("$path_csv cannot accesible.");
  fwrite($fcsv, $data_csv);
  fclose($fcsv);

  $files = scandir('csv');

  $li = '';
  foreach ($files as $key => $file) {
    if (strpos("salt$file", '.csv')) {
      if ("csv/$file" != $path_csv) {
        //auto-delete
        if (strpos("salt$file", '_filtered.csv')) {
          unlink("csv/$file");
        } else {
          $li .= "<li class='mb1 mt1' id=li_csv_$key><a href='csv/$file'>$file</a> <span class=btn_aksi id='li_csv_$key" . "__delete_file__$file" . "__csv'>$img_delete</span></></li>";
        }
      }
    }
  }


  $tb_stok = "
    <a href='$path_csv' class='btn btn-primary btn-sm'>Download CSV</a>
    <hr>
    History CSV lainnya:
    <ul>
      $li
    </ul>
  ";
} else {
  $tb_stok = "
    <div style='overflow:scroll'>
      <table class='table table-striped' style='widsth:2000px'>
        <thead>
          <th>NO</th>
          <th>TGL MASUK</th>
          <th>PO</th>
          <th>ID</th>
          <th>LOT</th>
          <th>ROLL</th>
          <th>LOKASI</th>
          <th class=darkred>TRANSIT</th>
          <th class=darkred>TR-FS</th>
          <th>RETUR</th>
          <th>GANTI</th>
          <th class=darkblue>PASS-QC</th>
          <th class=darkblue>PASS-QC-FS</th>
          <th class=darkred>PICK</th>
          <th class=green>RETUR-DO</th>
          <th>STOCK</th>
        </thead>
        $tr
      </table>
    </div>
  ";
}

echo
"
  <div class='flexy flex-between mb2'>
    <div class=flexy>
      <div>
        <form method=post>
          <div class=flexy>
            <div>
              <input class='form-control form-control-sm' placeholder='Filter ...' name=keyword id=keyword value='$keyword' maxlength=15 $bg_keyword>
            </div>
            <div>
              $select
            </div>
            <div>
              <input type=hidden name=cat value='$cat'>
              <button class='btn btn-primary btn-sm' name=btn_filter>Filter</button>
            </div>
            <div>
              <button class='btn btn-success btn-sm' name=btn_get_csv value=1>Get CSV</button>
            </div>
          </div>
        </form>
      </div>
      <div class='$hide_clear'><a href='?stok&cat=$cat' class=kecil>Clear<span class=f18> </span></a></div>
      <div class='kecil abu'>Tampil <span class='darkblue f18'>$jumlah_tampil</span> data of $jumlah_records records</div>
    </div>
    <div class='mb2 kanan'>
      <a class='btn btn-sm btn-info' href='?master&p=barang'>Master Barang</a>
    </div>
  </div>  

  $tb_stok
";
