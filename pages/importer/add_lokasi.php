<?php
$get_kode = $_GET['kode'] ?? '';
$get_from = $_GET['from'] ?? '';
$get_id_kategori = $_GET['id_kategori'] ?? die(erid('id_kategori'));
$cat = $get_id_kategori == 1 ? 'aks' : 'fab';
$CAT = strtoupper($cat);
$judul = 'Add Lokasi untuk ' . $arr_kategori[$get_id_kategori];
set_title($judul);
$href_back = "?$get_from&id_kategori=$get_id_kategori";
$btn_back = !$get_from ? '' : "<a href='$href_back' class='btn btn-success btn-sm'>Kembali</a>";

echo "
<div class='pagetitle'>
  <h1>$judul</h1>
  <div class='mt2 mb4'>$btn_back</div>
</div>
";


if (isset($_POST['btn_simpan_lokasi_baru'])) {

  $blok = $_POST['btn_simpan_lokasi_baru'];
  $blok_baru = "$blok.$CAT";

  $s = "INSERT INTO tb_blok (blok,id_kategori) 
  VALUES ('$blok_baru',$get_id_kategori) 
  ON DUPLICATE KEY UPDATE id_kategori=$get_id_kategori";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Simpan blok baru sukses.');

  $s = "INSERT INTO tb_lokasi (
    kode,
    blok
  ) VALUES (
    '$_POST[kode_lokasi_baru]',
    '$blok_baru'
  )
  ON DUPLICATE KEY UPDATE blok='$blok_baru'
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Simpan lokasi baru sukses.');

  $s = "UPDATE tb_importer SET LOC = '$_POST[kode_lokasi_baru]' WHERE LOC = '$get_kode'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Update lokasi baru pada importer sukses.');

  jsurl($href_back, 1000);
}

if (isset($_POST['btn_ubah_kategori_lokasi'])) {
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';
  $arr = explode('__', $_POST['btn_ubah_kategori_lokasi']);
  $s = "UPDATE tb_blok SET id_kategori=$arr[1] WHERE blok='$arr[0]'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Update kategori lokasi sukses.');
  jsurl($href_back, 1000);
}

if (isset($_POST['btn_add_lokasi'])) {
  $brand = $_POST['brand'] ? "'$_POST[brand]'" : 'NULL';
  $s = "INSERT INTO tb_lokasi 
  (
    kode,blok,brand
  ) VALUES (
    '$_POST[kode]','$_POST[blok]',$brand
  )";
  // die($s);
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Buat lokasi baru sukses.');
  jsurl($href_back, 1000);
}

// handle duplikat
$s = "SELECT a.blok, c.nama as kategori  
FROM tb_lokasi a 
JOIN tb_blok b ON a.blok=b.blok 
JOIN tb_kategori c ON b.id_kategori=c.id  
where a.kode='$get_kode'";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q)) {
  $d = mysqli_fetch_assoc($q);
  $get_kode_cat = $get_kode . '.' . $CAT;
  $form = "
    <div>
      Jika ingin menambah kode lokasi, silahkan Simpan.
      <form method=post class=mt1>
        <div class=flexy>
          <div>
            <input name=kode_lokasi_baru value='$get_kode_cat' class='form-control' >
          </div>
          <div>
            <button class='btn btn-primary' name=btn_simpan_lokasi_baru value=$d[blok]>Simpan ke Lokasi $CAT Baru</button>
          </div>
        </div>
      </form>
      <hr>
      Jika lokasi tersebut termasuk kategori $arr_kategori[$get_id_kategori], maka silahkan klik tombol berikut:
    </div>
    <form method=post class=mt2>
      <button class='btn btn-danger' name=btn_ubah_kategori_lokasi value=$d[blok]__$get_id_kategori>Ubah Kategori Lokasi tersebut ke $arr_kategori[$get_id_kategori]</button>
    </form>
  ";
  echo div_alert('danger', "Perhatian! Kode lokasi $get_kode sudah ada di database pada blok $d[blok] dan kategori $d[kategori]
  <hr>
  $form
  ");
  exit;
}

$s = "SELECT blok FROM tb_blok where id_kategori=$get_id_kategori";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$opt_blok = '';
while ($d = mysqli_fetch_assoc($q)) {
  $selected = strpos($d['blok'], 'CAMPUR') ? 'selected' : '';
  $brand_show = $d['brand'] ?? 'no-brand';
  $opt_blok .= "<option value='$d[blok]' $selected>Blok $d[blok] ~ $brand_show</option>";
}


echo "
  <form method=post>
    <input required class='form-control mb2' value='$get_kode' name=kode placeholder='Kode Lokasi...'>
    <select class='form-control mb1' name='blok'>
      $opt_blok
    </select>
    <div class='mb3 f12'>Jika tidak ada blok atau blok keliru silahakan <a href='?manage_blok&id_kategori=$get_id_kategori&from=add_lokasi&before=importer'>Manage Blok</a></div>
    <input class='form-control mb2' name=brand placeholder='Brand... (opsional)'>
    <button class='btn btn-primary w-100' name=btn_add_lokasi>Add Lokasi</button>
  </form>
";
