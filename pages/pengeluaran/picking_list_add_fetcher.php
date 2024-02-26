<?php 
include '../../conn.php';
$unset = '<span class="f12 miring red consolas">unset</span>';
$null = '<span class="f12 miring abu consolas">null</span>';
$img_detail = '<img class="zoom pointer" src="assets/img/icons/detail.png" alt="detail" height=20px>';

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
  FROM tb_sj_kumulatif a 
  JOIN tb_sj_item b ON a.id_sj_item=b.id 
  JOIN tb_sj c ON b.kode_sj=c.kode 
  JOIN tb_barang e ON b.kode_barang=e.kode 
  JOIN tb_satuan f ON e.satuan=f.satuan 
  JOIN tb_lokasi g ON a.kode_lokasi=g.kode 
  LEFT JOIN tb_pick h ON a.id=h.id_kumulatif AND h.id_do='$id_do'
  WHERE h.id is null 
  AND e.id_kategori = $id_kategori 
  AND $sql_keyword  
";



$s = "SELECT 1 $sql_from ";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_records = mysqli_num_rows($q);

$s = "SELECT 
a.id as id_kumulatif,
a.tanggal_qc,
a.is_fs,
a.no_lot,
a.kode_lokasi,
c.kode_po,
e.kode as kode_barang,
e.nama as nama_barang, 
e.keterangan as keterangan_barang,
f.satuan, 
f.step, 
g.brand, 
(
  SELECT sum(p.qty) FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
  WHERE q.id=a.id 
  AND q.is_fs is null
  AND q.tanggal_qc is null) qty_transit,
(
  SELECT sum(p.qty) FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
  WHERE q.id=a.id 
  AND q.is_fs is not null
  AND q.tanggal_qc is null) qty_tr_fs,

(
  SELECT sum(p.qty) FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
  WHERE q.id=a.id 
  AND q.is_fs is null
  AND q.tanggal_qc is not null) qty_qc,
(
  SELECT sum(p.qty) FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
  WHERE q.id=a.id 
  AND q.is_fs is not null
  AND q.tanggal_qc is not null) qty_qc_fs,

(
  SELECT sum(p.qty) FROM tb_pick p 
  WHERE p.id_kumulatif=a.id 
  AND is_hutangan is null) qty_pick_by_other ,
(
  SELECT sum(p.qty) FROM tb_retur p 
  WHERE p.id_kumulatif=a.id 
  ) qty_retur,
(
  SELECT sum(p.qty) FROM tb_ganti p 
  JOIN tb_retur q ON p.id_retur=q.id  
  WHERE q.id_kumulatif=a.id 
  ) qty_ganti 

$sql_from 


