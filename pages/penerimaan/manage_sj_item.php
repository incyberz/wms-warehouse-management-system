<style>.suspended{display:none}</style>
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
(
  SELECT SUM(p.qty) FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_sj_kumulatif=q.id 
  JOIN tb_sj_item r ON q.id_sj_item=r.id 
  WHERE q.id_sj_item!=a.id 
  AND q.is_fs is null 
  AND r.kode_barang=a.kode_barang) qty_parsial,
    -- QTY Parsial adalah qty_datang pada penerimaan lain 
(
  SELECT SUM(p.qty) FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_sj_kumulatif=q.id 
  WHERE q.id_sj_item=a.id 
  AND q.is_fs is null) qty_datang,   
(
  SELECT SUM(p.qty) FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_sj_kumulatif=q.id 
  WHERE q.id_sj_item=a.id 
  AND q.is_fs is not null) qty_diterima_fs,   
(
  SELECT stok 
  FROM tb_trx 
  WHERE id_barang=b.id 
  ORDER BY tanggal DESC LIMIT 1) stok,   
(
  SELECT tanggal 
  FROM tb_trx 
  WHERE id_barang=b.id 
  ORDER BY tanggal DESC LIMIT 1) last_trx


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
    $qty_parsial=floatval($d['qty_parsial']);
    $qty_datang=floatval($d['qty_datang']);
    $qty_diterima_fs=floatval($d['qty_diterima_fs']);
    $satuan=$d['satuan'];
    $keterangan=$d['keterangan'];
    $step=$d['step'];
    $stok=$d['stok'] ?? 0;

    $qty_adjusted = $qty;

    // update value is_valid_all_qty
    if(!$qty) $is_valid_all_qty = false;

    $link = "<a href='?penerimaan&p=manage_sj_kumulatif&id_sj_item=$id_sj_item'>$img_sum</a>";
    $qty_diterima_show = $qty_datang ? "$qty_datang $link" : "<span class='kecil miring red'>(belum ada)</span> $link";
    $qty_diterima_fs_show = $qty_diterima_fs ? "$qty_diterima_fs $img_fs" : '-';
    $qty_disabled = $qty_datang ? 'disabled' : '';

    $qty_parsial_show = $qty_parsial ? "$qty_parsial <span onclick='alert(\"QTY Parsial artinya QTY Datang yang ada di Surat Jalan lain.\")'>$img_sum_disabled</span>" : '-';
    $qty_kurang = $qty_adjusted - $qty_datang  - $qty_parsial;
    $qty_kurang_show = $qty_kurang;

    $qty_selisih = -$qty_kurang;
    $qty_selisih_show = $qty_selisih<0 ? "<span class=darkred>$qty_selisih</span>" : $qty_selisih;
    
    // jangan menerima lagi jika qty_kurang = 0
    if(!$qty_kurang) $qty_diterima_show = '-';

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
        <td class='kanan suspended'>
          <input class='form-control' type=number step=$step name=qty_adjusted__$id_sj_item value='$qty' $qty_disabled>
        </td>
        <td class=kanan>$qty_parsial_show</td>
        <td class=kanan>$qty_diterima_show</td>
        <td class='kanan suspended'>$qty_kurang_show</td>
        <td class=kanan>$qty_selisih_show</td>
        <td class=kanan>$qty_diterima_fs_show</td>
      </tr>
    ";

  }

}

?>
<div class="wadah">
  <div class="sub_form">Sub Form Manage SJ Item</div>
  <div class="f20 darkblue tebal mb2">Item Surat Jalan</div>
  <form method="post">
    <div style='overflow:scroll;'>
      <table class="table table-striped" style='width:2000px'>
        <thead class=gradasi-hijau>
          <th>NO</th>
          <th>ID</th>
          <th>UOM</th>
          <th>QTY PO</th>
          <th class=suspended>QTY Adjusted</th>
          <th class=kanan>QTY Parsial</th>
          <th class=kanan>QTY Datang</th>
          <th class='kanan suspended'>QTY Kurang</th>
          <th class=kanan>Selisih</th>
          <th class=kanan>QTY FS</th>
          <th class=kanan>%</th>
          <th class=kanan>Ket</th>
          <th class=kanan>Tanggal Konfirm</th>
          <th class=kanan>Feedback Proc</th>
          <th class=kanan>3% QTY PO</th>
          <th class=kanan>Selisih Kdt</th>
          <th class=kanan>Ket</th>
        </thead>
      
        <?=$tr?>
        <tfoot class=gradasi-kuning>
          <tr>
            <td colspan=3>TOTAL</td>
            <td class=kanan>?</td>
            <td colspan=100%>?</td>
          </tr>
          <tr class=suspended>
            <td colspan=100%>
              <button class="btn btn-primary w-100" name=btn_save_qty_adjusted>Save QTY Adjusted</button>
            </td>
          </tr>
        </tfoot>
      
      </table>
    </div>
  </form>
  <table class='darkabu f12 flexy'>
    <tr>
      <td valign=top><b>Catatan:</b></td> 
      <td valign=top>
        <ul>
          <li>QTY Datang adalah Summary dari qty subitem</li>
          <li>Klik tombol SUM <?=$img_sum?> pada tiap item untuk manage subitem</li>
          <li>QTY Parsial adalah QTY Datang pada Surat Jalan yang berbeda</li>
          <li>QTY Kurang adalah sisa PO yang belum datang</li>
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
