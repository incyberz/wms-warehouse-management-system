<script>
  $(function(){
    let link_ajax = '';
    $('.btn_aksi').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let elemen = rid[0];
      let aksi = rid[1];
      let id_item = rid[2] ?? '';
      let from_db = rid[3] ?? '';

      console.log(elemen,aksi,id_item);

      if(aksi=='delete'){
        let y = confirm('Yakin untuk hapus?');
        if(!y) return;

        console.log('#source_'+elemen+'__'+id_item);
        if(id_item==''){
          $('#source_'+elemen).fadeOut();
          console.log('hide '+'#source_'+elemen);
          $('#'+elemen).fadeOut();
          console.log('#'+elemen+' NO AJAX DELETE');
        }else{
          $('#source_'+elemen+'__'+id_item).fadeOut();
          console.log('hide '+'#source_'+elemen+'__'+id_item);
          $('#'+elemen+'__'+id_item).fadeOut();
          console.log('hide '+'#'+elemen+'__'+id_item);
          // ====================================================
          // AJAX DELETE
          // ====================================================
          if(from_db=='from_db'){
            link_ajax = `ajax/delete_data.php?p=${elemen}&id=${id_item}`;
            $.ajax({
              url:link_ajax,
              success:function(a){
                if(a.trim()=='sukses'){
                  location.reload(); //zzz
                }else{
                  alert(`Tidak dapat menghapus data ${elemen}.`);
                  console.log(a);
                }
              }
            })
          }
        }


      }else if(aksi=='toggle' || aksi=='pra_tambah'){
        if(id_item!=''){
          $('#'+elemen+'__'+id_item).slideToggle();
          console.log('toggle #'+elemen+'__'+id_item);
        }else{
          $('#'+elemen).slideToggle(); 
          console.log('toggle'+'#'+elemen);
        }
      }else if(aksi=='close' || aksi=='cancel'){
        if(id_item!=''){
          let id = rid[2];
          $('#'+elemen+'__'+id).slideUp();
          console.log('#'+elemen+'__'+id+' slideUp');
        }else{
          $('#'+elemen).slideUp();
          console.log('#'+elemen+' slideUp');
        }
      }else if(aksi=='tambah'){
        console.log(aksi,elemen);
        link_ajax = `ajax/insert_data.php?p=${elemen}`;
        $.ajax({
          url:link_ajax,
          success:function(a){
            if(a.trim()=='sukses'){
              location.reload();
            }else if(a.toLowerCase().search('Duplicate entry')){
              alert(`Data NEW-${elemen} sudah ada. Silahkan edit kontennya!`)
              console.log(a);
            }else{
              alert(`Tidak dapat menambah ${elemen} baru.`);
              console.log(a);
            }
          }
        })
      }else{
        alert('Handler untuk aksi: '+aksi+" akan segera dibangun. Terimakasih sudah mencoba!");
        console.log('Belum ada handler untuk aksi: '+aksi);
      }


      
    })
  })
</script>