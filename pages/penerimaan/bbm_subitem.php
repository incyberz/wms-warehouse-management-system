<?php
$pesan_tambah = '';
if(isset($_POST['btn_tambah_subitem'])){
  $s = "INSERT INTO tb_bbm_subitem (id_bbm_item) VALUES ($_POST[id_bbm_item])";
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
d.kode as no_po,
e.nama as nama_barang,
e.kode as kode_barang,
e.satuan,
(
  SELECT SUM(qty) FROM tb_bbm_subitem WHERE id_bbm_item=a.id) qty_subitem

FROM tb_bbm_item a 
JOIN tb_bbm b ON a.id_bbm=b.id 
JOIN tb_po_item c ON a.id_po_item=c.id 
JOIN tb_po d ON c.id_po=d.id 
JOIN tb_barang e ON c.id_barang=e.id 
WHERE a.id_po_item=$id_po_item 
AND a.id_bbm=$id_bbm
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);

$nama_barang = $d['nama_barang'];
$kode_barang = $d['kode_barang'];
$id_bbm_item = $d['id_bbm_item'];
$no_po = $d['no_po'];
$no_bbm = $d['no_bbm'];
$qty_diterima = $d['qty_diterima'];
$qty_subitem = $d['qty_subitem'];
$satuan = $d['satuan'];
$pengiriman_ke = $d['pengiriman_ke'];

$qty_diterima = str_replace('.0000','',$qty_diterima);

?>
<div class="pagetitle">
  <h1>BBM Sub Items</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?po">PO Home</a></li>
      <li class="breadcrumb-item"><a href="?po&p=terima_barang">Cari PO</a></li>
      <li class="breadcrumb-item"><a href="?po&p=terima_barang&no_po=<?=$no_po?>&id_bbm=<?=$id_bbm?>">BBM</a></li>
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
    <td><?=$no_po?></td>
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
    <td><?=$qty_diterima?> <?=$satuan?></td>
  </tr>
</table>
<h2>Sub Items</h2>
<?php
echo $pesan_tambah;

$s = "SELECT a.*,a.id as id_bbm_subitem 
FROM tb_bbm_subitem 
WHERE id_bbm_item=$id_bbm_item
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$div = '';
while($d=mysqli_fetch_assoc($q)){
  $id_bbm_subitem=$d['id_bbm_subitem'];

  $div.= "<div class=wadah>$id_bbm_subitem</div>";
}

echo $div=='' ? div_alert('danger', 'Belum ada sub-item.') : "
  <div class=flexy>
    $div
  </div>
";


if($qty_diterima==$qty_subitem){
  // qty habis

}else{
  // boleh nambah
  ?>
  <form method=post>
    <button class="btn btn-primary btn-sm" name=btn_tambah_subitem>Tambah Sub item</button>
    <input type="hiddena" name=id_bbm_item value='<?=$id_bbm_item?>'>
  </form>


  <?php 
}
