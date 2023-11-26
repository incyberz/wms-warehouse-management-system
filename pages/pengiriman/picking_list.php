<?php if(!isset($img_add)){ ?>
  <div class="pagetitle">
    <h1>Proses Picking</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?do">Home DO</a></li>
        <li class="breadcrumb-item active">Picking</li>
      </ol>
    </nav>
  </div>

  <p>Picking adalah proses kedua setelah adanya Delivery Order. Terdiri dari:</p>
  <ul>
    <li>Pengecekan Stok Gudang</li>
    <li>Jika barang tersedia maka masukan ke Picking List</li>
    <li>Jika barang kosong maka pilih Retur DO pada item barang tersebut</li>
    <li>Jika barang tidak mencukupi, namun tetap dilakukan delivery seadanya maka tentukan QTY Deliveri dan selisih kekurangan dari QTY Request akan masuk ke Retur DO</li>
  </ul>

  <div class='alert alert-info'>
    Proses Picking harus diawali dengan pembuatan atau pemilihan Delivery Order. 
    <hr>
    <a href="?do&p=data_do">Pilih Data DO</a> | <a href="?do&p=tambah_do">Tambah DO baru</a>
  </div>



  
<?php }else{ ?>
<!-- ================================================ -->
<!-- INTERNAL PICKING LIST -->
<!-- ================================================ -->
<div class="wadah gradasi-hijau">
  <h2 class=tengah>Picking List</h2>
  <div class="wadah">
    <div class="row">
      <div class="col-6">
        <div class="row">
          <div class="col-4 miring abu">Nomor DO</div>
          <div class="col-8"><?=$id_do?></div>
          <div class="col-4 miring abu">Delivery No</div>
          <div class="col-8">19836436</div>
          <div class="col-4 miring abu">Operator</div>
          <div class="col-8">Iin Sholihin</div>
        </div>
      </div>
      <div class="col-6">
        <div class="row">
          <div class="col-4 miring abu">Tanggal Pengiriman</div>
          <div class="col-8">09 - 10 - 2023</div>
          <div class="col-4 miring abu">OTP</div>
          <div class="col-8">IN2</div>
          <div class="col-4 miring abu">Total QTY</div>
          <div class="col-8">6.102.43</div>
        </div>
      </div>
    </div>
  </div>

  <table class="table table-striped">
    <thead class=gradasi-hijau>
      <th>NO</th>
      <th>ITEM</th>
      <th>NAMA ITEM</th>
      <th>DESKRIPSI</th>
      <th>LOT</th>
      <th>LOCATION</th>
      <th>NOMOR PO</th>
      <th>QTY</th>
    </thead>
    
    <tr>
      <td>1</td>
      <td>K001</td>
      <td>KAIN KATUN JET BLACK <?=$img_delete?></td>
      <td>(tees/hand wash) 25x70 putih hitam </td>
      <td class=tengah>1982372001</td>
      <td class=tengah>A.A2.1</td>
      <td class=tengah>101907734</td>
      <td class=kanan>948.12</td>
    </tr>
    <tr>
      <td>2</td>
      <td>K002</td>
      <td>KAIN JET BLACK <?=$img_delete?></td>
      <td>Quibusdam reiciendis suscipit nam. Adipisci eum, cupiditate blanditiis voluptatem reprehenderit.</td>
      <td class=tengah>1982372001</td>
      <td class=tengah>A.B1.1</td>
      <td class=tengah>10786777</td>
      <td class=kanan>1.123.12</td>
    </tr>
    <tr>
      <td>3</td>
      <td>K003</td>
      <td>KATUN JET BLACK <?=$img_delete?></td>
      <td>100% cotton (tees/hand wash) 25x70 putih hitam</td>
      <td class=tengah>1982372001</td>
      <td class=tengah>A.A3.2</td>
      <td class=tengah>106436363</td>
      <td class=kanan>763.67</td>
    </tr>
    <tr>
      <td><span class='miring abu kecil'>3</span></td>
      <td colspan=7 ><span class="green pointer">Tambah item <?=$img_add?></span></td>
    </tr> 
  
  </table>
  <hr>
  <button class='btn btn-primary'>Cetak Picking List</button>
</div>
<?php } ?>