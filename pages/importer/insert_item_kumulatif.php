<?php
$id_kategori = $_GET['id_kategori'] ?? die(erid('id_kategori'));
$judul = 'Insert Item Kumulatif ' . $arr_kategori[$id_kategori];
set_title($judul);
?>
<div class="pagetitle">
  <h1><?= $judul ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?importer">Import</a></li>
      <li class="breadcrumb-item"><a href="?import_data_barang">Import Barang</a></li>
      <li class="breadcrumb-item"><a href="?import_data_po">Import PO</a></li>
      <li class="breadcrumb-item"><a href="?import_data_sj">Import SJ</a></li>
      <li class="breadcrumb-item"><a href="?import_data_item">Import Item</a></li>
      <li class="breadcrumb-item active"><?= $judul ?></li>
    </ol>
  </nav>
</div>
<?php




























# ===========================================
# POST PROCESSORS
# ===========================================
if (isset($_POST['btn_insert_item_kumulatif'])) {
  $s = "SELECT * FROM tb_importer a 
  JOIN tb_importer_kumulatif b ON a.id_auto=b.id_importer 
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $jumlah_importer = mysqli_num_rows($q);
  if ($jumlah_importer) {
    while ($d = mysqli_fetch_assoc($q)) {
      $id_auto = $d['id_auto'];
      $id_sj_item = $d['id_sj_item'];
      $nomor = $d['nomor'];
      $kode_lokasi = $d['LOC'];
      $kode_kumulatif = $d['kode_kumulatif'];
      $tmp_qty = $d['SISA_STOCK'];
      $no_lot = $d['LOT'];
      $no_lot_or_null = $no_lot ? "'$no_lot'" : 'NULL';

      if (!$id_sj_item || !$nomor) die('id_sj_item || nomor_urut_kumulatif tidak boleh null');

      // replace dot
      $tmp_qty = str_replace('.', '', $tmp_qty);
      // replace comma with dot
      $tmp_qty = str_replace(',', '.', $tmp_qty);

      // cek duplikat kumulatif
      $s2 = "SELECT 1 FROM tb_sj_kumulatif WHERE kode_kumulatif='$kode_kumulatif'";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      if (mysqli_num_rows($q2)) {
        // jika ada duplikat
        echolog("kode kumulatif <u class=darkred>$kode_kumulatif</u> telah ada pada database ==================== SKIPPED");
      } else {

        echolog("insert kumulatif <u class=darkred>$kode_kumulatif</u>");
        $s2 = "INSERT INTO tb_sj_kumulatif (
        id_sj_item, 
        kode_lokasi,
        kode_kumulatif,
        tmp_qty,
        no_lot,
        nomor,
        tanggal_masuk,
        tanggal_qc
        ) VALUES (
          $id_sj_item, 
          '$kode_lokasi',
          '$kode_kumulatif',
          $tmp_qty,
          $no_lot_or_null,
          $nomor,
          CURRENT_TIMESTAMP,
          CURRENT_TIMESTAMP
        )";
        $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      }

      echolog("deleting temporary kumulatif importer: <u class=darkred>$kode_kumulatif</u>");
      $s2 = "DELETE FROM tb_importer WHERE id_auto=$id_auto";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));

      // echo '<pre>';
      // var_dump($s2);
      // echo '</pre>';
      // exit;
    }
  } else {
    // empty tb_importer_po
    // zzz here
    // empty tb_importer_kumulatif
  }
}

if (isset($_POST['btn_update_id_sj_item_importer'])) {
  echolog('calculating kumulatif importer');
  $s = "SELECT id_auto,PO,ID_BARU FROM tb_importer WHERE id_sj_item IS NULL";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $jumlah_unready = mysqli_num_rows($q);
  echolog("$jumlah_unready items", false);
  if ($jumlah_unready) {
    while ($d = mysqli_fetch_assoc($q)) {
      $PO = $d['PO'];
      $ID_BARU = $d['ID_BARU'];
      $s2 = "SELECT a.id as id_sj_item FROM tb_sj_item a 
      JOIN tb_sj b ON a.kode_sj=b.kode 
      WHERE b.kode_po='$PO' AND a.kode_barang='$ID_BARU'";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
      // die($s2);
      if (mysqli_num_rows($q2)) {
        $d2 = mysqli_fetch_assoc($q2);
        $id_sj_item = $d2['id_sj_item'];
        $s3 = "UPDATE tb_importer SET id_sj_item='$id_sj_item' WHERE id_auto='$d[id_auto]'";
        $q3 = mysqli_query($cn, $s3) or die(mysqli_error($cn));
        // die($s3);
      } else {
        die('id_sj_item tidak ditemukan');
      }
    }
  }


  echo div_alert('info', 'Update all null item sukses.');
  jsurl();
  exit;
}

