<?php
set_title('Manage Item Kumulatif Penerimaan');

$pesan_tambah = '';
if(isset($_POST['btn_simpan'])){
  $id = $_POST['id_sj_kumulatif'];

  $s = "SELECT a.is_fs,b.kode_barang,c.kode_po  
  FROM tb_sj_kumulatif a 
  JOIN tb_sj_item b ON a.id_sj_item=b.id 
  JOIN tb_sj c ON b.kode_sj=c.kode 
  WHERE a.id=$id";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  if(mysqli_num_rows($q)==0) die('Data tidak ditemukan.');
  if(mysqli_num_rows($q)>1) die(erid('(id::not_uniq)'));
  $d = mysqli_fetch_assoc($q);

  $kode_po = $d['kode_po'] ?? die('kode_po::null');
  $kode_barang = $d['kode_barang'] ?? die('kode_barang::null');
  $is_fs = $d['is_fs'];// ?? die('is_fs::null');

  $kode_lokasi = $_POST['kode_lokasi'] ?? die('kode_lokasi::null');
  $no_lot = $_POST['no_lot'] ?? die('no_lot::null');

  // ID - PO - LOT - LOKASI - FS
  $kode_kumulatif = "$kode_barang~$kode_po~$no_lot~$kode_lokasi~$is_fs";
  $kode_kumulatif = strtoupper(str_replace(' ','',$kode_kumulatif));

  $s = "SELECT 1 FROM tb_sj_kumulatif WHERE kode_kumulatif='$kode_kumulatif'";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)){
    echo div_alert('danger', "Untuk Lot $no_lot dan lokasi $kode_lokasi sudah ada. Silahkan pakai kode lot/lokasi lainnya!<hr><a href='javascript:history.go(-1)'>Kembali</a>");
    exit;
  }


  unset($_POST['id_sj_kumulatif']);
  unset($_POST['btn_simpan']);
  unset($_POST['keterangan_barang']);

  $pairs = '__';
  foreach ($_POST as $key => $value) {
    $value = clean_sql($value);
    $value = $value=='' ? 'NULL' : "'$value'";
    $pairs .= ",$key = $value";
  }
  $pairs .= ",kode_kumulatif = '$kode_kumulatif'";
  $pairs = str_replace('__,','',$pairs);

  $s = "UPDATE tb_sj_kumulatif SET $pairs WHERE id=$id";
  echo $s;
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  jsurl();
  exit;
}

if(isset($_POST['btn_tambah_subitem']) || isset($_POST['btn_tambah_subitem_fs'])){

  $id_sj_item = $_POST['id_sj_item'] ?? die(erid('id_sj_item'));

  $is_fs = isset($_POST['btn_tambah_subitem_fs']) ? 1 : 'NULL';
  $s = "INSERT INTO tb_sj_kumulatif (id_sj_item,nomor,is_fs) VALUES ($id_sj_item,$_POST[nomor],$is_fs)";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $pesan_tambah = div_alert('success','Tambah Sub item sukses.');

  $s = "SELECT id as id_sj_kumulatif FROM tb_sj_kumulatif WHERE id_sj_item=$id_sj_item ORDER BY nomor DESC LIMIT 1";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $d = mysqli_fetch_assoc($q);
  $id_sj_kumulatif = $d['id_sj_kumulatif'];

  
  // $arr = explode('?',$_SERVER['REQUEST_URI']);
  // jsurl("?$arr[1]");
  jsurl("?penerimaan&p=manage_sj_kumulatif&id_sj_item=$id_sj_item&id_sj_kumulatif=$id_sj_kumulatif");
  exit;
  
}

$id_sj_item = $_GET['id_sj_item'] ?? die(erid('id_sj_item'));
if($id_sj_item==''){
  echo div_alert('danger',"ID item invalid. <a href='?penerimaan&p=bbm&kode_sj=$kode_sj'>Pilih Item dari BBM</a>");
  exit;
} 
$s = "SELECT a.*,
a.id as id_sj_item,
a.kode_sj,
b.kode_po,
a.qty as qty_po,
c.id as id_bbm,
c.kode as no_bbm,
c.tanggal_masuk,
d.nama as nama_barang,
d.kode as kode_barang,
d.satuan,
e.kode as kategori,
e.nama as nama_kategori,
f.step,
(
  SELECT SUM(qty) FROM tb_sj_kumulatif WHERE id_sj_item=a.id and is_fs is null) qty_subitem,
