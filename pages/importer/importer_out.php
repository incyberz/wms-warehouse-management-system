<?php
# ===========================================
# SYARAT VARIABEL
# ===========================================
$id_kategori = $_GET['id_kategori'] ?? '';
$get_trx = $_GET['trx'] ?? 'in';
$trx = $get_trx;

if (isset($_POST['btn_ulang_dari_awal'])) {
  $s = "DELETE FROM tb_importer_out";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('success', 'Reset tabel importer sukses.');

  // $s = "DELETE FROM tb_importer_po";
  // $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  // echo div_alert('success', 'Reset tabel importer_po sukses.');

  // $s = "DELETE FROM tb_importer_kumulatif";
  // $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  // echo div_alert('success', 'Reset tabel importer_kumulatif sukses.');

  jsurl();
}

if (!$id_kategori) {

  $s = "SELECT * FROM tb_importer_out LIMIT 1";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    echo div_alert('info', 'Pada tabel importer sudah ada data temporer. Klik Tombol Import Data atau Anda dapat Ulang dari Awal
    <hr>
    <form method=post><button class="btn btn-danger btn-sm" name=btn_ulang_dari_awal>Ulang dari Awal</button></form>
    ');
  }

  echo "
    <h1>Import Data dari Excel</h1>
    <hr>
    <h2 class=mb4>Import Penerimaan</h2>
    <a href='?importer&id_kategori=1' class='btn btn-success mb2'>Penerimaan Aksesoris</a>
    <a href='?importer&id_kategori=2' class='btn btn-success mb2'>Penerimaan Fabric</a>
    <hr>
    <h2 class=mb4>Import Pengeluaran</h2>
    <a href='?importer&id_kategori=1&trx=out' class='btn btn-warning mb2'>Pengeluaran Aksesoris</a>
    <a href='?importer&id_kategori=2&trx=out' class='btn btn-warning mb2'>Pengeluaran Fabric</a>
  ";
  exit;
}

















# ===========================================
# REUPLOAD CSV PROCESSORS
# ===========================================
if (isset($_POST['btn_reupload_csv'])) {
  if (unlink("csv/tmp_out.csv")) {
    // upload berhasil
    echo div_alert('success', 'Hapus CSV Pengeluaran berhasil.');
    jsurl('', 2000);
  } else {
    // upload gagal
    die(div_alert('danger', 'Tidak bisa hapus file CSV Pengeluaran temporer.'));
  }
}

# ===========================================
# FILES PROCESSORS
# ===========================================
if (isset($_FILES['input_file_csv'])) {
  if (move_uploaded_file($_FILES['input_file_csv']['tmp_name'], "csv/tmp_out.csv")) {
    // upload berhasil
    echo div_alert('success', 'Upload CSV Pengeluaran berhasil.');
    jsurl('', 2000);
  } else {
    // upload gagal
    die(div_alert('danger', 'Tidak bisa move upload file CSV Pengeluaran.'));
  }
}

# ===========================================
# POST PROCESSORS
# ===========================================
if (isset($_POST['btn_delete_row'])) { // delete row
  echolog('deleting row');
  $s = "DELETE FROM tb_importer_out WHERE id_auto = $_POST[btn_delete_row]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echolog('sukses');
  jsurl('', 500);
}

if (isset($_POST['btn_set_null'])) { // set null id lama
  echolog('updating tb_importer_out, set null ID');
  $s = "UPDATE tb_importer_out SET ID = NULL where id_auto = $_POST[btn_set_null]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echolog('sukses');
  jsurl('', 500);
}

if (isset($_POST['btn_update_konten'])) {
  echolog('updating tb_importer_out, updating ID-lama');
  $s = "UPDATE tb_importer_out SET ID = '$_POST[ID]' where id_auto = $_POST[btn_update_konten]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echolog('sukses');
  jsurl('', 500);
}

if (isset($_POST['btn_update_dg_id_lama'])) {
  echolog('Replacing ID-baru dengan id-lama');
  $s = "UPDATE tb_importer_out SET ID_BARU = '$_POST[ID]' where id_auto = $_POST[btn_update_dg_id_lama]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echolog('sukses');
  jsurl('', 500);
}




















# ===========================================
# DESAIN JUDUL
# ===========================================
$ada_error = 0;
$judul = 'Import Data Pengeluaran ' . $arr_kategori[$id_kategori];
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
      <li class="breadcrumb-item"><a href="?importer">Importer Home</a></li>
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
# CSV Pengeluaran HANDLER
# ===========================================
$file_tmp_csv = 'csv/tmp_out.csv';
if (file_exists($file_tmp_csv)) {
  $ada_file_csv = 1;
} else { // FILE CSV Pengeluaran BELUM ADA
  // UPLOAD CSV Pengeluaran AVAILABLE
  $ada_file_csv = 0;
}

