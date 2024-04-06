<?php
$kode_do = $_GET['kode_do'] ?? die('Kode DO belum terdefinisi');
$cat = $_GET['cat'] ?? 'aks';
$id_kategori = $cat == 'aks' ? 1 : 2;

$judul = 'Surat Jalan Pengeluaran';
set_title($judul);
echo "<h1 class='f20'>$judul</h1>";

$tr = "<tr><td colspan=100%><div class='alert alert-danger'>Belum ada item</div></td></tr>";
$s = "SELECT 
a.id as id_pick,
a.qty_allocate,
b.no_lot,
b.kode_lokasi,
b.is_fs,
d.kode_po,
e.tanggal_delivery,
f.brand,
g.satuan,
g.kode_lama,
g.kode as kode_barang,
g.nama as nama_barang,
g.keterangan as keterangan_barang,

(SELECT COUNT(1) FROM tb_roll WHERE id_kumulatif=b.id ) count_roll

FROM tb_pick a 
JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id 
JOIN tb_sj_item c ON b.id_sj_item=c.id 
JOIN tb_sj d ON c.kode_sj=d.kode 
JOIN tb_do e ON a.id_do=e.id 
JOIN tb_lokasi f ON b.kode_lokasi=f.kode  
JOIN tb_barang g ON c.kode_barang=g.kode  
AND e.kode_do='$kode_do' 
ORDER BY a.tanggal_allocate   
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
  // $is_hutangan = $d['is_hutangan'];
  $count_roll = $d['count_roll'];
  $is_fs = $d['is_fs'];
  $satuan = $d['satuan'];
  $tanggal_delivery = $d['tanggal_delivery'];


  //pengeluaran
  $qty_allocate = floatval($d['qty_allocate']);













  # =======================================================
  # FINAL TR LOOP
  # =======================================================
  $tr .= "
    <tr>
      <td>$i</td>
      <td>
        $d[kode_po]
      </td>
      <td>
        $no_lot
      </td>
      <td>
        $kode_lokasi $brand
      </td>

      <td>
        <div>$d[kode_barang]</div>
        <div class='f12 abu'>
          <div>Kode lama: $d[kode_lama]</div>
        </div>
      </td>
      <td>
          <div>$d[nama_barang]</div>
          <div>$d[keterangan_barang]</div>
      </td>
      <td>
        $qty_allocate
      </td>
      <td>$satuan</td>
    </tr>
  ";
}

echo "
  <table class='table'>
    <thead>
      <th>No</th>
      <th>PO</th>
      <th>LOT</th>
      <th>LOKASI</th>
      <th>ITEM</th>
      <th>DESKRIPSI</th>
      <th>Allocate</th>
      <th>UOM</th>
    </thead>
    $tr
  </table>
";

echo "
  <form method='post'>
    <div class='wadah'>
      <div class='flexy mb2'>
        <div>Tanggal Delivery</div>
        <div>
          <input type='date' name='tanggal_delivery' class='form-control' value='$tanggal_delivery'>
        </div>
      </div>
      <button class='btn btn-primary' name=btn_cetak>Cetak Surat Jalan</button>
    </div>
  </form>
";
