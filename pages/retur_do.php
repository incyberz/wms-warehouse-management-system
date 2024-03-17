<?php
$judul = 'Retur DO';
set_title($judul);
echo "
<div class='pagetitle'>
  <h1>$judul</h1>
  <nav>
    <ol class='breadcrumb'>
      <li class='breadcrumb-item'><a href='?stok_opname'>Back to Stok Opname</a></li>
      <li class='breadcrumb-item active'>$judul</li>
    </ol>
  </nav>
</div>

<img src='assets/img/icons/retur_do.png' alt='retur_do' width='100px' class='mt-2 mb-2'>

<p>Retur DO adalah barang yang dikembalikan dari Buyer karena kondisi tertentu.</p>
";

# =====================================================
# GET ID PICK FIRST
# =====================================================
$get_id_kumulatif = $_GET['id_kumulatif'] ?? die(erid('id_kumulatif'));

$s = "SELECT 
c.kode_po,
a.no_lot,
a.kode_lokasi,
b.kode_barang,
d.nama as nama_barang, 
d.keterangan as keterangan_barang, 
d.kode_lama,
a.is_fs

FROM tb_sj_kumulatif a 
JOIN tb_sj_item b ON a.id_sj_item=b.id 
JOIN tb_sj c ON b.kode_sj=c.kode 
JOIN tb_barang d ON b.kode_barang=d.kode 
WHERE a.id=$get_id_kumulatif";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);

$tr = '';
foreach ($d as $key => $value) {
  if ($key == 'is_fs') continue;
  $kolom = strtoupper(str_replace('_', ' ', $key));
  $value = $value ?? $null_gray;
  $tr .= "
    <tr>
      <td class='f14 abu'>$kolom</td>
      <td>$value</td>
    </tr>
  ";
}


$s = "SELECT 
b.qty as qty_pick,
b.id as id_pick,
c.kode_do,
c.date_created as tanggal_do,
c.id_kategori,
d.nama as kategori,
f.satuan

FROM tb_sj_kumulatif a 
JOIN tb_pick b ON a.id=b.id_kumulatif 
JOIN tb_do c ON b.id_do=c.id 
JOIN tb_kategori d ON c.id_kategori=d.id  
JOIN tb_sj_item e ON a.id_sj_item=e.id 
JOIN tb_barang f ON e.kode_barang=f.kode 
WHERE a.id=$get_id_kumulatif";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$tr_do = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $id_pick = $d['id_pick'];
  $cat = $d['id_kategori'] == 1 ? 'aks' : 'fab';
  $qty_pick = floatval($d['qty_pick']);
  $tanggal_do_show = date('d-M-y, H:i', strtotime($d['tanggal_do']));
  $tr_do .= "
    <tr>
      <td class='f14 abu'>$i</td>
      <td><a href='?pengeluaran&p=buat_do&kode_do=$d[kode_do]&cat=$cat'>$d[kode_do]</a></td>
      <td>$tanggal_do_show</td>
      <td>$d[kategori]</td>
      <td>$qty_pick</td>
      <td>$d[satuan]</td>
      <td><a href='?pengeluaran&p=buat_do&kode_do=$d[kode_do]&cat=$cat&retur_do=1' class='btn btn-sm btn-success'>Retur DO</a></td>
    </tr>
  ";
}

echo "
<div class='wadah gradasi-abu'>
  <div class='sub_form mb2'>Info Item Kumulatif</div>
  <table class='table table-striped'>$tr</table>
</div>
<div class='wadah gradasi-hijau'>
  <div class='sub_form mb2'>Info Picked by DO</div>
  <table class='table table-striped'>
    <thead class='gradasi-toska'>
      <th>No</th>
      <th>Nomor DO</th>
      <th>Tanggal DO</th>
      <th>Material</th>
      <th>QTY Pick</th>
      <th>UOM</th>
      <th>Retur DO</th>
    </thead>
    $tr_do
  </table>
</div>
";
