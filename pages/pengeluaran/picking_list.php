<?php
$belum_red = '<span class="red f12 miring">belum</span>';
$belum_abu = '<span class="abu f12 miring">belum</span>';
// allocate sangat penting untuk WH
$belum = $id_role == 3 ? $belum_red : $belum_abu;
$jumlah_valid_pick = 0;
$jumlah_valid_allocate = 0;

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
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';
  unset($_POST['btn_simpan_allocate']);
  echo 'Processing Update Allocate ...<hr>';
  foreach ($_POST as $key => $qty_allocate) {
    $qty_allocate = $qty_allocate ? $qty_allocate : 'NULL';
    $arr = explode('__', $key);
    $s = "UPDATE tb_pick SET 
    qty_allocate=$qty_allocate,
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
if ($jumlah_item) {
  include 'sql_pick.php';
  $s = "$sql_pick
  AND i.kode_do='$kode_do' 
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

    $allocator = $allocator ? ucwords(strtolower($allocator)) : $allocator;
    $picker = $picker ? ucwords(strtolower($picker)) : $picker;

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

    //pengeluaran
    $qty_pick = floatval($d['qty_pick']);
    $qty_allocate = floatval($d['qty_allocate']);
    $qty_pick_by_other = floatval($d['qty_pick_by_other']);
    $qty_allocate_by_other = floatval($d['qty_allocate_by_other']);











    # =======================================================
    # QTY CALCULATION
    # =======================================================
    // exception for hutangan
    if ($is_hutangan) {
      $qty_hutangan = $qty_pick;
      $qty_pick = 0;
    }

    $qty_datang = $qty_transit + $qty_tr_fs + $qty_qc + $qty_qc_fs - $qty_retur + $qty_ganti;
    $stok_available = $qty_qc + $qty_qc_fs - $qty_pick_by_other - $qty_pick - $qty_retur + $qty_ganti;

    // qty stok akhir
    if ($sebagai = 'WH') { // id_role=3
      $qty_pick_or_allocate = $qty_allocate;
      $stok_akhir = $qty_datang - $qty_allocate - $qty_allocate_by_other;
    } else { // id_role=7 PPIC
      $qty_pick_or_allocate = $qty_pick;
      $stok_akhir = $qty_datang - $qty_pick_or_allocate - $qty_pick_by_other;
    }
    $stok_akhir_show = number_format($stok_akhir, 2);

    // set max
    $qty_set_max_pick = $stok_available + $qty_pick;
    $max_pick = $is_hutangan ? '' : $qty_set_max_pick;
    $qty_set_max_allocate = $qty_pick;
    $max_allocate = $is_hutangan ? '' : $qty_set_max_allocate;

    # =======================================================
    # END QTY CALCULATION
    # =======================================================









    // qty_show
    $qty_allocate_show = $qty_allocate ? $qty_allocate : $belum;

    // lock / unlock allocate
    if ($d['boleh_allocate']) {
      $hide_unlock = 'hideit';
      $hide_lock = '';
    } else {
      $hide_unlock = '';
      $hide_lock = 'hideit';
    }

    $toggle_allocate_show = "
    <div class=mb1>
      <span id=unlock__$id_pick class='toggle_allocate $hide_unlock btn btn-danger btn-sm'>Unlock</span>
      <span id=lock__$id_pick class='toggle_allocate $hide_lock btn btn-success btn-sm'>Lock</span>
    </div>";

    // exception for pic1 as Head PPIC
    if ($username != 'pic1') {
      $toggle_allocate_show = '<span class="btn btn-secondary btn-sm" onclick="alert(\'Hanya Pic1 yang bisa Lock/Unlock.\')">Lock</span>';
    }

    // tanggal_show
    $tanggal_pick_show = date('d-M H:i', strtotime($tanggal_pick));
    $tanggal_allocate_show = date('d-M H:i', strtotime($tanggal_allocate));

    // other show
    $no_lot_show = $no_lot ? $no_lot : $null;
    $brand_show = $brand ? $brand : '';

    // repeat_show
    $repeat_show = $d['is_repeat'] ? '<span class="tebal consolas miring abu bg-yellow">repeat item</span>' : '';


    if ($qty_pick) $jumlah_valid_pick++;

    $cetak_qr_do = '';
    if ($qty_allocate) {
      $jumlah_valid_allocate++;
      $cetak_qr_do = "<a onclick='return confirm(\"Maaf, masih dalam tahap coding!\")' target=_blank class='btn btn-sm btn-success mt1 w-100' href='cetak_qr_pengeluaran.php?id_pick=$id_pick'>Cetak QR</a>";
    }

    // qty for input
    $qty_input_pick = $qty_pick ? $qty_pick : '';
    $qty_input_allocate = $qty_allocate ? $qty_allocate : '';

    // qty for input exception hutangan
    $qty_input_pick = $is_hutangan ? $qty_hutangan : $qty_input_pick;

    $gradasi = $qty_pick ? '' : 'merah';
    $hutangan_show = $is_hutangan ? "<span class='badge bg-red mb1 bold'>HUTANGAN</span>" : '';
    $fs_icon_show = $is_fs ? $fs_icon : '';





    if ($qty_allocate) {
      $btn_delete = "<span onclick='alert(\"Tidak dapat menghapus item yang sudah di allocate.\")'>$img_delete_disabled</span>";
      $disabled_input_qty_pick = 'disabled';
    } else {
      $btn_delete = "<button name=btn_delete_item_picking value=$id_pick style='border: none;background:none' onclick='return confirm(\"Yakin untuk hapus item ini?\")'>$img_delete</button>";
      $disabled_input_qty_pick = '';
    }

    if ($id_role == 3) {
      echolog('wh only', false);
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

      echolog(" qty_pick:$qty_pick");

      if ($qty_pick) {
        if ($d['boleh_allocate']) {
          if ($qty_allocate) {
            $set_max = '';
          } else {
            $set_max = "
              <div class='f12 darkabu mt1'>
                <span class='set_max_allocate pointer' id=set_max_allocate__$id_pick>
                  Set Max : <span id=qty_pick__$id_pick>$qty_pick</span>
                </span>
              </div>
            ";
          }
          $input_allocate = "
            <input 
              class='form-control qty_allocate' 
              id=qty_allocate__$id_pick 
              name=qty_allocate__$id_pick 
              type=number 
              step=$step 
              max='$max_allocate' 
              value='$qty_input_allocate'
            />
            $set_max
          ";
        } else {
          $input_allocate = "<span class='consolasa f10 red miring'>Line Locked<br>by $picker</span>";
        }
      } else {
        if ($is_hutangan) {
          $input_allocate = "<span class='consolasa f10 abu miring'>-</span>";
        } else {
          $input_allocate = "<span class='consolasa f10 red miring'>Belum Pick</span>";
        }
      }
    } // end if $id_role == 3

    if ($id_role == 7) {
      // pic only
      $ket = "
        <div class='miring abu f12'>Picked by: </div>
        <div class='f12'>$picker</div>
        <div class='abu f10'>$tanggal_pick_show</div>
      ";

      if ($is_hutangan) {
        // exception for hutangan view PPIC
        $input_allocate = '-';
        $set_max = '';
      } elseif ($qty_allocate) {
        $input_allocate = "
          $qty_allocate_show
          <div class='f12 abu'>by: $allocator</div>
          <div class='f10 abu'>$tanggal_allocate_show</div>
        ";
        $set_max = '';
      } else {
        $input_allocate = "$toggle_allocate_show $qty_allocate_show";
        $set_max = "
          <div class='f12 darkabu mt1'>
            <span class='set_max pointer' id=set_max_pick__$id_pick>
              Set Max : <span id=qty_set_max_pick__$id_pick>$qty_set_max_pick</span>
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
          max='$max_pick' 
          value='$qty_input_pick' 
          $disabled_input_qty_pick
        />
        $set_max
      ";
    } // end if role==7 PIC






















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
          <div>$d[kode_barang] $repeat_show $hutangan_show</div>
          <div class='f12 abu'>
            <div>Kode lama: $d[kode_lama]</div>
            <div>$d[nama_barang]</div>
            <div>$d[keterangan_barang]</div>
          </div>
        </td>
        <td class='pic_only' width=50px>
          $btn_delete
        </td>
        <td class='pic_only'>
          <span id=stok_available__$id_pick>$stok_available</span> 
          <span class=btn_aksi id=stok_available_info$id_pick" . "__toggle>$img_detail</span> $fs_icon_show
          <div id=stok_available_info$id_pick class='hideit wadah f12 mt1'>
            <div class=darkred>Transit: $qty_transit</div>
            <div class=darkred>Tr-FS: $qty_tr_fs</div>
            <div class=abu>Retur: $qty_retur</div>
            <div class=abu>Ganti: $qty_ganti</div>
            <div class=green>QTY QC: $qty_qc</div>
            <div class=green>QTY QC-FS: $qty_qc_fs</div>
          </div> 
        </td>
        <td class='pic_only darkred' id=qty_pick_by_other__$id_pick>$qty_pick_by_other</td>
        <td width=100px>
          $input_pick
        </td>
        <td class=wh_only>
          <div class=darkblue id=qty_datang__$id_pick>$qty_datang</div>
          <div class='darkred f12' id=qty_allocate_by_other__$id_pick>-$qty_allocate_by_other</div>
        </td>
        <td width=150px>
          $input_allocate
          $cetak_qr_do
        </td>
        <td><span id=stok_akhir__$id_pick>$stok_akhir_show</span></td>
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

// button Cetak Barcode dan Surat Jalan
$button_surat_jalan = '';
if ($id_role == 3) {
  $button_surat_jalan = "<a onclick='return confirm(\"Maaf, masih dalam tahap coding!\")' class='btn btn-success w-100' href='?pengeluaran&p=surat_jalan&kode_do=$kode_do&cat=$cat'>Cetak Surat Jalan</a>";
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
      <th class='pic_only'>Stok Available</th>
      <th class='darkred pic_only'>Picked <div class='f10 abu'>by other DO</div>
      </th>
      <th>QTY Pick</th>
      <th class='wh_only darkblue'>
        QTY Datang
        <div class="darkred f11">Allocate di Line lain</div>
      </th>
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
    <tr class=wh_only>
      <td colspan=100%>
        <?= $button_surat_jalan ?>
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
      let qty_datang = $('#qty_datang__' + id).text();
      let qty_pick_by_other = $('#qty_pick_by_other__' + id).text();
      let stok_akhir = 0;
      if (id_role == 3) {
        // for WH user ambil dari allocate
        let qty_allocate = $('#qty_allocate__' + id).val();
        stok_akhir = qty_datang - qty_allocate - qty_pick_by_other;
        console.log(stok_available, qty_allocate);
      } else if (id_role == 7) {
        // for PIC user
        let qty_pick = $('#qty_pick__' + id).val();
        stok_akhir = qty_datang - qty_pick - qty_pick_by_other;
      } else {
        alert('Invalid id_role at picking_list JS');
      }
      $('#stok_akhir__' + id).text(stok_akhir);
    }

    $('.set_max').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      let qty_set_max = $('#qty_set_max_pick__' + id).text();
      console.log(aksi, id, qty_set_max);

      $('#qty_pick__' + id).val(qty_set_max);
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

    $('.toggle_allocate').click(function() {
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id_pick = rid[1];
      console.log(aksi, id_pick);
      let link_ajax = `ajax/toggle_lock_line_do.php?aksi=${aksi}&id_pick=${id_pick}`
      $.ajax({
        url: link_ajax,
        success: function(a) {
          if (a.trim() == 'sukses') {
            $('#' + tid).hide();
            if (aksi == 'unlock') {
              $('#lock__' + id_pick).fadeIn();
            } else {
              $('#unlock__' + id_pick).fadeIn();
            }
          } else {
            alert(a);
          }
        }
      });

    });
  })
</script>