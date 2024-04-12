<?php
$judul = 'UOM Report';
set_title($judul);
// to do : fix decimal
$cat = $_GET['cat'] ?? 'aks'; //default AKS
$id_kategori = $cat == 'aks' ? 1 : 2;
$jenis_barang = $cat == 'aks' ? 'Aksesoris' : 'Fabric';
$arr_satuan = ['PCS', 'YD', 'KG', 'M'];
$perlu_reload = 0;

include 'include/date_managements.php';
include 'sql_opname.php';

# =====================================================
# BREAD HANDLER
# =====================================================
$bread = "<li class='breadcrumb-item'><a href='?uom_report&cat=fab'>Report Fabric</a></li><li class='breadcrumb-item active'>Aksesoris</li>";
if ($cat == 'fab')
  $bread = "<li class='breadcrumb-item'><a href='?uom_report&cat=aks'>Report Aksesoris</a></li><li class='breadcrumb-item active'>Report Fabric</li>";
echo "
  <div class='pagetitle'>
    <h1>$judul $jenis_barang</h1>
    <nav>
      <ol class='breadcrumb'>
        $bread
      </ol>
    </nav>
  </div>
";

# =====================================================
# PROCESSOR CSV
# =====================================================
if (isset($_POST['btn_download_csv'])) {
  $tanggal_awal = $_GET['tanggal_awal'] ?? '2020-1-1';
  $tanggal_akhir = $_GET['tanggal_akhir'] ?? $today;
  $s = "SELECT * FROM tb_rekap WHERE tanggal>='$tanggal_awal' AND tanggal <='$tanggal_akhir'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $src = "csv/uom_report-$today.csv";
  $file = fopen($src, "w+");
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    if ($i == 1) {
      $arr_header = [];
      foreach ($d as $key => $value) {
        array_push($arr_header, $key);
      }
      fputcsv($file, $arr_header);
    }
    fputcsv($file, $d);
  }

  fclose($file);

  $arr = explode('?', $_SERVER['REQUEST_URI']);
  echo "<a href='?$arr[1]' class='btn btn-success'>Back to Report</a> <a href='$src' class='btn btn-primary'>Download CSV</a>";

  exit;
}

# =====================================================
# PROCESSOR FILTER
# =====================================================
if (isset($_POST['btn_filter'])) {
  $select_waktu = $_POST['select_waktu'] ?? die(erid('select_waktu'));
  $tanggal_awal = $_POST['tanggal_awal'] ?? '';
  $tanggal_akhir = $_POST['tanggal_akhir'] ?? '';
  $show_no_trx = $_POST['show_no_trx'] ?? '';
  $deep_filter = $_POST['deep_filter'] ?? '';
  jsurl("?$parameter&cat=$cat&select_waktu=$select_waktu&tanggal_awal=$tanggal_awal&tanggal_akhir=$tanggal_akhir&show_no_trx=$show_no_trx&deep_filter=$deep_filter");
  exit;
}

$debug .= "<br>today: <span id=today>$today</span>";
$debug .= "<br>hari_ini: <span id=hari_ini>$hari_ini</span>";
$debug .= "<br>ahad_skg: <span id=ahad_skg>$ahad_skg</span>";
$debug .= "<br>ahad_depan: <span id=ahad_depan>$ahad_depan</span>";
$debug .= "<br>senin_skg: <span id=senin_skg>$senin_skg</span>";
$debug .= "<br>awal_bulan: <span id=awal_bulan>$awal_bulan</span>";
$debug .= "<br>awal_tahun: <span id=awal_tahun>$awal_tahun</span>";

# =====================================================
# GET MINIMUM TANGGAL FROM KUMULATIF
# =====================================================
$s = "SELECT tanggal_masuk as tanggal_minimum FROM tb_sj_kumulatif ORDER BY tanggal_masuk LIMIT 1";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);
$tanggal_minimum = date('Y-m-d', strtotime($d['tanggal_minimum']));

