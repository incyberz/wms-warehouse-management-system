<?php




# ===========================================
# PROCESSORS
# ===========================================
if (isset($_POST['btn_delete_row'])) { // delete row
  echolog('deleting row');
  $s = "DELETE FROM tb_importer WHERE id_auto = $_POST[btn_delete_row]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echolog('sukses');
  jsurl('', 500);
}

if (isset($_POST['btn_set_null'])) { // set null id lama
  echolog('updating tb_importer, set null ID');
  $s = "UPDATE tb_importer SET ID = NULL where id_auto = $_POST[btn_set_null]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echolog('sukses');
  jsurl('', 500);
}

if (isset($_POST['btn_update_konten'])) {
  echolog('updating tb_importer, updating ID-lama');
  $s = "UPDATE tb_importer SET ID = '$_POST[ID]' where id_auto = $_POST[btn_update_konten]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echolog('sukses');
  jsurl('', 500);
}

if (isset($_POST['btn_update_dg_id_lama'])) {
  echolog('Replacing ID-baru dengan id-lama');
  $s = "UPDATE tb_importer SET ID_BARU = '$_POST[ID]' where id_auto = $_POST[btn_update_dg_id_lama]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echolog('sukses');
  jsurl('', 500);
}





















# ===========================================
# SYARAT VARIABEL
# ===========================================
$id_kategori = 1; // AKS ZZZ DEBUG
$ada_error = 0;

$judul = 'Import Data Penerimaan ' . $arr_kategori[$id_kategori];
set_title($judul);
?>
<style>
  .log {
    font-family: consolas;
    font-size: 12px;
    background: yellow;
  }
</style>
<div class="pagetitle">
  <h1><?= $judul ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item active"><?= $judul ?></li>
    </ol>
  </nav>
</div>
<?php
function validateDate($date, $format = 'Y-m-d')
{
  $d = DateTime::createFromFormat($format, $date);
  return $d && $d->format($format) === $date;
}



# ===========================================
# GET ARRAY LOKASI
# ===========================================
$arr_lokasi = array();
$arr_brand = array();
$s = "SELECT a.kode,a.brand FROM tb_lokasi a 
JOIN tb_blok b ON a.blok=b.blok WHERE b.id_kategori=$id_kategori";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
while ($d = mysqli_fetch_assoc($q)) {
  array_push($arr_lokasi, $d['kode']);
  array_push($arr_brand, $d['brand'] ?? 'no-brand');
}

# ===========================================
# GET ARRAY SATUAN
# ===========================================
$arr_satuan = array();
$s = "SELECT satuan FROM tb_satuan";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
while ($d = mysqli_fetch_assoc($q)) {
  array_push($arr_satuan, $d['satuan']);
}



# ===========================================
# GET ARRAY LOKASI
# ===========================================
$arr_csv = baca_csv('csv/tmp.csv', ';');

// loop untuk mendapatkan jumlah kolom yang valid
$arr_header = array();
$arr_header_kolom_importer = '';
$arr_header_isi_importer = '';
foreach ($arr_csv as $key => $d) {
  foreach ($d as $key2 => $isi) {
    $isi = strtoupper(trim($isi));
    if (!$isi) { // jika cell kosong maka stop
      break;
    } else {
      $isi = str_replace(' ', '_', $isi);
      array_push($arr_header, $isi);
      $arr_header_kolom_importer .= ",$isi";
      $arr_header_isi_importer .= ",'$isi'";
    }
  }
  break; // hanya loop pertama saja
}

$arr_header_kolom_importer = substr($arr_header_kolom_importer, 1);
$arr_header_isi_importer = substr($arr_header_isi_importer, 1);

