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

  $s = "UPDATE tb_bbm_subitem SET $pairs WHERE id=$id";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));




  jsurl("?penerimaan&p=bbm_subitem&id_bbm=$_GET[id_bbm]&id_po_item=$_GET[id_po_item]&id_bbm_subitem=$id");
  exit;
}
if(isset($_POST['btn_tambah_subitem'])){
  $s = "INSERT INTO tb_bbm_subitem (id_bbm_item,nomor) VALUES ($_POST[id_bbm_item],$_POST[nomor])";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $pesan_tambah = div_alert('success','Tambah Sub item sukses.');
}




$id_po_item = $_GET['id_po_item'] ?? die(erid('id_po_item'));
$id_bbm = $_GET['id_bbm'] ?? die(erid('id_bbm'));
$s = "SELECT a.*,
a.id as id_bbm_item,
b.nomor as pengiriman_ke,
b.id as id_bbm,
b.kode as no_bbm,
b.tanggal_terima,
d.kode as kode_po,
e.nama as nama_barang,
e.kode as kode_barang,
e.satuan,
f.kode as kategori,
f.nama as nama_kategori,
(
  SELECT SUM(qty) FROM tb_bbm_subitem WHERE id_bbm_item=a.id) qty_subitem

FROM tb_bbm_item a 
JOIN tb_bbm b ON a.id_bbm=b.id 
JOIN tb_sj_item c ON a.id_po_item=c.id 
JOIN tb_sj d ON c.id_po=d.id 
JOIN tb_barang e ON c.id_barang=e.id 
JOIN tb_kategori f ON e.id_kategori=f.id 
WHERE a.id_po_item=$id_po_item 
AND a.id_bbm=$id_bbm
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);

$nama_barang = $d['nama_barang'];
$kode_barang = $d['kode_barang'];
$id_bbm_item = $d['id_bbm_item'];
$kode_po = $d['kode_po'];
$no_bbm = $d['no_bbm'];
$qty_diterima = $d['qty_diterima'];
$qty_subitem = $d['qty_subitem'];
$satuan = $d['satuan'];
$pengiriman_ke = $d['pengiriman_ke'];
$kategori = $d['kategori'];
$nama_kategori = $d['nama_kategori'];
$tanggal_terima = $d['tanggal_terima'];

$qty_diterima = str_replace('.0000','',$qty_diterima);

?>
<div class="pagetitle">
  <h1>BBM Sub Items</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">PO Home</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=terima_barang">Cari PO</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=terima_barang&kode_po=<?=$kode_po?>&id_bbm=<?=$id_bbm?>">BBM</a></li>
      <li class="breadcrumb-item active">Sub Items</li>
    </ol>
  </nav>
</div>
<p>Page ini digunakan untuk pencatatan Sub Item dan Pencetakan Label.</p>


<?php
# =======================================================================
# PROCESSORS 
# =======================================================================

?>
<h2>Item: <?=$kode_barang?></h2>
<table class="table table-hover">
  <tr>
    <td>Pengiriman ke</td>
    <td><?=$pengiriman_ke ?></td>
  </tr>
  <tr>
    <td>Nomor PO</td>
    <td><?=$kode_po?></td>
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
FROM tb_bbm_subitem a  
WHERE a.id_bbm_item=$id_bbm_item
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
    $last_kode_rak = $d['kode_rak'];

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
    <a href='?penerimaan&p=bbm_subitem&id_bbm=$id_bbm&id_po_item=$id_po_item&id_bbm_subitem=$id_bbm_subitem'>
      <div class='f12'>Subitem-$i</div>
      <div class=kecil>$qty_show</div>
    </a>
  </div>";
}

echo "<h1>
$last_no_lot = '';
$last_no_roll = '';
$last_jenis_bahan = '';
$last_kode_rak = '';

</h1>";


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
    <input type='hidden' name=id_bbm_item value='$id_bbm_item'>
    <input type='hidden' name=nomor value='$nomor'>
  </form>
  ";
}

$sisa_qty = $qty_diterima-$qty_subitem;
$sisa_qty_show = "Sisa QTY: <span id=sisa_qty>$sisa_qty</span>";

echo $div=='' ? div_alert('danger', 'Belum ada sub-item.') : "
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
  $kode_rak = '';
  
  $s = "SELECT a.*,
  (SELECT brand FROM tb_lokasi WHERE kode=a.kode_rak) brand 
  FROM tb_bbm_subitem a 
  WHERE a.id=$get_id_bbm_subitem";
  // echo $s;
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0) die(div_alert('danger','Data subitem tidak ditemukan'));

  $d = mysqli_fetch_assoc($q);
  $qty = $d['qty'];
  $no_lot = $d['no_lot'] ?? $last_no_lot;
  $no_roll = $d['no_roll'] ?? $last_no_roll;
  $jenis_bahan = $d['jenis_bahan'] ?? $last_jenis_bahan;
  $kode_rak = $d['kode_rak'] ?? $last_kode_rak;
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

  if($qty and $kode_rak){
    $btn_simpan = "<button class='btn btn-primary' name=btn_simpan >Update</button>";
  }else{
    if($last_kode_rak){
      $btn_simpan = "<button class='btn btn-primary' name=btn_simpan >Simpan</button>";
    }else{
      $btn_simpan = "<button class='btn btn-primary' id=btn_simpan disabled>Simpan</button>";
    }
  }

  echo "
  <div class='wadah mt2'>
    <form method=post>
      <input type='hidden' name=id_bbm_subitem value=$get_id_bbm_subitem>

      QTY ($satuan) $bintang
      <input class='form-control mb2' type=number min=0 max=$sisa_qty step=0.0001 required name=qty value=$qty>
      Lot Number
      <input class='form-control mb2' maxlength=20 name=no_lot value='$no_lot'>
      Roll Number
      <input class='form-control mb2' maxlength=20 name=no_roll value='$no_roll'>
      Jenis bahan
      <input class='form-control mb2' maxlength=100 name=jenis_bahan value='$jenis_bahan'>
      Kode Rak ($kategori) $bintang
      <input class='form-control mb2' id=kode_rak_show value='$kode_rak' disabled>
      <input class='form-control mb2 hideit' minlength=2 maxlength=20 name=kode_rak id=kode_rak value='$kode_rak' required>
      <span class='btn btn-secondary btn-sm' id=pilih_kode_rak>Pilih Kode Rak</span>
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

  if($qty && $no_lot && $no_roll && $kode_rak){

    $tgl = date('d-m-Y',strtotime($tanggal_terima));

    $bar = "<img width=300px alt='barcode' src='include/barcode.php?codetype=code39&size=50&text=".$kode_barang."&print=false'/>";
    $no_po_dll = "$kode_po $no_lot ($qty)$satuan $no_roll ($kode_rak $this_brand) $tgl";
    echo "<div id=blok_cetak class=hideit>";
    include 'cetak_label.php';

    $cetak_semua = $sisa_qty==0 ? "
      <form action=cetak.php method=post target=_blank>
        <button class='btn btn-success btn-sm' name=btn_cetak_semua_label value=$id_bbm_item>Cetak Semua Label</button>
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
    <div class='tengah alert alert-danger hideit'>
      Silahkan isi dahulu kolom QTY, No. Lot, No. Roll, dan Pilih Kode Rak !
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
      $('#kode_rak').val(val[0]);
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