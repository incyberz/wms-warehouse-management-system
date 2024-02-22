<?php
// echo '<pre>';
// var_dump($_POST);
// echo '</pre>';
# ========================================================
# HANDLER PENERIMAAN PERTAMA, KEDUA DST
# ========================================================
if(isset($_POST['btn_tambah_sj_selanjutnya']) || isset($_POST['btn_get_po_items'])){

  // $id_kategori = $_POST['id_kategori'] ?? die(erid('id_kategori'));
  $id_kategori = $_POST['id_kategori'] ?? 1; // zzz debug here
  echo div_alert('danger','id_kategori by passed.');

  if(isset($_POST['btn_get_po_items'])){
    $kode_po = clean_sql($_POST['kode_po']);
    $kode = "$kode_po-001";
    $id_supplier = clean_sql($_POST['id_supplier']);

  }else{

    $arr = explode('-',$_POST['btn_tambah_sj_selanjutnya']);
    $kode_po = $arr[0];
    $no = 0;
    if($arr[1]=='new'){
      // get max nomor
      $s = "SELECT kode FROM tb_sj WHERE kode_po='$kode_po'";
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      while($d=mysqli_fetch_assoc($q)){
        $arr2 = explode('-',$d['kode']);
        $no_db = intval($arr2[1]);
        if($no_db>$no) $no = $no_db; 
      }

      //max nomor++
      $no++;
      if($no<10){
        $no = "00$no";
      }elseif($no<100){
        $no = "0$no";
      }else{
        $no = $no;
      }
    }else{
      die('Not `new` parameter at btn_new value.');
    }
    $kode = "$arr[0]-$no";
    $id_supplier = $arr[2];

  }
  $s = "INSERT INTO tb_sj 
  (kode,kode_po,id_supplier,id_kategori) VALUES 
  ('$kode','$kode_po','$id_supplier',$id_kategori) 
  ";

  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  jsurl("?penerimaan&p=manage_sj&kode_sj=$kode");
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
  <h3 class='f18 darkblue mb4' id=judul>Cari Data Surat Jalan</h3>
  <div class="mb1">Nomor PO | <span id=ket_digit class='consolas abu f12'>0 digit</span></div>
  
  <?php $debug_no_po = 'AK202401026'; ?>
  <form method=post id=form_simpan >
    <input type="text" class="form-control mb2 consolas f24" id=no_po name=no_po maxlength=15 style='letter-spacing:5px' placeholder='misal... AK202401026' value="<?=$debug_no_po?>">
    
    <div id="hasil_ajax_po"></div>
    <div class='abu f12 mb4'>Nomor PO diatas belum ada pada database. Anda dapat membuat penerimaan baru dengan No. PO tsb.</div>
    <input type="hidden" class="form-control mb2" id=kode_po name=kode_po required>
    <input type="hidden" class="form-control mb2" id=id_supplier name=id_supplier required>

    <button class="btn btn-primary" id=btn_get_po_items name=btn_get_po_items disabled>Get PO Items</button>

  </form>  
</div>


<script>
  $(function(){
    $('#no_po').keyup(function(){
      let no_po = $(this).val().trim().toUpperCase();
      $(this).val(no_po);

      $('#ket_digit').text(no_po.length+' digit');
      // console.log(no_po);
      $('#kode_po').val(no_po);
      $('#btn_get_po_items').prop('disabled',1);
      $('#supplier').val('');

      if(no_po.length<3 || no_po.length>15){
          $('#hasil_ajax_po').html("<div class='kecil abu mb2'>Silahkan ketik minimal 3 huruf untuk mencari PO atau silahkan masukan PO baru!</div>");
          // $('#form_simpan').fadeOut();
          return;
      }

      link_ajax = "ajax/cari_nomor_sj.php?no_po="+no_po;
      $.ajax({
        url:link_ajax,
        success:function(a){
          $('#hasil_ajax_po').html(a);
          if(a==''){
            // $('#form_simpan').show();
            $('#judul').text("Buat Penerimaan Surat Jalan Baru");
          }else{
            $('#judul').text("Cari Data Surat Jalan");
            // $('#form_simpan').hide();

          }
        }
      })
    });

    $('#supplier').keyup(function(){
      let no_po = $(this).val().trim();

      if(no_po.length<3 || no_po.length>15){
          $('#hasil_ajax_supplier').html("<div class='kecil abu mb2'>Silahkan ketik minimal 3 huruf untuk mencari Data Supplier!</div>");
          return;
      }

      link_ajax = "ajax/cari_supplier.php?no_po="+no_po;
      $.ajax({
        url:link_ajax,
        success:function(a){
          $('#hasil_ajax_supplier').html(a);
        }
      })
    });

    $('#no_po').keyup();
    $('#supplier').keyup();

  });


  $(document).on("click",".pilih_supplier",function() {
    let tid = $(this).prop('id');
    let rid = tid.split('__');
    let aksi = rid[0];
    let id_supplier = rid[1];

    $('#id_supplier').val(id_supplier);
    $('#supplier').val($('#nama_supplier__'+id_supplier).text());
    $('#hasil_ajax_supplier').html('');

    $('#btn_get_po_items').prop('disabled',0);

  });

</script>