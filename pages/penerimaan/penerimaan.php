<?php 
//$p = $_GET['p'] ?? jsurl('?'); 
$p='penerimaan';

$tr_hasil = "
  <tr>
    <td>Lokasi</td>
    <td>PO / Supplier</td>
    <td>ID / Item / Keterangan</td>
    <td>Tanggal Masuk</td>
    <td>QTY / Data Lebih</td>
    <td>Proyeksi</td>
  </tr>
";

$input_select = "<select class='form-control form-control-sm'><option>--pilih--</option></select>";
$input_text = "<input type=text class='form-control form-control-sm' />";
$input_date = "<input type=date class='form-control form-control-sm' />";
$input_number = "<input type=number class='form-control form-control-sm' />";

$new_lokasi = $input_select;
$new_po = $input_text;
$new_id = $input_text;
$new_tgl = $input_date;
$new_qty = $input_number;
$new_proyeksi = $input_text;


$tr_new = "
  <tr>
    <td>$new_lokasi</td>
    <td>$new_po</td>
    <td>$new_id</td>
    <td>$new_tgl</td>
    <td>$new_qty</td>
    <td>$new_proyeksi</td>
  </tr>
";
?>
<section class='section'>
  <?php include 'pages/sheet_nav.php'; ?>
  <div id="blok_<?=$p?>" class='mt2'>

    <table class='table'>
      <tr>
        <td colspan=100% class='kecil abu'>Filter:</td>
      </tr>
      <tr>
        <td><input type="text" class="form-control form-control-sm" placeholder='lokasi'></td>
        <td><input type="text" class="form-control form-control-sm" placeholder='PO'></td>
        <td><input type="text" class="form-control form-control-sm" placeholder='ID'></td>
        <td><input type="text" class="form-control form-control-sm" placeholder='yyyy-mm-dd'></td>
        <td>-</td>
        <td><input type="text" class="form-control form-control-sm" placeholder='proyeksi'></td>
      </tr>
      <tr>
        <td colspan=100%>10 data dari 3567 records</td>
      </tr>
      <tr class='tebal gradasi-hijau tengah'>
        <td>Lokasi</td>
        <td>PO / Supplier</td>
        <td>ID / Item / Keterangan</td>
        <td>Tanggal Masuk</td>
        <td>QTY / Data Lebih</td>
        <td>Proyeksi</td>
      </tr>
      <?=$tr_hasil?>
      <tr>
        <td colspan=100% class='kecil abu'>Tambah Penerimaan:</td>
      </tr>
      <?=$tr_new?>



    </table>

  </div>
</section>