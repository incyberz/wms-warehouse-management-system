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
      $('#selisih__'+id).hide();

    });


    $('.qty_datang').change(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      
      let qty_datang = $(this).val();
      if(qty_datang==''){
        $('#btn_sesuai__'+id).show();
        $('#selisih__'+id).html('');
        return;
      }
      qty_datang = parseFloat(qty_datang);

      let satuan = $('#satuan__'+id).text();
      let qty_po = parseFloat($('#qty_po__'+id).text());
      let qty_sebelumnya = parseFloat($('#qty_sebelumnya__'+id).text());

      if(isNaN(qty_sebelumnya)) qty_sebelumnya = 0;

      $('#img_check__'+id).hide();
      if(isNaN(qty_datang)||isNaN(qty_po)){
        $('#selisih__'+id).html('masukan QTY yang benar..');
        $('#btn_sesuai__'+id).fadeOut();
      }else{
        // $('#btn_sesuai__'+id).fadeIn();
        console.log('qty_po, qty_datang, qty_sebelumnya: ',qty_po, qty_datang, qty_sebelumnya);
        let selisih = Math.round((qty_po - qty_datang - qty_sebelumnya)*10000) / 10000;
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

      console.log(qty_datang,qty_po);


    })
  })
</script>