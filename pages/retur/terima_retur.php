<?php
$pesan_tambah = '';
if(isset($_POST['btn_terima_retur'])){
  unset($_POST['btn_terima_retur']);
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';

  $pairs = '__';
  $koloms = '__';
  $values = '__';
  foreach ($_POST as $key => $value) {
    $value = clean_sql($value);
    $value = ($value==''||$value=='-') ? 'NULL' : "'$value'";
    if($key!='id') $pairs .= ",$key = $value";
    $koloms .= ",$key";
    $values .= ",$value";
  }
  $pairs = str_replace('__,','',$pairs);
  $koloms = str_replace('__,','',$koloms);
  $values = str_replace('__,','',$values);

  $s = "INSERT INTO tb_ganti ($koloms) VALUES ($values) ON DUPLICATE KEY UPDATE $pairs ";
  echo $s;
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  // $arr = explode('?',$_SERVER['REQUEST_URI']);
  // jsurl("?$arr[1]");
  // exit;
}


$id_retur = $_GET['id_retur'] ?? die(erid('id_retur'));
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
d.keterangan as keterangan_barang,
d.satuan,
e.kode as kategori,
e.nama as nama_kategori,
f.step,
(
  SELECT SUM(qty) FROM tb_sj_kumulatif WHERE id_retur=a.id and is_fs is null) qty_kumulatif_item,
(
  SELECT qty FROM tb_retur WHERE id=a.id) qty_retur

FROM tb_sj_item a 
JOIN tb_sj b ON a.kode_sj=b.kode 
JOIN tb_bbm c ON b.kode=c.kode_sj 
JOIN tb_barang d ON a.kode_barang=d.kode 
JOIN tb_kategori e ON d.id_kategori=e.id 
JOIN tb_satuan f ON d.satuan=f.satuan 
WHERE a.id=$id_retur 
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);

$nama_barang = $d['nama_barang'];
$kode_barang = $d['kode_barang'];
$keterangan_barang = $d['keterangan_barang'];
$id_sj_item = $d['id_sj_item'];
$kode_po = $d['kode_po'];
$kode_sj = $d['kode_sj'];
$no_bbm = $d['no_bbm'];
$qty_po = $d['qty_po'];
$qty_datang = $d['qty_datang'];
$qty_kumulatif_item = $d['qty_kumulatif_item'];
$qty_retur = $d['qty_retur'];
$satuan = $d['satuan'];
$step = $d['step'];
// $pengiriman_ke = $d['pengiriman_ke'];
$kategori = $d['kategori'];
$nama_kategori = $d['nama_kategori'];
$tanggal_masuk = $d['tanggal_masuk'];

$qty_po = floatval($qty_po);
$qty_datang = floatval($qty_datang);
$qty_kumulatif_item = floatval($qty_kumulatif_item);
$qty_retur = floatval($qty_retur);


$nama_kategori = ucwords(strtolower($nama_kategori));