# =====================================================
# GET FILTER HANDLER
# =====================================================
$get_select_waktu = $_GET['select_waktu'] ?? 'hari_ini';
$get_tanggal_awal = $_GET['tanggal_awal'] ?? $today;
$get_tanggal_akhir = $_GET['tanggal_akhir'] ?? $today;
$get_show_no_trx = $_GET['show_no_trx'] ?? '';
$get_deep_filter = $_GET['deep_filter'] ?? '';

if ($get_select_waktu == 'hari_ini') {
  $arr_tanggal = [$today];
} elseif ($get_select_waktu == 'kemarin') {
  $arr_tanggal = [$kemarin];
} elseif ($get_select_waktu == 'minggu_ini') {
  $arr_tanggal = [$senin_skg];
  for ($i = 1; $i < 5; $i++) {
    array_push($arr_tanggal, date('Y-m-d', strtotime("+$i day", strtotime($senin_skg))));
  }
} elseif ($get_select_waktu == 'minggu_kemarin') {
  $senin_kemarin = date('Y-m-d', strtotime("-7 day", strtotime($senin_skg)));
  $arr_tanggal = [$senin_kemarin];
  for ($i = 1; $i < 5; $i++) {
    array_push($arr_tanggal, date('Y-m-d', strtotime("+$i day", strtotime($senin_kemarin))));
  }
} elseif ($get_select_waktu == 'bulan_ini') {
  $arr_tanggal = [$awal_bulan];
  $selisih_hari = (strtotime('today') - strtotime($awal_bulan)) / (60 * 60 * 24);
  for ($i = 1; $i <= $selisih_hari; $i++) {
    array_push($arr_tanggal, date('Y-m-d', strtotime("+$i day", strtotime($awal_bulan))));
  }
} elseif ($get_select_waktu == 'tahun_ini') {
  $arr_tanggal = [$awal_tahun];
  $selisih_hari = (strtotime('today') - strtotime($awal_tahun)) / (60 * 60 * 24);
  for ($i = 1; $i <= $selisih_hari; $i++) {
    array_push($arr_tanggal, date('Y-m-d', strtotime("+$i day", strtotime($awal_tahun))));
  }
} elseif ($get_select_waktu == 'antara_tanggal') {
  $arr_tanggal = [$get_tanggal_awal];
  $selisih_hari = (strtotime($get_tanggal_akhir) - strtotime($get_tanggal_awal)) / (60 * 60 * 24);
  for ($i = 1; $i <= $selisih_hari; $i++) {
    array_push($arr_tanggal, date('Y-m-d', strtotime("+$i day", strtotime($get_tanggal_awal))));
  }
}

$tanggal_awal = date('Y-m-d', strtotime($arr_tanggal[0]));
$tanggal_akhir = date('Y-m-d', strtotime($arr_tanggal[count($arr_tanggal) - 1]));

