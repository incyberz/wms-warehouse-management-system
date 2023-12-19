<?php
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
$date_created = $d['date_created'];

echo "<span id=id_sj class=hideit>$id_sj</span>";
?>
<div class="mb2 wadah">
  <div class="f20 darkblue tebal mb2">Surat Jalan Info</div>

  Nomor Surat Jalan 
  <input type="text" class="form-control mt1 mb2" value='<?=$kode_sj?>' disabled>
  Nomor PO 
  <input type="text" class="form-control mt1 mb2" value='<?=$kode_po?>' disabled>
  Supplier 
  <input type="text" class="form-control mt1 mb2" value='<?=$nama_supplier?>' disabled>
  Tanggal Terima 
  <input type="text" class="form-control mt1 mb2" value='<?=date('D, M d, Y, H:i',strtotime($tanggal_terima))?>' disabled>
</div>