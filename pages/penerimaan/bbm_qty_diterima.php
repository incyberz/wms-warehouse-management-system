<?php
# =======================================================================
# SELECT PROYEKSI
# =======================================================================
$masih_bisa_edit = 0;
$arr_bln = ['JAN','FEB','MAR','APR','MEI','JUN','JUL','AGS','SEP','OKT','NOV','DES',];
$arr_thn = [2023,2024];
$opt_proyeksi = '<option value=null>--Pilih--</option>';
foreach ($arr_thn as $thn) {
  foreach ($arr_bln as $bln) {
    $opt_proyeksi.= "<option>$bln-$thn</option>";
  }
}

$opt_ppic = '<option value=null>--Pilih--</option>';
$opt_ppic .= '<option>PPIC 1</option>';
$opt_ppic .= '<option>PPIC 2</option>';
$opt_ppic .= '<option>PPIC 3</option>';


# =======================================================================
# GET ITEM PO
# =======================================================================
$s = "SELECT *,
a.id as id_sj_item,
0 as qty_sebelumnya,
b.id as id_barang,
b.kode as kode_barang,
b.nama as nama_barang,
(SELECT step FROM tb_satuan WHERE satuan=b.satuan) step,
(SELECT SUM(qty) FROM tb_sj_subitem WHERE id_sj_item=a.id) qty_subitem

FROM tb_sj_item a 
JOIN tb_barang b ON a.kode_barang=b.kode 
WHERE a.kode_sj='$kode_sj' 
";
// echo "<h1>GET ITEM PO</h1><pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0){
  $tr = "<tr><td colspan=100% ><div class='alert alert-danger'>Belum ada item barang pada PO ini</div></td></tr>";
  $saya_menyatakan_disabled = 'disabled';
  $hide_saya_menyatakan = 'hideit';
}else{
  $hide_saya_menyatakan = '';
  $tr = '';
  $i=0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $id = $d['id_sj_item'];
    $step = $d['step'] ?? 0.0001;
    $qty = $d['qty'];
    $qty_subitem = $d['qty_subitem'];
    $qty_diterima = $d['qty_diterima'];
    $qty_sebelumnya = $d['qty_sebelumnya'];
    $satuan = $d['satuan'];

    $qty = floatval($qty);
    $qty_subitem = floatval($qty_subitem);
    $qty_diterima = floatval($qty_diterima);
    $qty_sebelumnya = floatval($qty_sebelumnya);

    // pernah terima maka set partial
    if($qty_diterima) $pernah_terima = 1;
    
    $qty = $step==1 ? round($qty,0) : $qty;
    $qty_sama = $qty==$qty_diterima ? 1 : 0;

    $qty_final = $qty-$qty_sebelumnya;
    $qty_sama_final = $qty_final==$qty_diterima ? 1 : 0;
    
    $hideit_check = $qty_sama_final ? '' : 'hideit';
    $hideit_sesuai = ($qty_sama_final || $qty_diterima>0) ? 'hideit' : '';
    
    if($qty_sama_final){
      $sisa_show='';
    }else{
      $selisih = $qty_diterima-$qty;
      if($selisih<0){
        $selisih_abs = abs($selisih);
        $sisa_show = "Kurang $selisih_abs $satuan";
      }else{
        $sisa_show = "<span class=' blue'>Lebih $selisih $satuan | <span class=miring>Free Supplier</span></span>";

      }
    }


    $qty_sebelumnya_show = $d['qty_sebelumnya'] ? "<div class='kecil miring abu'>-<span id=qty_sebelumnya__$id>$d[qty_sebelumnya]</span></div>" : '';

    $qty_subitem_color = $qty_diterima==$qty_subitem ? 'hijau' : 'red';
    if($qty_diterima!=$qty_subitem) $all_qty_allocated = 0;

    $total_qty_diterima += $qty_diterima;
    $total_qty_subitem += $qty_subitem;

    $qty_max = $qty * 2;

    //disabled edit qty diterima jika sudah ada subitem
    $qty_diterima_disabled = $qty_subitem ? 'disabled' : '';
    if(!$qty_subitem) $masih_bisa_edit = 1;

    if($qty_diterima){
      $select_proyeksi = "<select class='form-control form-control-sm select_save' name=proyeksi__$id id=proyeksi__$id>$opt_proyeksi</select>";
      $select_ppic = "<select class='form-control form-control-sm select_save' name=kode_ppic__$id id=kode_ppic__$id>$opt_ppic</select>";
      $link_manage_sub_item = "
        <a href='?penerimaan&p=bbm_subitem&kode_sj=$kode_sj&id_sj_item=$id'>
          <span class='kecil $qty_subitem_color'>$qty_subitem $satuan</span>
          <span class=hide_cetak>$img_next</span>
        </a>
      ";
    }else{
      $select_proyeksi = '-';
      $select_ppic = '-';
      $link_manage_sub_item = '-';
    }
    
    $tr .= "
      <tr>
        <td>$i</td>
        <td>
          <span class=darkblue>$d[kode_barang] <a class=hide_cetak href='?master&p=barang&keyword=$d[kode_barang]' onclick='return confirm(\"Ingin mengubah data barang ini?\")'>$img_edit</a></span>
          <div class='darkabu f14'>$d[nama_barang]</div>
        </td>
        <td>
          <span id=qty_po__$id>$qty</span> 
          <span id=satuan__$id>$d[satuan]</span>
          $qty_sebelumnya_show
        </td>
        <td>
          <div class=flexy>
            <div>
              <input id='qty_diterima__$id' class='form-control form-control-sm qty_diterima' type=number step='$step' required name=qty_diterima__$id min=0 max=$qty_max value='$qty_diterima' $qty_diterima_disabled>
              <div class='mt1 abu kecil' id=selisih__$id>$sisa_show</div>
            </div>
            <div>
              <span class='$hideit_sesuai btn btn-success btn-sm btn_sesuai' id=btn_sesuai__$id>Sesuai</span>
              <div class='$hideit_check' id=img_check__$id>$img_check</div>
            </div>
          </div>
        </td>
        <td class=hide_cetak>
          $link_manage_sub_item
        </td>
        <td class='hide_cetak hideit'>
          $select_proyeksi
        </td>
        <td class='hide_cetak hideit'>
          $select_ppic
        </td>
      </tr>
    ";
  }
}

