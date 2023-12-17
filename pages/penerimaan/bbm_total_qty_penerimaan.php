<?php
# =======================================================================
# TOTAL QTY PENERIMAAN
# =======================================================================
# =======================================================================
# JIKA ADA SISA QTY DAN TIDAK ADA BBM KOSONG --> SHOW BTN TAMBAH
# =======================================================================
$tambah_penerimaan = '';
if($sisa_qty<0){
  die(div_alert('danger', "QTY diterima tidak boleh > QTY PO"));
}elseif($sisa_qty){
  if($ada_bbm_kosong){
    $tambah_penerimaan = "Sisa QTY : $sisa_qty  <div class='kecil miring abu mt2'>Silahkan isi dahulu BBM dengan QTY masih kosong.</div>";
  }else{
    $tambah_penerimaan = "Sisa QTY : $sisa_qty | <a href='?penerimaan&p=terima_barang&kode_po=$kode_po&penerimaan=selanjutnya' onclick='return confirm(\"Tambah Penerimaan untuk PO ini?\")' >$img_add Tambah Penerimaan </a>";
  }
  $tambah_penerimaan = "<div class='bordered br5 p2'>$tambah_penerimaan</div>";
}

echo "
  <h2>Total QTY Penerimaan</h2>
  <div class='flexy mb4 mt1'>
    <div class='kecil p1'>No BBM</div>
    $link_no_bbm
    <div>$tambah_penerimaan</div>
  </div>
";