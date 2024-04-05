<?php
$judul = 'Manage Item Kumulatif';
set_title($judul);

$pesan = '';
$pesan_qty_kum_nol = '';
$form_tambah_kumulatif = '';

# ============================================
# DELETE KUMULATIF PROCESSOR
# ============================================
if (isset($_POST['btn_delete_item_kumulatif'])) {
  $s = "DELETE FROM tb_sj_kumulatif WHERE id=$_POST[btn_delete_item_kumulatif]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  echo div_alert('info', 'Delete item kumulatif sukses.');
  jsurl('', 1000);
}

# ============================================
# UPDATE KUMULATIF PROCESSOR
# ============================================
if (isset($_POST['btn_simpan'])) {
  $id_kumulatif = $_POST['id_kumulatif'];

  $s = "SELECT 
  a.is_fs,
  b.kode_barang,
  c.kode_po  
  FROM tb_sj_kumulatif a 
  JOIN tb_sj_item b ON a.id_sj_item=b.id 
  JOIN tb_sj c ON b.kode_sj=c.kode 
  WHERE a.id=$id_kumulatif";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  if (mysqli_num_rows($q) == 0) die('Data tidak ditemukan.');
  if (mysqli_num_rows($q) > 1) die(erid('(id::not_uniq)'));
  $d = mysqli_fetch_assoc($q);

  $kode_po = $d['kode_po'] ?? die('kode_po::null');
  $kode_barang = $d['kode_barang'] ?? die('kode_barang::null');
  $is_fs = $d['is_fs']; // ?? die('is_fs::null');

  $kode_lokasi = $_POST['kode_lokasi'] ?? die('kode_lokasi::null');
  $no_lot = $_POST['no_lot'] ?? die('no_lot::null');

  // ID - PO - LOT - LOKASI - FS
  $kode_kumulatif = "$kode_barang~$kode_po~$no_lot~$kode_lokasi~$is_fs";
  $kode_kumulatif = strtoupper(str_replace(' ', '', $kode_kumulatif));

  $s = "SELECT 1 FROM tb_sj_kumulatif WHERE kode_kumulatif='$kode_kumulatif'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    echo div_alert('danger', "Untuk Lot $no_lot dan lokasi $kode_lokasi sudah ada. Silahkan pakai kode lot/lokasi lainnya!<hr><a href='javascript:history.go(-1)'>Kembali</a>");
    exit;
  }


  unset($_POST['id_kumulatif']);
  unset($_POST['btn_simpan']);
  unset($_POST['keterangan_barang']);

  $pairs = '__';
  foreach ($_POST as $key => $value) {
    $value = clean_sql($value);
    $value = $value == '' ? 'NULL' : "'$value'";
    $pairs .= ",$key = $value";
  }
  $pairs .= ",kode_kumulatif = '$kode_kumulatif'";
  $pairs = str_replace('__,', '', $pairs);

  $s = "UPDATE tb_sj_kumulatif SET $pairs WHERE id=$id_kumulatif";
  echo $s;
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));

  jsurl();
  exit;
}

