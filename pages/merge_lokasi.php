<?php
if ($id_role != 3) die(div_alert('danger', 'Maaf, hanya login WH agar dapat mengakses fitur ini.'));


if (isset($_POST['btn_merge'])) {
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';

  echolog('moving items');
  $s = "UPDATE tb_sj_kumulatif SET kode_lokasi = '$_POST[lokasi_tujuan]' WHERE kode_lokasi='$_POST[btn_merge]'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Moving items sukses.');



  if (isset($_POST['hapus_lokasi_asal'])) {
    echolog('deleting lokasi asal');
    $s = "DELETE FROM tb_lokasi WHERE kode='$_POST[btn_merge]'";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    echo div_alert('success', 'Hapus Lokasi awal sukses.');
  }
  jsurl('', 1000);
  exit;
}


$kode_lokasi = $_GET['kode_lokasi'] ?? die('Membutuhkan parameter kode_lokasi');
$id_kategori = $_GET['id_kategori'] ?? die('Membutuhkan parameter id_kategori');
$judul = "Merge Lokasi $kode_lokasi";

$kategori = '';
if ($id_kategori) {
  if ($id_kategori == 1) {
    $kategori = 'Aksesoris';
  } elseif ($id_kategori == 2) {
    $kategori = 'Fabric';
  } else {
    die('id_kategori tidak sesuai.');
  }
  $judul .= " :: $kategori";
  $not_id_kategori = $id_kategori == 1 ? 2 : 1;
  $not_kategori = $id_kategori == 1 ? 'Fabric' : 'Aksesoris';
}

set_title($judul);
echo "
  <div class='pagetitle'>
    <h1>$judul</h1>
    <nav>
      <ol class='breadcrumb'>
        <li class='breadcrumb-item'><a href='#' onclick='history.go(-1)'>Back</a></li>
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

  $order_by = $_GET['order_by'] ?? 'kode_lokasi';
  $asc = $_GET['asc'] ?? 'asc';
  $not_asc = $asc == 'asc' ? 'desc' : 'asc';

  $s = "SELECT  
  a.id as id_kumulatif,
  d.kode as kode_barang,
  d.kode_lama,
  d.nama as nama_barang,
  d.keterangan as keterangan_barang,
  a.kode_lokasi

  FROM tb_sj_kumulatif a 
  JOIN tb_sj_item b ON a.id_sj_item=b.id 
  JOIN tb_sj c ON b.kode_sj=c.kode 
  JOIN tb_barang d ON b.kode_barang=d.kode 

  WHERE a.kode_lokasi='$kode_lokasi' 
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (!mysqli_num_rows($q)) {
    echo div_alert('info', "Semua item pada lokasi $kode_lokasi sudah dipindahkan, redirecting to Manage Lokasi");
    jsurl("?manage_lokasi&id_kategori=$id_kategori", 3000);
    exit;
  }

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
        $th .= "<th>$kolom</th>";
      }
    }




    $tr .= "
      <tr >
        $td
        <td><a class='btn btn-sm btn-success' href='?relokasi&id_kumulatif=$d[id_kumulatif]'>Relokasi</a></td>
      </tr>
    ";
  }

  echo "
    <p class='biru miring'>Merge Lokasi artinya merelokasi semua item yang ada pada lokasi tersebut ke lokasi baru. Jika ingin relokasi per item silahkan klik tombol Relokasi.</p>
    <h2 class=mt4>List Item Kumulatif</h2>
    <table class=table>
      <thead>
        $th
        <th>AKSI</th>
      </thead>
      $tr
    </table>
  ";


  $s = "SELECT * FROM tb_lokasi a 
  JOIN tb_blok b ON a.blok=b.blok 
  WHERE b.id_kategori=$id_kategori 
  AND kode != '$kode_lokasi'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $opt = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $brand_show = $d['brand'] ? $d['brand'] : '(no-brand)';
    $opt .= "<option value='$d[kode]'>$d[kode] ~ BLOK-$d[blok] ~ $brand_show</option>";
  }
  echo "
  <h2 class='mt4 mb2'>Lokasi Tujuan</h2>
  <form method=post class='wadah'>
    <div class=flexy>
      <div>Merge (pindahkan) semua item diatas ke lokasi: </div>
      <div><select class='form-control' name=lokasi_tujuan>$opt</select></div>
    </div>
    <label class=mb2>
      <input type=checkbox name=hapus_lokasi_asal checked value=1> Hapus Lokasi Asal ($kode_lokasi)
      <div class='f12 abu'>Jika lokasi awal tidak digunakan lagi maka sebaiknya hapus saja lokasi asal</div>
    </label>
    <button name=btn_merge value='$kode_lokasi' class='btn btn-danger w-100' onclick='return confirm(\"Pindahkan semua item diatas ke lokasi baru?\")'>Merge</button>
  </form>
  ";
}