# =====================================================
# LOOP TIAP TANGGAL
# =====================================================
$tr = '';
$no_tgl = 0;
foreach ($arr_tanggal as $tanggal) {
  $weekday = date('w', strtotime($tanggal));
  $nama_hari = $arr_nama_hari[$weekday];
  $tanggal_show = date('d F Y', strtotime($tanggal));
  $gradasi = $weekday == 6 ? 'kuning' : '';
  $gradasi = $weekday == 0 ? 'merah' : $gradasi;

  $besok = date('Y-m-d', strtotime("+1 day", strtotime($tanggal)));

  # =====================================================
  # QUICK FILTER MODE
  # =====================================================
  $s = "SELECT * FROM tb_rekap WHERE tanggal='$tanggal'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $ada_rekap = mysqli_num_rows($q);

  $sum_trx = 0;
  if (!$ada_rekap || $get_deep_filter || $tanggal == $today) {
    # =====================================================
    # DEEP FILTER MODE
    # =====================================================
    if ($tanggal != $today) {
      $perlu_reload = 1;
      echolog("insert/update data rekap untuk tanggal $tanggal");
    }
    foreach ($arr_satuan as $key => $satuan) {
      $s = "SELECT 
        -- =================================
        -- PEMASUKAN $satuan
        -- =================================
      (
        SELECT sum(a.qty) FROM tb_roll a 
        JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id 
        JOIN tb_sj_item c ON b.id_sj_item=c.id 
        JOIN tb_barang d ON c.kode_barang=d.kode
        WHERE b.tanggal_masuk >= '$tanggal'
        AND b.tanggal_masuk < '$besok' AND d.satuan='$satuan') qty_kumulatif_total,
      (
        SELECT sum(a.qty) FROM tb_retur a 
        JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id 
        JOIN tb_sj_item c ON b.id_sj_item=c.id 
        JOIN tb_barang d ON c.kode_barang=d.kode
        WHERE b.tanggal_masuk >= '$tanggal'
        AND b.tanggal_masuk < '$besok' AND d.satuan='$satuan') qty_retur_total,
      (
        SELECT sum(a.qty) FROM tb_ganti a 
        JOIN tb_retur b ON a.id_retur=b.id 
        JOIN tb_sj_kumulatif c ON b.id_kumulatif=c.id 
        JOIN tb_sj_item d ON c.id_sj_item=d.id 
        JOIN tb_barang e ON d.kode_barang=e.kode
        WHERE c.tanggal_masuk >= '$tanggal'
        AND c.tanggal_masuk < '$besok' AND e.satuan='$satuan' AND e.satuan='$satuan') qty_ganti_total,
    
        -- =================================
        -- PENGELUARAN $satuan
        -- =================================
      (
        SELECT sum(a.qty_allocate) FROM tb_pick a 
        JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id 
        JOIN tb_sj_item c ON b.id_sj_item=c.id 
        JOIN tb_barang d ON c.kode_barang=d.kode
        WHERE b.tanggal_masuk >= '$tanggal'
        AND b.tanggal_masuk < '$besok' AND d.satuan='$satuan') qty_allocate_total,
      (
        SELECT sum(a.qty) FROM tb_retur_do a 
        JOIN tb_pick b ON a.id_pick=b.id 
        JOIN tb_sj_kumulatif c ON b.id_kumulatif=c.id 
        JOIN tb_sj_item d ON c.id_sj_item=d.id 
        JOIN tb_barang e ON d.kode_barang=e.kode
        WHERE c.tanggal_masuk >= '$tanggal'
        AND c.tanggal_masuk < '$besok' AND e.satuan='$satuan') qty_retur_do_total,
    
    
    
        -- =================================
        -- PEMASUKAN $satuan SEBELUMNYA
        -- =================================
      (
        SELECT sum(a.qty) FROM tb_roll a 
        JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id 
        JOIN tb_sj_item c ON b.id_sj_item=c.id 
        JOIN tb_barang d ON c.kode_barang=d.kode
        WHERE b.tanggal_masuk < '$tanggal' AND d.satuan='$satuan') qty_kumulatif_total_sebelumnya,
      (
        SELECT sum(a.qty) FROM tb_retur a 
        JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id 
        JOIN tb_sj_item c ON b.id_sj_item=c.id 
        JOIN tb_barang d ON c.kode_barang=d.kode
        WHERE b.tanggal_masuk < '$tanggal' AND d.satuan='$satuan') qty_retur_total_sebelumnya,
      (
        SELECT sum(a.qty) FROM tb_ganti a 
        JOIN tb_retur b ON a.id_retur=b.id 
        JOIN tb_sj_kumulatif c ON b.id_kumulatif=c.id 
        JOIN tb_sj_item d ON c.id_sj_item=d.id 
        JOIN tb_barang e ON d.kode_barang=e.kode
        WHERE c.tanggal_masuk < '$tanggal' AND e.satuan='$satuan') qty_ganti_total_sebelumnya,
    
        -- =================================
        -- PENGELUARAN $satuan SEBELUMNYA
        -- =================================
      (
        SELECT sum(a.qty_allocate) FROM tb_pick a 
        JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id 
        JOIN tb_sj_item c ON b.id_sj_item=c.id 
        JOIN tb_barang d ON c.kode_barang=d.kode
        WHERE b.tanggal_masuk < '$tanggal' AND d.satuan='$satuan') qty_allocate_total_sebelumnya,
      (
        SELECT sum(a.qty) FROM tb_retur_do a 
        JOIN tb_pick b ON a.id_pick=b.id 
        JOIN tb_sj_kumulatif c ON b.id_kumulatif=c.id 
        JOIN tb_sj_item d ON c.id_sj_item=d.id 
        JOIN tb_barang e ON d.kode_barang=e.kode
        WHERE c.tanggal_masuk < '$tanggal' AND e.satuan='$satuan') qty_retur_do_total_sebelumnya,
        1
      ";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      $d = mysqli_fetch_assoc($q);

      $masuk[$satuan] = $d['qty_kumulatif_total']
        - $d['qty_retur_total']
        + $d['qty_ganti_total'];
      $keluar[$satuan] = $d['qty_allocate_total']
        - $d['qty_retur_do_total'];
      $prev_stok[$satuan] = $d['qty_kumulatif_total_sebelumnya']
        - $d['qty_retur_total_sebelumnya']
        + $d['qty_ganti_total_sebelumnya']
        - $d['qty_allocate_total_sebelumnya']
        + $d['qty_retur_do_total_sebelumnya'];
      $stok[$satuan] = $prev_stok[$satuan] + $masuk[$satuan] - $keluar[$satuan];
      $sum_trx += $masuk[$satuan] + $keluar[$satuan];
      // echolog("sum_trx:$sum_trx = masuk:$masuk[$satuan] + keluar:$keluar[$satuan];");
    } // end foreach


    # ==================================================
    # AUTO SAVE :: DEEP FILTER MODE
    # ==================================================
    $s = "INSERT INTO tb_rekap (
      tanggal,
      masuk_PCS,
      masuk_YD,
      masuk_KG,
      masuk_M,
      keluar_PCS,
      keluar_YD,
      keluar_KG,
      keluar_M,
      stok_PCS,
      stok_YD,
      stok_KG,
      stok_M
    ) VALUES (
      '$tanggal',
      $masuk[PCS],
      $masuk[YD],
      $masuk[KG],
      $masuk[M],
      $keluar[PCS],
      $keluar[YD],
      $keluar[KG],
      $keluar[M],
      $stok[PCS],
      $stok[YD],
      $stok[KG],
      $stok[M]
    ) ON DUPLICATE KEY UPDATE 
      tanggal = '$tanggal',
      masuk_PCS = $masuk[PCS],
      masuk_YD = $masuk[YD],
      masuk_KG = $masuk[KG],
      masuk_M = $masuk[M],
      keluar_PCS = $keluar[PCS],
      keluar_YD = $keluar[YD],
      keluar_KG = $keluar[KG],
      keluar_M = $keluar[M],
      stok_PCS = $stok[PCS],
      stok_YD = $stok[YD],
      stok_KG = $stok[KG],
      stok_M = $stok[M],
      last_update = CURRENT_TIMESTAMP
    ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  } else { // quick filter
    $d = mysqli_fetch_assoc($q);
    $masuk['PCS'] = floatval($d['masuk_PCS']);
    $masuk['YD'] = floatval($d['masuk_YD']);
    $masuk['KG'] = floatval($d['masuk_KG']);
    $masuk['M'] = floatval($d['masuk_M']);
    $keluar['PCS'] = floatval($d['keluar_PCS']);
    $keluar['YD'] = floatval($d['keluar_YD']);
    $keluar['KG'] = floatval($d['keluar_KG']);
    $keluar['M'] = floatval($d['keluar_M']);
    $stok['PCS'] = floatval($d['stok_PCS']);
    $stok['YD'] = floatval($d['stok_YD']);
    $stok['KG'] = floatval($d['stok_KG']);
    $stok['M'] = floatval($d['stok_M']);

    foreach ($arr_satuan as $key => $satuan) {
      $sum_trx += $masuk[$satuan] + $keluar[$satuan];
    }
  }





  if (!$get_show_no_trx and !$sum_trx and $tanggal != $today) {
    // jika tidak diceklis dan tidak ada sum_trx
    continue;
  }
  $class_no_trx = $sum_trx ? '' : 'f10 miring abu';

  $nol = '<span class="f12 miring">0</span>';
  $masuk_PCS = $masuk['PCS'] ? number_format($masuk['PCS'], 0) : $nol;
  $masuk_YD = $masuk['YD'] ? number_format($masuk['YD'], 2) : $nol;
  $masuk_KG = $masuk['KG'] ? number_format($masuk['KG'], 2) : $nol;
  $masuk_M = $masuk['M'] ? number_format($masuk['M'], 2) : $nol;
  $keluar_PCS = $keluar['PCS'] ? number_format($keluar['PCS'], 0) : $nol;
  $keluar_YD = $keluar['YD'] ? number_format($keluar['YD'], 2) : $nol;
  $keluar_KG = $keluar['KG'] ? number_format($keluar['KG'], 2) : $nol;
  $keluar_M = $keluar['M'] ? number_format($keluar['M'], 2) : $nol;
  $stok_PCS = $stok['PCS'] ? number_format($stok['PCS'], 0) : $nol;
  $stok_YD = $stok['YD'] ? number_format($stok['YD'], 2) : $nol;
  $stok_KG = $stok['KG'] ? number_format($stok['KG'], 2) : $nol;
  $stok_M = $stok['M'] ? number_format($stok['M'], 2) : $nol;

  $no_tgl++;
  $tr .= "
    <tr class='gradasi-$gradasi $class_no_trx'>
      <td>$no_tgl</td>
      <td>
        $nama_hari, $tanggal_show
      </td>
      <td class='kanan green'>$masuk_PCS</td>
      <td class='kanan green'>$masuk_YD</td>
      <td class='kanan green'>$masuk_KG</td>
      <td class='kanan green'>$masuk_M</td>
      <td class='kanan darkred'>$keluar_PCS</td>
      <td class='kanan darkred'>$keluar_YD</td>
      <td class='kanan darkred'>$keluar_KG</td>
      <td class='kanan darkred'>$keluar_M</td>
      <td class='kanan'>$stok_PCS</td>
      <td class='kanan'>$stok_YD</td>
      <td class='kanan'>$stok_KG</td>
      <td class='kanan'>$stok_M</td>
    </tr>
  ";
}
$tr = $tr ? $tr : "<tr><td class='gradasi-merah p2' colspan=100%>No data.</td></tr>";