if (isset($_POST['btn_tambah_item_kumulatif']) || isset($_POST['btn_tambah_item_kumulatif_fs'])) {

  $pesan = '';
  $id_sj_item = $_POST['id_sj_item'] ?? die(erid('id_sj_item'));
  $kode_lokasi = $_POST['kode_lokasi'] ?? die(erid('kode_lokasi'));
  $no_lot = $_POST['no_lot'] ?? die(erid('no_lot'));
  $nomor = $_POST['nomor'] ?? die(erid('nomor'));
  $kode_barang = $_POST['kode_barang'] ?? die(erid('kode_barang'));
  $kode_po = $_POST['kode_po'] ?? die(erid('kode_po'));
  $is_fs = isset($_POST['btn_tambah_item_kumulatif_fs']) ? 1 : 'NULL';
  $is_fs_empty_space = $is_fs == 1 ? 1 : '';

  // ID - PO - LOT - LOKASI - FS
  $kode_kumulatif = "$kode_barang~$kode_po~$no_lot~$kode_lokasi~$is_fs_empty_space";
  $kode_kumulatif = strtoupper(str_replace(' ', '', $kode_kumulatif));

  // check duplikasi
  $pesan .= '<br>checking duplikasi kumulatif... ';
  $s = "SELECT 1 FROM tb_sj_kumulatif WHERE kode_kumulatif='$kode_kumulatif'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    echo div_alert('danger', "Untuk Lot $no_lot dan lokasi $kode_lokasi sudah ada. Silahkan pakai kode lot/lokasi lainnya!");
    jsurl('', 3000);
  }
  $pesan .= 'no-duplikat.';


  $s = "INSERT INTO tb_sj_kumulatif 
  (
    id_sj_item,kode_lokasi,kode_kumulatif,no_lot,nomor,is_fs
  ) VALUES (
    $id_sj_item,'$kode_lokasi','$kode_kumulatif','$no_lot',$nomor,$is_fs
  )";
  $pesan .= '<br>inserting new kumulatif... ';
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $pesan .= 'success.';

  $pesan .= div_alert('success', 'Tambah Kumulatif Item sukses.');

  echo $pesan;
  jsurl('', 2000);
  exit;
}

$id_sj_item = $_GET['id_sj_item'] ?? die(erid('id_sj_item'));
if (!$id_sj_item) {
  echo div_alert('danger', 'id_sj_item is null');
  exit;
}
$s = "SELECT a.*,
a.id as id_sj_item,
a.kode_sj,
b.kode_po,
a.qty as qty_adjusted,
b.tanggal_terima,
d.nama as nama_barang,
d.kode as kode_barang,
d.kode_lama,
d.id_kategori,
d.satuan,
e.kode as kategori,
e.nama as nama_kategori,
f.step,
(
  SELECT SUM(p.qty) 
  FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id  
  WHERE q.id_sj_item=a.id and q.is_fs is null) qty_kumulatif,
(
  SELECT SUM(p.qty) 
  FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id  
  WHERE q.id_sj_item=a.id and q.is_fs is not null) qty_kumulatif_fs,
(
  SELECT SUM(p.qty) FROM tb_roll p 
  JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
  JOIN tb_sj_item r ON q.id_sj_item=r.id 
  WHERE q.id_sj_item!=a.id 
  AND q.is_fs is null 
  AND r.kode_barang=a.kode_barang) qty_parsial
    -- QTY Parsial adalah qty_datang pada penerimaan lain 
FROM tb_sj_item a 
JOIN tb_sj b ON a.kode_sj=b.kode 
JOIN tb_barang d ON a.kode_barang=d.kode 
JOIN tb_kategori e ON d.id_kategori=e.id 
JOIN tb_satuan f ON d.satuan=f.satuan 
WHERE a.id=$id_sj_item 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (!mysqli_num_rows($q)) die(div_alert('danger', 'Data Item Kumulatif tidak ditemukan'));
$d = mysqli_fetch_assoc($q);

$id_kategori = $d['id_kategori'];
$nama_barang = $d['nama_barang'];
$kode_barang = $d['kode_barang'];
$kode_lama = $d['kode_lama'];
$id_sj_item = $d['id_sj_item'];
$kode_po = $d['kode_po'];
$kode_sj = $d['kode_sj'];
$qty_parsial = $d['qty_parsial'];
$qty_adjusted = $d['qty_adjusted'];
$qty_kumulatif = $d['qty_kumulatif'];
$qty_kumulatif_fs = $d['qty_kumulatif_fs'];
$satuan = $d['satuan'];
$step = $d['step'];
// $pengiriman_ke = $d['pengiriman_ke'];
$kategori = $d['kategori'];
$nama_kategori = $d['nama_kategori'];
$tanggal_terima = $d['tanggal_terima'];

$qty_parsial = floatval($qty_parsial);
$qty_adjusted = floatval($qty_adjusted);
$qty_kumulatif = floatval($qty_kumulatif);
$qty_kumulatif_fs = floatval($qty_kumulatif_fs);

$qty_datang = $qty_kumulatif + $qty_kumulatif_fs;
$qty_kurang = $qty_adjusted - $qty_parsial;

