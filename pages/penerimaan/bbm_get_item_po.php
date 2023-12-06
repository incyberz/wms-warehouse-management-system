<?php
# =======================================================================
# GET ITEM PO
# =======================================================================
$s = "SELECT *,
a.id as id_item,
b.id as id_barang,
b.kode as kode_barang,
b.nama as nama_barang,
(SELECT step FROM tb_satuan WHERE satuan=b.satuan) step,
(SELECT qty_diterima FROM tb_bbm_item WHERE id_po_item=a.id AND id_bbm=$id_bbm) qty_diterima,
(
  SELECT SUM(p.qty_diterima) 
  FROM tb_bbm_item p 
  JOIN tb_bbm q ON p.id_bbm=q.id 
  WHERE id_po_item=a.id 
  AND q.tanggal_terima < '$tanggal_terima' ) qty_sebelumnya

FROM tb_po_item a 
JOIN tb_barang b ON a.id_barang=b.id 
WHERE a.id_po=$id_po 
";
// echo "<h1>GET ITEM PO</h1><pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0){
  $tr = "<tr><td colspan=100% ><div class='alert alert-danger'>Belum ada item barang pada PO ini</div></td></tr>";
  $saya_menyatakan_disabled = 'disabled';
}else{
  $tr = '';
  $i=0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $id = $d['id_item'];
    $step = $d['step'] ?? 0.0001;
    $qty = $d['qty'];
    $qty_diterima = $d['qty_diterima'];
    $qty_sebelumnya = $d['qty_sebelumnya'];
    $satuan = $d['satuan'];

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
      $selisih = $qty-$qty_diterima;
      $sisa_show = "Kurang $selisih $satuan";
    }


    $qty_sebelumnya_show = $d['qty_sebelumnya'] ? "<div class='kecil miring abu'>-<span id=qty_sebelumnya__$id>$d[qty_sebelumnya]</span></div>" : '';

    $qty_sebelumnya_show = str_replace('.0000','',$qty_sebelumnya_show);
    $qty_diterima = str_replace('.0000','',$qty_diterima);
    $qty = str_replace('.0000','',$qty);
    
    
    $tr .= "
      <tr>
        <td>$i</td>
        <td>
          <span class=darkblue>$d[kode_barang] <a href='?master&p=barang&keyword=$d[kode_barang]' onclick='return confirm(\"Ingin mengubah data barang ini?\")'>$img_edit</a></span>
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
              <input id='qty_diterima__$id' class='form-control form-control-sm qty_diterima' type=number step='$step' required name=qty_diterima__$id min=0 max=$qty_final value=$qty_diterima>
              <div class='mt1 abu kecil' id=selisih__$id>$sisa_show</div>
            </div>
            <div>
              <span class='$hideit_sesuai btn btn-success btn-sm btn_sesuai' id=btn_sesuai__$id>Sesuai</span>
              <div class='$hideit_check' id=img_check__$id>$img_check</div>
            </div>
          </div>
          
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
    </thead>
    $tr
  </table>
";













# =======================================================================
# QTY DITERIMA PADA BBM
# =======================================================================
echo "<h2>QTY Diterima pada BBM $no_bbm</h2>";

if(isset($_POST['btn_simpan'])){
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';
  $id_bbm = $_POST['id_bbm'];

  foreach ($_POST as $key => $qty_diterima) {
    if(strpos("salt$key",'qty_diterima__')){
      $arr = explode('__',$key);
      $id_po_item = $arr[1];

      $s = "SELECT id as id_bbm_item FROM tb_bbm_item WHERE id_bbm=$id_bbm AND id_po_item=$id_po_item";
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      if(mysqli_num_rows($q)>1){
        die("Tidak boleh 2 id_bbm_item untuk 1 id_bbm<hr>id_bbm: $id_bbm | id_po_item: $id_po_item ");
      }else{
        if(mysqli_num_rows($q)){
          $d = mysqli_fetch_assoc($q);
          $s = "UPDATE tb_bbm_item SET qty_diterima=$qty_diterima WHERE id=$d[id_bbm_item]";
        }else{
          $s = "INSERT INTO tb_bbm_item (id_bbm,id_po_item,qty_diterima) VALUES ($id_bbm,$id_po_item,$qty_diterima)";
        }
        $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      }
    }
  }

  $s = "UPDATE tb_bbm SET tanggal_terima=CURRENT_TIMESTAMP WHERE id=$id_bbm";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  jsurl("?po&p=terima_barang&no_po=$no_po");
  exit;
}

echo "
  <form method=post>
    <input type='hidden' name=id_bbm value=$id_bbm>
    $tb_items

    <div class='flexy'>
      <div class='pt1'>
        <input type='checkbox' id=saya_menyatakan $saya_menyatakan_disabled>
      </div>
      <div>
        <label for='saya_menyatakan'>Saya menyatakan telah menerima dan mengukur semua kuantitas item dari PO ini.</label>
      </div>
    </div>
    <div class='flexy mt4'>
      <div style=flex:1>
        <button class='btn btn-primary btn-sm w-100' disabled id=btn_simpan name=btn_simpan>$btn_simpan_caption</button>
      </div>
      <div style=flex:1>
        <span class='btn btn-success btn-sm w-100 btn_aksi' id=blok_cetak__toggle disabled>Prasyarat Cetak BBM</span>
      </div>
    </div>
  </form>
";

include 'bbm_cetak.php';