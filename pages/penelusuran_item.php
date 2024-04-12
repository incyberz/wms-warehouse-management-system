<?php
$judul = 'Penelusuran Item';
set_title($judul);
echo "
<div class='pagetitle'>
  <h1>$judul</h1>
  <nav>
    <ol class='breadcrumb'>
      <li class='breadcrumb-item'><a href='?stok_opname'>Stok Opname</a></li>
      <li class='breadcrumb-item active'>$judul</li>
    </ol>
  </nav>
</div>
";

$kode_barang = $_GET['kode_barang'] ?? '';

if (!$kode_barang) {
  echo "
    Untuk Penelusuran item:
    <ol>
      <li>Masuk <a href='?stok_opname'>Stok Opname</a></li>
      <li>Cari Item yang akan ditelusuri</li>
      <li>Pada kode barang yang muncul klik tombol detail</li>
    </ol>
  ";
} else {
  $s = "SELECT 
  b.nama as kategori,
  a.kode as kode_barang,
  a.kode_lama,
  a.nama as nama_barang,
  a.keterangan as keterangan_barang,
  a.satuan
  -- a.harga 
  FROM tb_barang a 
  JOIN tb_kategori b ON a.id_kategori=b.id 
  WHERE a.kode='$kode_barang'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    $d = mysqli_fetch_assoc($q);
    $satuan = $d['satuan'];

    $tr = '';
    foreach ($d as $key => $value) {
      $tr .= "
        <tr>
          <td class='f14 abu miring'>$key</td>
          <td>:</td>
          <td>$value</td>
        </tr>
      ";
    }

    echo "
    <h2 class=mt4>Item Properties</h2>
    <table class=table>
      $tr
    </table>
    ";
  } else {
    die("Barang dengan kode $kode_barang tidak ditemukan.");
  }




  # ===================================================
  # PENGELUARAN
  # ===================================================
  $s = "SELECT 
  a.id as id_sj_item,
  b.kode_po,
  b.tanggal_po,
  c.nama as supplier,
  a.kode_sj,
  a.qty,
  b.tanggal_terima,
  (
    SELECT SUM(qty) FROM tb_roll p
    JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id
    WHERE q.id_sj_item=a.id) qty_diterima,
  (
    SELECT COUNT(1) FROM tb_sj_kumulatif 
    WHERE id_sj_item=a.id) kumulatif_count

  FROM tb_sj_item a 
  JOIN tb_sj b ON a.kode_sj=b.kode 
  JOIN tb_supplier c ON b.kode_supplier=c.kode 
  JOIN tb_barang d ON a.kode_barang=d.kode 
  WHERE a.kode_barang='$kode_barang'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    $tr = '';
    $th = '<th>NO</th>';
    $i = 0;
    $total_terima = 0;
    while ($d = mysqli_fetch_assoc($q)) {
      if (!$d['qty_diterima']) continue;
      $total_terima += floatval($d['qty_diterima']);
      $i++;
      $td = "<td>$i</td>";
      foreach ($d as $key => $value) {
        if ($key == 'id_sj_item') continue;
        if ($key == 'qty' || $key == 'qty_diterima') $value = floatval($value);
        if (strpos("salt$key", 'tanggal')) $value = date('d-M-y', strtotime($value));
        $td .= "<td>$value</td>";
        if ($i == 1) {
          $kolom = strtoupper(str_replace('_', ' ', $key));
          $th .= "<th>$kolom</th>";
        }
      }




      $tr .= "
        <tr>
          $td
        </tr>
      ";
    }

    echo "
      
      <h2 class=mt4>History Penerimaan</h2>
      <table class=table>
        <thead>
          $th
        </thead>
        $tr
      </table>
      Total QTY Terima : $total_terima $satuan
    ";
  } else {
    echo "<h2 class=mt4>History Penerimaan</h2>";
    echo div_alert('info', 'Belum ada data penerimaan untuk item ini.');
  }



  # ===================================================
  # PENGELUARAN
  # ===================================================
  $s = "SELECT 
  d.kode_do,
  e.nama as picker,
  a.tanggal_pick,
  a.qty as qty_pick,
  f.nama as allocator,
  a.tanggal_allocate,
  a.qty_allocate

  FROM tb_pick a 
  JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id 
  JOIN tb_sj_item c ON b.id_sj_item=c.id 
  JOIN tb_do d ON a.id_do=d.id
  JOIN tb_user e ON a.pick_by=e.id
  JOIN tb_user f ON a.allocate_by=f.id
  WHERE c.kode_barang='$kode_barang'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    $tr = '';
    $th = '<th>NO</th>';
    $i = 0;
    $total_keluar = 0;
    while ($d = mysqli_fetch_assoc($q)) {
      $total_keluar += floatval($d['qty_allocate']);
      $i++;
      $td = "<td>$i</td>";
      foreach ($d as $key => $value) {
        if ($key == 'id_sj_item') continue;
        if (strpos("salt$key", 'qty')) $value = floatval($value);
        if (strpos("salt$key", 'tanggal')) $value = date('d-M-y', strtotime($value));
        $td .= "<td>$value</td>";
        if ($i == 1) {
          $kolom = strtoupper(str_replace('_', ' ', $key));
          $th .= "<th>$kolom</th>";
        }
      }




      $tr .= "
      <tr>
        $td
      </tr>
    ";
    }

    echo "
    
    <h2 class=mt4>History Pengeluaran</h2>
    <table class=table>
      <thead>
        $th
      </thead>
      $tr
    </table>
    Total QTY Pengeluaran : $total_keluar $satuan
    ";
  } else {
    echo "<h2 class=mt4>History Pengeluaran</h2>";
    echo div_alert('info', 'Belum ada data pengeluaran untuk item ini.');
  }
}
