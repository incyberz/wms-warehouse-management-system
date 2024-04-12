<?php
$judul = 'Update Status PO';
if ($id_role != 3) die(div_alert('danger', 'Maaf, hanya login WH agar dapat mengakses fitur ini.'));

set_title($judul);
echo "
  <div class='pagetitle'>
    <h1>$judul</h1>
    <nav>
      <ol class='breadcrumb'>
        <li class='breadcrumb-item'><a href='?penerimaan&p=data_sj'>Data Surat Jalan</a></li>
        <li class='breadcrumb-item active'>$judul</li>
      </ol>
    </nav>
  </div>
";

$kode_po = $_GET['kode_po'] ?? '';
if ($kode_po) {
  echo "Kode PO $kode_po OK";
} else {

  $s = "SELECT kode_po FROM tb_sj ORDER BY kode_po";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  while ($d = mysqli_fetch_assoc($q)) {
    $s2 = "SELECT 1 FROM tb_po WHERE kode_po='$d[kode_po]'";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    if (mysqli_num_rows($q2)) {
      // echo "<div>$d[kode_po] exists, skipped...</div>";
    } else {
      $s2 = "INSERT INTO tb_po (kode_po) VALUES ('$d[kode_po]')";
      echolog('INSERT NEW PO');
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    }
  }





  $order_by = $_GET['order_by'] ?? 'a.kode_po';
  $asc = $_GET['asc'] ?? 'asc';
  $not_asc = $asc == 'asc' ? 'desc' : 'asc';


  $s = "SELECT 
  a.kode_po,
  b.tanggal_po,
  b.kode as surat_jalan,
  b.tanggal_terima,
  a.tanggal_update,
  a.status_update,
  (
    SELECT count(1) FROM tb_sj_item p
    JOIN tb_sj q ON p.kode_sj=q.kode
    WHERE q.kode_po=a.kode_po) count_item_with_empty   
  FROM tb_po a 
  JOIN tb_sj b ON a.kode_po=b.kode_po 
  WHERE a.status_update is null 
  AND a.kode_po != 'STOCK' 
  AND b.kode NOT LIKE '%-999' -- bukan importer
  ORDER BY $order_by $asc
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));


  $tr = '';
  $th = '<th>NO</th>';
  $i = 0;
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    $td = "<td>$i</td>";
    foreach ($d as $key => $value) {
      if ($key == 'count_item_with_empty') continue;
      if (!$value) {
        if ($key == 'status_update') $value = '<span class="f12 miring abu consolas">belum</span>';
        if ($key == 'tanggal_update') $value = '<span class="f12 miring abu consolas">null</span>';
      }
      $td .= "<td>$value</td>";
      if ($i == 1) {
        $kolom = strtoupper(str_replace('_', ' ', $key));
        if ($key == 'surat_jalan') {
          $new_order_by = 'b.kode';
        } else {
          $new_order_by = $key;
        }
        $and_not_asc = $key == $new_order_by ? "&asc=$not_asc" : '';
        $asc_show = $new_order_by == $order_by ?  "<span class='f10'>$asc</span>" : '';

        $th .= "<th><a href='?update_status_po&order_by=$new_order_by$and_not_asc'>$kolom $asc_show</a></th>";
      }
    }

    $s2 = "SELECT 
    a.kode_barang,
    (
      SELECT sum(qty) FROM tb_roll p 
      JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id
      WHERE q.id_sj_item=a.id) qty_diterima 
    FROM tb_sj_item a 
    JOIN tb_sj b ON a.kode_sj=b.kode 
    JOIN tb_barang c ON a.kode_barang=c.kode 
    WHERE b.kode_po='$d[kode_po]'
    ";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    $jumlah_item = 0;

    $li = '';
    while ($d2 = mysqli_fetch_assoc($q2)) {
      $li .= "<li>$d2[kode_barang]</li>";
      if ($d2['qty_diterima']) $jumlah_item++;
    }

    $grad = $jumlah_item != $d['count_item_with_empty'] ? 'gradasi-merah' : '';



    $tr .= "
      <tr class='$grad'>
        $td
        <td><ul>$li</ul></td>
        <td>
          <form method=post>
            <button class='btn btn-success btn-sm'>Update Status PO</button>
          </form>
        </td>
      </tr>
    ";
  }

  echo "
    
    <h2 class=mt4>List PO yang belum diupdate</h2>
    <table class=table>
      <thead>
        $th
        <th>ITEMS</th>
        <th>AKSI</th>
      </thead>
      $tr
    </table>
  ";




  die("
    <div>Cara Update Status PO</div>
  ");
}
