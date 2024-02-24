<?php
# ========================================================
# HANDLER PENERIMAAN PERTAMA
# ========================================================
if(isset($_POST['btn_get_po_items'])){
  echo '
    <div class="consolas wadah gradasi-kuning">
      <div class="f20 darkblue">Processing Penerimaan Pertama...</div>
      <hr>
  ';
  $kode_po = clean_sql($_POST['kode_po']);
  $kode = "$kode_po-001";
  $kode_sj = $kode;

  
  // $kode_po = $kode_po;
  include 'pages/test_api.php';
  // echo '<pre>';
  // var_dump($arr_item_po);
  // echo '</pre>';
  if($arr_item_po){

    $count = count($arr_item_po);
    echo "<br>Item PO ditemukan... $count items";
    foreach ($arr_item_po as $item_po) {
      $id_kategori = strtoupper($item_po->jenis_barang)=='AKS' ? 1 : 2;
      $kode_barang = $item_po->kode;
      $kode_supplier = $item_po->supplier_id;
      $nama_supplier = $item_po->supplier_name;

      # ==============================================
      # AUTO INSERT BARANG
      # ==============================================
      echo "<br>Checking kode barang $kode_barang... ";
      $s = "SELECT 1 FROM tb_barang WHERE kode='$kode_barang'";
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      if(mysqli_num_rows($q)){
        echo 'barang sudah ada pada database.';
      }else{
        $kode = $item_po->kode;
        $nama = $item_po->item_name;
        $keterangan = $item_po->item_desc;
        $satuan = $item_po->uom;
        $harga = floatval($item_po->price);
        $kode_lama = $item_po->kode_lama;
        $kode_lama = $kode_lama=='NULL' ? 'NULL' : "'$kode_lama'";

        echo 'barang belum ada... Inserting barang... ';
        $s2 = "INSERT INTO tb_barang 
        (id_kategori,kode,nama,keterangan,kode_lama,satuan,harga) VALUES 
        ('$id_kategori','$kode','$nama','$keterangan',$kode_lama,'$satuan',$harga)";
        $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
        echo 'insert success.';
      }
  
    }
    # ==============================================
    # AUTO INSERT SUPPLIER
    # ==============================================
    echo "<br>Checking kode supplier $kode_supplier... ";
    $s = "SELECT 1 FROM tb_supplier WHERE kode='$kode_supplier'";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    if(mysqli_num_rows($q)){
      echo 'supplier sudah ada pada database.';
    }else{
      echo 'supplier belum ada... Inserting supplier... ';
      $s2 = "INSERT INTO tb_supplier (kode,nama) VALUES ('$kode_supplier','$nama_supplier')";
      $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
      echo 'insert success.';


    }

    # ==============================================
    # INSERT NEW SJ
    # ==============================================
    $tanggal_terima = date('Y-m-d',strtotime($item_po->order_date));
    echo "<hr><div class='darkblue mb2 mt4'>INSERT NEW SURAT JALAN... STARTED.</div>";
    echo "checking kode surat jalan: $kode_sj... ";
    $s = "SELECT 1 FROM tb_sj WHERE kode='$kode_sj'";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    if(mysqli_num_rows($q)){
      echo '<span class="bold red">kode-SJ telah ada.</span>';
      
    }else{ // ready to insert
      echo 'ready to insert.';
      echo '<br>inserting New Surat Jalan...';
      $s = "INSERT INTO tb_sj 
      (
        id_kategori,
        kode,
        kode_po,
        kode_supplier,
        tanggal_terima
      ) VALUES (
        $id_kategori,
        '$kode_sj',
        '$kode_po',
        '$kode_supplier',
        '$tanggal_terima'
      )";
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      echo 'insert success.';
    }

    # ==============================================
    # INSERT NEW ITEMS OF SJ
    # ==============================================
    echo "<hr><div class='darkblue mb2 mt4'>INSERT NEW ITEMS OF SURAT JALAN... STARTED.</div>";
    foreach ($arr_item_po as $item_po) {
      $kode_barang = $item_po->kode;
      echo "<br>checking items... kode_sj: $kode_sj AND kode_barang: $kode_barang... ";
      $s = "SELECT 1 FROM tb_sj_item WHERE kode_sj='$kode_sj' AND kode_barang='$kode_barang'";
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      if(mysqli_num_rows($q)){
        echo '<span class=red>item telah ada.</span>';
      }else{
        $qty_po = $item_po->qty ?? 0;
        $qty = $qty_po ?? 0; // as qty adjusted
        echo 'ready to insert... ';
        $s2 = "INSERT INTO tb_sj_item 
        (
          kode_sj,
          kode_barang,
          qty_po,
          qty
        ) VALUES (
          '$kode_sj',
          '$kode_barang',
          $qty_po,
          $qty
        )";
        $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
        echo 'insert success.';
      }
      
    }

    die("
      <hr>
      <div>
        <div class='f12 mb2'>Semua proses telah selesai. Silahkan menuju Manage Surat Jalan</div>
        <a href='?penerimaan&p=manage_sj&kode_sj=AK202401026-001' class='btn btn-primary'>Manage Surat Jalan</a>
      </div>
    ");

  }else{
    die("
      <div class=red>Data item dari PO <u>$kode_po</u> tidak ditemukan.</div>
    </div>
    ");
  }
  echo '</div>';
}

# ========================================================
# HANDLER PENERIMAAN PARSIAL
# ========================================================
if(isset($_POST['btn_add_parsial'])){

  $pesan = '';
  $id_kategori = $_POST['id_kategori'] ?? die(erid('id_kategori'));
  $kode_supplier = $_POST['kode_supplier'] ?? die(erid('kode_supplier'));

  $arr = explode('-',$_POST['btn_add_parsial']);
  $kode_po = $arr[0];
  $no = 0;
  if($arr[1]=='new'){
    // get max nomor
    $s = "SELECT kode as kode_sj_max FROM tb_sj WHERE kode_po='$kode_po' 
    ORDER BY kode DESC LIMIT 1;";
    $pesan.= '<br>selecting kode_sj_max... ';
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    $d=mysqli_fetch_assoc($q);
    $kode_sj_max = $d['kode_sj_max'];
    $pesan .= "kode_sj_max: $kode_sj_max";
    $arr2 = explode('-',$kode_sj_max);
    $no = intval($arr2[1]);

    //max nomor++
    $no++;
    if($no<10){
      $no = "00$no";
    }elseif($no<100){
      $no = "0$no";
    }else{
      $no = $no;
    }
    $pesan.= "<br>incrementing counter... no: $no";
  }else{
    die('Not `new` parameter at btn_new value.');
  }
  $new_kode_sj = "$arr[0]-$no";
  $pesan.= "<br>creating new_kode_sj... new_kode_sj: $new_kode_sj";
  
  $s = "INSERT INTO tb_sj 
  (kode,kode_po,kode_supplier,id_kategori) VALUES 
  ('$new_kode_sj','$kode_po','$kode_supplier',$id_kategori) 
  ";
  $pesan.= "<br>inserting data with new_kode_sj... ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $pesan.= 'success.';
  
  // get items from kode_sj_max
  $s = "SELECT kode_barang,qty_po,qty FROM tb_sj_item WHERE kode_sj='$kode_sj_max'";
  $pesan.= "<br>selecting items with kode_sj_max... ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  while($d=mysqli_fetch_assoc($q)){
    // insert sj_item 
    $s2 = "INSERT INTO tb_sj_item 
    (
      kode_sj, kode_barang, qty_po, qty
    ) VALUES (
      '$new_kode_sj', '$d[kode_barang]', $d[qty_po], $d[qty]
    )";
    $pesan.= "<br>inserting item to new_kode_sj... kode_barang: $d[kode_barang]... ";
    $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
    $pesan.= 'success.';
  }
  
  $pesan.= '<hr>All process penerimaan parsial success.';
  jsurl("?penerimaan&p=manage_sj&kode_sj=$new_kode_sj",2000);
  exit;
}


?>
<div class="pagetitle">
  <h1>Terima Barang</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=data_sj">Data SJ</a></li>
      <li class="breadcrumb-item active">Terima Barang</li>
    </ol>
  </nav>
</div>

<p>Page ini untuk menerima barang sesuai dengan Surat Jalan yang diterima.</p>

<div class="wadah gradasi-toska">
  <div class="sub_form">Form Terima Barang</div>
  <h3 class='f18 darkblue mb4' id=judul>Cari Nomor PO</h3>
  <div class="mb1">Nomor PO | <span id=ket_digit class='consolas abu f12'>0 digit</span></div>
  
  <?php $debug_kode_po = 'AK202401026'; ?>
  <form method=post  >
    <input required type="text" class="form-control mb2 consolas f24" id=kode_po name=kode_po minlength=5 maxlength=15 style='letter-spacing:5px' placeholder='misal... AK202401026' value="<?=$debug_kode_po?>">
    
    <div id="hasil_ajax_po"></div>

    <!-- BLOK TERIMA BARU -->
    <div id="blok_terima_baru" class=hideit>
      <div class='abu f12 mb4'>Nomor PO diatas belum ada pada database. Anda dapat membuat penerimaan baru dengan No. PO tsb.</div>
  
      <button class="btn btn-primary" id=btn_get_po_items name=btn_get_po_items>Get PO Items via API</button>
    </div> 

  </form>  
</div>


<script>
  $(function(){
    $('#kode_po').keyup(function(){
      let kode_po = $(this).val().trim().toUpperCase();
      $(this).val(kode_po);

      $('#ket_digit').text(kode_po.length+' digit');
      // console.log(kode_po);

      if(kode_po.length<3 || kode_po.length>15){
          $('#hasil_ajax_po').html("<div class='kecil abu mb2'>Silahkan ketik minimal 3 huruf untuk mencari PO atau silahkan masukan PO baru!</div>");
          $('#blok_terima_baru').fadeOut();
          return;
      }

      link_ajax = "ajax/cari_nomor_sj.php?keyword="+kode_po;
      $.ajax({
        url:link_ajax,
        success:function(a){
          $('#hasil_ajax_po').html(a);
          if(a==''){
            $('#blok_terima_baru').show();
            $('#judul').text("Buat Penerimaan Surat Jalan Baru");
          }else{
            $('#judul').text("Cari Nomor PO");
            $('#blok_terima_baru').hide();

          }
        }
      })
    });

    $('#kode_po').keyup();

  });

</script>