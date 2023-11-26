<?php
$nama_buyer = $nama_usaha;
$alamat_buyer = $alamat_usaha;
$telp_buyer = "$no_telp_kantor / $no_hp_kantor";
$wa_buyer = $no_wa_kantor;

$id_po = 'new';
$nomor_po = date('Ymd').'01-MTL';
$tanggal_pemesanan = date('Y-m-d');
$tanggal_pengiriman = date('Y-m-d');
$jangka_waktu = '45 hari kontrabon';
$perintah_po = "Harap dikirim ke $nama_buyer barang-barang sesuai jumlah, spesifikasi dibawah ini, dengan ketentuan sebagaimana tertera dalam Purchase Order ini:";

?>

<div class="pagetitle">
  <h1>Tambah PO</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?po">PO Home</a></li>
      <li class="breadcrumb-item"><a href="?po&p=data_po">Data PO</a></li>
      <li class="breadcrumb-item active">Tambah PO</li>
    </ol>
  </nav>
</div>

<div class="bordered p2 bg-white">

  <div class="mb1">
    <div class="row">
      <div class="col-4">
        <div class="bordered p1 h-100">
          <div class="tebal"><?=$nama_buyer?></div>
          <div class="kecil">
            <?=$alamat_buyer?> 
            Telp. <?=$telp_buyer?>
            WA. <?=$wa_buyer?>
          </div>
        </div>
      </div>
      <div class="col-4 tengah f30">
        <div class="bordered p1 h-100" style="margin: 0 -25px">
          Purchase Order
          <hr style='margin: 5px 0'>
          <span id="nomor_po"><?=$nomor_po?></span>
        </div>
      </div>
      <div class="col-4">
        <div class="bordered p1 h-100">
          <div class='flex-between'>
            <div>Kepada Yth.</div>
            <div><span class='btn_aksi' id="edit_judul_po__toggle"><?=$img_edit?></span></div>
          </div>
          <div class="tebal ">CV KURNIA JAYA PERKASA</div> 
          <div>Jalan Letkol Ga Manulang No. 73 Padalarang</div>
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
              <div>No. Supplier</div>
              <div>SUP001</div>
            </div>
            <div class="col-4">
              <div class=" h-100 " style="border-left: solid 1px #ccc; border-right: solid 1px #ccc">
                <div>Kontak Personal</div>
                <div>CV. Kurnia Jaya Perkasa</div>
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
                <div>45 hr Kontrabon</div>
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
            <div class="col-5">
              <div>Tempat Penagihan:</div>
            </div>
            <div class="col-7">
              <div class="h-100">
                <div><?=$alamat_buyer?></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>























  <!-- ================================================================ -->
  <!-- EDIT JUDUL PO -->
  <!-- ================================================================ -->
  <div class="gradasi-kuning border-merah br5 mt2 mb2 p2" id=edit_judul_po>
    <div class="row">
      <div class="col-1">&nbsp;</div>
      <div class="col-10">
        <h2 class='tengah darkblue f20'>Edit Judul PO</h2>
      </div>
      <div class="col-1 kanan">
        <span class="btn_aksi" id="edit_judul_po__close"><?=$img_close?></span>
      </div>
    </div>
    <div class="wadah kecil flex-between bg-white">
      <div>
        <b>Identitas Perusahaan</b>
        <div class=""><span class="abu miring">Perusahaan :</span> <?=$nama_usaha?></div>
        <div class=""><span class="abu miring">Alamat Usaha :</span> <?=$alamat_usaha?></div>
        <div class=""><span class="abu miring">Telepon Kantor :</span> <?=$no_telp_kantor?></div>
        <div class=""><span class="abu miring">Whatsapp Kantor :</span> <?=$no_wa_kantor?></div>

      </div>
      <div>
        <a href="?identitas_perusahaan" onclick='return confirm("Menuju laman Edit Perusahaan?")'><?=$img_edit?></a>
      </div>
    </div>

    Nomor PO:
    <input class="form-control mb2" value='<?=$nomor_po?>'>

    <div class="wadah bg-white">
      Buyer:
      <input class="form-control mb2" value='<?=$nama_buyer?>'>

      Alamat Buyer:
      <input class="form-control mb2" value='<?=$alamat_buyer?>'>

      Telfon/HP Buyer:
      <input class="form-control mb2" value='<?=$telp_buyer?>'>
    </div>

    <div class="wadah bg-white">
      Pilih Supplier:
      <select class="form-control mb2">
        <option>CV. KURNIA JAYA PERKASA</option>
      </select>

      <div class="wadah flex-between kecil">
        <div>
          <div><span class="abu miring">Nama Supplier:</span> <span id="nama_supplier_edit">???</span></div>
          <div><span class="abu miring">Alamat Supplier:</span> <span id="alamat_supplier_edit">???</span></div>
          <div><span class="abu miring">No HP Supplier:</span> <span id="no_hp_supplier_edit">???</span></div>
          <div><span class="abu miring">No Telp Supplier:</span> <span id="no_telp_supplier_edit">???</span></div>
          <div><span class="abu miring">No WA Supplier:</span> <span id="no_wa_supplier_edit">???</span></div>
        </div>
        <div>
          <a href="?supplier&id_supplier=zzz" onclick='return confirm("Edit Data Supplier ini?")'><?=$img_edit?></a>
        </div>
      </div>

    </div>


    Tanggal Pemesanan:
    <input type='date' class="form-control mb2" value='<?=$tanggal_pemesanan?>'>

    Tanggal Pengiriman:
    <input type='date' class="form-control mb2" value='<?=$tanggal_pengiriman?>'>

    Jangka Waktu Pembayaran:
    <input class="form-control mb2" value='<?=$jangka_waktu?>'>

    Tempat Pengiriman:
    <input class="form-control mb2" value='<?=$alamat_buyer?>'>

    Tempat Penagihan:
    <input class="form-control mb2" value='<?=$alamat_buyer?>'>

    <div class="flex-between mt-4">
      <button class="btn btn-primary btn_aksi" id=edit_judul_po__simpan>Simpan</button> 
      <button class="btn btn-danger btn_aksi" id=edit_judul_po__cancel>Cancel</button> 
    </div>
  </div>
  <!-- END // EDIT JUDUL PO -->
  




















  <!-- ================================================================ -->
  <!-- PERINTAH PO DAN KETERANGANS -->
  <!-- ================================================================ -->
  <div class='bordered mt1 p1'>
    <?=$perintah_po?> 
    <span class="btn_aksi" id="edit_perintah_po__toggle"><?=$img_edit?></span>
  </div>
  <!-- ================================================================ -->
  <!-- EDIT PERINTAH PO -->
  <!-- ================================================================ -->
  <div class="gradasi-kuning border-merah br5 mt2 mb2 p2" id=edit_perintah_po>
    <div class="row">
      <div class="col-1">&nbsp;</div>
      <div class="col-10">
        <h2 class='tengah darkblue f20'>Edit Perintah PO</h2>
      </div>
      <div class="col-1 kanan">
        <span class="btn_aksi" id="edit_perintah_po__close"><?=$img_close?></span>
      </div>
    </div>

    Perintah PO:
    <textarea rows="10" class="form-control mb2"><?=$perintah_po?></textarea>

    <div class="flex-between mt-4">
      <button class="btn btn-primary btn_aksi" id=edit_perintah_po__simpan>Simpan</button> 
      <button class="btn btn-danger btn_aksi" id=edit_perintah_po__cancel>Cancel</button> 
    </div>
  </div>
  <!-- END // EDIT PERINTAH PO -->

  


  <div class='bordered mt1 p1 mb4'>
    <div style='display:grid; grid-template-columns: 50px auto'>
      <div>Ket:</div>
      <div>
        <ul>
          <li class=mb2>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aspernatur quod, ipsam animi voluptates ex deserunt minima magnam ipsa tenetur distinctio quos iusto earum sunt rem illum adipisci repudiandae, fugit consequatur! <?=$img_edit?> <?=$img_delete?></li>
          <li class=mb2>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aspernatur quod, ipsam animi voluptates ex deserunt minima magnam ipsa tenetur distinctio quos iusto earum sunt rem illum adipisci repudiandae, fugit consequatur! <?=$img_edit?> <?=$img_delete?></li>
          <li class=mb2>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aspernatur quod, ipsam animi voluptates ex deserunt minima magnam ipsa tenetur distinctio quos iusto earum sunt rem illum adipisci repudiandae, fugit consequatur! <?=$img_edit?> <?=$img_delete?></li>
          <li class=mb2>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aspernatur quod, ipsam animi voluptates ex deserunt minima magnam ipsa tenetur distinctio quos iusto earum sunt rem illum adipisci repudiandae, fugit consequatur! <?=$img_edit?> <?=$img_delete?></li>
          <li class=mb2>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aspernatur quod, ipsam animi voluptates ex deserunt minima magnam ipsa tenetur distinctio quos iusto earum sunt rem illum adipisci repudiandae, fugit consequatur! <?=$img_edit?> <?=$img_delete?></li>
        </ul>
        <div class='mb1 pl4 ml2'><?=$img_add?></div>
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
    <tfoot class=gradasi-kuning>
      <tr>
        <td colspan=4 class=kanan>Total</td>
        <td class=kanan>522,00</td>
        <td>&nbsp;</td>
        <td class=kanan>60.211.711,11</td>
      </tr>
      <tr>
        <td colspan=4 class=kanan>PPN 11%</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class=kanan>6.623.711,11</td>
      </tr>
      <tr>
        <td colspan=4 class=kanan>Total + PPN</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class=kanan>66.835.711,11</td>
      </tr>
    </tfoot>
  
  </table>
  
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
              <div class='abu kecil'>Adm PO</div>
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
                <div class='abu kecil'>Supplier</div>
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

<div class="alert alert-info mt-2">Perhatian! Jika PO sudah disahkan (diverifikasi) maka PO dan item-itemnya tidak dapat lagi diubah.</div>

<div>
  <button class="btn btn-primary">Cetak PO</button>
  <button class="btn btn-danger">Hapus</button>
</div>


<script>
  $(function(){
    $('.btn_aksi').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];

      console.log(aksi,id);

      if(id=='toggle'){ $('#'+aksi).slideToggle(); }
      if(id=='close' || id=='cancel'){ $('#'+aksi).slideUp(); }

      if(aksi=='edit_judul_po'){
      }
      
    })
  })
</script>