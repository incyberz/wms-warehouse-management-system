<?php
if (isset($_POST['btn_add_blok'])) {
  $blok_baru = strtoupper($_POST['blok_baru']);
  $s = "INSERT INTO tb_blok 
  (
    blok,id_kategori
  ) VALUES (
    '$blok_baru',$_POST[btn_add_blok]
  )";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Buat blok baru sukses.');
  jsurl('', 1000);
}

$get_before = $_GET['before'] ?? '';
$get_from = $_GET['from'] ?? '';
$judul = 'Manage Blok';
set_title($judul);

echo "
  <div class='pagetitle'>
    <h1>$judul</h1>
    <nav>
      <ol class='breadcrumb'>
        <li class='breadcrumb-item'><a href='?manage_lokasi&id_kategori=1'>Manage Lokasi Aksesoris</a></li>
        <li class='breadcrumb-item'><a href='?manage_lokasi&id_kategori=2'>Manage Lokasi Fabric</a></li>
        <li class='breadcrumb-item active'>$judul</li>
      </ol>
    </nav>
  </div>
";


$s = "SELECT a.*,
(
  SELECT count(1) 
  FROM tb_lokasi 
  WHERE blok=a.blok) sub_count 
  
FROM tb_blok a 
ORDER BY a.id_kategori,a.blok
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr_blok = '';
$i = 0;
$last_id_kategori = ''; // untuk pemisah
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  if ($i != 1 and $last_id_kategori != $d['id_kategori']) {
    $border = 'solid 3px #fcf';
  } else {
    $border = '';
  }
  $brand_show = $d['brand'] ?? 'no-brand';
  $kategori = $arr_kategori[$d['id_kategori']];
  $id_kategori_change = $d['id_kategori'] == 1 ? 2 : 1;
  $change_to = $arr_kategori[$id_kategori_change];
  $btn_delete = $d['sub_count'] ? '' : "<button class='btn btn-danger btn-sm' name=btn_delete_blok value=$d[blok]>Delete</button>";
  $gradasi = $d['sub_count'] ? '' : 'kuning';

  $btn_change_to = "<button class='btn btn-warning btn-sm' name=btn_change_to value='$d[blok]__$id_kategori_change'>Change to $change_to</button>";
  $btn_change_to = ''; // riskan


  $tr_blok .= "
    <tr style='border-top: $border' class='gradasi-$gradasi'>
      <td>$i</td>
      <td>$d[blok]</td>
      <td>$kategori</td>
      <td>$d[sub_count]</td>
      <td>
        <form method=post class=m0>
          $btn_delete
          $btn_change_to
        </form>
      </td>
    </tr>
  ";
  $last_id_kategori = $d['id_kategori'];
}


echo "
  <table class='table'>
    <thead>
      <th>No</th>
      <th>Blok</th>
      <th>Untuk</th>
      <th>Sub Lokasi Count</th>
      <th>Aksi</th>
    </thead>
    $tr_blok
  </table>
  <form method=post class='wadah mt2'>
    <div class=sub_form>Form Tambah Blok</div>
    <input required class='form-control mb2 upper' placeholder='Nama blok...' name=blok_baru>
    <button class='btn btn-primary' name=btn_add_blok value=1>Add Blok Aksesoris</button>
    <button class='btn btn-primary' name=btn_add_blok value=2>Add Blok Fabric</button>
  </form>
";
