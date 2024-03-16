<?php
$id_kategori = $_GET['id_kategori'] ?? die(erid('id_kategori'));
$judul = 'Insert Data Roll ' . $arr_kategori[$id_kategori];
set_title($judul);
?>
<div class="pagetitle">
  <h1><?= $judul ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?importer">Import Data</a></li>
      <li class="breadcrumb-item"><a href="?import_data_barang">Import Data Barang</a></li>
      <li class="breadcrumb-item"><a href="?insert_item_kumulatif&id_kategori=<?= $id_kategori ?>">Insert Item Kumulatif</a></li>
      <li class="breadcrumb-item active"><?= $judul ?></li>
    </ol>
  </nav>
</div>
<?php
$s = "SELECT 
a.id as id_kumulatif,
a.tmp_qty,
(
  SELECT sum(qty) 
  FROM tb_roll 
  WHERE id_kumulatif=a.id) qty_roll

FROM tb_sj_kumulatif a 
JOIN tb_sj_item b ON a.id_sj_item=b.id
JOIN tb_sj c ON b.kode_sj=c.kode 

WHERE c.id_kategori=$id_kategori 
AND b.kode_sj like '%-999' -- khusus STOK AWAL
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_data = mysqli_num_rows($q);
echolog("Checking qty roll dari $jumlah_data data kumulatif.");
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $id_kumulatif = $d['id_kumulatif'];
  // echo "<br>qty_roll: $d[qty_roll] | tmp_qty: $d[tmp_qty] | ";
  if ($d['tmp_qty'] and !$d['qty_roll']) {
    $s2 = "INSERT INTO tb_roll (
      id_kumulatif,
      no_roll,
      qty,
      keterangan
    ) VALUES (
      '$id_kumulatif',
      '1',
      '$d[tmp_qty]',
      'Bundle stok awal'
    )";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    echolog("Inserting data roll ke $i with id: $id_kumulatif");
  }
}

echo div_alert('success', 'Proses insert data roll selesai.<hr><a href="?stok_kumulatif" class="btn btn-primary">Lihat Stok Opname</a>');
