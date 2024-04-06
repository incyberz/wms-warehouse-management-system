<?php
$belum_red = '<span class="red f12 miring">belum</span>';
$belum_abu = '<span class="abu f12 miring">belum</span>';
// allocate sangat penting untuk WH
$belum = $id_role == 3 ? $belum_red : $belum_abu;

$judul = 'Retur DO';
set_title($judul);




















# =============================================================
# PROCESSORS
# =============================================================
if (isset($_POST['btn_tambah_retur_do'])) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';

  $s = "INSERT INTO tb_retur_do (
    id_pick,
    qty,
    alasan_retur
  ) VALUES (
    $_POST[btn_tambah_retur_do],
    $_POST[qty],
    '$_POST[alasan]'
  )";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));


  echo div_alert('success', 'Tambah Retur DO success.');
  jsurl();
}

if (isset($_POST['btn_delete_item_picking'])) {
  echo 'Processing Delete DO Item ...<hr>';
  // $s = "DELETE FROM tb_pick WHERE id=$_POST[btn_delete_item_picking]";
  // $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  // unset($_POST['btn_delete_item_picking']);
  // echo div_alert('success', 'Delete DO Item success.');
  jsurl();
}




















$tr = "<tr><td colspan=100%><div class='alert alert-danger'>Belum ada item</div></td></tr>";
$jumlah_item_valid = 0;
if ($jumlah_item) {
  $s = "SELECT 
  a.id as id_pick,
  a.is_hutangan,
  a.qty_allocate,
  a.tanggal_allocate,
  b.no_lot,
  b.kode_lokasi,
  b.is_fs,
  b.tmp_qty,
  c.kode_sj,
  -- b.qty as qty_kumulatif_item,
  d.kode_po,
  f.kode as kode_barang,
  f.nama as nama_barang, 
  f.keterangan as keterangan_barang,
  g.satuan, 
  g.step, 
  h.brand, 
  (
    SELECT count(1) FROM tb_retur_do 
    WHERE id_pick = a.id) count_retur_do,
  (
    SELECT count(1) FROM tb_roll 
    WHERE id_kumulatif = b.id) count_roll,
  (
    SELECT nama FROM tb_user 
    WHERE id = a.allocate_by) allocator

    

  FROM tb_pick a 
  JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id 
  JOIN tb_sj_item c ON b.id_sj_item=c.id 
  JOIN tb_sj d ON c.kode_sj=d.kode 
  JOIN tb_barang f ON c.kode_barang=f.kode 
  JOIN tb_satuan g ON f.satuan=g.satuan 
  JOIN tb_lokasi h ON b.kode_lokasi=h.kode 
  JOIN tb_do i ON a.id_do=i.id 
  WHERE i.kode_do='$kode_do' 
  AND i.id_kategori=$id_kategori 

  ORDER BY a.is_hutangan, a.tanggal_pick  
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $count_pick = mysqli_num_rows($q);

  $tr = '';
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $id_pick = $d['id_pick'];
    $no_lot = $d['no_lot'];
    $kode_lokasi = $d['kode_lokasi'];
    $brand = $d['brand'];
    $is_hutangan = $d['is_hutangan'];
    $count_retur_do = $d['count_retur_do'];
    $count_roll = $d['count_roll'];
    $is_fs = $d['is_fs'];
    $satuan = $d['satuan'];
    $step = $d['step'];
    $allocator = $d['allocator'];

    $allocator = ucwords(strtolower($allocator));

    // tanggal
    $tanggal_allocate = $d['tanggal_allocate'];

    //pengeluaran
    $qty_allocate = floatval($d['qty_allocate']);


    // tanggal_show
    $tanggal_allocate_show = date('d-M H:i', strtotime($tanggal_allocate));

    // other show
    $no_lot_show = $no_lot ? $no_lot : $null;
    $brand_show = $brand ? $brand : '';


    $fs_icon_show = $is_fs ? $fs_icon : '';
    if ($qty_allocate) {
      $ket = "
        <div class='miring abu f12'>Allocated by: </div>
        <div class='f12'>$allocator</div>
        <div class='abu f10'>$tanggal_allocate_show</div>
      ";
    } else {
      $ket = '';
    }

    $form_retur_do = "
      <form method=post class=wadah>
        <div class=sub_form>Form Tambah Retur DO</div>
        <div class=flexy>
          <div>
            <input required type=number step=$step min=$step max=$qty_allocate placeholder='qty...' class='form-control form-control-sm' name=qty>
          </div>
          <div>
            <input required minlength=3 maxlength=100 placeholder='alasan...' class='form-control form-control-sm' name=alasan>
          </div>
          <div>
            <button class='btn btn-success btn-sm' name=btn_tambah_retur_do value=$id_pick>Retur</button>
          </div>
        </div>
      </form>
    ";






















    # =======================================================
    # FINAL TR LOOP
    # =======================================================
    $tr .= "
      <tr>
        <td>$i</td>
        <td>
          $d[kode_po]
          <div class='f12 abu'>Lot: $no_lot_show</div>
          <div class='f12 abu'>Lokasi: $kode_lokasi $brand_show</div>
          <div class='f12 abu'>Roll: $count_roll</div>
        </td>
        <td width=20%>
          <div>$d[kode_barang]</div>
          <div class='f12 abu'>
            <div>$d[nama_barang]</div>
            <div>$d[keterangan_barang]</div>
          </div>
        </td>
        <td>$satuan</td>
        <td>
          $qty_allocate
          $ket
        </td>
        <td>$form_retur_do</td>
      </tr>
    ";
  }
}






































echo "
<h2><a href='?pengeluaran&p=buat_do&kode_do=$kode_do&cat=$cat'>$img_prev Kembali ke Picking List $jenis_barang</a></h2>
<div class='sub_form'>Sub Form Retur DO</div>
<table class='table'>
  <thead>
    <th>No</th>
    <th>PO</th>
    <th>ITEM</th>
    <th>UOM</th>
    <th>Allocate</th>
    <th>Retur DO</th>
  </thead>
  $tr
</table>
";
?>


































<script>
  $(function() {

  })
</script>