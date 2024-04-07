<?php
$judul = 'Manage Lokasi';
if ($id_role != 3) die(div_alert('danger', 'Maaf, hanya login WH agar dapat mengakses fitur ini.'));


if (isset($_POST['btn_relokasi'])) {
  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';
}


$id_kategori = $_GET['id_kategori'] ?? '';
$bread_home = '';
$kategori = '';
if ($id_kategori) {
  if ($id_kategori == 1) {
    $kategori = 'Aksesoris';
  } elseif ($id_kategori == 2) {
    $kategori = 'Fabric';
  } else {
    die('id_kategori tidak sesuai.');
  }
  $judul .= " $kategori";
  $bread_home = "<li class='breadcrumb-item'><a href='?manage_lokasi'>Manage Lokasi Home</a></li>";
}

set_title($judul);
echo "
  <div class='pagetitle'>
    <h1>$judul</h1>
    <nav>
      <ol class='breadcrumb'>
        $bread_home
        <li class='breadcrumb-item active'>$judul</li>
      </ol>
    </nav>
  </div>
";

if (!$id_kategori) {
  echo "
    <div>Manajemen Lokasi untuk:</div>
    <a class='btn btn-success' href='?manage_lokasi&id_kategori=1'>AKSESORIS</a>
    <a class='btn btn-success' href='?manage_lokasi&id_kategori=2'>FABRIC</a>
  ";
} else {

  $order_by = $_GET['order_by'] ?? 'count_kumulatif_item';
  $asc = $_GET['asc'] ?? 'asc';
  $not_asc = $asc == 'asc' ? 'desc' : 'asc';

  $s = "SELECT  
  a.kode as kode_lokasi,
  a.blok,
  a.brand,
  c.kode as kategori,
  (SELECT COUNT(1) FROM tb_sj_kumulatif WHERE kode_lokasi=a.kode) count_kumulatif_item

  FROM tb_lokasi a 
  JOIN tb_blok b ON a.blok=b.blok 
  JOIN tb_kategori c ON b.id_kategori=c.id 
  WHERE b.id_kategori=$id_kategori 
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
      $td .= "<td>$value</td>";
      if ($i == 1) {
        $kolom = strtoupper(str_replace('_', ' ', $key));
        $and_not_asc = $key == $order_by ? "&asc=$not_asc" : '';
        $th .= "<th><a href='?manage_lokasi&id_kategori=$id_kategori&order_by=$key$and_not_asc'>$kolom</a></th>";
      }
    }
    $tr .= "
      <tr>
        $td
        <td>
          <form method=post>
            <button class='btn btn-sm btn-danger' value='$d[kode_lokasi]' name=btn_hapus_lokasi>Hapus</button>
          </form>
        </td>
      </tr>
    ";
  }

  echo "
    
    <h2 class=mt4>List Lokasi untuk $kategori</h2>
    <table class=table>
      <thead>
        $th
        <th>HAPUS</th>
      </thead>
      $tr
    </table>
  ";
}
