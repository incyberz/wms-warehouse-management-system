<?php
if(isset($_POST['btn_save_qty_adjusted'])){
  unset($_POST['btn_save_qty_adjusted']);

  $pesan = '';
  foreach ($_POST as $key => $value) {
    $arr = explode('__',$key);
    $s = "UPDATE tb_sj_item SET qty=$value WHERE id=$arr[1]";
    $pesan.= "<br>updating sj_item, id:$arr[1]... ";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    $pesan.= 'sukses.';
  }
  echo "<div class='wadah gradasi-kuning consolas'>$pesan</div>";
  jsurl('',2000);
}

$debug .= "<br>id_kategori: <span id=id_kategori>$id_kategori</span>";
$is_valid_all_qty = true;

$tr = "
  <tr class='alert alert-danger'>
    <td colspan=100% height=100px valign=middle><span class=red>Belum ada item pada Surat Jalan</span> | <a href='#' onclick='alert(\"Fitur ini masih dalam tahap pengembangan. Terimakasih.\")'>Get Item via API</a></td>
  </tr>
";

$s = "SELECT 
a.id as id_sj_item,
a.qty_po,
a.qty,
b.satuan,
b.keterangan,
b.id as id_barang,  
b.kode as kode_barang,  
b.nama as nama_barang,
c.step,
(SELECT SUM(p.qty) FROM tb_roll p 
JOIN tb_sj_kumulatif q ON p.id_sj_kumulatif=q.id 
WHERE q.id_sj_item=a.id) qty_diterima,   
(SELECT stok FROM tb_trx WHERE id_barang=b.id ORDER BY tanggal DESC LIMIT 1) stok,   
(SELECT tanggal FROM tb_trx WHERE id_barang=b.id ORDER BY tanggal DESC LIMIT 1) last_trx

FROM tb_sj_item a 
JOIN tb_barang b ON a.kode_barang=b.kode 
JOIN tb_satuan c ON b.satuan=c.satuan  
WHERE a.kode_sj='$kode_sj'";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$count_item = mysqli_num_rows($q);
if($count_item){
  $tr = '';
  $no = 0;
  while($d=mysqli_fetch_assoc($q)){
    $no++;

    $id=$d['id_sj_item'];
    $id_sj_item=$d['id_sj_item'];
    $qty_po=floatval($d['qty_po']);
    $qty=floatval($d['qty']);
    $qty_diterima=floatval($d['qty_diterima']);
    $satuan=$d['satuan'];
    $keterangan=$d['keterangan'];
    $step=$d['step'];
    $stok=$d['stok'] ?? 0;

    // update value is_valid_all_qty
    if(!$qty) $is_valid_all_qty = false;

    $qty_diterima_show = $qty_diterima ? $qty_diterima : '<span class="kecil miring red">(belum ada)</span>';
    $qty_disabled = $qty_diterima ? 'disabled' : '';

    $tr .= "
      <tr id=source_edit_sj_item__$id>
        <td>$no</td>
        <td>
          <div class=darkblue>$d[kode_barang]</div>
          <div class='darkabu f12'>$d[nama_barang]</div> 
          <div class='darkabu f12'>$d[keterangan]</div> 
        </td>
        <td>$satuan</td>
        <td class=kanan>$qty_po</td>
        <td class=kanan>
          <input class='form-control' type=number step=$step name=qty_adjusted__$id_sj_item value='$qty' $qty_disabled>
        </td>
        <td>
          $qty_diterima_show 
          <a href='?penerimaan&p=manage_sj_kumulatif&id_sj_item=$id_sj_item'>$img_next</a>
        </td>
      </tr>
    ";

  }

}

?>
<div class="wadah">
  <div class="sub_form">Sub Form Manage SJ Item</div>
  <div class="f20 darkblue tebal mb2">Item Surat Jalan</div>
  <form method="post">

    <table class="table table-striped">
      <thead class=gradasi-hijau>
        <th>NO</th>
        <th>KODE</th>
        <th>UOM</th>
        <th>QTY PO</th>
        <th>QTY Adjusted</th>
        <th>QTY Diterima</th>
      </thead>
    
      <?=$tr?>
      <tfoot class=gradasi-kuning>
        <tr>
          <td colspan=3>TOTAL</td>
          <td class=kanan>?</td>
          <td colspan=100%>?</td>
        </tr>
        <tr>
          <td colspan=100%>
            <button class="btn btn-primary w-100" name=btn_save_qty_adjusted>Save QTY Adjusted</button>
          </td>
        </tr>
      </tfoot>
    
    </table>
  </form>
  <table class='darkabu f12 flexy'>
    <tr>
      <td valign=top><b>Catatan:</b></td> 
      <td valign=top>
        <ul>
          <li>QTY Diterima adalah Summary dari qty subitem</li>
          <li>Klik tombol next <?=$img_next?> pada tiap item untuk manage subitem</li>
        </ul>
      </td>
    </tr>
  </table>

</div>

<?php
if($count_item){
  if($is_valid_all_qty){
    // Next Process

  }else{

  }
}else{

} 