ORDER BY qty_qc DESC, qty_qc_fs DESC  

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
    $id_kumulatif=$d['id_kumulatif'];
    $is_fs=$d['is_fs'];
    $satuan=$d['satuan'];
    $no_lot=$d['no_lot'];
    $kode_lokasi=$d['kode_lokasi'];

    // qty pemasukan
    $qty_transit=$d['qty_transit'];
    $qty_tr_fs=$d['qty_tr_fs'];
    $qty_qc=$d['qty_qc'];
    $qty_qc_fs=$d['qty_qc_fs'];

    // qty pengeluaran
    $qty_pick_by_other=$d['qty_pick_by_other'];
    $qty_retur=$d['qty_retur'];
    $qty_ganti=$d['qty_ganti'];

    //stok akhir
    $stok_available = $qty_qc + $qty_qc_fs -$qty_retur+$qty_ganti - $qty_pick_by_other;

    // qty show
    $nol = '<span class="abu miring kecil">0</span>';
    $qty_transit_show = $qty_transit ? "<span class='tebal red'>$qty_transit</span>" : $nol;
    $qty_tr_fs_show = $qty_tr_fs ? "<span class='tebal purple'>$qty_tr_fs</span>" : $nol;
    $qty_qc_show = $qty_qc ? "<span class='tebal darkblue'>$qty_qc</span>" : $nol;
    $qty_qc_fs_show = $qty_qc_fs ? "<span class='tebal hijau'>$qty_qc_fs</span>" : $nol;
    $stok_available_show = $stok_available ? "<span class='tebal biru'>$stok_available</span>" : $nol;
    $qty_pick_by_other_show = $qty_pick_by_other ? "<span class='tebal darkred'>$qty_pick_by_other</span>" : $nol;
    $qty_retur_show = $qty_retur ? "<span class='abu'>$qty_retur</span>" : $nol;
    $qty_ganti_show = $qty_ganti ? "<span class='abu'>$qty_ganti</span>" : $nol;


    $no_lot_show = $no_lot ? $no_lot : $null;

    $kode_lokasi_brand = "$d[kode_lokasi] <span class='abu f12'>$d[brand]</span>";
    $lokasi_show = ($qty_pick_by_other || $stok_available) ? "<span class='darkblue f16'>$kode_lokasi_brand</span>" : "<span class=abu>$kode_lokasi_brand</span>";

    $btn_add = $stok_available ? "<div id=div_btn_add__$id_kumulatif><button class='btn btn-success btn-sm btn_add mb1 w-100' id=btn_add__$id_kumulatif>Add</button></div>" : '<button class="btn btn-secondary btn-sm mb1 w-100" disabled>Add</button>';
    $fs_show = $is_fs ? ' <b class="f14 ml1 mr1 biru p1 pr2 br5" style="display:inline-block;background:green;color:white">FS</b>' : '';
    $btn_hutangan = !$stok_available ? "<div id=div_btn_hutangan__$id_kumulatif><button class='btn btn-danger btn-sm btn_add w-100' id=btn_hutangan__$id_kumulatif>Hutangan</button></div>" : '';

    $tr .= "
      <tr>
        <td>$i</td>
        <td>
          $d[kode_po]
          <div class='f12 abu'>Lot: $no_lot_show</div>
          <div class='f12 abu'>Lokasi: $kode_lokasi</div>
        </td>
        <td>
          <div>$d[kode_barang]</div>
          <div class='f12 abu'>
            <div>$d[nama_barang]</div>
            <div>$d[keterangan_barang]</div>
          </div>
        </td>
        <td>
          $stok_available_show 
          <span class=toggle id=toggle__stok_available_info$id_kumulatif>$img_detail</span>
          <div id=stok_available_info$id_kumulatif class='hideit bordered br5 p2 f12 mt1 gradasi-abu'>
            <ul class=m0>
              <li>Transit: $qty_transit_show</li>
              <li>Tr-FS: $qty_tr_fs_show</li>
              <li>QC: $qty_qc_show</li>
              <li>QC-FS: $qty_qc_fs_show</li>
              <li>Retur: $qty_retur_show</li>
              <li>Ganti: $qty_ganti_show</li>
              <li>Pick by other DO: $qty_pick_by_other_show</li>
            </ul>
          </div>
        </td>
        <td style='background:#efe'>$btn_add $btn_hutangan</td>
      </tr>
    ";
  }

}

$info_dibatasi = $jumlah_records>10 ? "<div class='alert alert-info mt2'>Hanya ditampilkan $jumlah_tampil dari $jumlah_records total records. Silahkan masukan keyword dengan lebih spesifik.</div>" : '';

echo "
  <div class='sub_form mt2'>Hasil Pencarian: Picking List Add Fetcher</div>
  <table class=table>
    <thead>
      <th>No</th>
      <th>PO</th>
      <th>ID</th>
      <th>Stok Available</th>
      <th style='background:#cfc' class=tengah>Aksi</th>
    </thead>

    $tr
  </table>$info_dibatasi~~~$jumlah_tampil~~~$jumlah_records
";

?>