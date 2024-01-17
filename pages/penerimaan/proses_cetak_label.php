<?php
$fs_show = $is_fs ? ' <b class="f14 ml1 mr1 biru p1 pr2 br5" style="display:inline-block;background:green;color:white">FS</b>' : '';
echo "<h2><span class=btn_aksi id=blok_manage_subitem__toggle>$img_prev</span>  | Proses Cetak Label / Manage Roll</h2>";
$tgl = date('d-m-Y',strtotime($tanggal_masuk));


$bar = "<img width=300px alt='barcode' src='include/barcode.php?codetype=code39&size=50&text=".$kode_barang."&print=false'/>";
$no_roll = 'Roll-xxx';
$no_po_dll = "$kode_po $no_lot ($qty)$satuan $no_roll ($kode_lokasi $this_brand) $tgl";
$data_roll_info = "
  <span> 
    <span class='f12 abu'>Kumulatif by PO: </span>$kode_po, 
    <span class='f12 abu'>ID: </span>$kode_barang, 
    <span class='f12 abu'>Lot: </span>$no_lot,
    <span class='f12 abu'>Rak: </span>$kode_lokasi <span class='f12 abu'>$this_brand</span>
  </span>
";

echo "
  <div id=blok_cetak class=hideita>
    <div>Label Preview:</div>
";
include 'cetak_label.php';

# =============================================================
# PROCESSORS: HAPUS ROLL
# =============================================================
if(isset($_POST['btn_hapus_roll'])){
  $id_roll = $_POST['btn_hapus_roll'];

  $s = "DELETE FROM tb_roll WHERE id=$id_roll";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  jsurl();
  exit;

}

# =============================================================
# PROCESSORS: TAMBAH ROLL
# =============================================================
if(isset($_POST['btn_tambah_roll'])){
  $id_sj_subitem = $_POST['btn_tambah_roll'];
  $roll_multiplier = $_POST['roll_multiplier'];

  $s = "SELECT COUNT(1) as count_roll FROM tb_roll WHERE id_sj_subitem=$id_sj_subitem";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $counter = 1;
  if(mysqli_num_rows($q)){
    $d = mysqli_fetch_assoc($q);
    $counter = $d['count_roll'];
    $counter++;
    if($counter<10) {$counter_str = "00$counter";}else
    if($counter<100){$counter_str = "0$counter";}else{$counter_str = $counter;}
  }

  $s = "INSERT INTO tb_roll (id_sj_subitem, no_roll) VALUES ($id_sj_subitem,'$counter_str')";
  if($roll_multiplier>1){
    for ($i=1; $i < $roll_multiplier; $i++) { 
      $counter++;
      if($counter<10) {$counter_str = "00$counter";}else
      if($counter<100){$counter_str = "0$counter";}else{$counter_str = $counter;}
      $s .= ",($id_sj_subitem,'$counter_str')";
    }
  }
  // die($s);
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  jsurl();
  exit;

}

# =============================================================
# PROCESSORS: DELETE ALL ROLL
# =============================================================
if(isset($_POST['btn_delete_all_roll'])){
  $id_sj_subitem = $_GET['id_sj_subitem'] ?? die(erid('id_sj_subitem'));
  $s = "DELETE FROM tb_roll WHERE id_sj_subitem=$id_sj_subitem";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  jsurl();
}

# =============================================================
# PROCESSORS: TAMBAH ROLL
# =============================================================
if(isset($_POST['btn_save_roll'])){
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';

  $id_sj_subitem = $_GET['id_sj_subitem'] ?? die(erid('id_sj_subitem'));

  $total_qty = 0;

  foreach ($_POST as $key => $value) {
    if(strpos("salt$key",'qty_roll__')){
      $arr = explode('__',$key);
      $id = $arr[1];

      $keterangan = $_POST["keterangan_roll__$id"];
      $keterangan = $keterangan ? "'$keterangan'" : 'NULL'; 

      $s = "UPDATE tb_roll SET qty=$value, keterangan=$keterangan WHERE id=$id";
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

      $total_qty += $value;

    }
  }

  $s = "UPDATE tb_sj_subitem SET qty=$total_qty WHERE id=$id_sj_subitem";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  jsurl();

}


