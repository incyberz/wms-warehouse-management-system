<div class="pagetitle">
  <h1>Bukti Barang Masuk</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?po">PO Home</a></li>
      <li class="breadcrumb-item"><a href="?po&p=terima_barang">Cari PO</a></li>
      <li class="breadcrumb-item active">BBM</li>
    </ol>
  </nav>
</div>


<?php
$tb_identitas_po = '';
$tb_items = '';
$no_bbm = '';
$id_bbm = '';
$today = date('Y-m-d');
$pernah_terima = 0; // untuk parsial penerimaan
$saya_menyatakan_disabled = '';
$penerimaan = $_GET['penerimaan'] ?? '';
$id_bbm = $_GET['id_bbm'] ?? '';
$link_no_bbm = '';
$sisa_qty = 0;
$ada_bbm_kosong = 0;
$btn_simpan_caption = "Simpan BBM";


$s = "SELECT 
a.id as id_po,
a.tempat_pengiriman,
a.tanggal_pemesanan,
a.tanggal_pengiriman,
a.date_created tanggal_buat_PO,
b.id as id_supplier,
b.kode as kode_supplier,
b.nama as nama_supplier,
b.alamat as alamat_supplier,
b.contact_person as contact_person,
b.no_telfon as telp_supplier, 
(SELECT count(1) FROM tb_po_item WHERE id_po=a.id) jumlah_item_barang, 
(SELECT count(1) FROM tb_bbm WHERE id_po=a.id) id_bbm_count, 
(SELECT COUNT(1) FROM tb_bbm WHERE tanggal_terima > '$today')+1 as  id_counter,
(SELECT SUM(qty) FROM tb_po_item WHERE id_po=a.id) sum_qty_po, 
(
  SELECT SUM(p.qty_diterima) 
  FROM tb_bbm_item p 
  JOIN tb_bbm q ON p.id_bbm=q.id 
  WHERE q.id_po=a.id) sum_qty_diterima 