# ===========================================
# CHECK IF TB_IMPORTER EXISTS
# ===========================================
$s = "SHOW TABLES LIKE 'tb_importer'";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q) == 0) {
  # ===========================================
  # CREATE TB_IMPORTER
  # ===========================================
  echolog('creating tb_importer');
  $sql_koloms = '';
  foreach ($arr_header as $nama_kolom) {
    $sql_koloms .= "$nama_kolom varchar(100) DEFAULT NULL,";
  }
  echolog('creating tb_importer');
  $s = "CREATE TABLE tb_importer (
    id_auto int(11) NOT NULL AUTO_INCREMENT, 
    $sql_koloms
    last_update timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id_auto)
  )";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echolog('sukses');

  # ===========================================
  # INSERT CSV INTO TB_IMPORTER
  # ===========================================
  echolog('<b class="darkblue f20">inserting csv data... mohon tunggu! waktu selesai tergantung banyaknya data</b>');
  foreach ($arr_csv as $key => $d) {
    $values = '';

    if ($key == 0) continue; // skip header

    // looping column data
    if ($d) { // selama masih ada row di csv 
      foreach ($d as $key2 => $isi) {
        $isi = str_replace('\'', '`', $isi);
        $isi = strtoupper(trim($isi));
        $isi = $isi ? "'$isi'" : 'NULL';
        $values .= ",$isi";
      }
      $values = substr($values, 1); // remove first comma
      echolog("inserting data #$key");
      $s = "INSERT INTO tb_importer ($arr_header_kolom_importer) VALUES ($values)";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      echolog('sukses');
      // if ($key > 10) die();
    }
  }

  die('<hr><b class="bold green f20">SUKSES INSERT DATA CSV KE TABEL IMPORTER. Silahkan refresh!</b>');
}






























