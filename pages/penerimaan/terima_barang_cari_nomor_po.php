<div class="pagetitle">
  <h1>Terima Barang</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?po">PO Home</a></li>
      <li class="breadcrumb-item active">Terima Barang</li>
    </ol>
  </nav>
</div>

<p>Page terima barang digunakan untuk verifikasi isi PO saat barang sudah datang ke Gudang.</p>
Nomor PO
<div class="flexy mb2 mt1">
  <div>
    <input type="text" class="form-control" id=keyword>
  </div>
  <div>
    <!-- <button class='btn btn-primary '>Next</button> -->
  </div>
</div>
<div id="hasil_ajax">
  Silahkan ketik minimal 3 huruf dan pilih nomor PO!
</div>


<script>
  $(function(){
    $('#keyword').keyup(function(){
      let keyword = $(this).val().trim();
      console.log(keyword);

      if(keyword.length<3 || keyword.length>15){
          $('#hasil_ajax').html("<div class='alert alert-info'>Silahkan ketik Nomor PO minimal 3 huruf ...");
          return;
      }

      link_ajax = "ajax/cari_nomor_po.php?keyword="+keyword;
      $.ajax({
        url:link_ajax,
        success:function(a){
          $('#hasil_ajax').html(a);
        }
      })
    })

  })
</script>