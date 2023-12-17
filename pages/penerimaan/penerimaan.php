<span id="tb" class=hideit>po</span>
<?php
include 'podo_styles.php';
include 'pages/must_login.php';

// $p = $_GET['p'] ?? '';
$p = isset($_GET['p']) ? $_GET['p'] : '';
if($p!=''){
  if(file_exists("pages/penerimaan/$p.php")){
    include "$p.php";
  }else{
    include 'na.php';
  }
}else{
?>

<!-- for btn_aksi -->

<div class="pagetitle">
  <h1>Proses Penerimaan</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?">Home Dashboard</a></li>
      <li class="breadcrumb-item active">Purchase Order</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  <div class="row">

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
      font-size: 20px;
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

    <div class="col-md-6">
      <div class='mt-4 mb-2' id="blok_step">
        <img src="assets/img/penerimaan.png" width="100%">
      </div>
      <div id="blok_steps">
        <div>
          <a href="?penerimaan&p=data_sj">Terima Surat Jalan</a> 
          <br><span class="jumlah_podo"><?=12 ?> <span class=satuan_podo>PO</span></span>
          <br><span class="jumlah_item_podo"><?=3412 ?> <span class=satuan_podo>item</span></span>
        </div>
        <div>
          <a href="?penerimaan&p=terima_barang">Terima Barang</a> 
          <br><span class="jumlah_podo"><?=11 ?> <span class=satuan_podo>PO</span></span>
          <br><span class="jumlah_item_podo"><?=3411 ?> <span class=satuan_podo>item</span></span>
        </div>
        <div>
          <a href="?penerimaan&p=penempatan">Penempatan</a> 
          <br><span class="jumlah_podo"><?=9 ?> <span class=satuan_podo>PO</span></span>
          <br><span class="jumlah_item_podo"><?=3051 ?> <span class=satuan_podo>item</span></span>
        </div>
        <div>
          <a href="?penerimaan&p=stok_gudang">Stok Gudang</a> 
          <br><span class="jumlah_podo"><?=9 ?> <span class=satuan_podo>PO</span></span>
          <br><span class="jumlah_item_podo"><?=2912 ?> <span class=satuan_podo>item</span></span>
        </div>
      </div>
    </div>
    
  </div>
</section>

<?php } ?>