(
  SELECT SUM(qty) FROM tb_sj_kumulatif WHERE id_sj_item=a.id and is_fs is not null) qty_subitem_fs

FROM tb_sj_item a 
JOIN tb_sj b ON a.kode_sj=b.kode 
JOIN tb_bbm c ON b.kode=c.kode_sj 
JOIN tb_barang d ON a.kode_barang=d.kode 
JOIN tb_kategori e ON d.id_kategori=e.id 
JOIN tb_satuan f ON d.satuan=f.satuan 
WHERE a.id=$id_sj_item 
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);

$nama_barang = $d['nama_barang'];
$kode_barang = $d['kode_barang'];
$id_sj_item = $d['id_sj_item'];
$kode_po = $d['kode_po'];
$kode_sj = $d['kode_sj'];
$no_bbm = $d['no_bbm'];
$qty_po = $d['qty_po'];
$qty_diterima = $d['qty_diterima'];
$qty_subitem = $d['qty_subitem'];
$qty_subitem_fs = $d['qty_subitem_fs'];
$satuan = $d['satuan'];
$step = $d['step'];
// $pengiriman_ke = $d['pengiriman_ke'];
$kategori = $d['kategori'];
$nama_kategori = $d['nama_kategori'];
$tanggal_masuk = $d['tanggal_masuk'];

$qty_po = floatval($qty_po);
$qty_diterima = floatval($qty_diterima);
$qty_subitem = floatval($qty_subitem);
$qty_subitem_fs = floatval($qty_subitem_fs);


$nama_kategori = ucwords(strtolower($nama_kategori));

$is_lebih = $qty_po<$qty_diterima ? 1 : 0;
$qty_fs = 0;
if($is_lebih){
  $qty_fs = $qty_diterima-$qty_po;
  $tr_free_supplier = "
    <tr class=blue>
      <td>QTY Lebih (Free Supplier)</td>
      <td>$qty_fs $satuan</td>
    </tr>
  ";

}else{
  $tr_free_supplier = '';
}
?>
<div id="blok_manage_subitem">
  <div class="pagetitle">
    <h1>Manage Item Kumulatif Penerimaan</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
        <li class="breadcrumb-item"><a href="?penerimaan&p=data_sj">Data SJ</a></li>
        <li class="breadcrumb-item"><a href="?penerimaan&p=manage_sj&kode_sj=<?=$kode_sj?>">Manage SJ</a></li>
        <li class="breadcrumb-item"><a href="?penerimaan&p=bbm&kode_sj=<?=$kode_sj?>">BBM</a></li>
        <li class="breadcrumb-item active">BBM Item Kumulatif</li>
      </ol>
    </nav>
  </div>


  <p>Page ini digunakan untuk pencatatan Item Kumulatif dan Pencetakan Label.</p>


<?php
# =======================================================================
# PROCESSORS 
# =======================================================================

?>
<h2>Item: <?=$kode_barang?> | <?=$nama_kategori?></h2>
<table class="table table-hover">
  <tr>
    <td>Surat Jalan</td>
    <td><?=$kode_sj?></td>
  </tr>
  <tr>
    <td>Nomor BBM</td>
    <td><?=$no_bbm?></td>
  </tr>
  <tr>
    <td>Nama Barang</td>
    <td><?=$nama_barang?></td>
  </tr>
  <tr>
    <td>QTY PO</td>
    <td>
      <span id="qty_po"><?=$qty_po?></span> 
      <span id="satuan"><?=$satuan?></span> 
    </td>
  </tr>
  <tr>
    <td>QTY Diterima</td>
    <td>
      <span id="qty_diterima"><?=$qty_diterima?></span> <?=$satuan?> 
    </td>
  </tr>
  <?=$tr_free_supplier?>
</table>

<?php
$with_fs = $qty_fs ? '/ Free Supplier' : '';
echo "<h2 class='mb3 mt4'>Sub Items $with_fs</h2>";
echo $pesan_tambah;

