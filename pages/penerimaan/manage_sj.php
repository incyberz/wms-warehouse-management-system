<?php 
$judul = 'Manage Item'; 
set_title($judul); 
?>
<style>
  .no-bullet{list-style: none}
</style>
<div class="pagetitle">
  <h1><?=$judul?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=data_sj">Data SJ</a></li>
      <li class="breadcrumb-item active"><?=$judul?></li>
    </ol>
  </nav>
</div>
<?php
include 'include/arr_supplier.php';
# ==========================================
# SUPPLIER
# ==========================================
$opt = '';
foreach ($arr_supplier as $id => $nama) {
  $opt.= "<option value=$id>$nama</option>";
}
$select_supplier = "
  <select class='form-control' name=id_supplier>$opt</select>
";


$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';
$debug .= "<br>aksi: $aksi";
if(!$kode_sj){
  jsurl('?penerimaan&p=terima_barang');
}else{

  echo "<span class=hideit id=kode_sj>$kode_sj</span>";

  # ================================================================
  # HEADER SJ -->
  # ================================================================
  include 'surat_jalan_info.php';

  # ================================================================
  # ITEMS SJ -->
  # ================================================================
  include 'manage_sj_item.php';
}