<?php
$judul = 'Retur Info';
set_title($judul);

echo "
<h1>$judul</h1>
<h2>Informasi Proses QC dan Retur Penerimaan</h2>

";
?>
<img src="assets/img/icons/retur_po.png" alt="retur_po" width='100px' class='mt-2 mb-2'>

<p>QC dan Retur dapat dilakukan dg cara:</p>
<ul>
  <li>Dilakukan dari <a href="?rekap_kumulatif">Rekap Kumulatif</a></li>
  <li>Lakukan Pencarian by PO atau ID (opsional)</li>
  <li>Kemudian klik tombol Next <?=$img_next?> pada kolom Retur</li>
</ul>

