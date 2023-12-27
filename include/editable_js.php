<script>
  $(function(){
    let old_val = '';
    $(".editable").click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let kolom = rid[0];
      let tabel = rid[1];
      let id = rid[2];

      old_val=$(this).val().trim();
      $('#'+kolom+'__check__'+id).hide();
    })
    $(".editable").change(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let kolom = rid[0];
      let tabel = rid[1];
      let id = rid[2];
      let new_val = $(this).val().trim();
      
      if(old_val==new_val){
        console.log('old_val==new_val :: aborted',`${old_val}==${new_val}`);
        return;
      }

      let link_ajax = `ajax/crud.php?tb=${tabel}&aksi=update&id=${id}&kolom=${kolom}&value=${new_val}`;
      
      $.ajax({
        url:link_ajax,
        success:function(a){
          console.log(a);
          if(a.trim()=='sukses'){
            $('#'+kolom+'__check__'+id).fadeIn();
          }
        }
      })
      
    })
  })
</script>