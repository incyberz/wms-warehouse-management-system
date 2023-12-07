<div class="pagetitle">
  <h1>Bukti Barang Masuk</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?po">PO Home</a></li>
      <li class="breadcrumb-item"><a href="?po&p=terima_barang">Cari PO</a></li>
      <li class="breadcrumb-item active">BBM</li>
    </ol>
  </nav>
</div>


<?php
# =======================================================================
# PROCESSORS 
# =======================================================================
include 'bbm_upload_process.php';
include 'bbm_verification_process.php';

$tb_identitas_po = '';
$tb_items = '';
$no_bbm = '';
$id_bbm = '';
$today = date('Y-m-d');
$pernah_terima = 0; // untuk parsial penerimaan
$saya_menyatakan_disabled = '';
$penerimaan = $_GET['penerimaan'] ?? '';
$id_bbm = $_GET['id_bbm'] ?? '';
$link_no_bbm = '';
$sisa_qty = 0;
$ada_bbm_kosong = 0;
$btn_simpan_caption = "Simpan BBM";
$arr_no_bbm = [];


# =======================================================================
# GET IDENTITAS PO 
# =======================================================================
include 'bbm_identitas_po.php';

# =======================================================================
# TOTAL QTY PENERIMAAN
# =======================================================================
include 'bbm_total_qty_penerimaan.php';

if($id_bbm==''){
  echo ('Silahkan pilih nomor BBM diatas!');
}else{
  # =======================================================================
  # CHECK IS VALID ID-BBM | GET TANGGAL TERIMA
  # =======================================================================
  $s = "SELECT a.*,
  (SELECT nama FROM tb_user WHERE id=a.diverifikasi_oleh) verifikator  
  FROM tb_bbm a 
  WHERE a.id=$id_bbm";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0) {
    jsurl("?po&p=terima_barang&no_po=$no_po");
    exit;
  }else{
    $d = mysqli_fetch_assoc($q);
    $tanggal_terima = $d['tanggal_terima'];
    $nomor = $d['nomor'];
    $diverifikasi_oleh = $d['diverifikasi_oleh'];
    $tanggal_verifikasi = $d['tanggal_verifikasi'];
    $verifikator = ucwords(strtolower($d['verifikator']));
    // echo "<h1>DEBUG $verifikator $diverifikasi_oleh</h1>";
  }
  
  # =======================================================================
  # GET ITEM PO
  # =======================================================================
  include 'bbm_get_item_po.php';
} 
include 'bbm_js.php';
?>