# ===========================================
# IMPORT CSV Pengeluaran KE TABEL IMPORTER
# ===========================================
if (isset($_POST['btn_import_csv'])) {

  // recheck
  $s = "SELECT * FROM tb_importer_out LIMIT 1";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    die('Sudah ada data di tabel importer. Proses harus diulang dari awal.');
  }

  $arr_csv = baca_csv($file_tmp_csv, ';');

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
  echolog('process CSV Pengeluaran to array... sukses');



  # ===========================================
  # INSERT CSV Pengeluaran INTO TB_IMPORTER
  # ===========================================
  echolog('<b class="darkblue f20">inserting csv data... mohon tunggu! waktu selesai tergantung banyaknya data</b>');
  foreach ($arr_csv as $key => $d) {
    $values = '';

    if ($key == 0) continue; // skip header

    // looping column data
    if ($d) { // selama masih ada row di csv 
      foreach ($d as $key2 => $isi) {
        if ($key2 > 15) continue; // abaikan data jika lebih dari count header
        // echolog("column #$key2 = $isi");
        $isi = str_replace('\'', '`', $isi);
        $isi = strtoupper(trim($isi));
        $isi = $isi ? "'$isi'" : 'NULL';
        $values .= ",$isi";
      }
      $values = substr($values, 1); // remove first comma
      echolog("inserting data #$key");
      $s = "INSERT INTO tb_importer_out ($arr_header_kolom_importer) VALUES ($values)";

      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      echolog('sukses');
      // if ($key > 10) die();
    }
  }

  echo '<hr><b class="bold green f20">SUKSES INSERT DATA CSV Pengeluaran KE TABEL IMPORTER. Silahkan refresh!</b>';
  jsurl('', 5000);
}


























































# ===========================================
# MAIN SELECT
# ===========================================
// loop untuk mendapatkan semua baris
$tr = '';
$i = 0;
$s = "SELECT * FROM tb_importer_out";
echolog('loop data from tabel importer');
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_row = mysqli_num_rows($q);
if (!$jumlah_row) {
  if ($ada_file_csv) {
    $hapus_dan_reupload = "
      <form method=post style='display:inline' class=m0>
        <button class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin untuk reupload CSV Pengeluaran?\")' name=btn_reupload_csv>Reupload CSV Pengeluaran</button>
      </form>
    ";
    echo div_alert('info', "File CSV Pengeluaran sudah ada. <hr> $hapus_dan_reupload");
    echo "
      <form method=post>
          <button class='btn btn-primary' name=btn_import_csv>Import semua data CSV Pengeluaran ke Tabel Importer</button>
      </form>
    ";
  } else {
    echo div_alert('info', "File CSV Pengeluaran belum ada.");
    echo "
      <form method=post enctype='multipart/form-data'>
        <div class='alert alert-info'>
          Belum ada data pada tabel importer.
        </div>
        <div class='wadah gradasi-hijau'>
          <div class='mb1 abu'>File CSV Stok Pengeluaran</div>
          <input required type=file class='form-control mb2' name=input_file_csv accept=.csv />
          <button class='btn btn-primary'>Upload Stok Format CSV</button>
          <div class='mb2 mt1  miring f12'>Jika file Anda masih format Excel, silahkan Save As dahulu dalam format CSV. Untuk baris pertama berupa nama-nama kolom yang harus sesuai dengan <a class='tebal green bg-yellow p1' href='csv/template-stok-pengeluaran.xlsx' target=_blank>Contoh Template Stok Pengeluaran XLSX</a></div>
        </div>
      </form>
    ";
  }
  exit;
} else {
  echo div_alert('info', "Terdapat $jumlah_row data pada tabel importer.");
}

while ($d = mysqli_fetch_assoc($q)) {
  // loop setiap baris
  # ===========================================
  # HAPUS JIKA KODE LOKASI DAN ID BARU NULL
  # ===========================================
  if (!$d['LOC'] && !$d['ID_BARU']) {
    echolog('menghaspus data dengan lokasi dan id_baru null');
    $s2 = "DELETE FROM tb_importer_out WHERE id_auto=$d[id_auto]";
    $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
    continue;
  }


  $tds =  '';
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
          strpos($isi, ' ') || strlen($isi) < 9 || strlen($isi) > 15
        ) {
          // ga boleh ada spasi
          // minimal 9 karakter
          // maksimal 15 karakter
          $sty = 'red';
          $gradasi = 'merah';
          $info = "ID jangan ada spasi, minimal 9 karakter, maksimal 15 karakter$form_update$set_null$delete_row";
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
          $info = "Lokasi $isi tidak ditemukan pada database <a class='btn btn-primary btn-sm' href='?add_lokasi&kode=$isi&id_kategori=$id_kategori&from=importer'>Add</a>";
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
    } elseif ($kolom == 'last_update' || $kolom == 'id_sj_item' || $kolom == 'nomor') {
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
$s = "DESCRIBE tb_importer_out";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
while ($d = mysqli_fetch_assoc($q)) {
  $th .= "<th>$d[Field]</th>";
}

echo "
  <table class='table table-bordered'>
    <thead class=f12>$th</thead>
    $tr
  </table>
  <div>$next</div>
";