$nama_kategori = ucwords(strtolower($nama_kategori));

$is_lebih = $qty_adjusted < $qty_datang ? 1 : 0;
$qty_tr_fs = 0;
if ($is_lebih) {
  $qty_tr_fs = $qty_datang - $qty_adjusted; // zzz uncheck
  $tr_free_supplier = "
    <tr class=blue>
      <td>QTY Lebih (Free Supplier)</td>
      <td>$qty_tr_fs $satuan</td>
    </tr>
  ";
} else {
  $tr_free_supplier = '';
}
?>
<div class="pagetitle">
  <h1><?= $judul ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=data_sj">Data SJ</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=manage_sj&kode_sj=<?= $kode_sj ?>">Manage SJ</a></li>
      <li class="breadcrumb-item active">Item Kumulatif</li>
    </ol>
  </nav>
</div>


<p>Page ini digunakan untuk pencatatan Item Kumulatif dan Pencetakan Label.</p>


<h2>Item: <?= $kode_barang ?> | <?= $nama_kategori ?></h2>
<table class="table table-hover">
  <tr>
    <td>Surat Jalan</td>
    <td><?= $kode_sj ?></td>
  </tr>
  <tr>
    <td>Nama Barang</td>
    <td><?= $nama_barang ?></td>
  </tr>
  <tr>
    <td>QTY Kurang</td>
    <td>
      <span id="qty_adjusted"><?= $qty_kurang ?></span>
      <span id="satuan"><?= $satuan ?></span>
    </td>
  </tr>
  <tr>
    <td>QTY Datang</td>
    <td>
      <span id="qty_datang"><?= $qty_datang ?></span> <?= $satuan ?>
    </td>
  </tr>
  <?= $tr_free_supplier ?>
</table>

<?php
$with_fs = $qty_tr_fs ? '/ Free Supplier' : '';
echo "<h2 class='mb3 mt4'>Item Kumulatif $with_fs</h2>";
echo $pesan;

$s = "SELECT 
a.no_lot,
a.is_fs,
a.kode_lokasi,
a.id as id_kumulatif,
a.tanggal_masuk,
c.kode as kode_barang,
c.nama as nama_barang,
c.keterangan as keterangan_barang,
c.satuan,
d.kode_po,
(
  SELECT sum(qty) FROM tb_roll WHERE id_kumulatif=a.id ) qty, 
(
  SELECT sum(qty) FROM tb_pick WHERE id_kumulatif=a.id ) sum_pick, 
(
  SELECT count(1) FROM tb_roll WHERE id_kumulatif=a.id ) count_roll 

