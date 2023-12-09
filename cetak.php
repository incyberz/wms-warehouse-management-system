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
  $jenis_bahan = $_POST['jenis_bahan'];
  $nama_barang = $_POST['nama_barang'];
}


if(isset($_POST['btn_cetak_semua_label'])){
  include 'conn.php';
  $s = "SELECT 
  a.*,
  e.kode as no_po,
  d.kode as kode_barang,
  d.nama as nama_barang,
  d.satuan,
  f.tanggal_terima,
  g.brand as this_brand


  FROM tb_bbm_subitem a 
  JOIN tb_bbm_item b ON a.id_bbm_item=b.id 
  JOIN tb_po_item c ON b.id_po_item=c.id 
  JOIN tb_barang d ON c.id_barang=d.id 
  JOIN tb_po e ON c.id_po=e.id 
  JOIN tb_bbm f ON b.id_bbm=f.id 
  JOIN tb_rak g ON a.kode_rak=g.kode 

  WHERE a.id_bbm_item=$_POST[btn_cetak_semua_label]";



  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0) die('Data BBM subitem tidak ditemukan.');


  $i=0;
  while($d=mysqli_fetch_assoc($q)){

    $nama_barang = $d['nama_barang'];
    $kode_barang = $d['kode_barang'];
    // $id_bbm_item = $d['id_bbm_item'];
    $no_po = $d['no_po'];
    $no_lot = $d['no_lot'];
    $no_roll = $d['no_roll'];
    $kode_rak = $d['kode_rak'];
    $this_brand = $d['this_brand'];
    $jenis_bahan = $d['jenis_bahan'];
    // $no_bbm = $d['no_bbm'];
    $qty = $d['qty'];
    $satuan = $d['satuan'];
    // $pengiriman_ke = $d['pengiriman_ke'];
    // $kategori = $d['kategori'];
    // $nama_kategori = $d['nama_kategori'];
    $tanggal_terima = $d['tanggal_terima'];
    $tgl = date('d-m-y',strtotime($tanggal_terima));

    $qty = str_replace('.0000','',$qty);


    // $id=$d['id'];
    $arr_kode_barang[$i] = $kode_barang;
    $arr_no_po_dll[$i] =  "$no_po $no_lot ($qty)$satuan $no_roll ($kode_rak $this_brand) $tgl";
    $arr_jenis_bahan[$i] = $jenis_bahan;
    $arr_nama_barang[$i] = $nama_barang;
    $i++;
  }
}

if(isset($arr_kode_barang)){
  foreach ($arr_kode_barang as $key => $value) {
    $kode_barang = $arr_kode_barang[$key];
    $no_po_dll = $arr_no_po_dll[$key];
    $jenis_bahan = $arr_jenis_bahan[$key];
    $nama_barang = $arr_nama_barang[$key];

    include 'pages/penerimaan/cetak_label.php';
  }

}elseif(isset($kode_barang)){
  include 'pages/penerimaan/cetak_label.php';
}else{
  echo '<h1>Page ini tidak dapat diakses secara langsung.</h1>';
  ?><script>
    setTimeout(function(){
      location.replace('index.php');
    },3000);
  </script><?php
}