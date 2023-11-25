<div class="pagetitle">
  <h1>Purchase Order / Penerimaan Barang</h1>
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

    .jumlah_po{
      font-family: verdana;
      font-size: 25px;
      color: #555;
    }

    .jumlah_item{
      color: #888;
      font-size: 14px;
    }
    .satuan{
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
          <a href="?data_po">Data PO</a> 
          <br><span class="jumlah_po"><?=12 ?> <span class=satuan>PO</span></span>
          <br><span class="jumlah_item"><?=3412 ?> <span class=satuan>item</span></span>
        </div>
        <div>
          <a href="?penerimaan">Penerimaan</a> 
          <br><span class="jumlah_po"><?=11 ?> <span class=satuan>PO</span></span>
          <br><span class="jumlah_item"><?=3411 ?> <span class=satuan>item</span></span>
        </div>
        <div>
          <a href="?penempatan">Penempatan</a> 
          <br><span class="jumlah_po"><?=9 ?> <span class=satuan>PO</span></span>
          <br><span class="jumlah_item"><?=3051 ?> <span class=satuan>item</span></span>
        </div>
        <div>
          <a href="?stok_gudang">Stok Gudang</a> 
          <br><span class="jumlah_po"><?=9 ?> <span class=satuan>PO</span></span>
          <br><span class="jumlah_item"><?=2912 ?> <span class=satuan>item</span></span>
        </div>
      </div>
    </div>
    
  </div>
</section>

