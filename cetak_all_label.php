<style>
  *{background:white}
</style>
<?php
include 'insho_styles.php';
echo "<link href='assets/vendor/bootstrap/css/bootstrap.min.css' rel='stylesheet'>";
echo "<link href='assets/css/style.css' rel='stylesheet'>";

if(isset($_POST['btn_cetak_label'])){
  $kode_barang = $_POST['kode_barang'];
  $no_po_dll = $_POST['no_po_dll'];
  $nama_barang = $_POST['nama_barang'];
  $keterangan_barang = $_POST['keterangan_barang'];
}


if(isset($_POST['btn_cetak_semua_label']) || isset($_GET['id_roll'])){

  $get_id_roll = $_GET['id_roll'] ?? '';
  $sql_where = $get_id_roll ? "h.id=$get_id_roll" : "a.id=$_POST[btn_cetak_semua_label]";

  include 'conn.php';
  $s = "SELECT
  a.no_lot,
  a.kode_lokasi, 
  e.kode as kode_po,
  d.kode as kode_barang,
  d.kode_lama as kode_lama,
  d.nama as nama_barang,
  d.keterangan as keterangan_barang,
  d.satuan,
  e.tanggal_terima,
  g.brand as this_brand,
  h.no_roll,
  h.qty 


  FROM tb_sj_kumulatif a 
  JOIN tb_sj_item c ON a.id_sj_item=c.id 
  JOIN tb_barang d ON c.kode_barang=d.kode 
  JOIN tb_sj e ON c.kode_sj=e.kode 
  JOIN tb_lokasi g ON a.kode_lokasi=g.kode 
  JOIN tb_roll h ON a.id=h.id_kumulatif 

  WHERE $sql_where";

  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0) die('Data Item Kumulatif tidak ditemukan.');


  $i=0;
  while($d=mysqli_fetch_assoc($q)){

    $nama_barang = $d['nama_barang'];
    $kode_barang = $d['kode_barang'];
    $kode_lama = $d['kode_lama'];
    $keterangan_barang = $d['keterangan_barang'];
    $kode_po = $d['kode_po'];
    $no_lot = $d['no_lot'];
    $no_roll = $d['no_roll'];
    $kode_lokasi = $d['kode_lokasi'];
    $this_brand = $d['this_brand'];
    $qty = $d['qty'];
    $satuan = $d['satuan'];
    $tanggal_terima = $d['tanggal_terima'];
    $tgl = date('d-m-y',strtotime($tanggal_terima));

    $qty = floatval($qty);
    $this_brand = $this_brand ? " $this_brand" : '';

    $arr_is_fs[$i] = $d['is_fs'] ?? 0;
    $arr_kode_barang[$i] = $kode_barang;
    $arr_no_po_dll[$i] =  "$kode_po $no_lot ($qty)$satuan $no_roll ($kode_lokasi $this_brand) $tgl";
    $arr_keterangan_barang[$i] = $keterangan_barang;
    $arr_nama_barang[$i] = $nama_barang;
    $i++;
  }
}

if(isset($arr_kode_barang)){
  foreach ($arr_kode_barang as $key => $value) {
    $kode_barang = $arr_kode_barang[$key];
    $no_po_dll = $arr_no_po_dll[$key];
    $keterangan_barang = $arr_keterangan_barang[$key];
    $nama_barang = $arr_nama_barang[$key];
    $is_fs = $arr_is_fs[$key];

    include 'cetak_label.php';
  }

}elseif(isset($kode_barang)){
  include 'cetak_label.php';
}else{
  echo '<h1>Page ini tidak dapat diakses secara langsung.</h1>';
  ?><script>
    setTimeout(function(){
      location.replace('index.php');
    },3000);
  </script><?php
}