// loop untuk mendapatkan semua baris
$tr = '';
$i = 0;
$s = "SELECT * FROM tb_importer LIMIT 20000";
echolog('loop data from tabel importer');
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_row = mysqli_num_rows($q);
while ($d = mysqli_fetch_assoc($q)) {

  // loop setiap baris
  $tds = $key ? "<td class='f12 abu miring'>$i</td>" : '';
  $i++;
  foreach ($d as $kolom => $isi) {

    // bersihkan isi cell
    $isi = str_replace('\'', '`', strtoupper(trim($isi)));

    # ===========================================
    # VALIDASI UNTUK SETIAP KOLOM
    # ===========================================

    $sty = '';
    $gradasi = '';
    $info = '';


    if ($kolom == 'id_auto') {
      $sty = 'abu f12 miring';
      $isi = $i;
    } elseif (
      $kolom == 'ID' || $kolom == 'ID_BARU'
    ) {

      if ($kolom == 'ID' and $isi == '') {
        $sty = 'abu f12 miring';
        $info = 'boleh null';
      } else {
        // ID (id_lama) boleh set to null
        $set_null = '';
        $form_update = '';
        $delete_row = '';
        if ($kolom == 'ID') {
          $set_null = "<hr><form method=post><button class='btn btn-danger btn-sm w-100' name=btn_set_null value=$d[id_auto]>SET NULL</button></form>";
          $form_update = "<hr><form method=post>
                <input required class='form-control mb1' type=text name=ID value='$isi'>
                <button class='btn btn-info btn-sm w-100' name=btn_update_konten value=$d[id_auto]>Update Konten</button></form>";
        } elseif ($kolom == 'ID_BARU') { // boleh update dg id-lama atau delete row
          $form_update = "<hr><form method=post>
                <input class='form-control form-control-sm tengah mb1' disabled value='$d[ID]'>
                <input type=hidden name=ID value='$d[ID]'>
                <button class='btn btn-info btn-sm w-100' name=btn_update_dg_id_lama value=$d[id_auto]>Update dengan ID Lama diatas</button></form>";
          $delete_row = "<hr><form method=post>
                <button class='btn btn-danger btn-sm w-100' name=btn_delete_row value='$d[id_auto]' onclick='return confirm(\"Yakin untuk Delete baris  data ini?\")'>Delete Row</button></form>";
        }



        if (
          strpos($isi, ' ') || strlen($isi) < 9 || strlen($isi) > 12
        ) {
          // ga boleh ada spasi
          // minimal 9 karakter
          // maksimal 12 karakter
          $sty = 'red';
          $gradasi = 'merah';
          $info = "ID jangan ada spasi, minimal 9 karakter, maksimal 12 karakter$form_update$set_null$delete_row";
          $ada_error = $kolom;
        } else {
          $sty = $kolom == 'ID' ? 'abu f12' : 'green';
        }
      }
    } elseif (
      $kolom == 'ITEM' || $kolom == 'DESKRIPSI'
    ) {
      $sty = 'abu f12'; // tanpa validasi
    } elseif (
      $kolom == 'TGL_MASUK'
    ) {
      if (validateDate($isi)) {
        $sty = 'green';
      } else {
        $sty = 'red';
        $gradasi = 'merah';
        $info = 'Gunakan format YYYY-MM-DD';
        $ada_error = $kolom;
      }
    } elseif (
      $kolom == 'PO'
    ) {
      // PO length == 9 || STOCK
      if (strlen($isi) != 9 and strlen($isi) != 11) {
        if (
          $isi == 'STOCK'
        ) {
          // kecuali PO STOCK
          $sty = 'green';
          $gradasi = 'kuning';
        } elseif (
          $isi == '101900494R'
          || $isi == '101900420R'
          || $isi == 'R50701311A'
          || $isi == 'R50901311A'
        ) {
          $sty = 'yellow';
          $gradasi = 'kuning';
          $info = 'Exception untuk PO 101900494R, 101900420R, R50701311A, R50901311A';
        } else {
          $sty = 'red';
          $gradasi = 'merah';
          $info = 'PO harus 9 karakter atau 11 karakter';
          $ada_error = $kolom;
        }
      } else {
        $sty = 'green';
      }
    } elseif (
      $kolom == 'LOT'
    ) {
      if ($isi) {
        $gradasi == 'hijau';
      }
    } elseif (
      $kolom == 'LOC'
    ) {
      if (in_array($isi, $arr_lokasi)) {
        $sty = 'green';
      } else {
        $sty = 'red';
        $gradasi = 'merah';
        if ($isi) {
          $info = "Lokasi $isi tidak ditemukan pada database <a class='btn btn-primary btn-sm' href='?add_lokasi&kode=$isi&id_kategori=$id_kategori&from=import_data'>Add</a>";
        } else {
          $info = "Lokasi tidak boleh kosong";
        }
        $ada_error = $kolom;
      }
    } elseif (
      $kolom == 'QTY_AWAL' || $kolom == 'MASUK' || $kolom == 'KELUAR'
    ) {
      $sty = 'abu consolas f12';
    } elseif (
      $kolom == 'SISA_STOCK'
    ) {
      // $isi = floatval($isi); // 0,4 menjadi 0 (error)
      if ($isi) {
        $sty = 'green';
      } else {
        $sty = 'red';
        $gradasi = 'merah';
        $info = "Sisa Stock tidak boleh 0";
        $ada_error = $kolom;
      }
    } elseif (
      $kolom == 'SATUAN'
    ) {
      if (in_array($isi, $arr_satuan)) {
        $sty = 'green';
      } else {
        $sty = 'red';
        $gradasi = 'merah';
        $info = "Satuan $isi tidak terdaftar pada tabel satuan.";
        $ada_error = $kolom;
      }
    } elseif (
      $kolom == 'CEK_TAHUN' || $kolom == 'BULAN' || $kolom == 'TAHUN'
    ) {
      $sty = 'abu f12 miring';
      $gradasi = 'kuning';
    } elseif ($kolom == 'last_update') {
      $sty = 'hideit';
    } else {
      // unknown kolom
      $sty = 'red';
      $gradasi = 'kuning';
      $info = "Belum ada validasi untuk kolom $kolom";
      $ada_error = $kolom;
    }















    $tds .= "<td class='$sty gradasi-$gradasi'>$isi<div class='red f10 consolas'>$info</div></td>";
  }
  if ($i < 4 || $i == $jumlah_row) { // untuk menghemat memory browser
    $tr .= "<tr>$tds</tr>";
  } elseif ($i == 4) {
    $tr .= "<tr class='gradasi-kuning'><td colspan=100% class='f12 abu miring consolas bg-yellow'>data lainnya disembunyikan untuk menghemat memory browser...</td></tr>";
  }
  if ($ada_error) {
    $tr .= "<tr>$tds</tr>";
    break;
  }
}

if ($ada_error) {
  $next = div_alert('danger', "Masih terdapat invalid-data pada kolom $ada_error. Silahkan di cek isinya! ");
} else {
  $next = div_alert('success', 'Semua Data CSV pada tabel importer sudah valid.') . "<hr><a class='btn btn-success btn-sm' href='?import_data_barang&id_kategori=$id_kategori'>Next Insert Data Barang (if not exist)</a>";
}

$th = '';
foreach ($arr_header as $kolom) {
  $th .= "<th>$kolom</th>";
}

echo "
  <table class='table table-bordered'>
    <thead class=f12><th>No</th>$th</thead>
    $tr
  </table>
  <div>$next</div>
";
