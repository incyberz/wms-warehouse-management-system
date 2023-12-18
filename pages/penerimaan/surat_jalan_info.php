<?php

?>
<div class="mb2 wadah">
  <div class="f20 darkblue tebal mb2">Surat Jalan Info</div>

  Nomor Surat Jalan 
  <input type="text" class="form-control mt1 mb2" value='<?=$kode_sj?>' disabled>
  Nomor PO 
  <input type="text" class="form-control mt1 mb2" value='<?=$kode_po?>' disabled>
  Supplier 
  <input type="text" class="form-control mt1 mb2" value='<?=$nama_supplier?>' disabled>
  Tanggal Terima 
  <input type="text" class="form-control mt1 mb2" value='<?=date('D, M d, Y, H:i',strtotime($tanggal_terima))?>' disabled>
</div>