$s = "SELECT a.*,
a.id as id_sj_kumulatif,
c.keterangan as keterangan_barang  ,
(
  SELECT sum(qty) FROM tb_picking WHERE id_sj_kumulatif=a.id ) sum_pick, 
(
  SELECT count(1) FROM tb_roll WHERE id_sj_kumulatif=a.id ) count_roll 

FROM tb_sj_kumulatif a  
JOIN tb_sj_item b ON a.id_sj_item=b.id 
JOIN tb_barang c ON b.kode_barang=c.kode 
WHERE a.id_sj_item=$id_sj_item
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_sub_item = mysqli_num_rows($q);
$div_subitem = '';
$i=0;
$ada_kosong=0;
$get_id_sj_kumulatif = $_GET['id_sj_kumulatif'] ?? '';
$last_no_lot = '';
$last_no_roll = '';
$last_keterangan_barang = '';
$last_kode_lokasi = '';
while($d=mysqli_fetch_assoc($q)){
  $i++;
  $id_sj_kumulatif=$d['id_sj_kumulatif'];
  $qty=$d['qty'];

  if($qty){
    $last_no_lot = $d['no_lot'];
    $last_keterangan_barang = $d['keterangan_barang'];
    $last_kode_lokasi = $d['kode_lokasi'];

    $qty = floatval($qty);
    $qty_show = "<span class=green>QTY: <span class=qty_subitem id=qty_subitem__$id_sj_kumulatif>$qty</span></span>";
    $pesan_kosong = '';
  }else{
    $pesan_kosong = '<div>Silahkan isi dahulu lot, lokasi, dan roll!</div>';
    $ada_kosong=1;
    $qty_show = "<span class=yellow>QTY: <span class=qty_subitem id=qty_subitem__$id_sj_kumulatif>0</span></span>";
  }


  $gradasi = $d['is_fs'] ? 'kuning' : 'hijau';
  $gradasi = $qty ? $gradasi : 'merah';
  $sty_border = $get_id_sj_kumulatif==$id_sj_kumulatif ? 'style="border: solid 3px blue"' : 'style="border: solid 3px #ccc"';

  $fs_info = $d['is_fs'] ? "<div class='biru f12'>Free Supplier</div>" : '';

  if($d['sum_pick']){
    $picked_info = "<span class='kecil darkred'>Picked: ".floatval($d['sum_pick']).'</span>';
  }else{
    $picked_info = '';
  }
  
  $roll_info = $d['count_roll'] ? "<div class='kecil darkred'>Roll count: ".floatval($d['count_roll']).'</div>' : '';
  
  if($d['sum_pick'] || $d['count_roll'] ){
    $btn_delete = '';
  }else{
    $btn_delete = "    
      <div>
        <span class='btn_aksi' id=sj_subitem__delete__$id_sj_kumulatif>$img_delete</span>
      </div>
    ";
  }


  $div_subitem.= "
  <div id=sj_subitem__$id_sj_kumulatif class='btn gradasi-$gradasi flexy' $sty_border>
    <div>
      <a href='?penerimaan&p=manage_sj_kumulatif&id_sj_item=$id_sj_item&id_sj_kumulatif=$id_sj_kumulatif'>
        <div class='f12'>Item Kumulatif-$i</div>
        $fs_info
        <div class=kecil>$qty_show</div>
        <div class=kecil>Lot: $d[no_lot]</div>
        <div class=kecil>$d[kode_lokasi]</div>
        $picked_info
        $roll_info
      </a>
    </div>
    $btn_delete

  </div>";
}

echo "<h1>DEBUG
last_no_lot:$last_no_lot = '';
last_no_roll:$last_no_roll = '';
last_keterangan_barang:$last_keterangan_barang = '';
last_kode_lokasi:$last_kode_lokasi = '';

</h1>";


if($qty_diterima<$qty_po){
  // barang kurang
  $sisa_qty = $qty_diterima-$qty_subitem;
  $sisa_fs = 0;
}else{
  $sisa_qty = $qty_po-$qty_subitem;
  $sisa_fs = $qty_fs-$qty_subitem_fs;
}

