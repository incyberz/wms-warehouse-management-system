<?php
$id_kategori = $_GET['id_kategori'] ?? '';
$kategori = $id_kategori == 1 ? 'Aksesoris' : 'Fabric';
$cat = $id_kategori == 1 ? 'aks' : 'fab';
$not_kategori = $id_kategori == 1 ? 'Fabric' : 'Aksesoris';
$not_id_kategori = $id_kategori == 1 ? 2 : 1;
if (!$id_kategori) {
  die("
    <div>History Relokasi untuk:</div>
    <a class='btn btn-success' href='?dashboard&p=inventory&id_kategori=1'>Aksesoris</a> 
    <a class='btn btn-success' href='?dashboard&p=inventory&id_kategori=2'>Fabric</a> 
  ");
}

$order_by = $_GET['order_by'] ?? 'kode_lokasi';
$asc = $_GET['asc'] ?? 'asc';
$not_asc = $asc == 'asc' ? 'desc' : 'asc';


$s = "SELECT 
e.kode as kode_barang,
e.kode_lama,
e.nama as nama_barang,
e.keterangan as keterangan_barang,
d.kode_po,
a.kode_lokasi_awal,
a.kode_lokasi_baru,
a.tanggal as tanggal_relokasi,
f.nama as relokator

FROM tb_history_relokasi a 
JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id 
JOIN tb_sj_item c ON b.id_sj_item=c.id 
JOIN tb_sj d ON c.kode_sj=d.kode 
JOIN tb_barang e ON c.kode_barang=e.kode 
JOIN tb_user f ON a.relokasi_by=f.id
WHERE e.id_kategori=$id_kategori 
ORDER BY $order_by $asc
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));

$tr = '';
$th = '<th class=f12>NO</th>';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $td = "<td>$i</td>";
  foreach ($d as $key => $value) {
    $td .= "<td>$value</td>";
    if ($i == 1) {
      $kolom = strtoupper(str_replace('_', ' ', $key));
      if ($key == 'kode_barang') {
        $new_order_by = 'e.kode';
      } elseif ($key == 'nama_barang') {
        $new_order_by = 'e.nama';
      } elseif ($key == 'keterangan_barang') {
        $new_order_by = 'e.keterangan';
      } elseif ($key == 'tanggal_relokasi') {
        $new_order_by = 'a.tanggal';
      } elseif ($key == 'relokator') {
        $new_order_by = 'f.nama';
      } else {
        $new_order_by = $key;
      }
      $and_not_asc = $new_order_by == $order_by ? "&asc=$not_asc" : '';
      $fsize = $new_order_by == $order_by ? '' : 'f12 abu';
      $asc_show = $new_order_by == $order_by ?  "<span class='f10'>$asc</span>" : '';
      $th .= "<th><a href='?dashboard&p=inventory&id_kategori=$id_kategori&order_by=$new_order_by$and_not_asc' class='$fsize'>$kolom $asc_show</a></th>";
    }
  }




  $tr .= "
      <tr>
        $td
      </tr>
    ";
}

echo "
    
    <h2 class=mt4>History Relokasi $kategori | <a href='?dashboard&p=inventory&id_kategori=$not_id_kategori'>$not_kategori</a></h2>
    <table class=table>
      <thead>
        $th
      </thead>
      $tr
    </table>
  ";
