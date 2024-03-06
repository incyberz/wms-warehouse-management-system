<?php

if (!in_array($id_role, [1, 2, 3, 9])) {
  // jika bukan petugas wh
  $pesan = div_alert('info', "<p>$img_locked Maaf, <u>hak akses Anda tidak sesuai</u> dengan fitur ini. Silahkan hubungi Pihak Warehouse jika ada kesalahan. terimakasih</p>");
  echo "
    <div class='pagetitle'>
      <h1>Proses Retur</h1>
      <nav>
        <ol class='breadcrumb'>
          <li class='breadcrumb-item'><a href='?'>Home Dashboard</a></li>
        </ol>
      </nav>
    </div>
    $pesan
  ";
  exit;
}
# ========================================================
# PETUGAS WH ONLY
# ========================================================
$pesan_tambah = '';
$today = date('Y-m-d');
$now = date('Y-m-d H:i:s');
$now_eng = date('M d, Y, H:i:s');

$id_kumulatif = $_GET['id_kumulatif'] ?? '';
if (!$id_kumulatif) {
  include 'retur_info.php';
  exit;
}





















# ========================================================
# PROCESOR SET TANGGAL QC
# ========================================================
if (isset($_POST['btn_set_tanggal_qc'])) {
  $s = "UPDATE tb_sj_kumulatif SET kode_lokasi='$_POST[kode_lokasi]', tanggal_qc = '$_POST[tgl_qc] $_POST[jam_qc]' WHERE id=$_POST[btn_set_tanggal_qc]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  echo div_alert('info', 'updating Tanggal QC...');
  jsurl('', 1000);
}

# ========================================================
# PROCESOR btn_tambah_retur
# ========================================================
if (isset($_POST['btn_tambah_retur'])) {
  echo 'Processing retur...<hr>';

  $alasan_retur = $_POST['alasan_retur'];
  $alasan_retur = $alasan_retur ? "'$alasan_retur'" : 'NULL';

  $s = "INSERT INTO tb_retur 
  (
    id_kumulatif,qty,alasan_retur) VALUES 
  (
    $_POST[btn_tambah_retur],$_POST[qty],$alasan_retur)
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl('', 1000);
}
# ========================================================
# PROCESOR btn_delete_retur
# ========================================================
if (isset($_POST['btn_delete_retur'])) {
  echo 'deleting retur...<hr>';
  $s = "DELETE FROM tb_retur WHERE id=$_POST[btn_delete_retur]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl('', 1000);
}


























# ========================================================
# MAIN SELECT
# ========================================================
$s = "SELECT 
a.tmp_qty as qty_datang,
a.tanggal_masuk,
b.id as id_sj_item,
b.kode_sj,
c.kode_po,
d.kode_lama,
d.kode as kode_barang,
d.nama as nama_barang,
d.keterangan as keterangan_barang,
d.satuan,
a.no_lot,
a.kode_lokasi,
a.is_fs,
a.tanggal_qc,
d.id_kategori,
e.step

FROM tb_sj_kumulatif a 
JOIN tb_sj_item b ON a.id_sj_item=b.id 
JOIN tb_sj c ON b.kode_sj=c.kode 
JOIN tb_barang d ON b.kode_barang=d.kode 
JOIN tb_satuan e ON d.satuan=e.satuan  

