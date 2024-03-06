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
  unset($_POST['btn_simpan_qty_pick']);
  echo 'Processing Update QTY Pick ...<hr>';
  foreach ($_POST as $key => $qty_pick) {
    $arr = explode('__', $key);
    $qty_pick = $qty_pick ? $qty_pick : 0;
    $s = "UPDATE tb_pick SET qty=$qty_pick WHERE id=$arr[1]";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  }
  echo div_alert('success', 'Update QTY Pick success.');
  jsurl();
}

if (isset($_POST['btn_simpan_allocate'])) {
  unset($_POST['btn_simpan_allocate']);
  echo 'Processing Update Allocate ...<hr>';
  foreach ($_POST as $key => $qty_pick) {
    $arr = explode('__', $key);
    $s = "UPDATE tb_pick SET 
    qty_allocate=$qty_pick,
    tanggal_allocate=CURRENT_TIMESTAMP,
    allocate_by=$id_user 
    WHERE id=$arr[1]";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  }
  echo div_alert('success', 'Update Allocate success.');
  jsurl();
}

if (isset($_POST['btn_delete_item_picking'])) {
  echo 'Processing Delete DO Item ...<hr>';
  $s = "DELETE FROM tb_pick WHERE id=$_POST[btn_delete_item_picking]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  unset($_POST['btn_delete_item_picking']);
  echo div_alert('success', 'Delete DO Item success.');
  jsurl();
}



















$debug .= "<br>jumlah_item:$jumlah_item";

$tr = "<tr><td colspan=100%><div class='alert alert-danger'>Belum ada item</div></td></tr>";
$jumlah_item_valid = 0;
if ($jumlah_item) {
  $s = "SELECT 
  a.id as id_pick,
  a.qty as qty_pick,
  a.is_hutangan,
  a.qty_allocate,
  a.tanggal_pick,
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
  echo '<pre>';
  var_dump($s);
  echo '</pre>';
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
    $tanggal_pick = $d['tanggal_pick'];
    $tanggal_allocate = $d['tanggal_pick'];

    // pemasukan
    $qty_transit = floatval($d['qty_transit']);
    $qty_tr_fs = floatval($d['qty_tr_fs']);
    $qty_qc = floatval($d['qty_qc']);
    $qty_qc_fs = floatval($d['qty_qc_fs']);
    $qty_retur = floatval($d['qty_retur']);
    $qty_ganti = floatval($d['qty_ganti']);

    if (strpos($d['kode_sj'], '-999')) $qty_qc = $d['tmp_qty'];


    //pengeluaran
    $qty_pick = floatval($d['qty_pick']);
    $qty_allocate = floatval($d['qty_allocate']);
    $qty_pick_by_other = floatval($d['qty_pick_by_other']);

    // qty calculation
    $qty_datang = $qty_transit + $qty_tr_fs + $qty_qc + $qty_qc_fs - $qty_retur + $qty_ganti;
    $stok_real = $qty_qc + $qty_qc_fs - $qty_pick_by_other - $qty_retur + $qty_ganti;

    $qty_pick_or_allocate = $id_role == 7 ? $qty_pick : $qty_allocate;
    $stok_akhir = $is_hutangan ? $stok_real :  $stok_real - $qty_pick_or_allocate;



    // qty_show
    $qty_allocate_show = $qty_allocate ? $qty_allocate : $belum;

    // tanggal_show
    $tanggal_pick_show = date('d-M H:i', strtotime($tanggal_pick));
    $tanggal_allocate_show = date('d-M H:i', strtotime($tanggal_allocate));

    // other show
    $no_lot_show = $no_lot ? $no_lot : $null;
    $brand_show = $brand ? $brand : '';


    if ($qty_pick) $jumlah_item_valid++;

    // qty for input
    $qty_pick_for_input = $qty_pick ? $qty_pick : '';
    $qty_allocate_for_input = $qty_allocate ? $qty_allocate : '';

    $gradasi = $qty_pick ? '' : 'merah';
    $hutangan_show = $is_hutangan ? "<span class='badge bg-red mb1 bold'>HUTANGAN</span>" : '';
    $max = $is_hutangan ? '' : $stok_real;
    $fs_icon_show = $is_fs ? $fs_icon : '';





    if ($qty_allocate) {
      $btn_delete = "<span onclick='alert(\"Tidak dapat menghapus item yang sudah di allocate.\")'>$img_delete_disabled</span>";
      $disabled_input_qty_pick = 'disabled';
    } else {
      $btn_delete = "<button name=btn_delete_item_picking value=$id_pick style='border: none;background:none' onclick='return confirm(\"Yakin untuk hapus item ini?\")'>$img_delete</button>";
      $disabled_input_qty_pick = '';
    }

    if ($id_role == 3) {
      // wh only
      $input_pick = "
        <div id=qty_pick__$id_pick>$qty_pick</div>
        <div class='abu f12'>by: $picker</div>
        <div class='abu f10'>$tanggal_pick_show</div>
      ";

      if ($qty_allocate) {
        $ket = "
          <div class='miring abu f12'>Allocated by: </div>
          <div class='f12'>$allocator</div>
          <div class='abu f10'>$tanggal_allocate_show</div>
        ";
      } else {
        $ket = '';
      }

      if ($stok_real and $qty_pick) {
        $input_allocate = "
          <input 
            class='form-control qty_allocate' 
            id=qty_allocate__$id_pick 
            name=qty_allocate__$id_pick 
            type=number 
            step=$step 
            max='$max' 
            value='$qty_allocate_for_input'
          />
          <div class='f12 darkabu mt1'>
            <span class='set_max_allocate pointer' id=set_max_allocate__$id_pick>
              Set Max : <span id=qty_pick__$id_pick>$qty_pick</span>
            </span>
          </div>
        ";
      } else {
        $input_allocate = '-';
      }
    }

    if ($id_role == 7) {
      // pic only
      $ket = "
        <div class='miring abu f12'>Picked by: </div>
        <div class='f12'>$picker</div>
        <div class='abu f10'>$tanggal_pick_show</div>
      ";

      if ($qty_allocate) {
        $input_allocate = "
          $qty_allocate_show
          <div class='f12 abu'>by: $allocator</div>
          <div class='f10 abu'>$tanggal_allocate_show</div>
        ";
        $set_max = '';
      } else {
        $input_allocate = $qty_allocate_show;
        $set_max = "
          <div class='f12 darkabu mt1'>
            <span class='set_max pointer' id=set_max__$id_pick>
              Set Max : <span id=stok_real__$id_pick>$stok_real</span>
            </span>
          </div>
        ";
      }

      $qty_pick_class = $is_hutangan ? 'qty_hutangan' : 'qty_pick';
      $input_pick = "
        <input 
          class='form-control $qty_pick_class' 
          id=qty_pick__$id_pick 
          name=qty_pick__$id_pick 
          type=number 
          step=$step 
          max='$max' 
          value='$qty_pick_for_input' 
          $disabled_input_qty_pick
        />
        $set_max
      ";
    }






















    # =======================================================
    # FINAL TR LOOP
    # =======================================================
    $tr .= "
      <tr class='gradasi-$gradasi'>
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
        <td class='pic_only' width=50px>
          $btn_delete
        </td>
        <td>
          <span id=stok_real__$id_pick>$stok_real</span> 
          <span class=btn_aksi id=stok_real_info$id_pick" . "__toggle>$img_detail</span> $fs_icon_show
          <div id=stok_real_info$id_pick class='hideit wadah f12 mt1'>
            <div class=darkred>Transit: $qty_transit</div>
            <div class=darkred>Tr-FS: $qty_tr_fs</div>
            <div class=abu>Retur: $qty_retur</div>
            <div class=abu>Ganti: $qty_ganti</div>
            <div class=green>QTY QC: $qty_qc</div>
            <div class=green>QTY QC-FS: $qty_qc_fs</div>
          </div> 
        </td>
        <td class=darkred>$qty_pick_by_other</td>
        <td width=100px>
          $input_pick
        </td>
        <td width=100px>
          $input_allocate
        </td>
        <td><span id=stok_akhir__$id_pick>$stok_akhir</span></td>
        <td>$satuan</td>
        <td>$ket</td>
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





































?>
<h2>Picking List <?= $jenis_barang ?></h2>
<div class="sub_form">Sub Form Picking List</div>
<form method="post">
  <table class="table">
    <thead>
      <th>No</th>
      <th>PO</th>
      <th>ITEM</th>
      <th class='pic_only'>&nbsp;</th>
      <th>Stok Available</th>
      <th class=darkred>Picked <div class='f10 abu'>by other DO</div>
      </th>
      <th>QTY Pick</th>
      <th>Allocate</th>
      <th>Stok Akhir</th>
      <th>UOM</th>
      <th>Keterangan</th>
    </thead>
    <?= $tr ?>
    <tr class=pic_only>
      <td colspan=100%>
        <?= $btn_simpan_picking_list ?>
      </td>
    </tr>
    <tr class=pic_only>
      <td colspan=100%>
        <div class=p2>
          <span class='pointer btn_aksi' id=picking_list_add__toggle><?= $img_add ?> Tambah Item <?= $jenis_barang ?></span>
        </div>
      </td>
    </tr>
    <tr class=wh_only>
      <td colspan=100%>
        <?= $btn_simpan_allocate ?>
      </td>
    </tr>
  </table>
</form>

<div id=picking_list_add class="hideit wadah gradasi-kuning">
  <?php include 'picking_list_add.php'; ?>
</div>




































<script>
  $(function() {

    let id_role = parseInt($('#id_role').text());

    function hitung_sa(id) {
      if (!id) {
        alert('Invalid id at hitung_sa at picking list JS');
        return;
      }
      if (id_role == 3) {
        // for WH user ambil dari allocate
        let stok_real = $('#stok_real__' + id).text();
        let qty_allocate = $('#qty_allocate__' + id).val();
        $('#stok_akhir__' + id).text(stok_real - qty_allocate);
        console.log(stok_real, qty_allocate);
      } else if (id_role == 7) {
        // for PIC user
        let stok_real = $('#stok_real__' + id).text();
        let qty_pick = $('#qty_pick__' + id).val();
        $('#stok_akhir__' + id).text(stok_real - qty_pick);
      } else {
        alert('Invalid id_role at picking_list JS');
      }
    }

    $('.set_max').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      let stok_real = $('#stok_real__' + id).text();

      $('#qty_pick__' + id).val(stok_real);
      console.log(tid, id, stok_real);
      hitung_sa(id);
    });

    $('.set_max_allocate').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      let qty_pick = $('#qty_pick__' + id).text();

      $('#qty_allocate__' + id).val(qty_pick);
      console.log(tid, id, qty_pick);
      hitung_sa(id);
    });

    $('.qty_pick').change(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      hitung_sa(id);
    });

    $('.qty_allocate').change(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      console.log('qty_allocate keyup', id);
      hitung_sa(id);
    });
  })
</script>