$tb_items = "
  <table class='table'>
    <thead>
      <th>No</th>
      <th>Kode / Item</th>
      <th>QTY-PO</th>
      <th>QTY Diterima</th>
      <th class=hide_cetak>QTY Subitems</th>
      <th class='hide_cetak hideit'>Proyeksi</th>
      <th class='hide_cetak hideit'>PPIC</th>
    </thead>
    $tr
  </table>
";













# =======================================================================
# QTY DITERIMA PADA BBM
# =======================================================================
echo "<h2>QTY Diterima pada BBM $no_bbm</h2>";
include 'bbm_process_simpan_item.php';

$hide_cek = $masih_bisa_edit ? '' : 'style="display:none"';

echo " 
  <form method=post >
    $tb_items
    <div class='mb2 f12 hide_cetak'><b>Catatan:</b> QTY diterima tidak bisa diubah jika sudah ada subitem.</div>

    <div class='hide_cetak $hide_saya_menyatakan'>
      <div class='flexy ' $hide_cek>
        <div class='pt1'>
          <input type='checkbox' id=saya_menyatakan $saya_menyatakan_disabled>
        </div>
        <div>
          <label for='saya_menyatakan'>Saya menyatakan telah menerima dan mengukur semua QTY PO ini</label>
        </div>
      </div>
      <div class='flexy mt4'>
        <div style=flex:1>
          <button class='btn btn-primary btn-sm w-100' disabled id=btn_simpan name=btn_simpan>$btn_simpan_caption</button>
        </div>
        <div style=flex:1>
          <span class='btn btn-success btn-sm w-100 btn_aksi' id=blok_cetak__toggle disabled>Verifikasi dan Cetak BBM</span>
        </div>
      </div>
    </div>
  </form>
";

include 'bbm_cetak.php';


?>
<script>
  $(function(){
    $('.select_save').change(function(){
      alert('Fitur Select and Save akan segera diaktifkan. Terimakasih sudah mencoba!');
    })
  })
</script>