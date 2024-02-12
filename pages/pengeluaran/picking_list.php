<?php
if(isset($_POST['btn_simpan_qty_pl'])){
  unset($_POST['btn_simpan_qty_pl']);
  echo 'Processing Update QTY Pick ...<hr>';
  foreach ($_POST as $key => $qty_pick) {
    $arr = explode('__',$key);
    $s = "UPDATE tb_picking SET qty=$qty_pick WHERE id=$arr[1]";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  }
  echo div_alert('success','Update QTY Pick success.');
  jsurl();
}

if(isset($_POST['btn_delete_item_picking'])){
  echo 'Processing Delete DO Item ...<hr>';
  $s = "DELETE FROM tb_picking WHERE id=$_POST[btn_delete_item_picking]";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  unset($_POST['btn_delete_item_picking']);
  echo div_alert('success','Delete DO Item success.');
  jsurl();
}



$tr = "<tr><td colspan=100%><div class='alert alert-danger'>Belum ada item</div></td></tr>";
$jumlah_item_valid = 0;
if($jumlah_item){
  $s = "SELECT a.*,
  a.qty as qty_pick,
  b.no_lot,
  b.kode_lokasi,
  b.is_fs,
  b.qty as qty_subitem,
  d.kode_po,
  f.kode as kode_barang,
  f.nama as nama_barang, 
  f.keterangan as keterangan_barang,
  g.satuan, 
  g.step, 
  h.brand, 
  (
    SELECT p.qty FROM tb_sj_subitem p
    JOIN tb_retur q ON p.id=q.id
    WHERE p.id=a.id_sj_subitem AND p.is_fs is null) qty_qc,
  (
    SELECT SUM(p.qty) FROM tb_picking p 
    WHERE p.id != a.id 
    AND p.id_sj_subitem = a.id_sj_subitem) qty_pick_by,
  (
    SELECT count(1) FROM tb_roll 
    WHERE id_sj_subitem = b.id) count_roll

  FROM tb_picking a 
  JOIN tb_sj_subitem b ON a.id_sj_subitem=b.id 
  JOIN tb_sj_item c ON b.id_sj_item=c.id 
  JOIN tb_sj d ON c.kode_sj=d.kode 
  JOIN tb_bbm e ON d.kode=e.kode_sj  
  JOIN tb_barang f ON c.kode_barang=f.kode 
  JOIN tb_satuan g ON f.satuan=g.satuan 
  JOIN tb_lokasi h ON b.kode_lokasi=h.kode 
  JOIN tb_do i ON a.id_do=i.id 
  WHERE i.kode_do='$kode_do' 
  AND i.id_kategori=$id_kategori 
  ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  
  $tr = '';
  $i=0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $id=$d['id'];
    $qty=floatval($d['qty']);
    $qty_qc=floatval($d['qty_qc']);
    $qty_subitem=floatval($d['qty_subitem']);
    $lot_info = $d['no_lot'] ? "<div>Lot: $d[no_lot]</div>" : "<div>Lot: $unset</div>";
    $roll_info = $d['count_roll'] ? "<div>Count Roll: $d[count_roll]</div>" : '-';
    $satuan = $d['satuan'];
    $step = $d['step'];
    $is_fs = $d['is_fs'];
    $qty_pick = $d['qty_pick'];
    $qty_pick_by = $d['qty_pick_by'];

    if($qty_pick) $qty_pick = floatval($qty_pick);
    if($qty_pick_by) $qty_pick_by = floatval($qty_pick_by);
    if($qty_pick) $jumlah_item_valid++;


    if($is_fs){
      $qty_fs = $qty_subitem;
      $qty = 0;
      // $tr_gradasi = 'biru';
      $qty_transit = 0;
    }else{
      $qty_transit = $qty_subitem-$qty_qc;
      // $tr_gradasi = '';
      $qty_fs = 0;
    }

    if($qty_transit) die("Pada Picking List QTY Transit tidak boleh > 0.<hr>id: $id");

    $qty_fs_show = $qty_fs ? "<td class='hijau'><div>$qty_fs $satuan</div></td>" : '<td>-</td>';
    $qty_qc_show = $qty_qc ? "<td class='hijau'><div>$qty_qc $satuan</div></td>" : '<td class="gradasi-merah">-</td>';
    $qty_qc_show = $qty_fs ? '<td>-</td>' : $qty_qc_show;

    $qty_qc_or_fs = $qty_qc;
    if($qty_qc<$qty_fs) $qty_qc_or_fs = $qty_fs;
    $qty_pick_for_input = $qty_pick ? $qty_pick : '';
    
    $gradasi = $qty_pick ? '' : 'merah';
    
    $stok_real = $qty_fs + $qty_qc - $qty_pick_by;
    $stok_akhir = $stok_real - $qty_pick;

    $hutangan_show = $d['is_hutangan'] ? "HUTANGAN" : '';
    $max = $d['is_hutangan'] ? '' : $stok_real;
    
    $tr .= "
      <tr class='gradasi-$gradasi'>
        <td>$i</td>
        <td>
          <div>PO: $d[kode_po]</div>
          <div>$d[kode_barang] <span class='badge bg-red mb1'>$hutangan_show</span></div>
          <div class='f12 abu'>
            <div>$d[nama_barang]</div>
            <div>$d[keterangan_barang]</div>
          </div>
        </td>
        <td width=50px>
          <button name=btn_delete_item_picking value=$id style='border: none;background:none' onclick='return confirm(\"Yakin untuk hapus item ini?\")'>$img_delete</button>
        </td>
        <td>
          $d[kode_lokasi] ~ $d[brand]
          $lot_info
          $roll_info
        </td>
        $qty_fs_show
        $qty_qc_show
        <td class=darkred>$qty_pick_by</td>
        <td>$stok_real</td>
        <td width=100px>
          <input class='form-control qty_pick' id=qty_pick__$id name=qty_pick__$id type=number step=$step max=$max required value='$qty_pick_for_input'>
          <div class='f12 darkabu mt1'><span class='set_max pointer' id=set_max__$id>Set Max : <span id=stok_real__$id>$stok_real</span></span></div>
        </td>
        <td>
          <span id=stok_akhir__$id>$stok_akhir</span> $satuan
        </td>
      </tr>
    ";
  }
}