# =============================================================
# FORM TAMBAH ROLL
# =============================================================
$select_roll_multiplier = '';
for ($i=1; $i <= 20 ; $i++) $select_roll_multiplier .= "<option value=$i>$i roll</option>";
$select_roll_multiplier = "<select class='form-control form-control-sm' name=roll_multiplier>$select_roll_multiplier</select>";

$form_tambah_roll = "
  <form method=post class=kanan>
    <div class=flexy style='gap:3px'>
      <div>
        <button class='btn btn-success btn-sm' name=btn_tambah_roll value=$id_sj_subitem>Tambah</button>
      </div>
      <div>
        $select_roll_multiplier
      </div>
    </div>
    
  </form>
";


# =============================================================
# LIST DATA ROLL
# =============================================================
$sisa_qty_roll = $is_fs ? $sisa_fs : $sisa_qty;

$s = "SELECT * FROM tb_roll WHERE id_sj_subitem=$id_sj_subitem ORDER BY no_roll";
// echo "<h1>$s</h1>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0){
  echo div_alert('danger mt2', "Belum ada data Roll untuk QTY Roll : $sisa_qty_roll $satuan $fs_show <hr>  $form_tambah_roll");
  $form_cetak_all = '';
}else{

  $tr = '';
  $i=0;
  $jumlah_roll = mysqli_num_rows($q);
  $sum_qty_roll = 0;

  if($qty_diterima<$qty_po){
    $max_input_qty = $qty ? ($qty_diterima - $qty_subitem + $qty) : $sisa_qty;
  }else{
    $max_input_qty = $qty ? ($qty_po - $qty_subitem + $qty) : $sisa_qty;
  }
  // echo "<hr>max_input_qty:$max_input_qty = qty:$qty ? (qty_po:$qty_po - qty_subitem:$qty_subitem + qty:$qty) : sisa_qty:$sisa_qty;";
  if($is_fs){
    $max_input_qty = $qty ? ($qty_diterima - $qty_po - $qty_subitem_fs + $qty) : $sisa_fs;
    // echo "max_input_qty:$max_input_qty = qty:$qty ? (qty_diterima:$qty_diterima - qty_po:$qty_po - qty_subitem_fs:$qty_subitem_fs + qty:$qty) : sisa_fs:$sisa_fs;";
  }
  
  $btn_cetak_all = "<button class='btn btn-success btn-sm' name=btn_cetak_semua_label value=$id_sj_subitem>Cetak Semua Roll Label</button>";
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $id_roll=$d['id'];
    $qty = floatval($d['qty']);
    $sum_qty_roll += $qty;
    if(!$qty) $btn_cetak_all = '<span class="abu miring kecil">Belum bisa cetak semua label karena masih ada QTY Roll yang kosong.</span>';

    $hapus = $i==$jumlah_roll ? "<button name=btn_hapus_roll value=$id_roll style='border:none;background:none' class=darkred onclick='return confirm(\"Yakin untuk hapus?\")'>$img_delete Hapus</button>" : "<span class='abu pointer f12' onclick='alert(\"Silahkan hapus dari row terbawah.\")'>$img_delete_disabled Hapus</span>";
    $cetak = $qty ? "<a href='#'><span class=green>Cetak</span></a>" 
    : "<span class='pointer abu f12' onclick='alert(\"Silahkan isi dahulu QTY!\")'>Cetak</span>";

    $tr .= "
      <tr>
        <td>$d[no_roll] <span class='f12 debug'>id: $id_roll</span></td>
        <td>
          <input class='form-control form-control-sm qty_roll' type=number min=$step max=$max_input_qty step=$step required name=qty_roll__$id_roll id=qty_roll__$id_roll value=$qty>
        </td>
        <td>
          <input class='form-control form-control-sm keterangan_roll' maxlength=10 name=keterangan_roll__$id_roll id=keterangan_roll__$id_roll value='$d[keterangan]'>
        </td>
        <td>$hapus | $cetak</td>
      </tr>
    ";
  }

  $thead = "
    <thead>
      <th>No Roll</th>
      <th>
        QTY Roll
        <span class=f12>
          <label>
            <input type=checkbox id=qty_roll_identik> identik
          </label>
        </span>
      </th>
      <th>
        Keterangan
        <span class=f12>
          <label>
            <input type=checkbox id=keterangan_roll_identik> identik
          </label>
        </span>
      </th>
      <th>Aksi</th>
    </thead>
  ";


  $tr_sum = "
    <tr>
      <td align=right>Jumlah QTY Roll</td>
      <td id=jumlah_qty_roll>$sum_qty_roll</td>
      <td colspan=100%>&nbsp;</td>
    </tr>
    <tr>
      <td align=right>Sisa QTY untuk Roll</td>
      <td><span id=sisa_qty_roll>$sisa_qty_roll</span> $satuan $fs_show</td>
      <td colspan=100%>&nbsp;</td>
    </tr>
  ";

  echo "
    <h2 class='mt4 mb2'>Data Roll </h2>
    <div class='flexy flex-between'>
      <div>
        $data_roll_info $fs_show
      </div
      <div>
        $form_tambah_roll
      </div
    </div>
    
    <form method=post id=form_untuk_hapus>
      <table class='table'>
        $thead
        $tr
        $tr_sum
      </table>
      <button id=btn_save_roll name=btn_save_roll value=$id_roll  class='btn btn-primary'>$img_save Save Data Roll</button>
    </form>
    <form method=post>
      <button id=btn_delete_all_roll name=btn_delete_all_roll value=$id_sj_subitem  class='btn btn-danger' onclick='return confirm(\"Yakin untuk hapus semua roll?\")'>$img_delete_disabled Delete All Roll</button>
    </form>
  ";

  
  $form_cetak_all = "
    <form action=cetak.php method=post target=_blank>
      $btn_cetak_all
    </form>
  ";
}




