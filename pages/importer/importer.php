<?php
# ===========================================
# SYARAT VARIABEL
# ===========================================
$id_kategori = 1; // AKS ZZZ DEBUG
$ada_error = 0;

$judul = 'Import Data Penerimaan ' . $arr_kategori[$id_kategori];
set_title($judul);
?>
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
foreach ($arr_csv as $key => $arr_row) {
  foreach ($arr_row as $key2 => $isi) {
    $isi = strtoupper(trim($isi));
    if (!$isi) { // jika cell kosong maka stop
      break;
    } else {
      array_push($arr_header, $isi);
    }
  }
  break; // hanya loop pertama saja
}

// loop untuk mendapatkan semua baris
$th = '';
$tr = '';
$valid_kolom = count($arr_header);
$i = 0;
foreach ($arr_csv as $key => $arr_row) {

  // loop setiap baris
  $tds = $key ? "<td class='f12 abu miring'>$i</td>" : '';
  $i++;
  foreach ($arr_row as $key2 => $isi) {
    if ($key2 >= $valid_kolom) { // abaikan data setelah valid kolom
      break;
    } else { // proses hanya valid kolom
      // bersihkan isi cell
      $isi = str_replace('\'', '`', strtoupper(trim($isi)));

      # ===========================================
      # VALIDASI UNTUK SETIAP KOLOM
      # ===========================================

      $sty = '';
      $gradasi = '';
      $info = '';
      $kolom = $arr_header[$key2];
      // debug
      // if ($kolom == 'ID' and $key == 3) {
      //   $isi = '12345 12234';
      // }
      if ($key) { // hanya baris 2 dst
        if (
          $kolom == 'ID' || $kolom == 'ID BARU'
        ) {
          if (strpos($isi, ' ') || strlen($isi) < 9 || strlen($isi) > 12) {
            // ga boleh ada spasi
            // minimal 9 karakter
            // maksimal 12 karakter
            $sty = 'red';
            $gradasi = 'merah';
            $info = 'ID jangan ada spasi, minimal 9 karakter, maksimal 12 karakter';
            $ada_error = $kolom;
          } else {
            $sty = $kolom == 'ID' ? 'abu f12' : 'green';
          }
        } elseif (
          $kolom == 'ITEM' || $kolom == 'DESKRIPSI'
        ) {
          $sty = 'abu f12'; // tanpa validasi
        } elseif (
          $kolom == 'TGL MASUK'
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
          if (strlen($isi) != 9) {
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
              $info = 'PO harus 9 karakter';
              $ada_error = $kolom;
            }
          } else {
            $sty = 'green';
          }
        } elseif ($kolom == 'LOT') {
          if ($isi) {
            $gradasi == 'hijau';
          }
        } elseif ($kolom == 'LOC') {
          if (in_array($isi, $arr_lokasi)) {
            $sty = 'green';
          } else {
            $sty = 'red';
            $gradasi = 'merah';
            $info = "Lokasi $isi tidak ditemukan pada database <a class='btn btn-primary btn-sm' href='?add_lokasi&kode=$isi&id_kategori=$id_kategori&from=import_data'>Add</a>";
            $ada_error = $kolom;
          }
        } elseif (
          $kolom == 'QTY AWAL' || $kolom == 'MASUK' || $kolom == 'KELUAR'
        ) {
          $sty = 'abu consolas f12';
        } elseif (
          $kolom == 'SISA STOCK'
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
          $kolom == 'CEK TAHUN' || $kolom == 'BULAN' || $kolom == 'TAHUN'
        ) {
          $sty = 'abu f12 miring';
          $gradasi = 'kuning';
        } else {
          // unknown kolom
          $sty = 'red';
          $gradasi = 'kuning';
          $info = 'Belum ada validasi untuk kolom ini';
          $ada_error = $kolom;
        }
      }


      if (!$key) { // baris pertama untuk header
        $th .= "<th>$kolom</th>";
      } else { // baris ke dua dst
        $tds    .= "<td class='$sty gradasi-$gradasi'>$isi<div class='red f10 consolas'>$info</div></td>";
      }
    }
  }
  if ($i < 4) { // untuk menghemat memory browser
    $tr .= "<tr>$tds</tr>";
  } elseif ($i == 4) {
    $tr .= "<tr><td colspan=100% class='f12 abu miring consolas'>data lainnya disembunyikan untuk menghemat memory...</td></tr>";
  }
  if ($ada_error) {
    $tr .= "<tr>$tds</tr>";
    break;
  }
}

if ($ada_error) {
  $next = div_alert('danger', "Masih terdapat invalid-data pada kolom $ada_error. Silahkan di cek isinya! ");
} else {
  $next = 'Next';
}

echo "
  <hr>Jumlah Valid Kolom: $valid_kolom<hr>
  <table class='table table-bordered'>
    <thead class=f12><th>No</th>$th</thead>
    $tr
  </table>
  <div>$next</div>
";
