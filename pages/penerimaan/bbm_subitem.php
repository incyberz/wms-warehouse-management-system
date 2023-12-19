<?php
$pesan_tambah = '';
if(isset($_POST['btn_simpan'])){
  $id = $_POST['id_bbm_subitem'];
  unset($_POST['id_bbm_subitem']);
  unset($_POST['btn_simpan']);

  $pairs = '__';
  foreach ($_POST as $key => $value) {
    $value = clean_sql($value);
    $value = $value=='' ? 'NULL' : "'$value'";
    $pairs .= ",$key = $value";
  }
  $pairs = str_replace('__,','',$pairs);

  $s = "UPDATE tb_sj_subitem SET $pairs WHERE id=$id";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $arr = explode('?',$_SERVER['REQUEST_URI']);
  jsurl("?$arr[1]");
  exit;
}
if(isset($_POST['btn_tambah_subitem'])){
  $s = "INSERT INTO tb_sj_subitem (id_sj_item,nomor) VALUES ($_POST[id_sj_item],$_POST[nomor])";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $pesan_tambah = div_alert('success','Tambah Sub item sukses.');
  
  $arr = explode('?',$_SERVER['REQUEST_URI']);
  jsurl("?$arr[1]");
  exit;
}

$id_sj_item = $_GET['id_sj_item'] ?? die(erid('id_sj_item'));
$s = "SELECT a.*,
a.id as id_sj_item,
a.kode_sj,
b.kode_po,
c.id as id_bbm,
c.kode as no_bbm,
c.tanggal_masuk,
d.nama as nama_barang,
d.kode as kode_barang,
d.satuan,
e.kode as kategori,
e.nama as nama_kategori,
(
  SELECT SUM(qty) FROM tb_sj_subitem WHERE id_sj_item=a.id) qty_subitem

FROM tb_sj_item a 
JOIN tb_sj b ON a.kode_sj=b.kode 
JOIN tb_bbm c ON b.kode=c.kode_sj 
JOIN tb_barang d ON a.kode_barang=d.kode 
JOIN tb_kategori e ON d.id_kategori=e.id 
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
$qty_diterima = $d['qty_diterima'];
$qty_subitem = $d['qty_subitem'];
$satuan = $d['satuan'];
// $pengiriman_ke = $d['pengiriman_ke'];
$kategori = $d['kategori'];
$nama_kategori = $d['nama_kategori'];
$tanggal_masuk = $d['tanggal_masuk'];

$qty_diterima = str_replace('.0000','',$qty_diterima);

$nama_kategori = ucwords(strtolower($nama_kategori));

?>
<div class="pagetitle">
  <h1>BBM Sub Items</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=data_sj">Data SJ</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=manage_sj&kode_sj=<?=$kode_sj?>">Manage SJ</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=bbm&kode_sj=<?=$kode_sj?>">BBM</a></li>
      <li class="breadcrumb-item active">BBM Subitems</li>
    </ol>
  </nav>
</div>
<p>Page ini digunakan untuk pencatatan Sub Item dan Pencetakan Label.</p>


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
    <td>QTY Diterima</td>
    <td>
      <span id="qty_diterima"><?=$qty_diterima?></span> 
      <span id="satuan"><?=$satuan?></span> 
    </td>
  </tr>
</table>
<h2 class='mb3 mt4'>Sub Items</h2>
<?php
echo $pesan_tambah;

