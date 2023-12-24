<?php
// echo '<pre>';
// var_dump($_POST);
// echo '</pre>';
# ========================================================
# HANDLER PENERIMAAN PERTAMA, KEDUA DST
# ========================================================
if(isset($_POST['btn_tambah_sj_selanjutnya']) || isset($_POST['btn_create'])){

  if(isset($_POST['btn_create'])){
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
  (kode,kode_po,id_supplier) VALUES 
  ('$kode','$kode_po','$id_supplier') 
  ";

  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  jsurl("?penerimaan&p=manage_sj&kode_sj=$kode");
  exit;
}


?>
<div class="pagetitle">
  <h1>Terima Surat Jalan</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=data_sj">Data SJ</a></li>
      <li class="breadcrumb-item active">Terima SJ</li>
    </ol>
  </nav>
</div>

<p>Page terima barang digunakan untuk verifikasi isi PO saat barang sudah datang ke Gudang.</p>

<!-- <div id="blok_buat_po" class=wadah>

  <label for="kode_po" class='mb1 kecil darkabu'>Nomor PO</label>
  <input type="text" class="form-control mb2" id=kode_po name=kode_po>
  <label for="supplier" class='mb1 kecil darkabu'>Supplier</label>
  <input type="text" class="form-control mb2" id=supplier name=supplier>
  <label for="tanggal_masuk" class='mb1 kecil darkabu'>Tanggal Masuk</label>
  <input type="text" class="form-control mb2" id=tanggal_masuk name=tanggal_masuk>

  <button class="btn btn-primary">Simpan</button>

</div> -->
<div class=wadah>
  <h3 class='f18 darkblue mb4' id=judul>Cari Data Surat Jalan</h3>
  Nomor PO
  <input type="text" class="form-control mb2" id=keyword maxlength=20>
  
  <div id="hasil_ajax_po"></div>

  <form method=post id=form_simpan class=hideit>
    <input type="hidden" class="form-control mb2" id=kode_po name=kode_po required>
    <input type="hidden" class="form-control mb2" id=id_supplier name=id_supplier required>

    Supplier
    
    <input type="text" class="form-control mb2" id=supplier name=supplier required maxlength=50>
    <div id="hasil_ajax_supplier"></div>

    <button class="btn btn-primary" id=btn_create name=btn_create disabled>Create</button>

  </form>  
</div>


<script>
  $(function(){
    $('#keyword').keyup(function(){
      let keyword = $(this).val().trim();
      // console.log(keyword);
      $('#kode_po').val(keyword);
      $('#btn_create').prop('disabled',1);
      $('#supplier').val('');

      if(keyword.length<3 || keyword.length>15){
          $('#hasil_ajax_po').html("<div class='kecil abu mb2'>Silahkan ketik minimal 3 huruf untuk mencari PO atau silahkan masukan PO baru!</div>");
          $('#form_simpan').fadeOut();
          return;
      }

      link_ajax = "ajax/cari_nomor_sj.php?keyword="+keyword;
      $.ajax({
        url:link_ajax,
        success:function(a){
          $('#hasil_ajax_po').html(a);
          if(a==''){
            $('#form_simpan').show();
            $('#judul').text("Buat Penerimaan Surat Jalan Baru");
          }else{
            $('#judul').text("Cari Data Surat Jalan");
            $('#form_simpan').hide();

          }
        }
      })
    });

    $('#supplier').keyup(function(){
      let keyword = $(this).val().trim();

      if(keyword.length<3 || keyword.length>15){
          $('#hasil_ajax_supplier').html("<div class='kecil abu mb2'>Silahkan ketik minimal 3 huruf untuk mencari Data Supplier!</div>");
          return;
      }

      link_ajax = "ajax/cari_supplier.php?keyword="+keyword;
      $.ajax({
        url:link_ajax,
        success:function(a){
          $('#hasil_ajax_supplier').html(a);
        }
      })
    });

    $('#keyword').keyup();
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

    $('#btn_create').prop('disabled',0);

  });

</script>