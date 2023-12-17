<?php
$durasi_bayar_show = $durasi_bayar=='' ? $unset : "$durasi_bayar hr kontrabon";
$tanggal_pemesanan = $tanggal_pemesanan ?? $date_created;
$tanggal_pengiriman = $tanggal_pengiriman ?? $date_created;

$tanggal_pemesanan_show = date('d - m - Y',strtotime($tanggal_pemesanan));
$tanggal_pengiriman_show = date('d - m - Y',strtotime($tanggal_pengiriman));

# ==========================================
# SELECT SUPPLIER
# ==========================================
$opt = '';
$ket_supplier = '';
$s = "SELECT * FROM tb_supplier";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
while($d=mysqli_fetch_assoc($q)){
  $alamat = $d['alamat']=='' ? "Alamat: $unset" : "Alamat: $d[alamat]";
  $no_telfon = $d['no_telfon']=='' ? "Telfon: $unset" : "Telp. $d[no_telfon]";
  $no_hp = $d['no_hp']=='' ? '' : ", HP. $d[no_hp]";
  $no_wa = $d['no_wa']=='' ? '' : ", WA. $d[no_wa]";
  $no_fax = $d['no_fax']=='' ? '' : ", Fax. $d[no_fax]";

  $hideit = $id_supplier==$d['id'] ? '' : 'hideit';
  $ket_supplier .= "
  <div class='ket_supplier $hideit' id=ket_supplier__$d[id]>
    $alamat $no_telfon $no_hp $no_wa $no_fax 
  </div>
  ";

  $selected = $id_supplier==$d['id'] ? 'selected' : '';
  $opt.= "<option value='$d[id]' $selected>$d[nama]</option>";
}

$select_supplier = "<select class='form-control' name=id_supplier>$opt</select>";


?>
<div class="mb1">
  <div class="row">
    <div class="col-4">
      <div class="bordered p1 h-100">
        <div class="abu miring">Supplier:</div>
        <div class="tebal"><?=$nama_buyer?></div>
        <div class="kecil">
          <?=$alamat_buyer?> 
          Telp. <?=$telp_buyer?>
          WA. <?=$wa_buyer?>
        </div>
      </div>
    </div>
    <div class="col-4 tengah">
      <div class="bordered p1 h-100" style="margin: 0 -25px">
        <span class="f30">SURAT JALAN</span>
        <hr style='margin: 5px 0'>
        <span class="abu f14">No PO:</span>
        <span id="kode_po" class=f24><?=$kode_po?></span>
      </div>
    </div>
    <div class="col-4">
      <div class="bordered p1 h-100">
        <div class='flex-between'>
          <div>Kepada Yth.</div>
          <div><span class='btn_aksi' id="edit_judul_po__toggle"><?=$img_edit?></span></div>
        </div>
        <div class="tebal "><?=$nama_supplier?></div> 
        <div><?=$alamat_supplier?></div>
        <div><?=$telp_supplier?></div>
      </div>
    </div>
  </div>
</div>

<div class="tengah">
  <div class="row">
    <div class="col-7 " style="padding-right: 0">
      <div class="bordered p1 h-100 ">
        <div class="row">
          <div class="col-4">
            <div class='abu miring'>No. Supplier</div>
            <div ><?=$kode_supplier?></div>
          </div>
          <div class="col-4">
            <div class=" h-100 " style="border-left: solid 1px #ccc; border-right: solid 1px #ccc">
              <div class='abu miring'>Kontak Personal</div>
              <div ><?=$contact_person?></div>
            </div>
          </div>
          <div class="col-4">
            <div class='abu miring'>Tanggal Pemesanan</div>
            <div ><?=$tanggal_pemesanan_show?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-5 " style="padding-left:0">
      <div class="bordered p1 h-100">
        <div class="row">
          <div class="col-6">
            <div class='abu miring'>Tanggal Pengiriman</div>
            <div ><?=$tanggal_pengiriman_show?></div>
          </div>
          <div class="col-6">
            <div class="h-100" style="border-left: solid 1px #ccc">
              <div class='abu miring'>Jangka Waktu Pembayaran</div>
              <div ><?=$durasi_bayar_show?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="">
  <div class="row">
    <div class="col-7 " style="padding-right: 0">
      <div class="bordered p1 h-100 ">
        <div class="flexy" style='gap:0 15px'>
          <div class="abu miring">Tempat Pengiriman:</div>
          <div><?=$alamat_buyer?></div>
        </div>
      </div>
    </div>
    <div class="col-5 " style="padding-left:0">
      <div class="bordered p1 h-100">
        <div class="flexy" style='gap:0 15px'>
          <div class="abu miring">Tempat Penagihan:</div>
          <div><?=$alamat_buyer?></div>
        </div>
      </div>
    </div>
  </div>