FROM tb_po a 
JOIN tb_supplier b ON a.id_supplier=b.id 
WHERE a.kode='$no_po'
";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0){
  die(div_alert('danger',"Nomor PO : $no_po tidak ditermukan."));
}else{
  $d = mysqli_fetch_assoc($q);

  $id_bbm_count = $d['id_bbm_count'];
  $id_po = $d['id_po'];

  if($id_bbm_count){
    # =======================================================================
    # LIST ID-BBM 
    # =======================================================================
    $s2 = "SELECT a.*, a.id as id_bbm,
    (SELECT SUM(qty_diterima) FROM tb_bbm_item WHERE id_bbm=a.id) qty_diterima_per_bbm 
    FROM tb_bbm a WHERE a.id_po=$id_po";
    $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));

    while($d2=mysqli_fetch_assoc($q2)){
      if($d2['id']==$id_bbm){
        $no_bbm = $d2['kode'];
        $secondary = 'success';
        $border = 'border-biru';
      }else{
        $secondary = 'secondary';
        $border = 'bordered';
      }
      if($d2['qty_diterima_per_bbm']==0){
        $ada_bbm_kosong = 1;
        $btn_simpan_caption = "Update BBM ini";
      }else{
        $btn_simpan_caption = "Simpan BBM";

      }
      $qty_diterima_per_bbm = $d2['qty_diterima_per_bbm'] ?? 0;
      $merah = $qty_diterima_per_bbm ? '' : 'merah';
      $tanggal = $qty_diterima_per_bbm ? eta(strtotime($d2['tanggal_terima'])-strtotime('now')) : '-';

      $id = 'bbm__delete__'.$d2['id_bbm'].'__id';
      $delete = $qty_diterima_per_bbm 
        ? "<span onclick='alert(\"BBM ini punya sub-data-qty sehingga tidak bisa langsung dihapus.\")'>$img_check</span>" 
        : "<span class='btn_aksi' id=$id>$img_delete</span>";

      $id = 'bbm__'.$d2['id_bbm'].'__vvvv';

      $qty_diterima_per_bbm = str_replace('.0000','',$qty_diterima_per_bbm);

      $link_no_bbm.= "
        <div id=$id class='$border br5 p2 gradasi-$merah'>
          <div class='flex-between' style=gap:10px>
            <div>
              <div class='f12 miring abu'>$d2[nomor]</div>
              <a class='btn btn-sm btn-$secondary' href='?po&p=terima_barang&no_po=$no_po&id_bbm=$d2[id]'>$d2[kode]</a>
            </div>
            <div>
              $delete
            </div>
          </div>
          <div class='kecil miring abu'>QTY diterima: $qty_diterima_per_bbm</div>
          <div class='kecil miring abu'>$tanggal</div>
        </div>
      ";
    }

  }


  
  if($id_bbm_count==0 || $penerimaan=='selanjutnya'){
    # =======================================================================
    # AUTO CREATED ID BBM
    # =======================================================================
    $id_counter = $d['id_counter'];
    echo "id_counter: $id_counter";
    if($id_counter>100){
      $counter = $id_counter;
    }elseif($id_counter>10){
      $counter = "0$id_counter";
    }else{
      $counter = "00$id_counter";
    }
    $no_bbm = date('ymd').'-'.$counter;
    $nomor = $id_bbm_count+1;

    $s = "INSERT INTO tb_bbm (id_po,kode,nomor) VALUES ($id_po,'$no_bbm',$nomor)";
    // echo $s;
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));



    jsurl("?po&p=terima_barang&no_po=$no_po");
    exit;
  }

  # =======================================================================
  # SISA QTY
  # =======================================================================
  $sisa_qty = $d['sum_qty_po'] - $d['sum_qty_diterima'];
  // echo "<h1>sisa_qty :  $sisa_qty = $d[sum_qty_po] - $d[sum_qty_diterima];</h1>";



  # =======================================================================
  # TAMPIL INFORMASI PO
  # =======================================================================
  $tr = '';
  foreach ($d as $key => $value) {
    if($key=='no_bbm'||$key=='tanggal_terima') continue;
    if(!strpos("salt$key",'id_')){
      $kolom = ucwords(str_replace('_',' ',$key));
      // $value = $value ?? '-';
      if($value=='') $value = '-';
      $tr .= "
        <tr>
          <td class='abu miring'>$kolom</td>
          <td>$value</td>
        </tr>
      ";
    }
  }
  $tb_identitas_po = "
    <table class=table>
      <thead>
        <th width=30%>Nomor PO</th>
        <th>$no_po <span id=identitas_po_toggle>$img_detail</span></th>
      </thead>
      <tbody class=hideit id=identitas_po>
        $tr
        <tr>
          <td>Surat Jalan</td>
          <td>
            <form method=post enctype='multipart/form-data'>
              <div class='flexy'>
                <div>
                  <input type='file' class='form-control form-control-sm'>
                </div>
                <div>
                  <button class='btn btn-success btn-sm' onclick='return confirm(\"Handler untuk upload masih dalam tahap pengembangan. Terimakasih sudah mencoba.\")'>Upload</button>
                </div>
              </div>
              
            </form>
          </td>
        </tr>
      </tbody>
    </table>
  ";
}

echo "
  <h2>Identitas PO</h2>
  $tb_identitas_po
";



# =======================================================================
# JIKA ADA SISA QTY DAN TIDAK ADA BBM KOSONG --> SHOW BTN TAMBAH
# =======================================================================
$tambah_penerimaan = '';
if($sisa_qty<0){
  die(div_alert('danger', "QTY diterima tidak boleh > QTY PO"));
}elseif($sisa_qty){
  if($ada_bbm_kosong){
    $tambah_penerimaan = "Sisa QTY : $sisa_qty  <div class='kecil miring abu mt2'>Silahkan isi dahulu BBM dengan QTY masih kosong.</div>";
  }else{
    $tambah_penerimaan = "Sisa QTY : $sisa_qty | <a href='?po&p=terima_barang&no_po=$no_po&penerimaan=selanjutnya' onclick='return confirm(\"Tambah Penerimaan untuk PO ini?\")' >$img_add Tambah Penerimaan </a>";
  }
  $tambah_penerimaan = "<div class='bordered br5 p2'>$tambah_penerimaan</div>";
}

echo "
  <h2>Total QTY Penerimaan</h2>
  <div class='flexy mb4 mt1'>
    <div class='kecil p1'>No BBM</div>
    $link_no_bbm
    <div>$tambah_penerimaan</div>
  </div>
";

