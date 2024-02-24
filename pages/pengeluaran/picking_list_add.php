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
  let id_do = '';

  $(function(){
    id_kategori = $('#id_kategori').text();
    id_do = $('#id_do').text();
    kode_do = $('#kode_do').val();

    if(!(kode_do.length==9 || kode_do.length==15)){
      console.log('kode_do.length != 9 or 15 ',kode_do.length);
      return;
    }
    
    $('#keyword').keyup(function(){
      let keyword = $(this).val().trim();
      if(keyword.length>2){
        let link_ajax = `pages/pengeluaran/picking_list_add_fetcher.php?keyword=${keyword}&id_kategori=${id_kategori}&id_do=${id_do}`;
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

    // console.log(id,kode_do,id_kategori);
    let link_ajax = `pages/pengeluaran/picking_list_add_assign.php?id_kumulatif=${id}&id_do=${id_do}`;
    console.log(link_ajax);
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