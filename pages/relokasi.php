<?php
$judul = 'Relokasi';
set_title($judul);
if ($id_role != 3) die(div_alert('danger', 'Maaf, hanya login WH agar dapat mengakses fitur ini.'));

echo "
  <div class='pagetitle'>
    <h1>$judul</h1>
    <nav>
      <ol class='breadcrumb'>
        <li class='breadcrumb-item'><a href='?stok_opname'>Stok Opname</a></li>
        <li class='breadcrumb-item active'>$judul</li>
      </ol>
    </nav>
  </div>
";

if (isset($_POST['btn_relokasi'])) {
  if ($_POST['kode_lokasi_awal'] != $_POST['kode_lokasi_baru']) {
    echo '<pre>';
    var_dump($_POST);
    echo '</pre>';

    $s = "INSERT INTO tb_history_relokasi (
      id_kumulatif,
      kode_lokasi_awal,
      kode_lokasi_baru,
      relokasi_by
    ) VALUES (
      $_POST[btn_relokasi],
      '$_POST[kode_lokasi_awal]',
      '$_POST[kode_lokasi_baru]',
      $id_user
    )";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

    $s = "UPDATE tb_sj_kumulatif SET kode_lokasi='$_POST[kode_lokasi_baru]' WHERE id=$_POST[btn_relokasi]";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

    echo div_alert('success', 'Relokasi sukses.');
    jsurl();
    exit;
  } else {
    echo div_alert('info', 'Lokasi baru sama dengan lokasi awal.');
  }
}


$id_kumulatif = $_GET['id_kumulatif'] ?? '';
if (!$id_kumulatif) {
  echo "
    <div>Untuk relokasi item:</div>
    <ol>
      <li>Menuju <a href='?stok_opname&cat=fab'>Stok Opname Fabric</a> | <a href='?stok_opname&cat=aks'>Aksesoris</a></li>
      <li>Silahkan Filter berdasarkan ID mana yang akan direlokasi</li>
      <li>Klik salah satu lokasi pada Kolom Lokasi untuk Relokasi Item</li>
    </ol>
  ";
} else {
  $s = "SELECT 
  d.kode as kode_barang, 
  d.kode_lama,
  d.nama as nama_barang, 
  d.keterangan as keterangan_barang, 
  e.nama as kategori,
  a.is_fs,
  a.no_lot,
  a.kode_lokasi  
  FROM tb_sj_kumulatif a 
  JOIN tb_sj_item b ON a.id_sj_item=b.id 
  JOIN tb_sj c ON b.kode_sj=c.kode 
  JOIN tb_barang d ON b.kode_barang=d.kode  
  JOIN tb_kategori e ON d.id_kategori=e.id  
  WHERE a.id=$id_kumulatif
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (!mysqli_num_rows($q)) die(div_alert('danger', 'Data Kumulatif tidak ditemukan'));
  $d = mysqli_fetch_assoc($q);

  $tr = '';
  foreach ($d as $key => $value) {
    $tr .= "
      <tr>
        <td class='abu miring'>$key</td>
        <td>:</td>
        <td>$value</td>
      </tr>
    ";
  }

  $id_kategori = strtoupper($d['kategori'] == 'FABRIC') ? 2 : 1;
  $kode_lokasi_awal = $d['kode_lokasi'];

  $s = "SELECT * FROM tb_lokasi a 
  JOIN tb_blok b ON a.blok=b.blok 
  WHERE b.id_kategori=$id_kategori
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $opt = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $selected = $kode_lokasi_awal == $d['kode'] ? 'selected' : '';
    $brand_show = $d['brand'] ? $d['brand'] : '(no-brand)';
    $opt .= "<option value='$d[kode]' $selected>$d[kode] ~ BLOK-$d[blok] ~ $brand_show</option>";
  }


  echo "
    <table class=table>
      $tr
      <tr class='gradasi-hijau'>
        <td class='tebal darkblue'>Relokasi ke</td>
        <td>:</td>
        <td>
          <form method=post>
            <input type=hidden name=kode_lokasi_awal value='$kode_lokasi_awal'>
            <select name=kode_lokasi_baru class='form-control mb2'>$opt</select>
            <button class='btn btn-sm btn-primary w-100' name=btn_relokasi value='$id_kumulatif' onclick='return confirm(\"Relokasi item ini?\")'>Relokasi</button>
          </form>
        </td>
      </tr>

    </table>
  ";

  $s = "SELECT 
  a.kode_lokasi_awal, 
  a.kode_lokasi_baru,
  a.tanggal as tanggal_relokasi,
  b.nama as relocator  
  FROM tb_history_relokasi a 
  JOIN tb_user b ON a.relokasi_by=b.id 
  WHERE a.id_kumulatif=$id_kumulatif";
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

        $th .= "<th>$kolom</th>";
      }
    }
    $tr .= "
      <tr>
        $td
        <td><a href='?penerimaan&p=manage_roll&id_kumulatif=$id_kumulatif'>Cetak</a></td>
      </tr>
    ";
  }

  echo "
    
    <h2 class=mt4>History Relokasi</h2>
    <table class=table>
      <thead>
        $th
        <th>Label</th>
      </thead>
      $tr
    </table>
  ";
}
