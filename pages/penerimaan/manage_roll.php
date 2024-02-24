<?php
$judul = 'Manage Roll dan Cetak Label';
set_title($judul);
$id_sj_kumulatif = $_GET['id_sj_kumulatif'] ?? die(erid('id_sj_kumulatif'));
$last_kode_lokasi = $_GET['last_kode_lokasi'] ?? '';
$last_no_lot = $_GET['last_no_lot'] ?? '';

$qty = 0;
$no_lot = '';
$keterangan_barang = '';
$kode_lokasi = '';

$s = "SELECT 
a.is_fs,
a.no_lot,
a.kode_lokasi,
b.qty as qty_adjusted,
b.id as id_sj_item,
c.kode as kode_barang,
c.keterangan as keterangan_barang,
c.id_kategori,
c.satuan,
d.tanggal_terima,
d.kode_po,
e.step,
(
  SELECT brand 
  FROM tb_lokasi 
  WHERE kode=a.kode_lokasi) brand,
(
  SELECT sum(qty) 
  FROM tb_roll 
  WHERE id_sj_kumulatif=a.id ) qty, 
(
  SELECT sum(qty) 
  FROM tb_picking 
  WHERE id_sj_kumulatif=a.id ) sum_pick, 
(
  SELECT sum(qty) 
  FROM tb_roll 
  WHERE id_sj_kumulatif=a.id ) sum_qty_roll, 
(
  SELECT count(1) 
  FROM tb_roll 
  WHERE id_sj_kumulatif=a.id ) count_roll,
(
  SELECT SUM(p.qty) 
  FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_sj_kumulatif=q.id  
  WHERE q.id_sj_item=b.id and q.is_fs is null) qty_kumulatif,
(
  SELECT SUM(p.qty) 
  FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_sj_kumulatif=q.id  
  WHERE q.id_sj_item=b.id and q.is_fs is not null) qty_kumulatif_fs

FROM tb_sj_kumulatif a 
JOIN tb_sj_item b ON a.id_sj_item=b.id 
JOIN tb_barang c ON b.kode_barang=c.kode 
JOIN tb_sj d ON b.kode_sj=d.kode 
JOIN tb_satuan e ON c.satuan=e.satuan 
WHERE a.id=$id_sj_kumulatif";
// echo $s;
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0) die(div_alert('danger','Data subitem tidak ditemukan'));

$d = mysqli_fetch_assoc($q);
$qty = floatval($d['qty']);
$is_fs = $d['is_fs'];
$id_sj_item = $d['id_sj_item'];
$no_lot = $d['no_lot'];
$kode_barang = $d['kode_barang'];
$keterangan_barang = $d['keterangan_barang'];
$kode_lokasi = $d['kode_lokasi'];
$this_brand = $d['brand'];
$sum_pick = floatval($d['sum_pick']);
$sum_qty_roll = floatval($d['sum_qty_roll']);
$count_roll = $d['count_roll'];
$id_kategori = $d['id_kategori'];
$tanggal_terima = $d['tanggal_terima'];
$satuan = $d['satuan'];
$step = $d['step'];
$kode_po = $d['kode_po'];
$qty_adjusted = floatval($d['qty_adjusted']);
$qty_kumulatif = floatval($d['qty_kumulatif']);
$qty_kumulatif_fs = floatval($d['qty_kumulatif_fs']);

$qty_sisa = $qty_adjusted-$qty_kumulatif;
$kategori = $arr_kategori[$id_kategori];
$qty_datang = $qty_kumulatif + $qty_kumulatif_fs;


$no_lot_or_last = $no_lot ?? $last_no_lot;
$kode_lokasi_or_last = $kode_lokasi ?? $last_kode_lokasi;