$id_role=7; //zzz debug
// $jumlah_item_valid=2; //zzz debug

// PIC only
$btn_verif_disabled = ($jumlah_item_valid && $jumlah_item_valid==$jumlah_item AND $id_role==7) ? '' : 'disabled';


if($jumlah_item){
  $valid_user_show = $id_role==7 ? "
    <span class=green>Anda berhak melakukan verifikasi.</span>
  " : "
    <span class='darkred'>Hanya <b>PIC Staf</b> yang dapat melakukan verifikasi</span> | 
    <a href='?master&p=user' target=_blank>Lihat Users</a>
  ";
  $invalid = $jumlah_item - $jumlah_item_valid;
  $valid_item_show = $jumlah_item_valid==$jumlah_item ? "
    <span class=green>Valid item: $jumlah_item_valid of $jumlah_item $img_check</span>
  " : "
    <span class=red>Masih ada $invalid QTY Pick yang kosong/tidak valid.</span>
  ";

  $btn_simpan_qty = "<button class='btn btn-primary' name=btn_simpan_qty_pl id=btn_simpan_qty_pl>Simpan QTY</button>";

  $tr_verif = "
    <tr>
      <td colspan=100%>
        <div class='mt1 mb1 kecil abu f12'>
          <ul>
            <li>$valid_item_show</li>
            <li>$valid_user_show</li>
            <li>
              Verifikasi bertujuan agar Picking List tidak bisa diubah/ditambah oleh user lain.
            </li>

          </ul>
        </div>
        <button class='btn btn-success' name=btn_verifikasi_pl id=btn_verifikasi_pl $btn_verif_disabled>Verifikasi Picking List</button>

      </td>
    </tr>
  ";
}else{
  $btn_simpan_qty = '<span class="f12 miring abu">no items</span>';
  $tr_verif = '';
}

?>
<h2>Picking List <?=$jenis_barang?></h2>
<form method="post">
  <table class="table">
    <thead>
      <th>No</th>
      <th colspan=2>ITEM</th>
      <th>INFO</th>
      <th>QTY FS</th>
      <th>QTY QC</th>
      <th class=darkred>Pick by<br><span class='f12'>Other DO</span></th>
      <th>Stok Real</th>
      <th>QTY Pick</th>
      <th>Stok Akhir</th>
    </thead>
    <?=$tr?>
    <tr>
      <td colspan=6>
        <div class=p2>
          <span class='pointer btn_aksi' id=picking_list_add__toggle><?=$img_add?> Tambah Item <?=$jenis_barang?></span>
        </div>
      </td>
      <td colspan=2>
        <?=$btn_simpan_qty?>
      </td>
    </tr>
  </table>
</form>

<div id=picking_list_add class="hideit wadah">
  <?php include 'picking_list_add.php'; ?>
</div>

<table class="table">
  <?=$tr_verif?>
</table>


<script>
  $(function(){

    function hitung_sa(id){
      let stok_real = $('#stok_real__'+id).text();
      let qty_pick = $('#qty_pick__'+id).val();

      $('#stok_akhir__'+id).text(stok_real-qty_pick);
    }

    $('.set_max').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      let stok_real = $('#stok_real__'+id).text();

      $('#qty_pick__'+id).val(stok_real);
      console.log(tid,id,stok_real);
      hitung_sa(id);
    });

    $('.qty_pick').keyup(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      hitung_sa(id);
    });
  })
</script>