$is_lebih = $qty_po<$qty_datang ? 1 : 0;
$qty_tr_fs = 0;
if($is_lebih){
  $qty_tr_fs = $qty_datang-$qty_po;
  $tr_free_supplier = "
    <tr class=blue>
      <td>QTY Lebih (Free Supplier)</td>
      <td>$qty_tr_fs $satuan</td>
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
  <h1>Penerimaan Retur</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=data_sj">Data SJ</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=manage_sj&kode_sj=<?=$kode_sj?>">Manage SJ</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=bbm&kode_sj=<?=$kode_sj?>">BBM</a></li>
      <li class="breadcrumb-item"><a href="?master_penerimaan&id=<?=$kode_barang?>&waktu=all_time">Master Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?retur&id_sj_item=<?=$id_sj_item?>">Retur</a></li>
      <li class="breadcrumb-item active">Penerimaan Retur</li>
    </ol>
  </nav>
</div>
<p>Page ini digunakan untuk Proses Penerimaan Retur Barang.</p>


<?php
# =======================================================================
# ITEM BARANG INFO 
# =======================================================================
?>
<h2>Info Retur</h2>
<table class="table table-hover">
  <tr>
    <td>Surat Jalan</td>
    <td><?=$kode_sj?></td>
  </tr>
  <tr>
    <td>Nomor BBM</td>
    <td><?=$no_bbm?></td>
  </tr>
  <!-- <tr>
    <td>Nama Barang</td>
    <td><?=$nama_barang?></td>
  </tr> -->
  <!-- <tr>
    <td>QTY PO</td>
    <td>
      <span id="qty_po"><?=$qty_po?></span> 
      <span id="satuan"><?=$satuan?></span> 
    </td>
  </tr>
  <tr>
    <td>QTY Datang</td>
    <td>
      <span id="qty_datang"><?=$qty_datang?></span> <?=$satuan?> 
    </td>
  </tr> -->
  <?=$tr_free_supplier?>
  <tr>
    <td>QTY Item Kumulatif</td>
    <td>
      <span id="qty_kumulatif_item"><?=$qty_kumulatif_item?></span> <?=$satuan?> 
    </td>
  </tr>
  <tr>
    <td>QTY Retur</td>
    <td>
      <span id="qty_retur"><?=$qty_retur?></span> <?=$satuan?> 
    </td>
  </tr>

</table>

<?php
# =======================================================================
# RETUR ITEM 
# =======================================================================
echo "<h2 class='mb3 mt4'>Penerimaan Retur: $kode_barang | $nama_kategori</h2>";

$get_id_retur = $_GET['id_retur'] ?? '';

if($get_id_retur!=''){

  $s = "SELECT a.*,
  c.kode as kode_barang,
  c.keterangan as keterangan_barang

  FROM tb_ganti a 
  JOIN tb_sj_item b ON a.id=b.id 
  JOIN tb_barang c ON b.kode_barang=c.kode 
  WHERE a.id=$get_id_retur";
  // echo $s;
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)){
    $d = mysqli_fetch_assoc($q);
    $qty = $d['qty'];
    $kode_barang = $d['kode_barang'];
    $keterangan_barang = $d['keterangan_barang'];
    $qty = floatval($qty);
    $btn_terima_retur = "<button class='btn btn-secondary' name=btn_terima_retur >Update Penerimaan Retur</button>";
  }else{
    $btn_terima_retur = "<button class='btn btn-primary' name=btn_terima_retur >Simpan Penerimaan Retur</button>";
    $qty = '';
  }


  # =======================================================================
  # INFO LOKASI dan BRAND
  # =======================================================================
  $s = "SELECT a.kode_lokasi,b.brand FROM tb_sj_kumulatif a 
  JOIN tb_lokasi b ON a.kode_lokasi=b.kode 
  WHERE id_retur=$id_retur";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $info_kode_lokasi='';
  $info_brand='';
  while($d=mysqli_fetch_assoc($q)){
    if(!strpos("salt$info_kode_lokasi",$d['kode_lokasi'])){
      if($info_kode_lokasi!='') $info_kode_lokasi.= ', ';
      $info_kode_lokasi .= $d['kode_lokasi'];
    }
    if(!strpos("salt$info_brand",$d['brand'])){
      if($info_brand!='') $info_brand.= ', ';
      $info_brand .= $d['brand'];
    } 
  }





  $max_input_qty = $qty_retur;

  echo "
  <div id=source_sj_item__$id_retur class='wadah mt2'>
    <form method=post>
      <input type='hidden' name=id value=$get_id_retur>

      QTY Penerimaan Retur ($satuan) $bintang ~ <u class='pointer darkblue kecil' id=set_max_qty>Set Max: <span id=max_input_qty>$max_input_qty</span></u>
      <input class='form-control mb1' type=number min=0 max=$max_input_qty step=$step required name=qty id=qty value=$qty>
      <div class='mb2 abu f12'><b>Catatan:</b> QTY Retur akan terkunci jika sudah ada Penerimaan Retur untuk retur ini.</div>
      Keterangan Barang | <a target=_blank href='?master&p=barang&keyword=$kode_barang'>Ubah</a>
      <input class='form-control mb2' maxlength=100 name=keterangan_barang value='$nama_barang / $keterangan_barang' disabled>
      Info Lokasi ($kategori) / Brand
      <input class='form-control mb2' id=kode_rak_show value='$info_kode_lokasi / $info_brand' disabled>
      <div class=mt3>
        $btn_terima_retur
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