if($id_bbm==''){
  echo ('Silahkan pilih nomor BBM diatas!');
}else{
  
  # =======================================================================
  # CHECK IS VALID ID-BBM | GET TANGGAL TERIMA
  # =======================================================================
  $s = "SELECT tanggal_terima FROM tb_bbm WHERE id=$id_bbm";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0) {
    jsurl("?po&p=terima_barang&no_po=$no_po");
    exit;
  }else{
    $d = mysqli_fetch_assoc($q);
    $tanggal_terima = $d['tanggal_terima'];
  }
  
  
  
  # =======================================================================
  # GET ITEM PO
  # =======================================================================
  $s = "SELECT *,
  a.id as id_item,
  b.id as id_barang,
  b.kode as kode_barang,
  b.nama as nama_barang,
  (SELECT step FROM tb_satuan WHERE satuan=b.satuan) step,
  (SELECT qty_diterima FROM tb_bbm_item WHERE id_po_item=a.id AND id_bbm=$id_bbm) qty_diterima,
  (
    SELECT SUM(p.qty_diterima) 
    FROM tb_bbm_item p 
    JOIN tb_bbm q ON p.id_bbm=q.id 
    WHERE id_po_item=a.id 
    AND q.tanggal_terima < '$tanggal_terima' ) qty_sebelumnya
  
  FROM tb_po_item a 
  JOIN tb_barang b ON a.id_barang=b.id 
  WHERE a.id_po=$id_po 
  ";
  // echo "<h1>GET ITEM PO</h1><pre>$s</pre>";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0){
    $tr = "<tr><td colspan=100% ><div class='alert alert-danger'>Belum ada item barang pada PO ini</div></td></tr>";
    $saya_menyatakan_disabled = 'disabled';
  }else{
    $tr = '';
    $i=0;
    while($d=mysqli_fetch_assoc($q)){
      $i++;
      $id = $d['id_item'];
      $step = $d['step'] ?? 0.0001;
      $qty = $d['qty'];
      $qty_diterima = $d['qty_diterima'];
      $qty_sebelumnya = $d['qty_sebelumnya'];
      $satuan = $d['satuan'];
  
      // pernah terima maka set partial
      if($qty_diterima) $pernah_terima = 1;
      
      $qty = $step==1 ? round($qty,0) : $qty;
      $qty_sama = $qty==$qty_diterima ? 1 : 0;

      $qty_final = $qty-$qty_sebelumnya;
      $qty_sama_final = $qty_final==$qty_diterima ? 1 : 0;
      
      $hideit_check = $qty_sama_final ? '' : 'hideit';
      $hideit_sesuai = ($qty_sama_final || $qty_diterima>0) ? 'hideit' : '';
      
      if($qty_sama_final){
        $sisa_show='';
      }else{
        $selisih = $qty-$qty_diterima;
        $sisa_show = "Kurang $selisih $satuan";
      }


      $qty_sebelumnya_show = $d['qty_sebelumnya'] ? "<div class='kecil miring abu'>-<span id=qty_sebelumnya__$id>$d[qty_sebelumnya]</span></div>" : '';

      $qty_sebelumnya_show = str_replace('.0000','',$qty_sebelumnya_show);
      $qty_diterima = str_replace('.0000','',$qty_diterima);
      $qty = str_replace('.0000','',$qty);
      
      
      $tr .= "
        <tr>
          <td>$i</td>
          <td>
            <span class=darkblue>$d[kode_barang] <a href='?master&p=barang&keyword=$d[kode_barang]' onclick='return confirm(\"Ingin mengubah data barang ini?\")'>$img_edit</a></span>
            <div class='darkabu f14'>$d[nama_barang]</div>
          </td>
          <td>
            <span id=qty_po__$id>$qty</span> 
            <span id=satuan__$id>$d[satuan]</span>
            $qty_sebelumnya_show
          </td>
          <td>
            <div class=flexy>
              <div>
                <input id='qty_diterima__$id' class='form-control form-control-sm qty_diterima' type=number step='$step' required name=qty_diterima__$id min=0 max=$qty_final value=$qty_diterima>
                <div class='mt1 abu kecil' id=selisih__$id>$sisa_show</div>
              </div>
              <div>
                <span class='$hideit_sesuai btn btn-success btn-sm btn_sesuai' id=btn_sesuai__$id>Sesuai</span>
                <div class='$hideit_check' id=img_check__$id>$img_check</div>
              </div>
            </div>
            
          </td>
        </tr>
      ";
    }
  }
  
  $tb_items = "
    <table class='table'>
      <thead>
        <th>No</th>
        <th>Kode / Item</th>
        <th>QTY-PO</th>
        <th>QTY Diterima</th>
      </thead>
      $tr
    </table>
  ";
  
  
  
  
  
  
  
  
  
  
  
  
  
  # =======================================================================
  # QTY DITERIMA PADA BBM
  # =======================================================================
  echo "<h2>QTY Diterima pada BBM $no_bbm</h2>";
  
  if(isset($_POST['btn_simpan'])){
    // echo '<pre>';
    // var_dump($_POST);
    // echo '</pre>';
    $id_bbm = $_POST['id_bbm'];
  
    foreach ($_POST as $key => $qty_diterima) {
      if(strpos("salt$key",'qty_diterima__')){
        $arr = explode('__',$key);
        $id_po_item = $arr[1];
  
        $s = "SELECT id as id_bbm_item FROM tb_bbm_item WHERE id_bbm=$id_bbm AND id_po_item=$id_po_item";
        $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
        if(mysqli_num_rows($q)>1){
          die("Tidak boleh 2 id_bbm_item untuk 1 id_bbm<hr>id_bbm: $id_bbm | id_po_item: $id_po_item ");
        }else{
          if(mysqli_num_rows($q)){
            $d = mysqli_fetch_assoc($q);
            $s = "UPDATE tb_bbm_item SET qty_diterima=$qty_diterima WHERE id=$d[id_bbm_item]";
          }else{
            $s = "INSERT INTO tb_bbm_item (id_bbm,id_po_item,qty_diterima) VALUES ($id_bbm,$id_po_item,$qty_diterima)";
          }
          $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
        }
      }
    }
  
    $s = "UPDATE tb_bbm SET tanggal_terima=CURRENT_TIMESTAMP WHERE id=$id_bbm";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  
    jsurl("?po&p=terima_barang&no_po=$no_po");
    exit;
  }
  
  echo "
    <form method=post>
      <input type='hidden' name=id_bbm value=$id_bbm>
      $tb_items
  
      <div class='flexy'>
        <div class='pt1'>
          <input type='checkbox' id=saya_menyatakan $saya_menyatakan_disabled>
        </div>
        <div>
          <label for='saya_menyatakan'>Saya menyatakan telah menerima dan mengukur semua kuantitas item dari PO ini.</label>
        </div>
      </div>
      <div class='flexy mt4'>
        <div style=flex:1>
          <button class='btn btn-primary btn-sm w-100' disabled id=btn_simpan name=btn_simpan>$btn_simpan_caption</button>
        </div>
        <div style=flex:1>
          <span class='btn btn-success btn-sm w-100 btn_aksi' id=blok_cetak__toggle disabled>Cetak BBM</span>
        </div>
      </div>
    </form>

    <div id='blok_cetak'>
      <h2>Cetak BBM</h2>
      <p>Syarat untuk mencetak BBM yaitu:</p>
      <ol>
        <li>Upload Surat Jalan $img_check</li>
        <li>Sudah diverifikasi oleh Petugas Receipt $img_check</li>
      </ol>
    </div>

  ";
} 
?>

































