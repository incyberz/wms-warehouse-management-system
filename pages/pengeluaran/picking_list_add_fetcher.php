<?php 
include '../../conn.php';
$unset = '<span class="kecil miring red consolas">unset</span>';

$keyword = $_GET['keyword'] ?? '';
$id_kategori = $_GET['id_kategori'] ?? die(erid('id_kategori'));
$id_do = $_GET['id_do'] ?? die(erid('id_do'));
if(!$id_kategori) die(erid('id_kategori::empty'));
$jenis_barang = $id_kategori==1 ? 'Aksesoris' : 'Fabric';


$sql_keyword = $keyword=='' ? '1' : "
(
  e.kode LIKE '%$keyword%' 
  OR e.nama LIKE '%$keyword%' 
  OR e.keterangan LIKE '%$keyword%' 
  OR c.kode_po LIKE '%$keyword%' 
)
";

$sql_from = "
  FROM tb_sj_subitem a 
  JOIN tb_sj_item b ON a.id_sj_item=b.id 
  JOIN tb_sj c ON b.kode_sj=c.kode 
  JOIN tb_bbm d ON c.kode=d.kode_sj  
  JOIN tb_barang e ON b.kode_barang=e.kode 
  JOIN tb_satuan f ON e.satuan=f.satuan 
  JOIN tb_lokasi g ON a.kode_lokasi=g.kode 
  LEFT JOIN tb_picking h ON a.id=h.id_sj_subitem AND h.id_do='$id_do'
  WHERE h.id is null 
  AND e.id_kategori = $id_kategori 
  AND $sql_keyword  
";



$s = "SELECT 1 $sql_from ";
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
  WHERE p.id=a.id AND p.is_fs is null) qty_terima,
(
  SELECT sum(p.qty) FROM tb_picking p 
  WHERE p.id_sj_subitem=a.id 
  ) qty_pick_by ,
(
  SELECT p.qty FROM tb_retur p 
  WHERE p.id=a.id 
  ) qty_retur,
(
  SELECT p.tanggal_retur FROM tb_retur p 
  WHERE p.id=a.id 
  ) tanggal_retur,
(
  SELECT p.qty FROM tb_terima_retur p 
  JOIN tb_retur q ON p.id=q.id  
  WHERE q.id=a.id 
  ) qty_balik 

$sql_from 


LIMIT 10 

";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_tampil = mysqli_num_rows($q);
if(mysqli_num_rows($q)==0){
  $tr = '<tr><td colspan=100%><div class="alert alert-danger">Data subitem '.$jenis_barang.' tidak ditemukan.</div></td></tr>';
}else{
  $tr = '';
  $i = 0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $id=$d['id'];
    $is_fs=$d['is_fs'];
    $satuan=$d['satuan'];
    $tanggal_retur=$d['tanggal_retur'];
    $qty=floatval($d['qty']);
    $qty_terima=floatval($d['qty_terima']);
    $qty_pick_by=floatval($d['qty_pick_by']);
    $qty_retur=floatval($d['qty_retur']);
    $qty_balik=floatval($d['qty_balik']);
    $lot_info = $d['no_lot'] ? "<div>Lot: $d[no_lot]</div>" : "<div>Lot: $unset</div>";

    // transit or after QC
    $qty_transit = 0;
    $qty_transit_fs = 0;
    $qty_qc = 0;
    $qty_qc_fs = 0;
    if($d['tanggal_retur']==''){ //belum QC
      if($is_fs){
        $qty_transit_fs = $qty;
      }else{
        $qty_transit = $qty;
      }
    }else{ //sudah QC
      if($is_fs){
        $qty_qc_fs = $qty;
      }else{
        $qty_qc = $qty;
      }
    }    

    //stok akhir
    $stok_akhir = $qty_qc + $qty_qc_fs - $qty_pick_by;

    $nol = '<span class="abu miring kecil">0</span>';
    $qty_transit_show = $qty_transit ? "<span class='tebal red'>PO: $qty_transit</span>" : $nol;
    $qty_transit_fs_show = $qty_transit_fs ? "<span class='tebal purple'>FS: $qty_transit_fs</span>" : $nol;
    $qty_qc_show = $qty_qc ? "<span class='tebal darkblue'>PO: $qty_qc</span>" : $nol;
    $qty_qc_fs_show = $qty_qc_fs ? "<span class='tebal hijau'>FS: $qty_qc_fs</span>" : $nol;
    $stok_akhir_show = $stok_akhir ? "<span class='tebal biru'>$stok_akhir $satuan</span>" : $nol;
    $qty_pick_by_show = $qty_pick_by ? "<span class='tebal darkred'>$qty_pick_by $satuan</span>" : $nol;

    $kode_lokasi_brand = "$d[kode_lokasi] <span class='abu f12'>$d[brand]</span>";
    $lokasi_show = ($qty_pick_by || $stok_akhir) ? "<span class='darkblue f16'>$kode_lokasi_brand</span>" : "<span class=abu>$kode_lokasi_brand</span>";

    $btn_add = $stok_akhir ? "<div id=div_btn_add__$id><button class='btn btn-success btn-sm btn_add mb1' id=btn_add__$id>Add</button></div>" : '<button class="btn btn-secondary btn-sm mb1" disabled>Add</button>';
    $fs_show = $is_fs ? ' <b class="f14 ml1 mr1 biru p1 pr2 br5" style="display:inline-block;background:green;color:white">FS</b>' : '';
    $btn_hutangan = !$stok_akhir ? "<div id=div_btn_hutangan__$id><button class='btn btn-danger btn-sm btn_add' id=btn_hutangan__$id>Hutangan</button></div>" : '';

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
          <div>Lokasi: $d[kode_lokasi] ~ $d[brand] $fs_show</div>
          $lot_info
          
        </td>
        <td>
          <div>$qty_transit_show</div>
          <div>$qty_transit_fs_show</div>
        </td>
        <td>
          <div>$qty_qc_show</div>
          <div>$qty_qc_fs_show</div>
        </td>
        <td>$qty_pick_by_show</td>
        <td>
          <div>$stok_akhir_show </div>
          <div class='abu f12'>$lokasi_show</div>
        </td>
        <td>
          $btn_add $btn_hutangan
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
      <th>Transit</th>
      <th>After QC</th>
      <th class=darkred>Pick by<br><span class=f12>Other DO</span></th>
      <th>Stok Akhir</th>
      <th>Add</th>
    </thead>

    $tr
  </table>$info_dibatasi~~~$jumlah_tampil~~~$jumlah_records
";

?>