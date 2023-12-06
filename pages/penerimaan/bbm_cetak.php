<?php




?>
<div id='blok_cetak' style='margin-top:40px'>

  <h2>Verifikasi dan Cetak BBM</h2>
  <p class=p1>Syarat untuk mencetak BBM yaitu:</p>
  <ol>
    <li>
      Silahkan Upload Surat Jalan untuk BBM No. <span class="darkblue"><?=$no_bbm?></span>

      <div>
        <div class=mb1>Status: <span class="miring abu">belum upload</span> <?=$img_warning?></div>
        <form method=post enctype='multipart/form-data'>
          <div class='flexy'>
            <div>
              <input type='file' class='form-control form-control-sm'>
            </div>
            <div>
              <button class='btn btn-success btn-sm' onclick='return confirm(\"Handler untuk upload masih dalam tahap pengembangan. Terimakasih sudah mencoba.\")'>Upload</button>
            </div>
          </div>
          
        </form>
      </div>

    </li>
    <li>
      BBM Sudah diverifikasi oleh Petugas Receipt 
      <div class="mt2">Status: <span class="abu miring">belum diverifikasi</span> <?=$img_warning?></div>
      <div class='mt2' style='display:grid;grid-template-columns:30px auto'>
        <div class=pt1><input type="checkbox" class=cek_bbm id=cek1></div>
        <div><label for="cek1">Saya <span class="tebal miring darkblue"><?=$nama_user?></span>, jabatan <span class="tebal miring darkblue"><?=$jabatan?></span>, menyatakan bahwa seluruh QTY pada BBM ini sudah benar-benar fix sesuai dengan kenyataan.</label></div>
        <div class=pt1><input type="checkbox" class=cek_bbm id=cek2></div>
        <div><label for="cek2">Saya tidak akan melakukan perubahan kembali pada data BBM ini.</label></div>
        <div class=pt1>&nbsp;</div>
        <div><button class='btn btn-primary btn-sm mt2' disabled>Verifikasi BBM</button></div>
      </div>
    </li>
  </ol>
  <hr>
  <button class="btn btn-success w-100" disabled>Cetak Bukti Barang Masuk</button>
</div>