<script>
  $(function(){

    $('#saya_menyatakan').click(function(){$('#btn_simpan').prop('disabled',!$(this).prop('checked'))})
    $('#identitas_po_toggle').click(function(){$('#identitas_po').fadeToggle()})

    $('.btn_sesuai').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
     
      $('#qty_diterima__'+id).val($('#qty_po__'+id).text());
      $('#btn_sesuai__'+id).hide();
      $('#img_check__'+id).show();

    });


    $('.qty_diterima').change(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      
      let qty_diterima = $(this).val();
      if(qty_diterima==''){
        $('#btn_sesuai__'+id).show();
        $('#selisih__'+id).html('');
        return;
      }
      qty_diterima = parseFloat(qty_diterima);

      let satuan = $('#satuan__'+id).text();
      let qty_po = parseFloat($('#qty_po__'+id).text());
      let qty_sebelumnya = parseFloat($('#qty_sebelumnya__'+id).text());

      $('#img_check__'+id).hide();
      if(isNaN(qty_diterima)||isNaN(qty_po)){
        $('#selisih__'+id).html('masukan QTY yang benar..');
        $('#btn_sesuai__'+id).fadeOut();
      }else{
        // $('#btn_sesuai__'+id).fadeIn();
        let selisih = Math.round((qty_po - qty_diterima - qty_sebelumnya)*10000) / 10000;
        if(selisih==0){
          $('#btn_sesuai__'+id).hide();
          $('#img_check__'+id).show();
          $('#selisih__'+id).html('');
        }else{
          $('#btn_sesuai__'+id).fadeOut();
          if(selisih>0){
            $('#selisih__'+id).html('Selisih : kurang '+selisih+' '+satuan);
          }else{
            $('#selisih__'+id).html('Selisih : lebih '+(-selisih)+' '+satuan);
          }
        }
      }

      console.log(qty_diterima,qty_po);


    })
  })
</script>

