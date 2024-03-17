<?php
$judul = 'Report Inbound-Outbound';
set_title($judul);
// to do : fix decimal
$cat = $_GET['cat'] ?? 'aks'; //default AKS
$id_kategori = $cat == 'aks' ? 1 : 2;
$jenis_barang = $cat == 'aks' ? 'Aksesoris' : 'Fabric';
include 'include/date_managements.php';
include 'sql_opname.php';

# =====================================================
# BREAD HANDLER
# =====================================================
$bread = "<li class='breadcrumb-item'><a href='?report&cat=fab'>Report Fabric</a></li><li class='breadcrumb-item active'>Aksesoris</li>";
if ($cat == 'fab')
  $bread = "<li class='breadcrumb-item'><a href='?report&cat=aks'>Report Aksesoris</a></li><li class='breadcrumb-item active'>Report Fabric</li>";
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
# PROCESSOR FILTER
# =====================================================
if (isset($_POST['btn_filter'])) {
  $select_waktu = $_POST['select_waktu'] ?? die(erid('select_waktu'));
  $tanggal_awal = $_POST['tanggal_awal'] ?? '';
  $tanggal_akhir = $_POST['tanggal_akhir'] ?? '';
  jsurl("?$parameter&cat=$cat&select_waktu=$select_waktu&tanggal_awal=$tanggal_awal&tanggal_akhir=$tanggal_akhir");
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

// cek tanggal masuk
# =====================================================
# SQL
# =====================================================
$tr = '';
$i = 0;
foreach ($arr_tanggal as $tanggal) {
  $i++;
  $weekday = date('w', strtotime($tanggal));
  $nama_hari = $arr_nama_hari[$weekday];
  $tanggal_show = date('d F Y', strtotime($tanggal));
  $gradasi = $weekday == 6 ? 'kuning' : '';
  $gradasi = $weekday == 0 ? 'merah' : $gradasi;

  $besok = date('Y-m-d', strtotime("+1 day", strtotime($tanggal)));

  $arr_satuan = ['PCS', 'YD', 'KG', 'M'];

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

    // $satuan
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
  }








  $tr .= "
    <tr class='gradasi-$gradasi'>
      <td>$i</td>
      <td>
        $nama_hari, $tanggal_show
      </td>
      <td>$masuk[PCS]</td>
      <td>$masuk[YD]</td>
      <td>$masuk[KG]</td>
      <td>$masuk[M]</td>
      <td>$keluar[PCS]</td>
      <td>$keluar[YD]</td>
      <td>$keluar[KG]</td>
      <td>$keluar[M]</td>
      <td>$prev_stok[PCS]</td>
      <td>$prev_stok[YD]</td>
      <td>$prev_stok[KG]</td>
      <td>$prev_stok[M]</td>
    </tr>
  ";
}


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
      <div>
        <button class='btn btn-success' name=btn_filter>Filter</button>
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
";


















?>
<script>
  $(function() {
    $('#select_waktu').change(function() {
      let waktu = $(this).val();
      let disabled = waktu == 'antara_tanggal' ? 0 : 1;
      $('#tanggal_awal').prop('disabled', disabled);
      $('#tanggal_akhir').prop('disabled', disabled);
    })
  })
</script>