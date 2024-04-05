<?php
$page_tujuan = "pages/$parameter.php";
if (!isset($parameter)) die('Routing memerlukan parameter.');

$arr_routing = [
  '' => 'pages/dashboard/dashboard',
  'dashboard' => 'pages/dashboard/dashboard',
  'home' => 'pages/dashboard/dashboard',
  'master' => 'pages/master/master',
  'importer' => 'pages/importer/importer',
  'importer_out' => 'pages/importer/importer_out',
  'import_data_barang' => 'pages/importer/import_data_barang',
  'cek_duplikat_kumulatif' => 'pages/importer/cek_duplikat_kumulatif',
  'import_data_item' => 'pages/importer/import_data_item',
  'import_data_po' => 'pages/importer/import_data_po',
  'import_data_sj' => 'pages/importer/import_data_sj',
  'import_data_kumulatif' => 'pages/importer/import_data_kumulatif',
  'insert_item_kumulatif' => 'pages/importer/insert_item_kumulatif',
  'insert_data_roll' => 'pages/importer/insert_data_roll',
  'super_delete_importer' => 'pages/importer/super_delete_importer',
  'add_lokasi' => 'pages/importer/add_lokasi',
  'manage_blok' => 'pages/importer/manage_blok',
  'stok_opname' => 'pages/stok/stok_opname',
  'report' => 'pages/stok/report',
];

if (array_key_exists($parameter, $arr_routing)) {
  include $arr_routing[$parameter] . '.php';
} else {
  switch ($parameter) {
    case 'update_barang':
      include 'pages/admin/update_barang.php';
      break;
    case 'penerimaan':
      include 'pages/penerimaan/penerimaan.php';
      break;
    case 'pengeluaran':
      include 'pages/pengeluaran/pengeluaran.php';
      break;
    case 'master_pengeluaran':
      include 'pages/pengeluaran/master_pengeluaran.php';
      break;
    case 'stok':
      include 'pages/stok/stok.php';
      break;
    case 'retur':
      include 'pages/retur/retur.php';
      break;
    case 'ganti':
      include 'pages/retur/ganti_retur.php';
      break;
    case 'terima_retur':
      include 'pages/retur/terima_retur.php';
      break;
    case 'cetak_label_penerimaan':
      include 'cetak_label.php';
      break;
    default:
      if (file_exists($page_tujuan)) {
        include $page_tujuan;
      } else {
        include 'na.php';
      };
  }
}
