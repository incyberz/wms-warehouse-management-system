<?php 
include '../../conn.php';
$unset = '<span class="kecil miring red consolas">unset</span>';

$keyword = $_GET['keyword'] ?? '';
$sql_keyword = $keyword=='' ? '1' : "
(
  e.kode LIKE '%$keyword%' 
  OR e.nama LIKE '%$keyword%' 
  OR e.keterangan LIKE '%$keyword%' 
  OR c.kode_po LIKE '%$keyword%' 
)
";
$s = "SELECT 1  
FROM tb_sj_subitem a 
JOIN tb_sj_item b ON a.id_sj_item=b.id 
JOIN tb_sj c ON b.kode_sj=c.kode 
JOIN tb_bbm d ON c.kode=d.kode_sj  
JOIN tb_barang e ON b.kode_barang=e.kode 
JOIN tb_satuan f ON e.satuan=f.satuan 
JOIN tb_lokasi g ON a.kode_lokasi=g.kode 
LEFT JOIN tb_picking h ON a.id=h.id_sj_subitem
WHERE h.id is null 
AND $sql_keyword   
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_records = mysqli_num_rows($q);

$s = "SELECT 
a.*,
c.kode_po,
e.kode as kode_barang,
e.nama as nama_barang, 
e.keterangan as keterangan_barang,
f.satuan, 
f.step, 
g.brand, 
(
  SELECT p.qty FROM tb_sj_subitem p
  JOIN tb_retur q ON p.id=q.id
  WHERE p.id=a.id AND p.is_fs is null) qty_stok

FROM tb_sj_subitem a 
JOIN tb_sj_item b ON a.id_sj_item=b.id 
JOIN tb_sj c ON b.kode_sj=c.kode 
JOIN tb_bbm d ON c.kode=d.kode_sj  
JOIN tb_barang e ON b.kode_barang=e.kode 
JOIN tb_satuan f ON e.satuan=f.satuan 
JOIN tb_lokasi g ON a.kode_lokasi=g.kode 
LEFT JOIN tb_picking h ON a.id=h.id_sj_subitem
WHERE h.id is null 
AND $sql_keyword   
LIMIT 10
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_tampil = mysqli_num_rows($q);
if(mysqli_num_rows($q)==0){
  $tr = '<tr><td colspan=100%><div class="alert alert-danger">Data subitem penerimaan tidak ditemukan.</div></td></tr>';
}else{
  $tr = '';
  $i = 0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $id=$d['id'];
    $is_fs=$d['is_fs'];
    $satuan=$d['satuan'];
    $qty=floatval($d['qty']);
    $qty_stok=floatval($d['qty_stok']);
    $lot_info = $d['no_lot'] ? "<div>Lot: $d[no_lot]</div>" : "<div>Lot: $unset</div>";
    $roll_info = $d['no_roll'] ? "<div>Roll: $d[no_roll]</div>" : '';

    if($is_fs){
      $qty_fs = $qty;
      $qty = 0;
      // $tr_gradasi = 'biru';
      $qty_transit = 0;
    }else{
      $qty_transit = $qty-$qty_stok;
      // $tr_gradasi = '';
      $qty_fs = 0;
    }

    $qty_transit_show = $qty_transit ? "<td class='merah'><div>$qty_transit $satuan</div></td>" : '<td>-</td>';
    $qty_fs_show = $qty_fs ? "<td class='gradasi-hijau'><div>$qty_fs $satuan</div></td>" : '<td>-</td>';
    $qty_stok_show = $qty_stok ? "<td class='gradasi-hijau'><div>$qty_stok $satuan</div></td>" : '<td class="gradasi-merah">-</td>';
    $qty_stok_show = $qty_fs ? '<td>-</td>' : $qty_stok_show;

    $btn_add = ($qty_stok || $qty_fs) ? "<div id=div_btn_add__$id><button class='btn btn-success btn-sm btn_add' id=btn_add__$id>Add</button></div>" : '<button class="btn btn-secondary btn-sm" disabled>Add</button>';

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
        <td>
          <div>Lokasi: $d[kode_lokasi] ~ $d[brand]</div>
          $lot_info
          $roll_info
        </td>
        $qty_transit_show
        $qty_fs_show
        $qty_stok_show
        <td>
          $btn_add
        </td>
      </tr>
    ";
  }

}

$info_dibatasi = $jumlah_records>10 ? "<div class='alert alert-info mt2'>Hanya ditampilkan $jumlah_tampil dari $jumlah_records total records. Silahkan masukan keyword dengan lebih spesifik.</div>" : '';

echo "
  <table class=table>
    <thead>
      <th>No</th>
      <th>ITEM</th>
      <th>INFO</th>
      <th>QTY Transit</th>
      <th>QTY FS</th>
      <th>QTY Real</th>
    </thead>

    $tr
  </table>$info_dibatasi~~~$jumlah_tampil~~~$jumlah_records
";

?>