if (isset($_POST['btn_update_nomor_urut_kumulatif'])) {
  echolog('calculating kumulatif importer');
  $s = "SELECT id_auto,PO,nomor FROM tb_importer WHERE nomor is null ORDER BY PO,ID_BARU ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $jumlah_unready = mysqli_num_rows($q);
  echolog("$jumlah_unready items", false);
  $last_po = '';
  $last_nomor = 0;
  $nomor = 0;
  if ($jumlah_unready) {
    while ($d = mysqli_fetch_assoc($q)) {
      $id_auto = $d['id_auto'];
      $PO = $d['PO'];
      // $nomor = $d['nomor'];

      // if last_po equal to PO maka nomor++
      echolog("last_po:$last_po == PO:$PO");
      if ($last_po == $PO) {
        $nomor++;
        echolog("EQUAL... nomor:$nomor", 0);
      } else {
        // else nomor = 1
        $nomor = 1;
        echolog("NOT EQUAL ====================================== ... nomor:$nomor", 0);
      }

      $s2 = "UPDATE tb_importer SET nomor='$nomor' WHERE id_auto='$id_auto'";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));

      $last_po = $PO;
      // last_po = po
    }
  }


  echo div_alert('info', 'Update all null item sukses.');
  jsurl();
  exit;
}






































# ===========================================
# MAIN SELECT
# ===========================================
echolog('calculating PO surat jalan');
$s = "SELECT 1 FROM tb_importer_po ";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_po_importer = mysqli_num_rows($q);
echolog("$jumlah_po_importer items", false);

echolog('calculating kumulatif importer with id_sj_item is null');
$s = "SELECT 1 FROM tb_importer WHERE id_sj_item IS NULL";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_unready_id_sj_item = mysqli_num_rows($q);
echolog("$jumlah_unready_id_sj_item items", false);
if ($jumlah_unready_id_sj_item) {
  echo "
  <form method=post>
  <button class='btn btn-primary' name=btn_update_id_sj_item_importer>Update $jumlah_po_importer id_sj_item pada $jumlah_unready_id_sj_item Kumulatif Importer</button>
  </form>
  ";
} else { // id_sj_item semua terisi
  echo div_alert('success', 'Semua kumulatif item pada importer sudah punya id_sj_item.');

  echolog('calculating kumulatif importer with no_urut_kumulatif is null');
  $s = "SELECT 1 FROM tb_importer WHERE nomor IS NULL";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $jumlah_unready_number = mysqli_num_rows($q);
  echolog("$jumlah_unready_number items", false);

  if ($jumlah_unready_number) {
    echo "
      <form method=post>
        <button class='btn btn-primary' name=btn_update_nomor_urut_kumulatif>Update $jumlah_unready_number Nomor Urut Kumulatif Importer</button>
      </form>
    ";
  } else { // id_sj_item dan nomor semua terisi
    echo div_alert('success', 'Semua kumulatif item pada importer sudah punya nomor urut kumulatif.');
    $s = "SELECT 1 FROM tb_importer ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    $jumlah_kumulatif_importer = mysqli_num_rows($q);
    if ($jumlah_kumulatif_importer) {
      echo "
        <form method=post>
          <button class='btn btn-primary' name=btn_insert_item_kumulatif>Insert $jumlah_kumulatif_importer Item Kumulatif Importer</button>
        </form>
      ";
    } else { // tabel importer sudah kosong
      // hapus file csv/tmp.csv
      $file_tmp_csv = "csv/tmp.csv";
      if (file_exists($file_tmp_csv)) {
        if (unlink($file_tmp_csv)) {
          echo div_alert('success', 'File temporer CSV berhasil dihapus.');
        } else {
          die(div_alert('danger', 'Tidak bisa menghapus file temporer CSV.'));
        }
      } else {
        echo div_alert('info', 'File temporer CSV sudah dihapus.');
      }

      // delete from tb_importer_po
      echolog('deleting tb_importer_po');
      $s = "DELETE FROM tb_importer_po";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      echolog('sukses');

      // delete from tb_importer_kumulatif
      echolog('deleting tb_importer_kumulatif');
      $s = "DELETE FROM tb_importer_kumulatif";
      $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
      echolog('sukses');

      echo div_alert('success', 'Proses Import Kumulatif seselai.');
      echo "<hr><a href='?insert_data_roll&id_kategori=$id_kategori' class='btn btn-success'>Next: Insert Data Roll</a>";
    }
  }
}
