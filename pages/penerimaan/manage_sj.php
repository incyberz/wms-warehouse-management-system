<style>
  .no-bullet{list-style: none}
</style>
<div class="pagetitle">
  <h1>Penerimaan Surat Jalan</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=data_sj">Data SJ</a></li>
      <li class="breadcrumb-item active">Manage SJ</li>
    </ol>
  </nav>
</div>
<?php
set_title('Penerimaan Surat jalan');

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
if($kode_sj==''){
  if(isset($_POST['btn_buat_po'])){
    $kode = clean_sql($_POST['kode']);
    $id_supplier = clean_sql($_POST['id_supplier']);
    $s = "INSERT INTO tb_sj (kode,id_supplier) VALUES ('$kode',$id_supplier)";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    jsurl("?penerimaan&p=manage_sj&kode_sj=$kode");
    exit;
  }

  $kode_sj = date('Ymd').'01-MTL';
  ?>
  <form method=post>
    <div class="wadah gradasi-hijau" style='max-width:500px;'>
      <h2 class='abu f20'>Create Purchase Order</h2>
      <hr>
      Nomor SJ
      <input name=kode type="text" class="form-control mt1 mb2 consolas f30 upper" value="<?=$kode_sj?>">
      Supplier
      <div class="mt1 mb2">
        <?=$select_supplier?>
      </div>
      <button class='btn btn-primary w-100' name=btn_buat_po>ZZZ Buat SJ Baru</button>
    </div>
  </form>


  <?php
}else{

  # ================================================================
  # HEADER SJ -->
  # ================================================================
  include 'surat_jalan_info.php';

  # ================================================================
  # ITEMS SJ -->
  # ================================================================
  include 'item_surat_jalan.php';
}