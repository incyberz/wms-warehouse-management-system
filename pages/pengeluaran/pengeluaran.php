

<?php
$p = $_GET['p'] ?? '';
set_title('Pengeluaran');

if($p!=''){
  include "$p.php";
}else{



?>

<div class="pagetitle">
  <h1>Pengeluaran</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?">Home Dashboard</a></li>
      <li class="breadcrumb-item active">Terima Delivery Order</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  <style type="text/css">
  #blok_step{
    /*background-color: #afa;*/
    /*background-image: url('img/alur_pmb.png');*/
  }

  #blok_steps{
    width: 100%;
    display: grid;
    grid-template-columns: 25% 25% 25% 25%;
  }

  #blok_steps div{
    /*border: solid 1px red;*/
    text-align: center;
    /* font-size: 14px; */
    /*margin-bottom: 20px;*/
  }

  .jumlah_podo{
    font-family: verdana;
    font-size: 25px;
    color: #555;
  }

  .jumlah_item_podo{
    color: #888;
    font-size: 14px;
  }
  .satuan_podo{
    color: #aaa;
    font-size: 12px;
  }
  </style>

  <div style='max-width:900px'>
    <div class='mt-4 mb-2' id="blok_step">
      <img src="assets/img/pengiriman.png" width="100%">
    </div>
    <div id="blok_steps">
      <div>
        <a href="?pengeluaran&p=data_do">Data DO</a> 
        <!-- <br><span class="jumlah_podo"><?=12 ?> <span class=satuan_podo>PO</span></span>
        <br><span class="jumlah_item_podo"><?=3412 ?> <span class=satuan_podo>item</span></span> -->
      </div>
      <div>
        <a href="?pengeluaran&p=terima_do">Terima DO Baru</a> 
        <!-- <br><span class="jumlah_podo"><?=11 ?> <span class=satuan_podo>PO</span></span>
        <br><span class="jumlah_item_podo"><?=3411 ?> <span class=satuan_podo>item</span></span> -->
      </div>
      <div>
        <a href="?pengeluaran&p=packing">Packing</a> 
        <!-- <br><span class="jumlah_podo"><?=9 ?> <span class=satuan_podo>PO</span></span>
        <br><span class="jumlah_item_podo"><?=3051 ?> <span class=satuan_podo>item</span></span> -->
      </div>
      <div>
        <a href="?pengeluaran&p=shipping">Shipping</a> 
        <!-- <br><span class="jumlah_podo"><?=9 ?> <span class=satuan_podo>PO</span></span>
        <br><span class="jumlah_item_podo"><?=2912 ?> <span class=satuan_podo>item</span></span> -->
      </div>
    </div>
  </div>
</section>

<?php } ?>