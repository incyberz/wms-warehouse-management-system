<?php

if(isset($_POST['btn_cetak_label'])){
  include 'insho_styles.php';
  echo "<link href='assets/vendor/bootstrap/css/bootstrap.min.css' rel='stylesheet'>";
  echo "<link href='assets/css/style.css' rel='stylesheet'>";

  $kode_barang = $_POST['kode_barang'];
  $no_po_dll = $_POST['no_po_dll'];
  $jenis_bahan = $_POST['jenis_bahan'];
  $nama_barang = $_POST['nama_barang'];
}

if(isset($kode_barang)){
  echo "
    <div >
      <div class='bordered p2 tengah' style='width:10cm; height:6cm'>
        <div class=mt4>
          <table>
            <tr>
              <td width=180px align=center>
    ";
  
              include 'include/qrcode.php';
              $qr = QRCode::getMinimumQRCode($kode_barang, QR_ERROR_CORRECT_LEVEL_L);
              $qr->printHTML('6px');
  
    echo "
              <div class='f20 mt2' >$kode_barang</div>
            </td>
            <td>
              <div class=f12>$no_po_dll</div>
              <div class='f16 mt2 mb1'>$jenis_bahan</div>
              <div class=f12>$nama_barang</div>
            </td>
          </tr>
        </table>
      </div>
    </div>
  ";

}else{
  echo '<h1>Page ini tidak dapat diakses secara langsung.</h1>';
  ?><script>
    setTimeout(function(){
      location.replace('index.php');
    },3000);
  </script><?php
}