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
b.alamat as alamat_supplier,
(SELECT tanggal_verifikasi FROM tb_bbm WHERE kode_sj=a.kode) tanggal_verifikasi_bbm

FROM tb_sj a   
JOIN tb_supplier b ON a.kode_supplier=b.kode 
WHERE a.kode='$kode_sj' ";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0){
  die(div_alert('danger',"Data SJ tidak ditemukan. <hr>Silahkan cek pada <a href='?penerimaan&p=data_sj'>List Data SJ</a>"));
}

$d = mysqli_fetch_assoc($q);

$id_sj = $d['id_sj'];
$kode_po = $d['kode_po'];
$tanggal_terima = $d['tanggal_terima'];
$tanggal_verifikasi_bbm = $d['tanggal_verifikasi_bbm'];

$tanggal_terima_tgl = date('Y-m-d',strtotime($tanggal_terima));

$debug .= "<br>id_sj: $id_sj";
$debug .= "<br>kode_po: $kode_po";
$debug .= "<br>tanggal_terima: $tanggal_terima";

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
$kode_sj_supplier = $d['kode_sj_supplier'];
$awal_terima = $d['awal_terima'];
$akhir_terima = $d['akhir_terima'];
$id_kategori = $d['id_kategori'];

$kategori = $arr_kategori[$id_kategori];
$cat = $arr_cat[$id_kategori];
# ==========================================
# PERTAMA ATAU PARSIAL
# ==========================================
$arr = explode('-',$kode_sj);
$parsial_number = intval($arr[1]);
if($parsial_number==1){
  $parsial_show = "<label class='upper badge bg-blue'> $cat 1st</label>";
  $sub_judul = "Penerimaan $kategori Pengiriman Pertama";
}else{
  $parsial_show = "<label class='upper badge bg-red'> $cat Parsial ke-$parsial_number</label>";
  $sub_judul = "Penerimaan $kategori Pengiriman Parsial ke-$parsial_number";
}

echo "<span id=id_sj class=hideit>$id_sj</span>";
?>
<div class="mb2 wadah gradasi-abu">
  <div class="sub_form">Sub Form Surat Jalan Info</div>
  <div class="f20 darkblue tebal mb2"><?=$sub_judul?></div>

  <div class="kanan">
    <?=$parsial_show?>
  </div>

  <div class="mb2">Kode Surat Jalan System : <span class="darkblue tebal"><?=$kode_sj?></span></div>

  <div class="mb1 mt2">
    Kode Surat Jalan dari Supplier 
    <span id="kode_sj_supplier__check__<?=$id_sj?>" class=hideit><?=$img_check?></span>
  </div>
  <input type="text" class="form-control mt1 mb2 editable" id=kode_sj_supplier__sj__<?=$id_sj?> value='<?=$kode_sj_supplier?>' placeholder='lihat pada Surat Jalan...'>
  
  <div class="flexy">

    <div class=pt2>
      Tanggal Terima
    </div>
    <div>
      <input type="date" class="mt1 mb2 editable form-control form-control-sm" id=tanggal_terima__sj__<?=$id_sj?> value='<?=$tanggal_terima_tgl?>'>
    </div>
    <div class=pt2>
      <span id="tanggal_terima__check__<?=$id_sj?>" class=hideit><?=$img_check?></span>
    </div>

    <div class=pt2>Pukul:</div>

    <div>
      <input type="time" class="mt1 mb2 editable form-control form-control-sm" id=awal_terima__sj__<?=$id_sj?> value='<?=$awal_terima?>'>
    </div>
    <div class=pt2>
      <span id="awal_terima__check__<?=$id_sj?>" class=hideit><?=$img_check?></span>
    </div>

    <div class=pt2>s.d</div>
    <div>
      <input type="time" class="mt1 mb2 editable form-control form-control-sm" id=akhir_terima__sj__<?=$id_sj?> value='<?=$akhir_terima?>'>
    </div>
    <div class=pt2>
      <span id="akhir_terima__check__<?=$id_sj?>" class=hideit><?=$img_check?></span>
    </div>
  </div>
  <div class="abu f12 miring mb4">)* untuk menghitung durasi penerimaan</div>
  


  <div><span class='btn_aksi miring mt3 abu' id=info_lainnya__toggle>Info lainnya <?=$img_detail?></span></div>
  <div id=info_lainnya class=hideit>
    <div class=mt2>Nomor Terima Barang </div>
    <input type="text" class="form-control mt1 mb2" value='<?=$kode_sj?>' disabled>
    Nomor PO 
    <input type="text" class="form-control mt1 mb2" value='<?=$kode_po?>' disabled>
    Supplier 
    <input type="text" class="form-control mt1 mb2" value='<?=$nama_supplier?>' disabled>
    Tanggal Terima 
    <input type="text" class="form-control mt1 mb2" value='<?=date('D, M d, Y, H:i',strtotime($tanggal_terima))?>' disabled>
  </div>
</div>