FROM tb_sj_kumulatif a  
JOIN tb_sj_item b ON a.id_sj_item=b.id 
JOIN tb_barang c ON b.kode_barang=c.kode 
JOIN tb_sj d ON b.kode_sj=d.kode
WHERE a.id_sj_item=$id_sj_item
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$count_kumulatif = mysqli_num_rows($q);
$tr_kumulatif = '';
$div_kumulatif_item = '';
$i = 0;
$ada_kosong = 0;
$last_no_lot = '';
$last_no_roll = '';
$last_keterangan_barang = '';
$last_kode_lokasi = '';
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $id_kumulatif = $d['id_kumulatif'];
  $qty = $d['qty'];
  $satuan = $d['satuan'];
  $no_lot = $d['no_lot'];
  $kode_barang = $d['kode_barang'];
  $nama_barang = $d['nama_barang'];
  $keterangan_barang = $d['keterangan_barang'];
  $kode_po = $d['kode_po'];
  $tanggal_masuk = $d['tanggal_masuk'];
  $is_fs = $d['is_fs'];

  $pic_cd = '?'; //zzz skipped
  $aging = '?'; //zzz skipped
  if ($qty) {
    $last_no_lot = $d['no_lot'];
    $last_keterangan_barang = $d['keterangan_barang'];
    $last_kode_lokasi = $d['kode_lokasi'];

    $qty = floatval($qty);
  } else {
    $pesan_qty_kum_nol = "<div class=''>Silahkan <b class=darkblue>Manage Roll</b> untuk Summary QTY Kumulatif, klik pada tombol $img_sum</div>";
    $ada_kosong = 1;
  }


  if ($d['sum_pick']) {
    $picked_info = "<span class='kecil darkred'>Picked: " . floatval($d['sum_pick']) . '</span>';
  } else {
    $picked_info = 'unpick';
  }

  $count_roll = $d['count_roll'];

  if ($d['sum_pick'] || $d['count_roll']) {
    $btn_delete = '';
  } else {
    $btn_delete = "    
      <form method=post style='display:inline'>
        <button class=transparan name=btn_delete_item_kumulatif value=$id_kumulatif>$img_delete</button>
      </form>
    ";
  }

  $qty_show = $qty ? $qty : '<b class=red>0</b>';
  $no_lot_show = $d['no_lot'] ? $d['no_lot'] : '<i class="f12 consolas">null</i>';
  $is_fs_show = $is_fs ? $fs_icon : '-';
  $jam_masuk = '<div class="f12 abu">' . date('H:i', strtotime($tanggal_masuk)) . '</div>';
  $tanggal_masuk_show = date('d-m-y', strtotime($tanggal_masuk)) . $jam_masuk;

  $tr_kumulatif .= "
    <tr>
      <td>$i</td>
      <td class=kecil>
        <a href='?penerimaan&p=manage_roll&id_kumulatif=$id_kumulatif&last_no_lot=$last_no_lot&last_kode_lokasi=$last_kode_lokasi'>
          $qty_show $img_sum $btn_delete
        </a>
      </td>
      <td class=kecil>$d[satuan]</td>
      <td class=kecil>$no_lot_show</td>
      <td class=kecil>$d[kode_lokasi]</td>
      <td>$count_roll</td>
      <td>$is_fs_show</td>
      <td>
        $kode_barang 
        <div class='f12 abu'>$nama_barang</div> 
        <div class='f12 abu'>$keterangan_barang</div>
      </td>
      <td>$kode_po</td>
      <td>$pic_cd</td>
      <td>$aging</td>
      <td>$tanggal_masuk_show</td>
    </tr>
  ";
}

$debug .= "
<br>last_no_lot:$last_no_lot = '';
<br>last_no_roll:$last_no_roll = '';
<br>last_keterangan_barang:$last_keterangan_barang = '';
<br>last_kode_lokasi:$last_kode_lokasi = '';
";


if ($qty_datang < $qty_adjusted) {
  // barang kurang
  // $qty_sisa = $qty_datang-$qty_kumulatif;
  // $debug.= "qty_sisa:$qty_sisa = qty_datang:$qty_datang-qty_kumulatif:$qty_kumulatif;";
  $sisa_fs = 0;
} else {
  $sisa_fs = $qty_tr_fs - $qty_kumulatif_fs;
}

$qty_sisa = $qty_kurang - $qty_datang;
$debug .= "<br>qty_sisa:$qty_sisa = qty_adjusted:$qty_adjusted-qty_kumulatif:$qty_kumulatif";

