$sisa_fs_info = $qty_fs? "<div class=biru>Sisa Free Supplier: <span id=sisa_fs>$sisa_fs</span> $satuan</div>":'';

$sisa_qty_show = "
  <div>Sisa QTY: <span id=sisa_qty>$sisa_qty</span> $satuan</div>
  $sisa_fs_info
";


# ======================================================
# FORM TAMBAH SUB-ITEM || FREE ITEM
# ======================================================
if($qty_diterima==$qty_subitem || $ada_kosong){
  // qty habis || ada yg kosong
  // tidak boleh nambah
  $form = '';
}else{
  // boleh nambah
  $nomor = $jumlah_sub_item+1;
  if($sisa_qty>0){
    $form = "
    <form method=post>
      <button class='btn btn-primary btn-sm' name=btn_tambah_subitem>Tambah Sub item</button>
      <input type='hidden' name=id_sj_item value='$id_sj_item'>
      <input type='hidden' name=nomor value='$nomor'>
    </form>
    ";
  }else{
    if($sisa_fs>0){
      $form = "
      <form method=post>
        <button class='btn btn-primary btn-sm' name=btn_tambah_subitem_fs>Tambah Free Supplier Item</button>
        <input type='hidden' name=id_sj_item value='$id_sj_item'>
        <input type='hidden' name=nomor value='$nomor'>
      </form>
      ";

    }else{
      $form = "<span class='hijau kecil miring'>All-allocated!</span>";
    }

  }
}


$form_pertama = "
  <form method=post>
    <button class='btn btn-primary btn-sm' name=btn_tambah_subitem>Tambah Item Kumulatif / Nomor Lot</button>
    <div class='mb2 f12 darkabu mt1'><b>Catatan:</b> QTY diterima akan terkunci jika Anda sudah menambahkan subitem.</div>

    <input type='hidden' name=id_sj_item value='$id_sj_item'>
    <input type='hidden' name=nomor value='1'>
  </form>
";

echo $div_subitem=='' ? div_alert('danger', "<div class=mb2>Belum ada sub-item.</div>  $form_pertama") : "
  <div class=flexy>
    $div_subitem
    <div class=kecil>
      $sisa_qty_show
      $pesan_kosong
      $form
    </div>
  </div>
  <div class='f12 mt2'>Catatan: Item Kumulatif tidak bisa dihapus jika sudah di pick atau sudah ada roll.</div>
";