$s = "SELECT a.*,a.id as id_bbm_subitem 
FROM tb_sj_subitem a  
WHERE a.id_sj_item=$id_sj_item
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_sub_item = mysqli_num_rows($q);
$div = '';
$i=0;
$ada_kosong=0;
$get_id_bbm_subitem = $_GET['id_bbm_subitem'] ?? '';
$last_no_lot = '';
$last_no_roll = '';
$last_jenis_bahan = '';
$last_kode_rak = '';
while($d=mysqli_fetch_assoc($q)){
  $i++;
  $id_bbm_subitem=$d['id_bbm_subitem'];
  $qty=$d['qty'];

  if($qty){
    $last_no_lot = $d['no_lot'];
    $last_no_roll = $d['no_roll'];
    $last_jenis_bahan = $d['jenis_bahan'];
    $last_kode_rak = $d['kode_lokasi'];

    $qty = str_replace('.0000','',$qty);
    $qty_show = "<span class=green>QTY: <span class=qty_subitem id=qty_subitem__$id_bbm_subitem>$qty</span></span>";
    $pesan_kosong = '';
  }else{
    $pesan_kosong = '<div>Silahkan isi dahulu qty!</div>';
    $ada_kosong=1;
    $qty_show = "<span class=yellow>QTY: <span class=qty_subitem id=qty_subitem__$id_bbm_subitem>0</span></span>";
  }

  $gradasi = $qty ? 'hijau' : 'merah';
  $sty_border = $get_id_bbm_subitem==$id_bbm_subitem ? 'style="border: solid 3px blue"' : 'style="border: solid 3px #ccc"';

  $div.= "<div class='btn gradasi-$gradasi' $sty_border>
    <a href='?penerimaan&p=bbm_subitem&id_sj_item=$id_sj_item&id_bbm_subitem=$id_bbm_subitem'>
      <div class='f12'>Subitem-$i</div>
      <div class=kecil>$qty_show</div>
    </a>
  </div>";
}

// echo "<h1>DEBUG
// last_no_lot:$last_no_lot = '';
// last_no_roll:$last_no_roll = '';
// last_jenis_bahan:$last_jenis_bahan = '';
// last_kode_rak:$last_kode_rak = '';

// </h1>";


if($qty_diterima==$qty_subitem || $ada_kosong){
  // qty habis || ada yg kosong
  // tidak boleh nambah
  $form = '';
}else{
  // boleh nambah
  $nomor = $jumlah_sub_item+1;
  $form = "
  <form method=post>
    <button class='btn btn-primary btn-sm' name=btn_tambah_subitem>Tambah Sub item</button>
    <input type='hidden' name=id_sj_item value='$id_sj_item'>
    <input type='hidden' name=nomor value='$nomor'>
  </form>
  ";
}

$sisa_qty = $qty_diterima-$qty_subitem;
$sisa_qty_show = "Sisa QTY: <span id=sisa_qty>$sisa_qty</span> $satuan";

$form_pertama = "
  <form method=post>
    <button class='btn btn-primary btn-sm' name=btn_tambah_subitem>Tambah Sub item</button>
    <input type='hidden' name=id_sj_item value='$id_sj_item'>
    <input type='hidden' name=nomor value='1'>
  </form>
";

echo $div=='' ? div_alert('danger', "<div class=mb2>Belum ada sub-item.</div>  $form_pertama") : "
  <div class=flexy>
    $div
    <div>
      $sisa_qty_show
      $pesan_kosong
      $form
    </div>
  </div>
";


