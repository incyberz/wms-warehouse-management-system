<?php
if(isset($_POST['btn_simpan_qty_do'])){
  unset($_POST['btn_simpan_qty_do']);
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';
  foreach ($_POST as $key => $qty_do) {
    $arr = explode('__',$key);
    $s = "UPDATE tb_picking SET qty=$qty_do WHERE id=$arr[1]";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  }
  echo div_alert('success','Update QTY DO success.');
  jsurl();
}



$tr = "<tr><td colspan=100%><div class='alert alert-danger'>Belum ada item</div></td></tr>";
if($jumlah_item){
  $s = "SELECT a.*,
  a.qty as qty_do,
  b.no_lot,
  b.no_roll,
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
    WHERE p.id=a.id_sj_subitem AND p.is_fs is null) qty_stok

  FROM tb_picking a 
  JOIN tb_sj_subitem b ON a.id_sj_subitem=b.id 
  JOIN tb_sj_item c ON b.id_sj_item=c.id 
  JOIN tb_sj d ON c.kode_sj=d.kode 
  JOIN tb_bbm e ON d.kode=e.kode_sj  
  JOIN tb_barang f ON c.kode_barang=f.kode 
  JOIN tb_satuan g ON f.satuan=g.satuan 
  JOIN tb_lokasi h ON b.kode_lokasi=h.kode 
  WHERE a.kode_do='$kode_do'
  ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  
  $tr = '';
  $i=0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $id=$d['id'];
    $qty=floatval($d['qty']);
    $qty_stok=floatval($d['qty_stok']);
    $qty_subitem=floatval($d['qty_subitem']);
    $lot_info = $d['no_lot'] ? "<div>Lot: $d[no_lot]</div>" : "<div>Lot: $unset</div>";
    $roll_info = $d['no_roll'] ? "<div>Roll: $d[no_roll]</div>" : '';
    $satuan = $d['satuan'];
    $step = $d['step'];
    $is_fs = $d['is_fs'];
    $qty_do = $d['qty_do'];

    if($qty_do) $qty_do = floatval($qty_do);


    if($is_fs){
      $qty_fs = $qty_subitem;
      $qty = 0;
      // $tr_gradasi = 'biru';
      $qty_transit = 0;
    }else{
      $qty_transit = $qty_subitem-$qty_stok;
      // $tr_gradasi = '';
      $qty_fs = 0;
    }

    if($qty_transit) die("Pada Picking List QTY Transit tidak boleh > 0.<hr>id: $id");

    $qty_fs_show = $qty_fs ? "<td class='hijau'><div>$qty_fs $satuan</div></td>" : '<td>-</td>';
    $qty_stok_show = $qty_stok ? "<td class='hijau'><div>$qty_stok $satuan</div></td>" : '<td class="gradasi-merah">-</td>';
    $qty_stok_show = $qty_fs ? '<td>-</td>' : $qty_stok_show;

    $qty_stok_or_fs = $qty_stok;
    if($qty_stok<$qty_fs) $qty_stok_or_fs = $qty_fs;
    $stok_akhir = $qty_stok_or_fs - $qty_do;
    
    
    $tr .= "
      <tr>
        <td>$i</td>
        <td>
          <div>PO: $d[kode_po]</div>
          <div>$d[kode_barang]</div>
          <div class='f12 abu'>
            <div>$d[nama_barang]</div>
            <div>$d[keterangan_barang]</div>
          </div>
        </td>
        <td width=50px>
          $img_delete
        </td>
        <td>
          $d[kode_lokasi] ~ $d[brand]
          $lot_info
          $roll_info
        </td>
        $qty_fs_show
        $qty_stok_show
        <td width=100px>
          <input class='form-control qty_do' id=qty_do__$id name=qty_do__$id type=number step=$step max=$qty_stok_or_fs required value=$qty_do>
          <div class='f12 darkabu mt1'><span class='set_max pointer' id=set_max__$id>Set Max : <span id=qty_stok_or_fs__$id>$qty_stok_or_fs</span></span></div>
        </td>
        <td>
          <span id=stok_akhir__$id>$stok_akhir</span> $satuan
        </td>
      </tr>
    ";
  }


}

?>
<h2>Picking List</h2>
<form method="post">
  <table class="table">
    <thead>
      <th>No</th>
      <th colspan=2>ITEM</th>
      <th>INFO</th>
      <th>QTY FS</th>
      <th>QTY Real</th>
      <th>QTY DO</th>
      <th>Stok Akhir</th>
    </thead>
    <?=$tr?>
    <tr>
      <td colspan=6>
        <div class=p2>
          <span class='pointer btn_aksi' id=picking_list_add__toggle><?=$img_add?> Tambah Item</span>
        </div>
      </td>
      <td colspan=2>
        <button class="btn btn-primary" name=btn_simpan_qty_do id=btn_simpan_qty_do>Simpan QTY DO</button>
      </td>
    </tr>
  </table>
</form>

<?php 
echo '<div id=picking_list_add class="hideit wadah">';
include 'picking_list_add.php'; 
echo '</div>';
?>
<script>
  $(function(){

    function hitung_sa(id){
      let qty_stok_or_fs = $('#qty_stok_or_fs__'+id).text();
      let qty_do = $('#qty_do__'+id).val();

      $('#stok_akhir__'+id).text(qty_stok_or_fs-qty_do);
    }

    $('.set_max').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      let qty_stok_or_fs = $('#qty_stok_or_fs__'+id).text();

      $('#qty_do__'+id).val(qty_stok_or_fs);
      console.log(tid,id,qty_stok_or_fs);
      hitung_sa(id);
    });

    $('.qty_do').keyup(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      hitung_sa(id);
    });
  })
</script>