if($get_id_sj_kumulatif!=''){
  $id_sj_kumulatif = $get_id_sj_kumulatif;

  ?>
  <style>
    .item_rak{
      cursor: pointer;
      transition: .2s;
      border: solid 2px white;
    }
    .item_rak:hover{
      border: solid 2px blue;
    }
  </style>
  <?php

  $qty = 0;
  $no_lot = '';
  $keterangan_barang = '';
  $kode_lokasi = '';
  
  $s = "SELECT a.*,
  c.kode as kode_barang,
  c.keterangan as keterangan_barang,
  (
    SELECT brand FROM tb_lokasi WHERE kode=a.kode_lokasi) brand,
  (
    SELECT sum(qty) FROM tb_picking WHERE id_sj_kumulatif=a.id ) sum_pick, 
  (
    SELECT sum(qty) FROM tb_roll WHERE id_sj_kumulatif=a.id ) sum_qty_roll, 
  (
    SELECT count(1) FROM tb_roll WHERE id_sj_kumulatif=a.id ) count_roll 

  FROM tb_sj_kumulatif a 
  JOIN tb_sj_item b ON a.id_sj_item=b.id 
  JOIN tb_barang c ON b.kode_barang=c.kode 
  WHERE a.id=$get_id_sj_kumulatif";
  // echo $s;
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0) die(div_alert('danger','Data subitem tidak ditemukan'));

  $d = mysqli_fetch_assoc($q);
  $qty = floatval($d['qty']);
  $is_fs = $d['is_fs'];
  $no_lot = $d['no_lot'];
  $kode_barang = $d['kode_barang'];
  $keterangan_barang = $d['keterangan_barang'];
  $kode_lokasi = $d['kode_lokasi'];
  $this_brand = $d['brand'];
  $sum_pick = floatval($d['sum_pick']);
  $sum_qty_roll = floatval($d['sum_qty_roll']);
  $count_roll = $d['count_roll'];

  $no_lot_or_last = $no_lot ?? $last_no_lot;
  $kode_lokasi_or_last = $kode_lokasi ?? $last_kode_lokasi;

  if($sum_pick || $count_roll){
    $picked_info = "<div class=''>No Lot: $no_lot</div>";
    $picked_info.= "<div class=''>Lokasi: $kode_lokasi</div>";
    $picked_info.= "<div class='kecil darkred'>Picked: $sum_pick, Count Roll: $count_roll</div>";
    $picked_info.= "<div class='kecil darkred miring'>Item tidak bisa diubah karena sudah di pick atau sudah ada roll (hapus data pick dan roll untuk mengubahnya)</div>";
    $div_rak = '';
    $pilih_kode_lokasi = '';
    $elemen_form_subitem = '';

  }else{

    
    // get lokasi dari DB
    $s = "SELECT * FROM tb_lokasi WHERE jenis='$kategori' ORDER BY id_gudang, blok";
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
    $pilih_kode_lokasi = "<span class='btn btn-secondary btn-sm' id=pilih_kode_lokasi>Pilih Kode Lokasi</span>";

    $elemen_form_subitem = "
      <div class=mt2>Lot Number</div>
      <input class='form-control mb1' maxlength=20 name=no_lot value='$no_lot_or_last'>
      <div class='mb2 f12 darkabu miring'>Lot Number boleh kosong</div>
      Kode Lokasi ($kategori) $bintang
      <input class='form-control mb2' id=kode_lokasi_show value='$kode_lokasi_or_last' disabled>
      <input class='form-control mb2 hideit' minlength=2 maxlength=20 name=kode_lokasi id=kode_lokasi value='$kode_lokasi_or_last' required>
      $pilih_kode_lokasi
      <div id='div_rak' class='hideit'>
        $div_rak
      </div>

      <div class=mt3>
        $btn_simpan
      </div>
    ";

  }



  /*
  // echo "<h1>$qty - $sisa_qty</h1>";
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
  */


  // QTY ($satuan) $bintang ~ <u class='pointer darkblue kecil' id=set_max_qty>Set Max: <span id=max_input_qty>$max_input_qty</span></u>
  // <input class='form-control mb2' type=number min=$step max=$max_input_qty step=$step required name=qty id=qty value=$qty $disabled>  

  
  echo "
  <div class='wadah mt2'>
    <div class=debug>id_sj_item:$id_sj_item</div>
    <div class=debug>id_sj_kumulatif:$id_sj_kumulatif</div>
    <form method=post>
      <input type='hidden' name=id_sj_kumulatif value=$get_id_sj_kumulatif>

      QTY : $sum_qty_roll $satuan (<span class='consolas abu miring kecil'> AutoSum dari Sub-Roll </span>) 
      $picked_info
      $elemen_form_subitem
    </form>
  </div>
  ";




  # =============================================================
  # PROSES CETAK LABEL
  # =============================================================
  echo '</div>'; // end blok_manage_subitem
  if($kode_lokasi){
    echo '
      <script>
        $(function(){
          $("#blok_manage_subitem").hide();
        })
      </script>
    ';
    include 'proses_cetak_label.php';
  }
}

// tampil data sebelumnya pada tambah subitems zzz plan





?>


<script>
  $(function(){
    // $('#set_max_qty').click(function(){
    //   $('#qty').val($('#max_input_qty').text())
    // });
    $('.item_rak').click(function(){
      let val = $(this).text().trim().split("\n");
      console.log(val[0]);
      $('#kode_lokasi').val(val[0]);
      $('#kode_lokasi_show').val(val[0]);
      $('#btn_simpan').prop('disabled',false);

      $('.blok_rak').slideUp();
      $('#div_rak').slideUp();
      $('#pilih_kode_lokasi').slideDown();

    });

    $('#pilih_kode_lokasi').click(function(){
      $(this).slideUp();
      $('#div_rak').slideDown();
    });

    $('.blok').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let blok = rid[1];

      console.log(blok);

      $('.blok_rak').slideUp();
      $('#blok_rak__'+blok).slideDown();
    })
  })
</script>