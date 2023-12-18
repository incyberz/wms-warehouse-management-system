<style>
  .no-bullet{list-style: none}
</style>
<div class="pagetitle">
  <h1>Penerimaan Surat Jalan</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=data_sj">Data SJ</a></li>
      <li class="breadcrumb-item active">Manage SJ</li>
    </ol>
  </nav>
</div>
<?php

include 'include/arr_supplier.php';
# ==========================================
# SUPPLIER
# ==========================================
$opt = '';
foreach ($arr_supplier as $id => $nama) {
  $opt.= "<option value=$id>$nama</option>";
}
$select_supplier = "
  <select class='form-control' name=id_supplier>$opt</select>
";


$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';
if($kode_sj==''){
  if(isset($_POST['btn_buat_po'])){
    $kode = clean_sql($_POST['kode']);
    $id_supplier = clean_sql($_POST['id_supplier']);
    $s = "INSERT INTO tb_sj (kode,id_supplier) VALUES ('$kode',$id_supplier)";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    jsurl("?penerimaan&p=manage_sj&kode_sj=$kode");
    exit;
  }

  $kode_sj = date('Ymd').'01-MTL';
  ?>
  <form method=post>
    <div class="wadah gradasi-hijau" style='max-width:500px;'>
      <h2 class='abu f20'>Create Purchase Order</h2>
      <hr>
      Nomor SJ
      <input name=kode type="text" class="form-control mt1 mb2 consolas f30 upper" value="<?=$kode_sj?>">
      Supplier
      <div class="mt1 mb2">
        <?=$select_supplier?>
      </div>
      <button class='btn btn-primary w-100' name=btn_buat_po>ZZZ Buat SJ Baru</button>
    </div>
  </form>


  <?php
}else{

  # ==========================================
  # GET DATA SJ
  # ==========================================
  $s = "SELECT 
  a.id as id_sj,
  a.*,
  b.id as id_supplier,
  b.nama as nama_supplier ,
  b.kode as kode_supplier ,
  b.contact_person ,
  b.no_telfon as telp_supplier ,
  b.alamat as alamat_supplier 

  FROM tb_sj a   
  JOIN tb_supplier b ON a.id_supplier=b.id 
  WHERE a.kode='$kode_sj' ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0){
    die(div_alert('danger',"Data SJ tidak ditemukan. <hr>Silahkan cek pada <a href='?penerimaan&p=data_sj'>List Data SJ</a>"));
  }

  $d = mysqli_fetch_assoc($q);

  $id_sj = $d['id_sj'];
  $kode_po = $d['kode_po'];
  $tanggal_terima = $d['tanggal_terima'];

  # ==========================================
  # TAMBAH BARANG BARU DAN TAMBAHKAN KE SJ ITEM
  # ==========================================
  if(isset($_POST['btn_simpan_dan_tambahkan']) || isset($_POST['btn_add_sj_item'])){
    include 'tambah_sj_item.php';
  }

  //buyer
  $nama_buyer = $nama_usaha;
  $alamat_buyer = $alamat_usaha;
  $telp_buyer = "$no_telp_kantor / $no_hp_kantor";
  $wa_buyer = $no_wa_kantor;

  //supplier
  $id_supplier = $d['id_supplier'];
  $kode_supplier = $d['kode_supplier'];
  $nama_supplier = $d['nama_supplier'];
  $telp_supplier = $d['telp_supplier'];
  $alamat_supplier = $d['alamat_supplier'];
  $contact_person = $d['contact_person'];

  // $keterangan = $d['keterangan'];
  // $ket_item = $d['ket_item'];

  $date_created = $d['date_created'];
  // $tanggal_pemesanan = $d['tanggal_pemesanan'];
  // $tanggal_pengiriman = $d['tanggal_pengiriman'];

  // $tanggal_pemesanan = $d['tanggal_pemesanan'];
  // $tanggal_pengiriman = $d['tanggal_pengiriman'];
  // $durasi_bayar = $d['durasi_bayar'];
  // $perintah_po = $d['perintah_po'];

  echo "<span id=id_sj class=hideit>$id_sj</span>";
    

  # ================================================================
  # HEADER SJ -->
  # ================================================================
  include 'surat_jalan_info.php';

  # ================================================================
  # ITEMS SJ -->
  # ================================================================
  include 'item_surat_jalan.php';
  
  


}