if (!$ada_kosong) {

  # ======================================================
  # FORM TAMBAH SUB-ITEM || FREE ITEM
  # ======================================================
  $new_count_kumulatif = $count_kumulatif + 1;

  # ==========================================
  # BLOK LOKASI
  # ==========================================
  $opt = '';
  $s = "SELECT blok FROM tb_blok WHERE id_kategori=$id_kategori";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  while ($d = mysqli_fetch_assoc($q)) {
    $opt .= "<option>$d[blok]</option>";
  }
  $select_blok_lokasi = "<select class='form-control mb2' name=blok_lokasi id=blok_lokasi><option value=0 selected>-- Blok Lokasi --</option>$opt</select>";

  # ==========================================
  # SELECT LOKASI
  # ==========================================
  $opt = '';
  $s = "SELECT a.* FROM tb_lokasi a 
  JOIN tb_blok b ON a.blok=b.blok 
  WHERE b.id_kategori=$id_kategori";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  while ($d = mysqli_fetch_assoc($q)) {
    $brand_show = $d['brand'] ?? '<i class="consolas f12">no brand</i>';
    $blok_underscore = str_replace('.', '_', $d['blok']);
    $opt .= "<option value='$d[kode]' class='opt_kode_lokasi opt_kode_lokasi__$blok_underscore'>$d[kode] ~ $brand_show</option>";
  }
  $select_lokasi = "
    <select class='form-control mb2' name=kode_lokasi id=kode_lokasi disabled>
      <option value=0 selected>-- Pilih Kode Lokasi --</option>
      $opt
    </select>
  ";

  if ($qty_sisa <= 0) {
    $btn = "<button class='btn btn-success ' name=btn_tambah_item_kumulatif_fs id=btn_tambah_item_kumulatif disabled>Tambah Kumulatif (Free Supplier)</button>";
  } else {
    $btn = "<button class='btn btn-primary ' name=btn_tambah_item_kumulatif id=btn_tambah_item_kumulatif disabled>Tambah Item Kumulatif</button>";
  }

  # ==========================================
  # FORM TAMBAH KUMULATIF
  # ==========================================
  $form_tambah_kumulatif = "
    <div class='mb1 bold darkblue'>QTY Sisa Kurang : <span id=qty_sisa>$qty_sisa</span> $satuan</div>
    <form method=post class='wadah gradasi-hijau' style=max-width:500px>
      <div class=sub_form>Form Tambah Kumulatif</div>
      <div class=mb2><span class='f12 abu'>ID:</span> $kode_barang, <span class='f12 abu'>PO:</span> $kode_po</div>
      <input class='form-control mb2 ' name=no_lot placeholder='Nomor Lot (opsional)'>
  
      <div class='row'>
        <div class='col-sm-6'>
          $select_blok_lokasi
        </div>
        <div class='col-sm-6'>
          $select_lokasi
        </div>
      </div>
      <div class='mt1 mb2'>
        <a href='?add_lokasi&id_kategori=$id_kategori'>Add Lokasi</a>
      </div>
  
      $btn
  
      <input type='hidden' name=id_sj_item value='$id_sj_item'>
      <input type='hidden' name=nomor value='$new_count_kumulatif'>
      <input type='hidden' name=kode_barang value='$kode_barang'>
      <input type='hidden' name=kode_po value='$kode_po'>
    </form>
  ";
}















# ==========================================
# FINAL ECHO
# ==========================================
if (!$tr_kumulatif) $tr_kumulatif = "<tr><td colspan=100%><div class='alert alert-danger'>Belum ada item kumulatif.</div></td></tr>";

echo "
  <table class=table>
    <thead>
      <th>No</th>
      <th>QTY Kumulatif</th>
      <th>UOM</th>
      <th>Lot</th>
      <th>Lokasi</th>
      <th>Roll Count</th>
      <th>FS</th>
      <th>ID</th>
      <th>PO</th>
      <th>PIC CD</th>
      <th>Aging</th>
      <th>Tanggal Masuk</th>
    </thead>
    $tr_kumulatif
  </table>
  <div class='f12 mt2 mb4'>Catatan: Item Kumulatif tidak bisa dihapus $img_delete jika sudah di pick atau sudah ada roll.</div>
  <div class=kecil>
    $pesan_qty_kum_nol
    $form_tambah_kumulatif
  </div>
";













































?>
<script>
  $(function() {
    $('#blok_lokasi').change(function() {
      let blok = $(this).val();
      let blok_underscore = blok.replace('.', '_');
      console.log(blok);
      let disabled = blok == 0 ? 1 : 0;
      $('.opt_kode_lokasi').hide();
      $('.opt_kode_lokasi__' + blok_underscore).show();
      $('#kode_lokasi').val(0);
      $('#kode_lokasi').prop('disabled', disabled);
      $('#btn_tambah_item_kumulatif').prop('disabled', 1);
    });
    $('#kode_lokasi').change(function() {
      let kode_lokasi = $(this).val();
      let disabled = kode_lokasi == 0 ? 1 : 0;
      $('#btn_tambah_item_kumulatif').prop('disabled', disabled);
    })
  })
</script>