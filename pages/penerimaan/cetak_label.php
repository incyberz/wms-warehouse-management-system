<?php



if(isset($kode_barang)){
  $fs_label_info = '';
  if(isset($is_fs)){
    if($is_fs){
      $fs_label_info = 'FREE SUPPLIER';
    }
  }
  echo "
    <div >
      <div class='bordered p2 tengah' style='width:10cm; height:6cm'>
        <div class=mt4>
          <table>
            <tr>
              <td width=180px align=center>
    ";
  
              require_once 'include/qrcode.php';
              $qr = QRCode::getMinimumQRCode($kode_barang, QR_ERROR_CORRECT_LEVEL_L);
              $qr->printHTML('6px');
  
    echo "
              <div class='f20 mt2' >$kode_barang</div>
            </td>
            <td>
              $fs_label_info
              <div class=f12>$no_po_dll</div>
              <div class='f16 mt2 mb1'>$nama_barang</div>
              <div class='f12'>$keterangan_barang</div>
            </td>
          </tr>
        </table>
      </div>
    </div>
  ";

}