if($get_id_bbm_subitem!=''){

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
  $no_roll = '';
  $jenis_bahan = '';
  $kode_lokasi = '';
  
  $s = "SELECT a.*,
  (SELECT brand FROM tb_lokasi WHERE kode=a.kode_lokasi) brand 
  FROM tb_sj_subitem a 
  WHERE a.id=$get_id_bbm_subitem";
  // echo $s;
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0) die(div_alert('danger','Data subitem tidak ditemukan'));

  $d = mysqli_fetch_assoc($q);
  $qty = $d['qty'];
  $no_lot = $d['no_lot'] ?? $last_no_lot;
  $no_roll = $d['no_roll'] ?? $last_no_roll;
  $jenis_bahan = $d['jenis_bahan'] ?? $last_jenis_bahan;
  $kode_lokasi = $d['kode_lokasi'] ?? $last_kode_rak;
  $this_brand = $d['brand'];

  $qty = str_replace('.0000','',$qty);

  $div = '';
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

  if($qty and $kode_lokasi){
    $btn_simpan = "<button class='btn btn-primary' name=btn_simpan >Update</button>";
  }else{
    if($last_kode_rak){
      $btn_simpan = "<button class='btn btn-primary' name=btn_simpan >Simpan</button>";
    }else{
      $btn_simpan = "<button class='btn btn-primary' name=btn_simpan id=btn_simpan disabled>Simpan</button>";
    }
  }

  // echo "<h1>$qty - $sisa_qty</h1>";
  $max_input_qty = $qty ? ($qty - $sisa_qty) : $sisa_qty;

  echo "
  <div class='wadah mt2'>
    <form method=post>
      <input type='hidden' name=id_bbm_subitem value=$get_id_bbm_subitem>

      QTY ($satuan) $bintang
      <input class='form-control mb2' type=number min=0 max=$max_input_qty step=0.0001 required name=qty value=$qty>
      Lot Number
      <input class='form-control mb2' maxlength=20 name=no_lot value='$no_lot'>
      Roll Number
      <input class='form-control mb2' maxlength=20 name=no_roll value='$no_roll'>
      Jenis bahan
      <input class='form-control mb2' maxlength=100 name=jenis_bahan value='$jenis_bahan'>
      Kode Lokasi ($kategori) $bintang
      <input class='form-control mb2' id=kode_rak_show value='$kode_lokasi' disabled>
      <input class='form-control mb2 hideit' minlength=2 maxlength=20 name=kode_lokasi id=kode_lokasi value='$kode_lokasi' required>
      <span class='btn btn-secondary btn-sm' id=pilih_kode_rak>Pilih Kode Lokasi</span>
      <div id='div_rak' class='hideit'>
        $div_rak
      </div>

      <div class=mt3>
        $btn_simpan
        <span class='btn btn-success btn_aksi' id=blok_cetak__toggle>Cetak Label</span>
      </div>
    </form>
  </div>
  ";

  if($qty && $no_lot && $no_roll && $kode_lokasi){

    $tgl = date('d-m-Y',strtotime($tanggal_masuk));

    $bar = "<img width=300px alt='barcode' src='include/barcode.php?codetype=code39&size=50&text=".$kode_barang."&print=false'/>";
    $no_po_dll = "$kode_po $no_lot ($qty)$satuan $no_roll ($kode_lokasi $this_brand) $tgl";
    echo "<div id=blok_cetak class=hideit>";
    include 'cetak_label.php';

    $cetak_semua = $sisa_qty==0 ? "
      <form action=cetak.php method=post target=_blank>
        <button class='btn btn-success btn-sm' name=btn_cetak_semua_label value=$id_sj_item>Cetak Semua Label</button>
      </form>
    ":"
      <div class='kecil miring darkred'>Semua QTY harus dialokasikan dahulu. (sisa QTY: $sisa_qty)</div>
      <button class='btn btn-success btn-sm' disabled>Cetak Semua Label</button>
    ";

    echo "
        <form method=post action=cetak.php target=_blank>
          <input type=hidden name=kode_barang value='$kode_barang'>
          <input type=hidden name=nama_barang value='$nama_barang'>
          <input type=hidden name=jenis_bahan value='$jenis_bahan'>
          <input type=hidden name=no_po_dll value='$no_po_dll'>
          <button class='btn btn-success btn-sm mt2' name=btn_cetak_label>Cetak</button>
        </form>
        <hr>
        $cetak_semua
      </div>
    ";

  }else{
    echo "
    <div id=blok_cetak class='tengah alert alert-danger hideit'>
      Agar dapat mencetak label, silahkan lengkapi dahulu kolom QTY, No. Lot, No. Roll, dan Pilih Kode Lokasi !
    </div>
    ";
  }  

}

// tampil data sebelumnya pada tambah subitems zzz plan





?>
<script>
  $(function(){
    $('.item_rak').click(function(){
      let val = $(this).text().trim().split("\n");
      console.log(val[0]);
      $('#kode_lokasi').val(val[0]);
      $('#kode_rak_show').val(val[0]);
      $('#btn_simpan').prop('disabled',false);

      $('.blok_rak').slideUp();
      $('#div_rak').slideUp();
      $('#pilih_kode_rak').slideDown();

    });

    $('#pilih_kode_rak').click(function(){
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