echo "
    $form_cetak_all
  </div>
";

?>
<script>
  $(function(){

    function hitung_jumlah_qty_roll(){
      let z = document.getElementsByClassName('qty_roll');
      let jumlah = 0;
      for (let i = 0; i < z.length; i++) {
        jumlah += parseFloat($('#'+z[i].id).val());
      }
      $('#jumlah_qty_roll').text(jumlah);

      let sisa_qty_roll = $('#sisa_qty_roll').text();
      if(jumlah>sisa_qty_roll){
        $('#btn_save_roll').prop('disabled',1);
      }else{
        $('#btn_save_roll').prop('disabled',0);
      }
    }

    $('.qty_roll').keyup(function(){
      let qty_roll_identik = $('#qty_roll_identik').prop('checked');
      if(qty_roll_identik==true){
        let val = $(this).val();
        let z = document.getElementsByClassName('qty_roll');
        for (let i = 0; i < z.length; i++) {
          z[i].value = val;
        }
      }
      hitung_jumlah_qty_roll();
    })

    $('.keterangan_roll').keyup(function(){
      let keterangan_roll_identik = $('#keterangan_roll_identik').prop('checked');
      if(keterangan_roll_identik==true){
        let val = $(this).val();
        let z = document.getElementsByClassName('keterangan_roll');
        for (let i = 0; i < z.length; i++) {
          z[i].value = val;
        }
      }
    })

    $('#qty_roll_identik').click(function(){
      let qty_roll_identik = $('#qty_roll_identik').prop('checked');
      let sisa_qty_roll = $('#sisa_qty_roll').text();
      let z = document.getElementsByClassName('qty_roll');
      if(qty_roll_identik==true){
        for (let i = 0; i < z.length; i++) {
          $('#'+z[i].id).prop('max',sisa_qty_roll/z.length);
          // console.log(z[i].id,sisa_qty_roll/z.length);
        }
      }else{
        for (let i = 0; i < z.length; i++) {
          $('#'+z[i].id).prop('max',sisa_qty_roll);
        }
      }
    })
  })
</script>