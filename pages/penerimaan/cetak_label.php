<?php
$id_bbm_item = $_GET['id_bbm_item'] ?? die(erid('id_bbm_item'));
$s = "SELECT * 
FROM tb_bbm_item a 
WHERE id=$id_bbm_item";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));


?>
<div class="pagetitle">
  <h1>Cetak Label</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?po">PO Home</a></li>
      <li class="breadcrumb-item"><a href="?po&p=terima_barang">Cari PO</a></li>
      <li class="breadcrumb-item"><a href="?po&p=terima_barang&no_po=<?=$no_po?>&id_bbm=<?=$id_bbm?>">BBM</a></li>
      <li class="breadcrumb-item active">Cetak Label</li>
    </ol>
  </nav>
</div>


<?php
# =======================================================================
# PROCESSORS 
# =======================================================================

?>
<h2>Data BBM</h2>
<table class="table">
  <tr>
    <td>Nomor PO</td>
    <td><?=$no_po?></td>
  </tr>
  <tr>
    <td>Nomor MMB</td>
    <td><?=$id_bbm?></td>
  </tr>
  <tr>
    <td>BBM Item</td>
    <td><?=$id_bbm_item?></td>
  </tr>
  <tr>
    <td>QTY BBM Item</td>
    <td><?=$id_bbm_item?></td>
  </tr>
</table>
