<?php
$nama_sender = 'CV. BIENSI FESYENINDO';
$alamat_sender = 'Jl. Cimincrang No. 2B Kota Bandung, Jawa Barat 40613 Indonesia Telepon (022)-7802482';

$nama_buyer = 'KABAG PRODUKSI TANJUNGSARI';
$alamat_buyer = 'Bagian Produksi, Jalan Pasar Tanjungsari No. 1 Sumedang Jawa Barat';

$img_edit = '<img class="zoom pointer" src="assets/img/icons/edit.png" alt="edit" height=20px>';
$img_delete = '<img class="zoom pointer" src="assets/img/icons/delete.png" alt="delete" height=20px>';
$img_add = '<img class="zoom pointer" src="assets/img/icons/add.png" alt="add" height=20px>';

?>

<div class="pagetitle">
  <h1>Tambah DO</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?do">DO Home</a></li>
      <li class="breadcrumb-item"><a href="?do&p=data_do">Data DO</a></li>
      <li class="breadcrumb-item active">Tambah DO</li>
    </ol>
  </nav>
</div>

<div class="bordered p2 bg-white">

  <div class="mb1">
    <div class="row">
      <div class="col-4">
        <div class="bordered p1 h-100">
          <div class="tebal"><?=$nama_sender?></div>
          <div class="kecil">
            <?=$alamat_sender?>
          </div>
        </div>
      </div>
      <div class="col-4 tengah f30">
        <div class="bordered p1 h-100" style="margin: 0 -25px">
          Delivery Order
          <hr>
          201911877-DEL
        </div>
      </div>
      <div class="col-4">
        <div class="bordered p1 h-100">
          Kepada Yth.
          <div class="tebal "><?=$nama_buyer?></div> 
          <div><?=$alamat_buyer?></div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="tengah">
    <div class="row">
      <div class="col-7 " style="padding-right: 0">
        <div class="bordered p1 h-100 ">
          <div class="row">
            <div class="col-4">
              <div>No. Buyer</div>
              <div>BUY001</div>
            </div>
            <div class="col-4">
              <div class=" h-100 p1" style="border-left: solid 1px #ccc; border-right: solid 1px #ccc">
                <div>Kontak Personal</div>
                <div>Kabag Produksi Tanjungsari</div>
              </div>
            </div>
            <div class="col-4">
              <div>Tanggal Pemesanan</div>
              <div>05 - 09 - 2023</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-5 " style="padding-left:0">
        <div class="bordered p1 h-100">
          <div class="row">
            <div class="col-6">
              <div>Tanggal Pengiriman</div>
              <div>09 - 10 - 2023</div>
            </div>
            <div class="col-6">
              <div class="h-100" style="border-left: solid 1px #ccc">
                <div>Jangka Waktu Pembayaran</div>
                <div>-</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="">
    <div class="row">
      <div class="col-7 " style="padding-right: 0">
        <div class="bordered p1 h-100 ">
          <div class="row">
            <div class="col-4">
              <div>Tempat Pengiriman:</div>
            </div>
            <div class="col-8">
              <div class=" h-100 p1" style="border: none">
                <div><?=$alamat_buyer?></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-5 " style="padding-left:0">
        <div class="bordered p1 h-100">
          <div class="row">
            <div class="col-3">
              <div>Lampiran:</div>
            </div>
            <div class="col-9">
              <div class="h-100">
                <div>Surat Perintah Kerja No. 34/MNG/X/2023</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class='bordered mt1 p1'>
    Harap dikirim ke <?=$nama_buyer?>, <?=$alamat_buyer?>, barang-barang sesuai Picking List berikut: <?=$img_edit?>
  </div>
  
  <div class='bordered mt1 p1 mb4'>
    <div style='display:grid; grid-template-columns: 50px auto'>
      <div>Ket:</div>
      <div>
        <ul>
          <li class=mb2>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aspernatur quod, ipsam animi voluptates ex deserunt minima magnam ipsa tenetur distinctio quos iusto earum sunt rem illum adipisci repudiandae, fugit consequatur! <?=$img_edit?> <?=$img_delete?></li>
          <li class=mb2>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aspernatur quod, ipsam animi voluptates ex deserunt minima magnam ipsa tenetur distinctio quos iusto earum sunt rem illum adipisci repudiandae, fugit consequatur! <?=$img_edit?> <?=$img_delete?></li>
          <li class=mb2>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aspernatur quod, ipsam animi voluptates ex deserunt minima magnam ipsa tenetur distinctio quos iusto earum sunt rem illum adipisci repudiandae, fugit consequatur! <?=$img_edit?> <?=$img_delete?></li>
        </ul>
        <div class='mb1 pl4 ml2'><?=$img_add?></div>
      </div>
  
    </div>
  </div>
  
  <div class="wadah gradasi-hijau">
    <h2 class=tengah>Picking List</h2>
    <div class="wadah">
      <div class="row">
        <div class="col-6">
          <div class="row">
            <div class="col-4 miring abu">Nomor DO</div>
            <div class="col-8">K02123123</div>
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
        <th>KETERANGAN ITEM</th>
        <th>SATUAN</th>
        <th>QUANTITY</th>
        <th>HARGA</th>
        <th>JUMLAH</th>
      </thead>
      
      <tr>
        <td>1</td>
        <td>K001</td>
        <td>KAIN KATUN JET BLACK <?=$img_delete?></td>
        <td>KG</td>
        <td class=kanan>503.00</td>
        <td class=kanan>115.315.32</td>
        <td class=kanan>58.033.876.21</td>
      </tr>
      <tr>
        <td>2</td>
        <td>K027</td>
        <td>KAIN BATIK ORI CIREBON MEGA MENDUNG <?=$img_delete?></td>
        <td>KG</td>
        <td class=kanan>19.00</td>
        <td class=kanan>116.315.32</td>
        <td class=kanan>2.208.108.21</td>
      </tr> 
      <tr>
        <td><span class='miring abu kecil'>3</span></td>
        <td colspan=6 ><span class="green pointer">Tambah item <?=$img_add?></span></td>
      </tr> 
    
    </table>
    <hr>
    <button class='btn btn-primary'>Cetak Picking List</button>
  </div>
  
  <div class=' mt1 p1'>
    <div style='display:grid; grid-template-columns: 70px auto'>
      <div class=tebal>Catatan:</div>
      <div>
        <ul>
          <li class=mb2>Lorem ipsum dolor sit amet consectetur  <?=$img_edit?> <?=$img_delete?></li>
          <li class=mb2>Lorem ipsum dolor sit amet consectetur  <?=$img_edit?> <?=$img_delete?></li>
        </ul>
        <div class='mb1 pl4 ml2'><?=$img_add?></div>
      </div>
    </div>
  </div>
  
  <div class="bordered p1 tengah">
    Diverifikasi oleh:
  </div>
  <div class="tengah">
    <div class="row">
      <div class="col-7 " style="padding-right: 0">
        <div class="bordered p1 h-100 ">
          <div class="row">
            <div class="col-4">
              <div class='abu kecil'>Adm DO</div>
              <div>Ajat Sudrajat</div>
              <div class='miring green kecil'>at Sun, Dec 11, 2023 12:32:54</div>
            </div>
            <div class="col-4">
              <div class=" h-100 " style="border-left: solid 1px #ccc; border-right: solid 1px #ccc">
              <div class='abu kecil'>Departemen</div>
              <div>Suhendar</div>
              <div class='miring green kecil'>at Mon, Dec 12, 2023 08:32:54</div>
              </div>
            </div>
            <div class="col-4">
              <div class='abu kecil'>Departemen</div>
              <div>Ujang Aries</div>
              <div class='miring green kecil'>at Mon, Dec 12, 2023 09:30:34</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-5 " style="padding-left:0">
        <div class="bordered p1 h-100">
          <div class="row">
            <div class="col-6 darkred">
              <div class='abu kecil'>Direktur</div>
              <div>Sulaiman Arifin</div>
              <div class='miring kecil'>Unverified</div>
            </div>
            <div class="col-6">
              <div class="h-100" style="border-left: solid 1px #ccc">
                <div class='abu kecil'>Buyer</div>
                <div>Syarif Amir</div>
                <div class='miring green kecil'>at Fri, Dec 6, 2023 09:23:02</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="alert alert-info mt-2">Perhatian! Jika DO sudah disahkan (diverifikasi) maka DO dan item-itemnya tidak dapat lagi diubah.</div>

<div>
  <button class="btn btn-primary">Cetak DO</button>
  <button class="btn btn-danger">Hapus</button>
</div>