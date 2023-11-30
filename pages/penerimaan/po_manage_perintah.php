<?php
if($perintah_po=='') $perintah_po = "Harap dikirim ke $nama_buyer barang-barang sesuai jumlah, spesifikasi dibawah ini, dengan ketentuan sebagaimana tertera dalam Purchase Order ini:";


?>
<div class='bordered mt1 p1'>
  <?=$perintah_po?> 
  <span class="btn_aksi" id="edit_perintah_po__toggle"><?=$img_edit?></span>
</div>
<div class="gradasi-kuning border-merah br5 mt2 mb2 p2 <?=$hideit?>" id=edit_perintah_po>
  <div class="row">
    <div class="col-1">&nbsp;</div>
    <div class="col-10">
      <h2 class='tengah darkblue f20'>Edit Perintah PO</h2>
    </div>
    <div class="col-1 kanan">
      <span class="btn_aksi" id="edit_perintah_po__close"><?=$img_close?></span>
    </div>
  </div>

  Perintah PO:
  <textarea rows="10" class="form-control mb2"><?=$perintah_po?></textarea>

  <div class="flex-between mt-2">
    <button class="btn btn-primary btn_aksi " id=edit_perintah_po__simpan>Simpan</button> 
    <span class="btn btn-danger btn_aksi " id=edit_perintah_po__cancel>Cancel</span> 
  </div>
</div>
