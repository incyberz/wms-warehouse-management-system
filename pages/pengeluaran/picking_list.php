<?php
$tr = "<tr><td colspan=100%><div class='alert alert-danger'>Belum ada item</div></td></tr>";
$tr_tambah = "<tr><td colspan=100%>$img_add Tambah Item</td></tr>";
if($jumlah_item){
  $s = "SELECT a.*,
  b.no_lot,
  b.no_roll,
  b.kode_lokasi,
  d.kode_po,
  f.kode as kode_barang,
  f.nama as nama_barang, 
  f.keterangan as keterangan_barang,
  g.satuan, 
  g.step, 
  h.brand, 
  1
  FROM tb_picking a 
  JOIN tb_sj_subitem b ON a.id_sj_subitem=b.id 
  JOIN tb_sj_item c ON b.id_sj_item=c.id 
  JOIN tb_sj d ON c.kode_sj=d.kode 
  JOIN tb_bbm e ON d.kode=e.kode_sj  
  JOIN tb_barang f ON c.kode_barang=f.kode 
  JOIN tb_satuan g ON f.satuan=g.satuan 
  JOIN tb_lokasi h ON b.kode_lokasi=h.kode 
  WHERE a.kode_do='$kode_do'";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  
  $tr = '';
  $i=0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $id=$d['id'];
    $qty=floatval($d['qty']);
    $lot_info = $d['no_lot'] ? "<div>Lot: $d[no_lot]</div>" : "<div>Lot: $unset</div>";
    $roll_info = $d['no_roll'] ? "<div>Roll: $d[no_roll]</div>" : '';
    $tr .= "
      <tr>
        <td>$i</td>
        <td>
          $d[kode_barang]
          <div class='f12 abu'>
            <div>$d[nama_barang]</div>
            <div>$d[keterangan_barang]</div>
          </div>
        </td>
        <td>
          $lot_info
          $roll_info
        </td>
        <td>$d[kode_lokasi] ~ $d[brand]</td>
        <td>$d[kode_po]</td>
        <td>$qty $d[satuan]</td>
      </tr>
    ";
  }


}

?>
<h2>Picking List</h2>
<table class="table">
  <thead>
    <th>No</th>
    <th>ITEM</th>
    <th>LOT</th>
    <th>LOKASI</th>
    <th>PO</th>
    <th>QTY</th>
  </thead>
  <?=$tr?>
  <?=$tr_tambah?>
</table>

<?php include 'picking_list_add.php'; ?>