</div>























<!-- ================================================================ -->
<!-- EDIT JUDUL PO -->
<!-- ================================================================ -->
<div class="gradasi-kuning border-merah br5 mt2 mb2 p2 <?=$hideit?>" id=edit_judul_po>
  <div class="row">
    <div class="col-1">&nbsp;</div>
    <div class="col-10">
      <h2 class='tengah darkblue f20'>Edit Judul PO</h2>
    </div>
    <div class="col-1 kanan">
      <span class="btn_aksi" id="edit_judul_po__close"><?=$img_close?></span>
    </div>
  </div>
  <div class="wadah kecil flex-between bg-white">
    <div>
      <b>Identitas Perusahaan</b>
      <div class=""><span class="abu miring">Perusahaan :</span> <?=$nama_usaha?></div>
      <div class=""><span class="abu miring">Alamat Usaha :</span> <?=$alamat_usaha?></div>
      <div class=""><span class="abu miring">Telepon Kantor :</span> <?=$no_telp_kantor?></div>
      <div class=""><span class="abu miring">Whatsapp Kantor :</span> <?=$no_wa_kantor?></div>

    </div>
    <div>
      <a href="?identitas_perusahaan" onclick='return confirm("Menuju laman Edit Perusahaan?")'><?=$img_edit?></a>
    </div>
  </div>

  Nomor PO:
  <input class="form-control mb2" value='<?=$kode_po?>'>

  <div class="wadah ">
    Buyer:
    <input class="form-control mb2" value='<?=$nama_buyer?>'>

    Alamat Buyer:
    <input class="form-control mb2" value='<?=$alamat_buyer?>'>

    Telfon/HP Buyer:
    <input class="form-control mb2" value='<?=$telp_buyer?>'>
  </div>

  <div class="wadah ">
    Pilih Supplier:
    <?=$select_supplier?>

    <div class="p1 flex-between kecil">
      <div><?=$ket_supplier?></div>
      <div>
        <a href="?master&p=supplier" onclick='return confirm("Edit Data Supplier ini?")'><?=$img_edit?></a>
      </div>
    </div>

  </div>


  Tanggal Pemesanan: <?=$tanggal_pemesanan?>
  <input type='date' class="form-control mb2" value='<?=date('Y-m-d',strtotime($tanggal_pemesanan))?>'>

  Tanggal Pengiriman:
  <input type='date' class="form-control mb2" value='<?=date('Y-m-d',strtotime($tanggal_pengiriman))?>'>

  Jangka Waktu Pembayaran:
  <input class="form-control mb2" value='<?=$durasi_bayar?>'>

  Tempat Pengiriman:
  <input class="form-control mb2" value='<?=$alamat_buyer?>'>

  Tempat Penagihan:
  <input class="form-control mb2" value='<?=$alamat_buyer?>'>

  <div class="flex-between mt-4">
    <button class="btn btn-primary btn_aksi" id=edit_judul_po__simpan>Simpan</button> 
    <button class="btn btn-danger btn_aksi" id=edit_judul_po__cancel>Cancel</button> 
  </div>
</div>