# =====================================================
# OPTIONS WAKTU
# =====================================================
$arr_waktu[0] = ['hari_ini', 'Hari ini'];
$arr_waktu[1] = ['kemarin', 'Kemarin'];
$arr_waktu[2] = ['minggu_ini', 'Minggu ini'];
$arr_waktu[3] = ['minggu_kemarin', 'Minggu kemarin'];
$arr_waktu[4] = ['bulan_ini', 'Bulan ini'];
$arr_waktu[5] = ['tahun_ini', 'Tahun ini'];
$arr_waktu[6] = ['antara_tanggal', 'Antara tanggal'];
$arr_waktu[7] = ['rekap_tahunan', 'Rekap Tahunan'];
$opt = '';
foreach ($arr_waktu as $key => $arr) {
  $selected = $arr[0] == $get_select_waktu ? 'selected' : '';
  $opt .= "<option value='$arr[0]' $selected>$arr[1]</option>";
}

// DISABLED VIEW FOR TANGGAL
$disabled_tanggal = $get_select_waktu == 'antara_tanggal' ? '' : 'disabled';

// download CSV jika ada data
$download_csv = !$no_tgl ? '' : "
  <form method=post>
    <button class='btn btn-success' name=btn_download_csv>Download CSV</button>
  </form>
";