if($sum_pick || $count_roll){
  $picked_info = "<div class=''>No Lot: $no_lot</div>";
  $picked_info.= "<div class=''>Lokasi: $kode_lokasi</div>";
  $picked_info.= "<div class='kecil darkred'>Picked: $sum_pick, Count Roll: $count_roll</div>";
  $picked_info.= "<div class='kecil darkred miring'>Item tidak bisa diubah karena sudah di pick atau sudah ada roll (hapus data pick dan roll untuk mengubahnya)</div>";
  $div_rak = '';
  $pilih_kode_lokasi = '';
  $elemen_form_kumulatif_item = '';

}else{

  
  // get lokasi dari DB
  $s = "SELECT a.* FROM tb_lokasi a 
  JOIN tb_blok_lokasi b ON a.blok=b.blok 
  WHERE b.id_kategori='$id_kategori' ORDER BY id_gudang, blok";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $rows = mysqli_num_rows($q);

  $last_id_gudang = '';
  $last_blok = '';
  $div_rak = '';
  $i=0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $kode = $d['kode'];
    $blok = $d['blok'];
    $brand = $d['brand'];
    $max_qty = $d['max_qty'];
    $persen_full = $d['persen_full'] ?? 0;
    $id_gudang = $d['id_gudang'];

    $end_div = $i>1 ? '</div>' : '';
    if($last_blok!=$blok ){
      $zblok = str_replace('.','_',$blok);
      $div_rak.= "
        $end_div
        <span class='btn btn-secondary btn-sm mt1 mb1 blok' id='blok__$zblok'>Blok: $blok</span>
        <div class='wadah flexy bg-white blok_rak ' id='blok_rak__$zblok' style='gap: 8px;display:none'>
      ";
    } 

    $persen_full = rand(1,100);

    $green = 255-$persen_full*2;
    $red = 255-(100-$persen_full*2);

    $div_rak.= "
      <div class='bordered br5 p1 item_rak' style='background: rgb($red,$green,100)'>
        $kode
        <div class='f10'>$persen_full%</div>
      </div>
    ";

    $last_id_gudang = $id_gudang;
    $last_blok = $blok;
    if($i==$rows) $div_rak .= '</div>';
  }

  if($kode_lokasi){
    $btn_simpan = "<button class='btn btn-primary' name=btn_simpan >Update</button>";
  }else{
    if($last_kode_lokasi){
      $btn_simpan = "<button class='btn btn-primary' name=btn_simpan >Simpan</button>";
    }else{
      $btn_simpan = "<button class='btn btn-primary' name=btn_simpan id=btn_simpan disabled>Simpan</button>";
    }
  }

  $picked_info = '';
}





# =============================================================
# PROSES CETAK LABEL
# =============================================================
$fs_show = $is_fs ? ' <b class="f14 ml1 mr1 biru p1 pr2 br5" style="display:inline-block;background:green;color:white">FS</b>' : '';
echo "
  <h2>
    <a href='?penerimaan&p=manage_sj_kumulatif&id_sj_item=$id_sj_item'>$img_prev</a>   
    $judul
  </h2>
  <div class=sub_form>Sub Form Manage Roll</div>
";
$tgl = date('d-m-Y',strtotime($tanggal_terima));


$bar = "<img width=300px alt='barcode' src='include/barcode.php?codetype=code39&size=50&text=".$kode_barang."&print=false'/>";
$no_roll = 'Roll-xxx';
$no_po_dll = "$kode_po $no_lot ($qty)$satuan $no_roll ($kode_lokasi $this_brand) $tgl";
$no_lot_show = $no_lot ? $no_lot : '<i class="abu f12 consolas">null</i>';
$data_roll_info = "
  <span> 
    <span class='f12 abu'>Kumulatif by PO: </span>$kode_po, 
    <span class='f12 abu'>ID: </span>$kode_barang, 
    <span class='f12 abu'>Lot: </span>$no_lot_show,
    <span class='f12 abu'>Rak: </span>$kode_lokasi <span class='f12 abu'>$this_brand</span>
  </span>
