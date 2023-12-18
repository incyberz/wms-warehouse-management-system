<?php
echo "<span class=hideit id=view_mode>$view_mode</span>";
$filePath = "uploads/bbm/$id_bbm.jpg";
if(file_exists($filePath)){
  $status_upload = "<div class=>Status: <span class='hijau'>sudah upload</span> $img_check</div>";
  $status_upload .= "<div class=mb1><span class='btn_aksi tebal abu kecil pointer' id=form_upload__toggle>Reupload</span> | <a href='$filePath' target=_blank>Lihat Gambar</a></div>";
  $hide_form = 'hideit';
  $sudah_upload = 1;
}else{
  $sudah_upload = 0;
  $hide_form = '';
  $status_upload = "<div class=mb1>Status: <span class='miring abu'>belum upload</span> $img_warning</div>";
}

if($tanggal_verifikasi==''||$diverifikasi_oleh==''){
  $status_verif = "
    <div class='mt2'>
      Status: <span class='abu miring'>belum diverifikasi</span> $img_warning
    </div>

    <div class='mt2' style='display:grid;grid-template-columns:30px auto'>
      <div class=pt1><input type='checkbox' class=cek_bbm id=cek1></div>
      <div><label for='cek1'>Saya <span class='tebal miring darkblue'>$nama_user</span>, jabatan <span class='tebal miring darkblue'>$jabatan</span>, menyatakan bahwa seluruh QTY pada BBM ini sudah benar-benar fix sesuai dengan kenyataan.</label></div>
      <div class=pt1><input type='checkbox' class=cek_bbm id=cek2></div>
      <div><label for='cek2'>Tidak akan ada lagi perubahan pada data BBM ini.</label></div>
      <div class=pt1>&nbsp;</div>
      <div>
        <form method=post>
          <input type='hidden' name=id_bbm value=$id_bbm>
          <button class='btn btn-primary btn-sm mt2' disabled id=btn_verifikasi name=btn_verifikasi>Verifikasi BBM</button>
        </form>
      </div>
    </div>  
  ";
  $terverifikasi = 0;
}else{
  $terverifikasi = 1;
  $status_verif = "<div class='mt2'>Status: <span class='hijau '>Diverifikasi oleh <b class='tebal miring'>$verifikator</b> pada $tanggal_verifikasi</span> $img_check</div>";
}

if($terverifikasi && $sudah_upload){
  $btn_cetak_bbm = "<a href='?penerimaan&p=terima_barang&kode_po=$kode_po&id_bbm=$id_bbm&view_mode=cetak' class='btn btn-success w-100' target=_blank'>Cetak Bukti Barang Masuk</a>";
}else{
  $btn_cetak_bbm = "<button class='btn btn-success w-100' disabled'>Belum bisa Cetak BBM</button>";
}


?>
<div id='blok_cetak' style='margin-top:40px' class='hide_cetak hideit'>

  <h2>Verifikasi dan Cetak BBM</h2>
  <p class=p1>Syarat untuk mencetak BBM yaitu:</p>
  <ol>
    <li>
      Silahkan Upload Surat Jalan No. <span class="darkblue"><?=$kode_sj?></span>

      <div>
        <?=$status_upload?>
        <form method=post enctype='multipart/form-data' id=form_upload class=<?=$hide_form?>>
          <input type="hidden" name=id_bbm value=<?=$id_bbm?>>
          <div class='flexy'>
            <div>
              <input type='file' class='form-control form-control-sm' accept='image/jpeg' required name=surat_jalan>
            </div>
            <div>
              <button class='btn btn-success btn-sm' name=btn_upload>Upload</button>
            </div>
          </div>
          
        </form>
      </div>

    </li>
    <li>
      BBM Sudah diverifikasi oleh <i>Assist. Head Warehouse</i> 
      <?=$status_verif?>

    </li>
  </ol>
  <hr>
  <?=$btn_cetak_bbm?>
</div>

<div class="show_cetak hideit">
  <hr>
  Printed From : <b>Warehouse Management System</b>
  <div class="kecil miring mb2">at : <?=date('D, F d, Y, H:i:s')?></div>

  <?php
  include 'include/qrcode.php';
  $url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

  $qr = QRCode::getMinimumQRCode($url, QR_ERROR_CORRECT_LEVEL_L);
  $qr->printHTML('4px');
  ?>

</div>



<script>
  $(function(){

    let view_mode =$('#view_mode').text();
    if(view_mode=='cetak'){
      $('.hide_cetak').hide();
      $('.show_cetak').show();
      window.print();
    }

    $('.cek_bbm').click(function(){
      if($('#cek1').prop('checked')&&$('#cek2').prop('checked')){
        $('#btn_verifikasi').prop('disabled',false);
      }else{
        $('#btn_verifikasi').prop('disabled',true);
      }
    })
  })
</script>