echo "
  <!-- ============================================== -->
  <!-- FORM FILTER TANGGAL -->
  <!-- ============================================== -->
  <form method='post'>
    <div class='flexy'>
      <div>
        <select name='select_waktu' id='select_waktu' class='form-control'>
          $opt
        </select>
      </div>
      <div>
        <input type='date' name='tanggal_awal' id='tanggal_awal' class='form-control' value='$tanggal_awal' min='$tanggal_minimum' max='$today' $disabled_tanggal>
      </div>
      <div>
        <input type='date' name='tanggal_akhir' id='tanggal_akhir' class='form-control' value='$tanggal_akhir' min='$tanggal_minimum' max='$today' $disabled_tanggal>
      </div>
      <div class=pt2>
        <label class='f12 pointer'>
          <input type='checkbox' name='show_no_trx' id='show_no_trx' value=1> 
          Show No Trx
        </label>
      </div>
      <div class=pt2>
        <label class='f12 pointer'>
          <input type='checkbox' name='deep_filter' id='deep_filter' value=1> 
          Deep Filter
        </label>
      </div>
      <div>
        <button class='btn btn-success' name=btn_filter id=btn_filter>Quick Filter</button>
      </div>
    </div>
  </form>

  <!-- ============================================== -->
  <!-- TABEL SHOW -->
  <!-- ============================================== -->
  <table class='table table-bordered'>
    <tr class='darkblue tengah gradasi-toska'>
      <td rowspan='2'>No</td>
      <td rowspan='2'>Tanggal</td>
      <td colspan='4' class='green'>Penerimaan</td>
      <td colspan='4' class='darkred'>Pengeluaran</td>
      <td colspan='4' >Stok</td>
    </tr>
    <tr class='darkblue tengah gradasi-toska'>
      <td class='green'>PCS</td>
      <td class='green'>YD</td>
      <td class='green'>KG</td>
      <td class='green'>M</td>
      <td class='darkred'>PCS</td>
      <td class='darkred'>YD</td>
      <td class='darkred'>KG</td>
      <td class='darkred'>M</td>
      <td>PCS</td>
      <td>YD</td>
      <td>KG</td>
      <td>M</td>
    </tr>
    $tr
  </table>

  $download_csv
";

















if ($perlu_reload) jsurl();
?>
<script>
  $(function() {
    $('#deep_filter').click(function() {
      let checked = $(this).prop('checked');
      if (checked) {
        let y = confirm('Perform Deep Filter?\n\nDeep Filter membutuhkan waktu yang lama karena Deep Filter akan menghitung ulang dimulai dari seluruh QTY Roll, QTY Kumulatif, QTY Retur, QTY Balik, QTY Allocate, dan QTY Retur DO, serta untuk 4 jenis satuan. Jika rekap sudah ada maka akan disimpan pada tabel rekap untuk keperluan Quick Filter.');
        if (!y) {
          $(this).prop('checked', false);
        } else {
          $('#btn_filter').text('Deep Filter');
        }
      } else {
        $('#btn_filter').text('Quick Filter');
      }
    });
    $('#select_waktu').change(function() {
      let waktu = $(this).val();
      let disabled = waktu == 'antara_tanggal' ? 0 : 1;
      $('#tanggal_awal').prop('disabled', disabled);
      $('#tanggal_akhir').prop('disabled', disabled);
    })
  })
</script>