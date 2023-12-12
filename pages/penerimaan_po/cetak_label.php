<?php



if(isset($kode_barang)){
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
              <div class=f12>$no_po_dll</div>
              <div class='f16 mt2 mb1'>$jenis_bahan</div>
              <div class=f12>$nama_barang</div>
            </td>
          </tr>
        </table>
      </div>
    </div>
  ";

}