<?php
// $p = $_GET['p'] ?? '';
$p = isset($_GET['p']) ? $_GET['p'] : '';
if($p!=''){
  if(file_exists("pages/laporan/$p.php")){
    include "$p.php";
  }else{
    include 'na.php';
  }
}else{
?>

<div class="pagetitle">
  <h1>Laporan</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?">Home Dashboard</a></li>
      <li class="breadcrumb-item active">Laporan</li>
    </ol>
  </nav>
</div>

<p>Page laporan dapat menampilkan:</p>
<ul>
  <li>Stok Opname Gudang</li>
  <li>QTY PO + retur per tanggal/minggu/bulan</li>
  <li>QTY DO + retur per tanggal/minggu/bulan</li>
</ul>

<div class="alert alert-danger">Page ini masih dalam tahap pengembangan. Terimakasih.</div>

<?php }?>