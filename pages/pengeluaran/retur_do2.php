<?php
$belum_red = '<span class="red f12 miring">belum</span>';
$belum_abu = '<span class="abu f12 miring">belum</span>';
// allocate sangat penting untuk WH
$belum = $id_role == 3 ? $belum_red : $belum_abu;

# =============================================================
# HAK AKSES PIC ONLY
# =============================================================
# =============================================================
# HAK AKSES WH ONLY
# =============================================================
// Jam masuk before Allocate zzz 
// jam keluar after Allocate





















# =============================================================
# PROCESSORS
# =============================================================
if (isset($_POST['btn_simpan_qty_pick'])) {
  // unset($_POST['btn_simpan_qty_pick']);
  // echo 'Processing Update QTY Pick ...<hr>';
  // foreach ($_POST as $key => $qty_pick) {
  //   $arr = explode('__', $key);
  //   $qty_pick = $qty_pick ? $qty_pick : 0;
  //   $s = "UPDATE tb_pick SET qty=$qty_pick WHERE id=$arr[1]";
  //   $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  // }
  echo div_alert('success', 'Update QTY Pick success.');
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



















$debug .= "<br>jumlah_item:$jumlah_item";

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
    -- =========================================
    -- PEMASUKAN
    -- =========================================
  (
    SELECT sum(p.qty) FROM tb_roll p 
    JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
    WHERE q.id=a.id_kumulatif 
    AND q.is_fs is null
    AND q.tanggal_qc is null) qty_transit,
  (
    SELECT sum(p.qty) FROM tb_roll p 
    JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
    WHERE q.id=a.id_kumulatif 
    AND q.is_fs is not null
    AND q.tanggal_qc is null) qty_tr_fs,

  (
    SELECT sum(p.qty) FROM tb_roll p 
    JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
    WHERE q.id=a.id_kumulatif 
    AND q.is_fs is null
    AND q.tanggal_qc is not null) qty_qc,
  (
    SELECT sum(p.qty) FROM tb_roll p 
    JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
    WHERE q.id=a.id_kumulatif 
    AND q.is_fs is not null
    AND q.tanggal_qc is not null) qty_qc_fs,
  (
    SELECT SUM(p.qty) FROM tb_retur p 
    JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
    WHERE q.id=a.id_kumulatif 
    ) qty_retur, 
    -- ALL RETUR = RETUR REG + RETUR FS  

  (
    SELECT SUM(p.qty) FROM tb_ganti p
    JOIN tb_retur q ON p.id_retur=q.id  
    JOIN tb_sj_kumulatif r ON q.id_kumulatif=r.id 
    WHERE r.id=a.id_kumulatif 
    ) qty_ganti, 
    -- ALL GANTI



    -- =========================================
    -- PENGELUARAN
    -- =========================================
  (
    SELECT SUM(p.qty) FROM tb_pick p 
    WHERE p.id != a.id 
    AND p.id_kumulatif = a.id_kumulatif) qty_pick_by_other,
  (
    SELECT count(1) FROM tb_roll 
    WHERE id_kumulatif = b.id) count_roll,


    -- =========================================
    -- PICKER | ALLOCATOR
    -- =========================================
  (
    SELECT nama FROM tb_user 
    WHERE id = a.pick_by) picker,
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
    $count_roll = $d['count_roll'];
    $is_fs = $d['is_fs'];
    $satuan = $d['satuan'];
    $step = $d['step'];
    $allocator = $d['allocator'];
    $picker = $d['picker'];

    $allocator = ucwords(strtolower($allocator));
    $picker = ucwords(strtolower($picker));

    // tanggal
    // $tanggal_pick = $d['tanggal_pick'];
    $tanggal_allocate = $d['tanggal_allocate'];

    // pemasukan
    $qty_transit = floatval($d['qty_transit']);
    $qty_tr_fs = floatval($d['qty_tr_fs']);
    $qty_qc = floatval($d['qty_qc']);
    $qty_qc_fs = floatval($d['qty_qc_fs']);
    $qty_retur = floatval($d['qty_retur']);
    $qty_ganti = floatval($d['qty_ganti']);

    // if (strpos($d['kode_sj'], '-999')) $qty_qc = $d['tmp_qty'];


    //pengeluaran
    // $qty_pick = floatval($d['qty_pick']);
    $qty_allocate = floatval($d['qty_allocate']);
    // $qty_pick_by_other = floatval($d['qty_pick_by_other']);

    // qty calculation
    $qty_datang = $qty_transit + $qty_tr_fs + $qty_qc + $qty_qc_fs - $qty_retur + $qty_ganti;
    // $stok_real = $qty_qc + $qty_qc_fs - $qty_pick_by_other - $qty_retur + $qty_ganti;

    $qty_pick_or_allocate = $id_role == 7 ? $qty_pick : $qty_allocate;
    // // $stok_akhir = $is_hutangan ? $stok_real :  $stok_real - $qty_pick_or_allocate;



    // qty_show
    $qty_allocate_show = $qty_allocate ? $qty_allocate : $belum;

    // tanggal_show
    // $tanggal_pick_show = date('d-M H:i', strtotime($tanggal_pick));
    $tanggal_allocate_show = date('d-M H:i', strtotime($tanggal_allocate));

    // other show
    $no_lot_show = $no_lot ? $no_lot : $null;
    $brand_show = $brand ? $brand : '';


    // if ($qty_pick) $jumlah_item_valid++;

    // qty for input
    // $qty_pick_for_input = $qty_pick ? $qty_pick : '';
    $qty_allocate_for_input = $qty_allocate ? $qty_allocate : '';

    // $gradasi = $qty_pick ? '' : 'merah';
    $hutangan_show = $is_hutangan ? "<span class='badge bg-red mb1 bold'>HUTANGAN</span>" : '';
    // $max = $is_hutangan ? '' : $stok_real;
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
      <form method=post>
        <div class=flexy>
          <div>
            <input required type=number step=$step min=$step max=$qty_allocate placeholder='qty...' class='form-control' name=qty>
          </div>
          <div>
            <input required minlength=3 maxlength=100 placeholder='alasan...' class='form-control' name=alasan>
          </div>
          <div>
            <button class='btn btn-success btn-sm' name=btn_tambah_retur_do>Retur</button>
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
        <td>
          <div>$d[kode_barang] $hutangan_show</div>
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


// button simpan for PIC || WH
$btn_simpan_allocate = '&nbsp;';
$btn_simpan_picking_list = '';
if ($jumlah_item) {
  if ($id_role == 7) { // PIC only
    $btn_simpan_picking_list = "<button class='btn btn-primary w-100' name=btn_simpan_qty_pick id=btn_simpan_qty_pick>Simpan Picking List</button>";
  } elseif ($id_role == 3) { // WH Only
    $btn_simpan_allocate = "<button class='btn btn-primary w-100' name=btn_simpan_allocate id=btn_simpan_allocate>Simpan Allocate</button>";
  } else {
    die('Invalid role for btn_simpan_allocate');
  }
} else {
  $btn_simpan_picking_list = '<span class="f12 miring abu">belum ada items</span>';
}




































echo "
<h2>Picking List $jenis_barang</h2>
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
  <tr class=wh_only>
    <td colspan=100%>
      $btn_simpan_allocate
    </td>
  </tr>
</table>
";
?>


































<script>
  $(function() {

  })
</script>