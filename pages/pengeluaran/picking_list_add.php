<table width=100% class=mb2>
  <tr>
    <td>
      <h2>Add Item <?=$jenis_barang?> to Picking List</h2>
    </td>
    <td width=30px class=kanan>
      <span class=btn_aksi id=picking_list_add__close><?=$img_close?></span>
    </td>
  </tr>
</table>

<div class="flexy">
  <div class="abu kecil">Search</div>
  <div>
    <input type="text" class="form-control form-control-sm" id=keyword maxlength=15 placeholder='ID atau PO minimal 3 huruf'>
  </div>
  <div class='kecil abu'>Tampil <span class='darkblue f18' id=jumlah_tampil>?</span> data of <span id=jumlah_records>?</span> records</div>
</div>
<div id="hasil_ajax"></div>
<script>
  let kode_do = '';
  let id_kategori = '';
  let kode_do_cat = '';
  $(function(){
    id_kategori = $('#id_kategori').text();
    kode_do = $('#kode_do').val();
    kode_do_cat = kode_do+id_kategori;

    if(kode_do_cat.length!=10){
      console.log('kode_do_cat.length != 10 ',kode_do_cat.length);
      return;
    }
    
    $('#keyword').keyup(function(){
      let keyword = $(this).val().trim();
      if(keyword.length>2){
        let link_ajax = `pages/pengeluaran/picking_list_add_fetcher.php?keyword=${keyword}&id_kategori=${id_kategori}&kode_do_cat=${kode_do_cat}`;
        $.ajax({
          url:link_ajax,
          success:function(a){
            let ra = a.split('~~~');
            $('#hasil_ajax').html(ra[0]);
            $('#jumlah_tampil').text(ra[1] ?? '?');
            $('#jumlah_records').text(ra[2] ?? '?');

          }
        })
      }
    });

  });

  $(document).on("click",".btn_add",function(){
    let tid = $(this).prop('id');
    let rid = tid.split('__');
    let aksi = rid[0];
    let id = rid[1];

    console.log(id,kode_do,id_kategori);
    let link_ajax = `pages/pengeluaran/picking_list_add_assign.php?id_sj_subitem=${id}&kode_do_cat=${kode_do_cat}`;
    $.ajax({
      url:link_ajax,
      success:function(a){
        if(a.trim()=='sukses'){
          $('#div_btn_add__'+id).html('<span class="kecil green miring">success.</span>');
        }else{
          alert('Tidak dapat assign item.');
          console.log(a);
        }
      }
    })

  })
</script>