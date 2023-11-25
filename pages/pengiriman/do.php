<?php
include 'podo_styles.php';

$p = $_GET['p'] ?? '';
if($p!=''){
  if(file_exists("pages/pengiriman/$p.php")){
    include "$p.php";
  }else{
    include 'na.php';
  }
}else{

$id_do = 1; //zzz debug
?>

<div class="pagetitle">
  <h1>Delivery Order / Pengiriman Barang</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?">Home Dashboard</a></li>
      <li class="breadcrumb-item active">Delivery Order</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  <div class="row">
    <div class="col-md-6">
      <div class='mt-4 mb-2' id="blok_step">
        <img src="assets/img/pengiriman.png" width="100%">
      </div>
      <div id="blok_steps">
        <div>
          <a href="?do&p=data_do">Data DO</a> 
          <br><span class="jumlah_podo"><?=12 ?> <span class=satuan_podo>DO</span></span>
          <br><span class="jumlah_item_podo"><?=3412 ?> <span class=satuan_podo>item</span></span>
        </div>
        <div>
          <a href="?do&p=picking_list">Picking</a> 
          <br><span class="jumlah_podo"><?=11 ?> <span class=satuan_podo>DO</span></span>
          <br><span class="jumlah_item_podo"><?=3411 ?> <span class=satuan_podo>item</span></span>
        </div>
        <div>
          <a href="?do&p=packing">Packing</a> 
          <br><span class="jumlah_podo"><?=9 ?> <span class=satuan_podo>DO</span></span>
          <br><span class="jumlah_item_podo"><?=3051 ?> <span class=satuan_podo>item</span></span>
        </div>
        <div>
          <a href="?do&p=shipping">Shipping</a> 
          <br><span class="jumlah_podo"><?=9 ?> <span class=satuan_podo>DO</span></span>
          <br><span class="jumlah_item_podo"><?=2912 ?> <span class=satuan_podo>item</span></span>
        </div>
      </div>
    </div>
    
  </div>
</section>

<?php }?>