";



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
  $id_sj_kumulatif = $_POST['btn_tambah_roll'];
  $roll_multiplier = $_POST['roll_multiplier'];

  $s = "SELECT COUNT(1) as count_roll FROM tb_roll WHERE id_sj_kumulatif=$id_sj_kumulatif";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $counter = 1;
  if(mysqli_num_rows($q)){
    $d = mysqli_fetch_assoc($q);
    $counter = $d['count_roll'];
    $counter++;
    if($counter<10) {$counter_str = "00$counter";}else
    if($counter<100){$counter_str = "0$counter";}else{$counter_str = $counter;}
  }

  $s = "INSERT INTO tb_roll (id_sj_kumulatif, no_roll) VALUES ($id_sj_kumulatif,'$counter_str')";
  if($roll_multiplier>1){
    for ($i=1; $i < $roll_multiplier; $i++) { 
      $counter++;
      if($counter<10) {$counter_str = "00$counter";}else
      if($counter<100){$counter_str = "0$counter";}else{$counter_str = $counter;}
      $s .= ",($id_sj_kumulatif,'$counter_str')";
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
  $id_sj_kumulatif = $_GET['id_sj_kumulatif'] ?? die(erid('id_sj_kumulatif'));
  $s = "DELETE FROM tb_roll WHERE id_sj_kumulatif=$id_sj_kumulatif";
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

  $id_sj_kumulatif = $_GET['id_sj_kumulatif'] ?? die(erid('id_sj_kumulatif'));

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

  $s = "UPDATE tb_sj_kumulatif SET tmp_qty=$total_qty WHERE id=$id_sj_kumulatif";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  jsurl();

}


# =============================================================
# FORM TAMBAH ROLL
# =============================================================
$select_roll_multiplier = '';
for ($i=1; $i <= 20 ; $i++) $select_roll_multiplier .= "<option value=$i>$i roll</option>";
$select_roll_multiplier = "<select class='form-control form-control-sm' name=roll_multiplier>$select_roll_multiplier</select>";
$free_supplier_caption = $is_fs ? 'Free Supplier' : '';

$form_tambah_roll = "
  <form method=post class=kanan>
    <div class=flexy style='gap:3px'>
      <div>
        <button class='btn btn-success btn-sm' name=btn_tambah_roll value=$id_sj_kumulatif>Tambah $free_supplier_caption</button>
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
$sisa_qty_roll = $qty_sisa;
$sisa_qty_roll -= $sum_qty_roll;

$s = "SELECT * FROM tb_roll WHERE id_sj_kumulatif=$id_sj_kumulatif ORDER BY no_roll";
// echo "<h1>$s</h1>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0){
  echo div_alert('danger mt2', "Belum ada data Roll. $fs_show <hr>  $form_tambah_roll");
  $form_cetak_all = '';
}else{

  $tr = '';
  $i=0;
  $jumlah_roll = mysqli_num_rows($q);
  $sum_qty_roll = 0;

  $max_input_qty = $qty_adjusted;
  
  $btn_cetak_all = "<button class='btn btn-success ' name=btn_cetak_semua_label value=$id_sj_kumulatif>Cetak Semua Label</button>";
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $id_roll=$d['id'];
    $qty = floatval($d['qty']);
    $sum_qty_roll += $qty;
    if(!$qty) $btn_cetak_all = '<span class="abu miring kecil">Belum bisa cetak semua label karena masih ada QTY Roll/Pack yang kosong.</span>';

    $hapus = $i==$jumlah_roll ? "<button name=btn_hapus_roll value=$id_roll style='border:none;background:none' class=darkred onclick='return confirm(\"Yakin untuk hapus?\")'>$img_delete Hapus</button>" : "<span class='abu pointer f12' onclick='alert(\"Silahkan hapus dari row terbawah.\")'>$img_delete_disabled Hapus</span>";
    $cetak = $qty ? "<a target=_blank href='cetak_all_label.php?id_roll=$id_roll'><span class=green>Cetak</span></a>" 
    : "<span class='pointer abu f12' onclick='alert(\"Silahkan isi dahulu QTY!\")'>Cetak</span>";

    $tr .= "
      <tr>
        <td>$d[no_roll]</td>
        <td>
          <input class='form-control form-control-sm qty_roll' type=number min=0 max=$qty_adjusted step=$step required name=qty_roll__$id_roll id=qty_roll__$id_roll value=$qty>
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
      <td align=right>Jumlah</td>
      <td id=jumlah_qty_roll>$sum_qty_roll</td>
      <td colspan=100%>&nbsp;</td>
    </tr>
    <tr>
      <td align=right>QTY Sisa</td>
      <td colspan=100%><span id=qty_sisa>$qty_sisa</span> $fs_show</td>
    </tr>
    <tr>
      <td align=right>QTY Sisa - Jumlah</td>
      <td colspan=100%><span id=selisih>$sisa_qty_roll</span>  </td>
    </tr>
  ";

  $tr_sum = "
    <tr>
      <td align=right>Jumlah</td>
      <td id=jumlah_qty_roll>$sum_qty_roll</td>
      <td colspan=100%>&nbsp;</td>
    </tr>
  ";

  echo "
    <div class='flexy flex-between'>
      <div>
        $data_roll_info $fs_show
      </div
      <div>
        $form_tambah_roll
      </div
    </div>
    
    <form method=post class=wadah>
      <table class='table'>
        $thead
        $tr
        $tr_sum
      </table>
      <button id=btn_save_roll name=btn_save_roll value=$id_roll  class='btn btn-primary'>$img_save Save QTY Roll</button>
    </form>
    <form method=post class=wadah>
      <button id=btn_delete_all_roll name=btn_delete_all_roll value=$id_sj_kumulatif  class='btn btn-danger' onclick='return confirm(\"Yakin untuk hapus semua roll?\")'>$img_delete_disabled Delete All Data Roll</button>
      <div class='f12 darkred mt1 consolas'><b>Perhatian!</b> Delete All artinya semua data roll diatas akan hilang, namun tidak di item kumulatif lain.</div>
    </form>
  ";

  
  $form_cetak_all = "
    <form action=cetak_all_label.php method=post target=_blank class=wadah>
      $btn_cetak_all
      <div class='f12 abu mt1 consolas'>Akan muncul Preview Cetak Label di Tab baru untuk semua data Roll diatas.</div>
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

      let qty_sisa = parseFloat($('#qty_sisa').text());
      let selisih = qty_sisa-jumlah;
      let selisih_show = '';
      $('#btn_save_roll').prop('disabled',0);
      if(selisih<0){
        $('#btn_save_roll').prop('disabled',1);
        selisih_show = `<span class=red>${selisih} <i class='f12 consolas'>jumlah roll tidak boleh melebihi QTY Adjusted</i></span>`;
      }else if(selisih==0){
        selisih_show = `${selisih} <div class='mt1 f12 consolas'>QTY Adjusted habis</div>`;
      }else{
        selisih_show = `${selisih} <div class='mt1 f12 consolas'>Sisa QTY Adjusted dapat dialokasikan ke kumulatif item lain atau penerimaan parsial</div>`;
      }
      $('#selisih').html(selisih_show);
    }

    $('.qty_roll').change(function(){
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
      // if(qty_roll_identik==true){
      //   for (let i = 0; i < z.length; i++) {
      //     $('#'+z[i].id).prop('max',sisa_qty_roll/z.length);
      //     // console.log(z[i].id,sisa_qty_roll/z.length);
      //   }
      // }else{
      //   for (let i = 0; i < z.length; i++) {
      //     $('#'+z[i].id).prop('max',sisa_qty_roll);
      //   }
      // }
    })
  })
</script>