<script>
  $(function(){
    let link_ajax = '';
    let id_po = $('#id_po').text();
    let tb = $('#tb').text();

    $('.btn_aksi').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let elemen = rid[0];
      let aksi = rid[1];
      let id_item = rid[2] ?? '';
      let kolom = rid[3] ?? '';

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
          if(kolom!=''){
            console.log(' delete with AJAX');
            link_ajax = `ajax/crud.php?tb=${elemen}&aksi=delete&id=${id_item}`;

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
          }else{
            console.log('not delete to from_db');

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
        link_ajax = `ajax/crud.php?tb=${elemen}&aksi=insert`;
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
      }else if(aksi=='save_item' || aksi=='delete_item'){
        // save item = add item ke list
        // delete item = remove from list
        if(aksi=='delete_item'){
          let y = confirm('Yakin untuk hapus list-item?');
          if(!y) return;
        }

        let kolom = rid[3];
        // console.log(aksi, 'untuk', kolom, id_po);

        if(id_po.length>0){
          let z = document.getElementsByClassName(kolom);
          let value_items = '';
          let cid = '';
          for (let i = 0; i < z.length; i++) {
            if(id_item==i){
              cid = i;
              continue;
            } 
            value_items += z[i].innerText + ' ___';
            console.log('LOOP =======',value_items);
          }
  
          if(aksi=='save_item'){
            value_items += $('#update_value__'+kolom+'__'+cid).val();
          }
          
          value_items = value_items.replace(/\s\s+/g, ' ');
          
          console.log(value_items, kolom, id_po);
          link_ajax = `ajax/crud.php?tb=po&aksi=update&id=${id_po}&kolom=${kolom}&value=${value_items}`;
          console.log(link_ajax);

          $.ajax({
            url:link_ajax,
            success:function(a){
              if(a.trim()=='sukses'){
                location.reload();
              }else{
                alert('Tidak bisa menyimpan item.');
                console.log(a);
              }
            }
          })

        }

      }else if(aksi=='simpan'){
        let value = $('#input_'+kolom).val().replace(/\s\s+/g, ' ');
        link_ajax = `ajax/crud.php?tb=${tb}&aksi=update&kolom=${kolom}&value=${value}&id=${id_po}`;
        $.ajax({
          url:link_ajax,
          success:function(a){
            if(a.trim()=='sukses'){
              // location.reload();
              $('#edit_'+kolom).slideUp();
              $('#target_'+kolom).text(value);
            }else{
              alert('Tidak bisa menyimpan item.');
              console.log(a);
            }
          }
        })

        console.log(id_po, kolom, value);
      }else{
        alert('Handler untuk aksi: '+aksi+" akan segera dibangun. Terimakasih sudah mencoba!");
        console.log('Belum ada handler untuk aksi: '+aksi);
      }


      
    })
  })
</script>