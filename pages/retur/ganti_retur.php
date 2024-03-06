<?php
$judul = 'Ganti Retur';

if (!in_array($id_role, [1, 2, 3, 9])) {
  // jika bukan petugas wh
  $pesan = div_alert('info', "<p>$img_locked Maaf, <u>hak akses Anda tidak sesuai</u> dengan fitur ini. Silahkan hubungi Pihak Warehouse jika ada kesalahan. terimakasih</p>");
  echo "
    <div class='pagetitle'>
      <h1>$judul</h1>
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

$id_retur = $_GET['id_retur'] ?? '';
if (!$id_retur) die(erid('id_retur'));





















# ========================================================
# PROCESOR SET TANGGAL QC
# ========================================================
if (isset($_POST['btn_set_tanggal_qc'])) {
  $s = "UPDATE tb_sj_kumulatif SET kode_lokasi='$_POST[kode_lokasi]', tanggal_qc = '$_POST[tgl_qc] $_POST[jam_qc]' WHERE id=$_POST[btn_set_tanggal_qc]";
  echo '<pre>';
  var_dump($s);
  echo '</pre>';
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  echo div_alert('info', 'updating Tanggal QC...');
  jsurl('', 10000);
}

# ========================================================
# PROCESOR btn_tambah_ganti
# ========================================================
if (isset($_POST['btn_tambah_ganti'])) {
  echo 'Processing ganti...<hr>';

  $s = "INSERT INTO tb_ganti 
  (
    id_retur,qty) VALUES 
  (
    $_POST[btn_tambah_ganti],$_POST[qty])
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl('', 1000);
}
# ========================================================
# PROCESOR btn_delete_ganti
# ========================================================
if (isset($_POST['btn_delete_ganti'])) {
  echo 'deleting ganti...<hr>';
  $s = "DELETE FROM tb_ganti WHERE id=$_POST[btn_delete_ganti]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl('', 1000);
}


























# ========================================================
# MAIN SELECT
# ========================================================
$s = "SELECT 
a.qty, 
a.tanggal_retur, 
a.alasan_retur,
a.id_kumulatif,
b.id_sj_item,
c.kode_sj, 
c.kode_barang, 
e.kode_lama,
e.nama as nama_barang, 
e.keterangan as keterangan_barang,
e.satuan, 
f.step
FROM tb_retur a 
JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id 
JOIN tb_sj_item c ON b.id_sj_item=c.id 
JOIN tb_sj d ON c.kode_sj=d.kode 
JOIN tb_barang e ON c.kode_barang=e.kode 
JOIN tb_satuan f ON e.satuan=f.satuan 
WHERE a.id = $id_retur 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q) == 0) die('Data retur tidak ditemukan');
$d = mysqli_fetch_assoc($q);
$kode_sj = $d['kode_sj'];
$id_sj_item = $d['id_sj_item'];
$id_kumulatif = $d['id_kumulatif'];
$kode_barang = $d['kode_barang'];

# =======================================================================
# PAGE TITLE & BREADCRUMBS 
# =======================================================================
set_title($judul);
?>
<div class="pagetitle">
  <h1><?= $judul ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=data_sj">Data SJ</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=manage_sj&kode_sj=<?= $kode_sj ?>">Manage SJ</a></li>
      <li class="breadcrumb-item"><a href="?rekap_kumulatif&id=<?= $kode_barang ?>&waktu=all_time">Rekap Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?retur&id_kumulatif=<?= $id_kumulatif ?>">Retur</a></li>
      <li class="breadcrumb-item active"><?= $judul ?></li>
    </ol>
  </nav>
</div>
<p>Page ini digunakan untuk Proses Pergantian Retur Barang.</p>


<?php
# =======================================================================
# KUMULATIF INFO 
# =======================================================================
$tr = '';
foreach ($d as $key => $value) {
  $kolom = strtoupper(str_replace('_', ' ', $key));
  if (
    $key == 'id'
    || $key == 'id_kumulatif'
    || $key == 'id_sj_item'
    || $key == 'step'
  ) {
    continue;
  } elseif ($key == 'is_fs') {
    $isi = $value ? 'FREE SUPPLIER ' . $img_fs : '<i class="abu f14">BUKAN FS</i>';
  } elseif ($key == 'tanggal_ganti') {
    if ($value) {
      $isi = date('d-M-Y H:i', strtotime($value));
    } else {
      continue;
    }
  } elseif ($key == 'qty') {
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
echo "<h2>Retur Properties</h2><table class=table>$tr</table>";


















































# =======================================================================
# TABEL GANTI 
# =======================================================================
if ($d['qty']) {
  $tr = '';
  $s2 = "SELECT a.*, a.id as id_ganti  
  FROM tb_ganti a 
  WHERE a.id_retur=$id_retur";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  $count_ganti = mysqli_num_rows($q2);
  $i = 0;
  while ($d2 = mysqli_fetch_assoc($q2)) {
    $i++;
    $id_ganti = $d2['id_ganti'];
    $qty = floatval($d2['qty']);
    $tr .= "
      <tr>
        <td>$i</td>
        <td>$qty</td>
        <td>$d2[tanggal_ganti]</td>
        <td>
          <form method=post class=m0>
            <button class=transparan value=$id_ganti name=btn_delete_ganti>$img_delete</button>
          </form>
        </td>
      </tr>
    ";
  }
  $tr = $tr ? $tr : '<tr><td colspan=100% ><div class="alert alert-warning">Belum ada data ganti</div></td></tr>';

  $tr_tambah = "
    <tr>
      <td colspan=100% >
        <form method=post>
          <div class=flexy>
            <div class='abu f12'>
              $img_add
            </div>
            <div>
              <input type=number step=$d[step] min=$d[step] max=$d[qty] required placeholder='QTY Ganti' class='form-control mb2' name=qty>
            </div>
            <div>
              <button class='btn btn-info' name=btn_tambah_ganti value=$id_retur>Tambah Ganti</button>
            </div>
          </div>
        </form>
      </td>
    </tr>
  ";


  echo "
  <div class='wadah gradasi-abu'>
    <div class=sub_form>Form Ganti</div>
    <table class=table>
      <thead class='gradasi-toska'>
        <th>No</th>
        <th>QTY Ganti</th>
        <th>Tanggal</th>
        <th>Aksi</th>
      </thead>
      $tr
      $tr_tambah
    </table>
  </div>
  ";
}
