<div class="pagetitle">
  <h1>Bukti Barang Masuk</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=data_sj">Data SJ</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=manage_sj&kode_sj=<?=$kode_sj?>">Manage SJ</a></li>
      <li class="breadcrumb-item active">BBM</li>
    </ol>
  </nav>
</div>


<?php
set_title('BBM');

# =======================================================================
# PROCESSORS 
# =======================================================================
include 'bbm_process_upload.php';
include 'bbm_process_verification.php';

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
$all_qty_allocated = 1;
$total_qty_diterima = 0;
$total_qty_kumulatif_item = 0;

# =======================================================================
# SURAT JALAN
# =======================================================================
include 'bbm_surat_jalan.php';

# =======================================================================
# BBM INFO
# =======================================================================
include 'bbm_info.php';

# =======================================================================
# CHECK IS VALID ID-BBM | GET TANGGAL TERIMA
# =======================================================================
$s = "SELECT a.*,
(SELECT nama FROM tb_user WHERE id=a.diverifikasi_oleh) verifikator  
FROM tb_bbm a 
WHERE a.kode_sj='$kode_sj'";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0) {
  echo 'numrows zero :' . $s;
  // jsurl("?penerimaan&p=terima_barang&kode_sj=$kode_sj");
  exit;
}else{
  $d = mysqli_fetch_assoc($q);
  $tanggal_masuk = $d['tanggal_masuk'];
  $diverifikasi_oleh = $d['diverifikasi_oleh'];
  $tanggal_verifikasi = $d['tanggal_verifikasi'];
  $verifikator = ucwords(strtolower($d['verifikator']));
  // echo "<h1>DEBUG $verifikator $diverifikasi_oleh</h1>";
}

# =======================================================================
# GET ITEM PO
# =======================================================================
include 'bbm_qty_diterima.php';

# =======================================================================
# BBM NEXT ACTIONS AFTER VERIFIKASI
# =======================================================================
if($all_qty_allocated){
  echo "
  <div class='mt3 hide_cetak wadah'>
    <div class=flexy>
      <div>Jam masuk</div>
      <div>
        <input class='editable form-control form-control-sm' type=time placeholder='Awal Masuk' id=awal_masuk__bbm__$id_bbm value=$awal_masuk>
      </div>
      <div>
        <span id='awal_masuk__check__$id_bbm' class=hideit>$img_check</span>
      </div>
      <div>
        s.d
      </div>
      <div>
        <input class='editable form-control form-control-sm' type=time placeholder='Akhir Masuk' id=akhir_masuk__bbm__$id_bbm value=$akhir_masuk> 
      </div>
      <div>
        <span id='akhir_masuk__check__$id_bbm' class=hideit>$img_check</span>
      </div>
    </div>    
    <div class=mt2>Next: <a href='?master_penerimaan&cat=$cat&po=$kode_po&waktu=all_time'>Proses Retur dari Master Penerimaan</a></div>
  </div>
  ";
}

# =======================================================================
# BBM JS
# =======================================================================
include 'bbm_js.php';
?>