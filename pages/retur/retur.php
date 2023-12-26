<?php
$pesan_tambah = '';
// if(isset($_POST['btn_simpan'])){
//   $id = $_POST['id_sj_subitem'];
//   unset($_POST['id_sj_subitem']);
//   unset($_POST['btn_simpan']);
//   unset($_POST['keterangan_barang']);

//   $pairs = '__';
//   foreach ($_POST as $key => $value) {
//     $value = clean_sql($value);
//     $value = $value=='' ? 'NULL' : "'$value'";
//     $pairs .= ",$key = $value";
//   }
//   $pairs = str_replace('__,','',$pairs);

//   $s = "UPDATE tb_sj_subitem SET $pairs WHERE id=$id";
//   $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

//   $arr = explode('?',$_SERVER['REQUEST_URI']);
//   jsurl("?$arr[1]");
//   exit;
// }

// if(isset($_POST['btn_tambah_subitem']) || isset($_POST['btn_tambah_subitem_fs'])){

//   $id_sj_item = $_POST['id_sj_item'] ?? die(erid('id_sj_item'));

//   $is_fs = isset($_POST['btn_tambah_subitem_fs']) ? 1 : 'NULL';
//   $s = "INSERT INTO tb_sj_subitem (id_sj_item,nomor,is_fs) VALUES ($id_sj_item,$_POST[nomor],$is_fs)";
//   $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

//   $pesan_tambah = div_alert('success','Tambah Sub item sukses.');

//   $s = "SELECT id as id_sj_subitem FROM tb_sj_subitem WHERE id_sj_item=$id_sj_item ORDER BY nomor DESC LIMIT 1";
//   $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
//   $d = mysqli_fetch_assoc($q);
//   $id_sj_subitem = $d['id_sj_subitem'];

  
//   // $arr = explode('?',$_SERVER['REQUEST_URI']);
//   // jsurl("?$arr[1]");
//   jsurl("?penerimaan&p=bbm_subitem&id_sj_item=$id_sj_item&id_sj_subitem=$id_sj_subitem");
//   exit;
  
// }

$id_sj_item = $_GET['id_sj_item'] ?? die(erid('id_sj_item'));
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
  SELECT SUM(qty) FROM tb_sj_subitem WHERE id_sj_item=a.id and is_fs is null) qty_subitem,
(
  SELECT SUM(qty) FROM tb_sj_subitem WHERE id_sj_item=a.id and is_fs is not null) qty_subitem_fs

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
# =======================================================================
# PAGE TITLE & BREADCRUMBS 
# =======================================================================
?>
<div class="pagetitle">
  <h1>Retur Barang</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=data_sj">Data SJ</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=manage_sj&kode_sj=<?=$kode_sj?>">Manage SJ</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=bbm&kode_sj=<?=$kode_sj?>">BBM</a></li>
      <li class="breadcrumb-item"><a href="?master_penerimaan&&id=<?=$kode_barang?>&waktu=all_time">Master Penerimaan</a></li>
      <li class="breadcrumb-item active">Retur</li>
    </ol>
  </nav>
</div>
<p>Page ini digunakan untuk Proses Retur Barang.</p>


<?php
# =======================================================================
# ITEM BARANG INFO 
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
  <tr>
    <td>QTY Subitem</td>
    <td>
      <span id="qty_subitem"><?=$qty_subitem?></span> <?=$satuan?> 
    </td>
  </tr>

</table>

<?php
# =======================================================================
# RETUR ITEM 
# =======================================================================
echo "<h2 class='mb3 mt4'>Retur Item</h2>";

$get_id_sj_item = $_GET['id_sj_item'] ?? '';

if($get_id_sj_item!=''){

  $s = "SELECT a.*,
  c.kode as kode_barang,
  c.keterangan as keterangan_barang

  FROM tb_retur a 
  JOIN tb_sj_item b ON a.id_sj_item=b.id 
  JOIN tb_barang c ON b.kode_barang=c.kode 
  WHERE a.id_sj_item=$get_id_sj_item";
  // echo $s;
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)){
    $d = mysqli_fetch_assoc($q);
    $qty = $d['qty'];
    $info_lot = $d['info_lot'];
    $info_roll = $d['info_roll'];
    $kode_barang = $d['kode_barang'];
    $keterangan_barang = $d['keterangan_barang'];
    $kode_lokasi = $d['kode_lokasi'];
    $metode_qc = $d['metode_qc'];
    $alasan_retur = $d['alasan_retur'];
    $this_brand = $d['brand'];
    $qty = floatval($qty);
    $btn_simpan = "<button class='btn btn-primary' name=btn_simpan >Update</button>";
  }else{
    $btn_simpan = "<button class='btn btn-primary' name=btn_simpan >Simpan</button>";
    $qty = 0;
    $info_lot = '';
    $info_roll = '';
    $keterangan_barang = '';
    $kode_lokasi = '-';
    $brand = '-';
    $metode_qc = '-';
    $alasan_retur = '-';
  }




  $max_input_qty = $qty_subitem;

  echo "
  <div id=source_sj_item__$id_sj_item class='wadah mt2'>
    <form method=post>
      <input type='hidden' name=id_sj_item value=$get_id_sj_item>

      QTY Retur ($satuan) $bintang ~ <u class='pointer darkblue kecil' id=set_max_qty>Set Max: <span id=max_input_qty>$max_input_qty</span></u>
      <input class='form-control mb2' type=number min=0 max=$max_input_qty step=$step required name=qty id=qty value=$qty>
      Metode QC
      <input class='form-control mb2' maxlength=100 name=metode_qc value='$metode_qc'>
      Alasan Retur
      <input class='form-control mb2' maxlength=100 name=alasan_retur value='$alasan_retur'>
      Info Lot Number
      <input class='form-control mb2' maxlength=100 name=info_lot value='$info_lot'>
      Info Roll Number
      <input class='form-control mb2' maxlength=100 name=info_roll value='$info_roll'>
      Keterangan Barang | <a target=_blank href='?master&p=barang&keyword=$kode_barang'>Ubah</a>
      <input class='form-control mb2' maxlength=100 name=keterangan_barang value='$nama_barang / $keterangan_barang' disabled>
      Info Lokasi ($kategori) / Brand
      <input class='form-control mb2' id=kode_rak_show value='$kode_lokasi / $brand' disabled>
      <div class=mt3>
        $btn_simpan
      </div>
    </form>
  </div>
  ";
}

?>
<script>
  $(function(){
    $('#set_max_qty').click(function(){
      $('#qty').val($('#max_input_qty').text())
    });
  })
</script>