<?php
include 'insho_styles.php';
include 'conn.php';
$id_pick = $_GET['id_pick'] ?? die('Page ini tidak bisa diakses secara langsung.');
$s = "SELECT 
e.kode as kode_barang,  
e.nama as nama_barang,  
e.keterangan as keterangan_barang,  
e.kode_lama,
e.satuan,
d.kode_po,
a.qty_allocate,
a.tanggal_allocate,
b.kode_lokasi,
b.no_lot,
b.is_fs,
f.brand as this_brand,
(SELECT count(1) FROM tb_roll WHERE id_kumulatif=b.id) count_roll

FROM tb_pick a 
JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id 
JOIN tb_sj_item c ON b.id_sj_item=c.id 
JOIN tb_sj d ON c.kode_sj=d.kode 
JOIN tb_barang e ON c.kode_barang=e.kode 
JOIN tb_lokasi f ON b.kode_lokasi=f.kode 

WHERE a.id=$id_pick";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);

$kode_barang = $d['kode_barang'];
$nama_barang = $d['nama_barang'];
$keterangan_barang = $d['keterangan_barang'];
$kode_lama = $d['kode_lama'];

$kode_po = $d['kode_po'];
$qty_allocate = $d['qty_allocate'];
$satuan = $d['satuan'];
// $no_roll = $d['no_roll'];
$kode_lokasi = $d['kode_lokasi'];
$this_brand = $d['this_brand'];
$no_lot = $d['no_lot'];
$is_fs = $d['is_fs'];
$tanggal_allocate = $d['tanggal_allocate'];
$this_brand = $d['this_brand'];

// count_roll
$count_roll = $d['count_roll'];
if ($count_roll == 0) {
  die('Belum ada Data Roll untuk data Pick ini.');
} elseif ($count_roll == 1) {
  // hanya data awal atau di bundle ke 1 roll
} else {
  // data roll real
}

// show
$tanggal_allocate_show = date('d/m/y H:i', strtotime($tanggal_allocate));
$qty_allocate = floatval($qty_allocate);

$no_roll = ''; //zzz
$no_po_dll = "$kode_po $no_lot ($qty_allocate)$satuan $no_roll ($kode_lokasi $this_brand) $tanggal_allocate_show";

$fs_label_info = '';
if ($is_fs) {
  $fs_label_info = 'FREE SUPPLIER';
}

$kode_barang_and_id_pick = "$kode_barang-$id_pick";


$count = $_POST['count'] ?? '';
if ($count) {

  for ($i = 1; $i <= $count; $i++) {

    echo "
      <div >
        <div class='bordered p2 tengah' style='width:10cm; height:6cm'>
          <div class=mt4>
            <table>
              <tr>
                <td width=180px align=center>
      ";

    require_once 'include/qrcode.php';
    $qr = QRCode::getMinimumQRCode($kode_barang_and_id_pick, QR_ERROR_CORRECT_LEVEL_L);
    $qr->printHTML('6px');

    echo "
                <div class='f20 mt2' >$kode_barang</div>
                <div class='f12 ' >$kode_lama</div>
              </td>
              <td>
                $fs_label_info
                <div class=f12>No. $i</div>
                <div class=f12>$no_po_dll</div>
                <div class='f16 mt2 mb1'>$nama_barang</div>
                <div class='f12'>$keterangan_barang</div>
              </td>
            </tr>
          </table>
        </div>
      </div>
    ";
  }
  echo "<script>window.print()</script>";
  exit;
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cetak Label Pengeluaran</title>
</head>

<body>
  <h2>Cetak Label Pengeluaran</h2>

  <?php
  $tr = '';
  foreach ($d as $key => $value) {
    $tr .= "
      <tr>
        <td class='f14 abu miring'>$key</td>
        <td>:</td>
        <td>$value</td>
      </tr>
    ";
  }
  echo "<div class=wadah><table>$tr</table></div>";


  $opt_count = '';
  for ($i = 1; $i <= 100; $i++) {
    $opt_count .= "<option value=$i>$i kali</option>";
  }

  echo "
  <form method=post>
    <div class='wadah flexy'>
      <div>Jumlah Cetak</div>
      <div>
        <select name=count class='form-control'>
          $opt_count
        </select>
      </div>
      <div>
        <button name=btn_cetak class='btn btn-primary'>
          Cetak
        </button>
      </div>
    </div>
  </form>
";


  ?>

</body>

</html>