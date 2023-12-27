

<?php
$p = $_GET['p'] ?? '';

if($p!=''){
  include "$p.php";
}else{



?>

<div class="pagetitle">
  <h1>Pengeluaran</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?pengeluaran">Pengeluaran</a></li>
      <li class="breadcrumb-item active">Home</li>
    </ol>
  </nav>
</div>

<section>
  <a href="?pengeluaran&p=terima_do">Buat DO</a>
</section>

<?php } ?>