WHERE a.id = $id_kumulatif
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q) > 1) die('Terdeteksi data duplikat.');
if (mysqli_num_rows($q) == 0) die(div_alert('danger', "Data item dg id_sj_item: $id_sj_item tidak ditemukan. | 
<a href='?rekap_kumulatif&id=&waktu=all_time'>Akses dari Rekap Penerimaan</a>"));
$d = mysqli_fetch_assoc($q);

$nama_barang = $d['nama_barang'];
$kode_barang = $d['kode_barang'];
$keterangan_barang = $d['keterangan_barang'];
$id_sj_item = $d['id_sj_item'];
$kode_po = $d['kode_po'];
$kode_sj = $d['kode_sj'];
// $qty_po = $d['qty_po'];
$qty_datang = $d['qty_datang'];
$satuan = $d['satuan'];
$step = $d['step'];
$kategori = $arr_kategori[$d['id_kategori']];
$tanggal_masuk = $d['tanggal_masuk'];

$qty_datang = floatval($qty_datang);

# =======================================================================
# PAGE TITLE & BREADCRUMBS 
# =======================================================================
set_title('Retur Barang');
?>
<div class="pagetitle">
  <h1>Retur Barang</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=data_sj">Data SJ</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=manage_sj&kode_sj=<?= $kode_sj ?>">Manage SJ</a></li>
      <li class="breadcrumb-item"><a href="?rekap_kumulatif&&id=<?= $kode_barang ?>&waktu=all_time">Rekap Penerimaan</a></li>
      <li class="breadcrumb-item active">Retur</li>
    </ol>
  </nav>
</div>
<p>Page ini digunakan untuk Proses Retur Barang.</p>


<?php
# =======================================================================
# KUMULATIF INFO 
# =======================================================================
$tr = '';
foreach ($d as $key => $value) {
  if (
    $key == 'id_sj_item'
    || $key == 'kode_sj'
    || $key == 'id_kategori'
    || $key == 'step'
  ) continue;
  $kolom = strtoupper(str_replace('_', ' ', $key));
  if ($key == 'is_fs') {
    $isi = $value ? 'FREE SUPPLIER ' . $img_fs : '<i class="abu f14">BUKAN FS</i>';
  } elseif ($key == 'tanggal_qc') {
    if ($value) {
      $isi = date('Y-m-d H:i', strtotime($value)) . " <span class=btn_aksi id=form_tanggal_qc__toggle>$img_edit</span>";
    } else {
      continue;
    }
  } elseif ($key == 'qty_datang') {
    $isi = floatval($value);
  } else {
    $isi = $value ? $value : $null;
  }

  $tr .= "
    <tr>
      <td>$kolom</td> 
      <td>$isi</td> 
    </tr>
  ";
}
echo "<h2>Kumulatif Item Properties</h2><table class=table>$tr</table>";























# =======================================================================
# FORM EDIT TANGGAL QC 
# =======================================================================
$opt = '';
$s2 = "SELECT kode,brand FROM tb_lokasi a 
JOIN tb_blok b ON a.blok=b.blok WHERE b.id_kategori = $d[id_kategori]";
$q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
while ($d2 = mysqli_fetch_assoc($q2)) {
  $selected = $d2['kode'] == $d['kode_lokasi'] ? 'selected' : '';
  $brand_show = $d2['brand'] ?? '<i class="f12 abu">(no brand)</i>';
  $opt .= "<option value='$d2[kode]' $selected>$d2[kode] ~ $brand_show</option>";
}
$select_lokasi = "<select class='form-control mb2' name=kode_lokasi>$opt</select>";

$tgl_qc = date('Y-m-d', strtotime($d['tanggal_qc']));
$value_tanggal_qc = $d['tanggal_qc'] ? $tgl_qc : $today;
$jam_qc = $d['tanggal_qc'] ? date('H:i', strtotime($d['tanggal_qc'])) : date('H:i', strtotime('now'));

$tanggal_qc_show = !$d['tanggal_qc'] ? $unset : "$tgl_qc $jam_qc";
$hide_form = $d['tanggal_qc'] ? 'hideit' : '';

echo "
  <form method=post class='wadah gradasi-hijau $hide_form' id=form_tanggal_qc>
    <div class=mb2>
      <label>
        <input required type=checkbox>
        Saya menyatakan QC telah selesai untuk item kumulatif ini
      </label>
    </div>

    <div class='f14 abu mb1'>Pindahkan ke Lokasi: (opsional)</div>
    $select_lokasi

    <div class='f14 abu mb1'>Tanggal Selesai QC: $tanggal_qc_show</div>
    <div class=flexy>
      <div>
        <input class='form-control' required type=date max='$today' name=tgl_qc value='$value_tanggal_qc' placeholder='Tanggal QC'>
      </div>
      <div>
      <input class='form-control' required type=time value='$jam_qc' name=jam_qc placeholder='Jam QC'>
      </div>
      <div>
      <button class='btn btn-success' onclick='return confirm(\"Set Tanggal QC? Status item akan berpindah dari Transit ke QC-Pass. \")' name=btn_set_tanggal_qc value=$id_kumulatif>Set Tanggal QC</button>
      </div>
    </div>
    <div class='miring abu f12 mt1'>Jika Tanggal QC sudah terisi maka status item akan berpindah dari Transit ke <b>QC-Pass</b> .</div>
  </form>
";



























# =======================================================================
# TABEL RETUR 
# =======================================================================
if ($d['tanggal_qc']) {
  $tr = '';
  $s2 = "SELECT a.*, a.id as id_retur,
  (
    SELECT sum(qty) FROM tb_ganti WHERE id_retur=a.id) qty_ganti 
  FROM tb_retur a 
  WHERE a.id_kumulatif=$id_kumulatif";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  $count_retur = mysqli_num_rows($q2);
  $i = 0;
  while ($d2 = mysqli_fetch_assoc($q2)) {
    $i++;
    $id_retur = $d2['id_retur'];
    $alasan_retur = $d2['alasan_retur'] ?? $null;
    $qty_ganti = floatval($d2['qty_ganti']);
    $qty = floatval($d2['qty']);
    $btn_delete = $qty_ganti ? '-' : "<form method=post class=m0><button class=transparan value=$id_retur name=btn_delete_retur>$img_delete</button></form>";
    $tr .= "
      <tr>
        <td>$i</td>
        <td>$qty</td>
        <td>$alasan_retur</td>
        <td>$d2[tanggal_retur]</td>
        <td>$qty_ganti <a href='?ganti&id_retur=$id_retur'>$img_next</a></td>
        <td>
          $btn_delete
        </td>
      </tr>
    ";
  }
  $tr = $tr ? $tr : '<tr><td colspan=100% ><div class="alert alert-warning">Belum ada data retur</div></td></tr>';

  $tr_tambah = "
    <tr>
      <td colspan=100% >
        <form method=post>
          <div class=flexy>
            <div class='abu f12'>
              $img_add
            </div>
            <div>
              <input type=number step=$d[step] min=$d[step] max=$d[qty_datang] required placeholder='QTY Retur' class='form-control mb2' name=qty>
            </div>
            <div>
              <input placeholder='alasan (opsional)...' class='form-control mb2' name=alasan_retur>
            </div>
            <div>
              <button class='btn btn-info' name=btn_tambah_retur value=$id_kumulatif>Tambah Retur</button>
            </div>
          </div>
        </form>
      </td>
    </tr>
  ";


  echo "
  <div class='wadah gradasi-abu'>
    <div class=sub_form>Form Retur</div>
    <table class=table>
      <thead class='gradasi-toska'>
        <th>No</th>
        <th>QTY Retur</th>
        <th>Alasan</th>
        <th>Tanggal</th>
        <th>QTY Ganti</th>
        <th>Aksi</th>
      </thead>
      $tr
      $tr_tambah
    </table>
  </div>
  ";
}
