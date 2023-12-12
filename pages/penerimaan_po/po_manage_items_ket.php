<?php
if(isset($_POST['btn_simpan_ket_item'])){
  $ket_item = clean_sql(strip_tags($_POST['ket_item']));
  $s = "UPDATE tb_po SET ket_item = '$ket_item' WHERE id=$_POST[id_po]";
  // var_dump($s);
  
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  jsurl("?po&p=po_manage&no_po=$no_po");

}

$ket_po = $ket_item;
//'Lorem ipsum dolor sit amet consectetur, adipisicing elit___Distinctio doloribus corporis ducimus, veniam officiis expedita quidem voluptates sed, magni veritatis odio officia___Praesentium ipsum iusto tenetur facilis odio accusamus temporibus';

$arr_ket_item = explode('___',$ket_po);
$li = '';
foreach ($arr_ket_item as $key => $item_ket_item) {
  if(strlen($item_ket_item)>1){
    $id_toggle = "edit_ket_item__toggle__$key";
    $id_save = "edit_ket_item__save_item__$key".'__ket_item';
    $id_close = "edit_ket_item__close__$key";
    $id_delete = "edit_ket_item__delete_item__$key".'__ket_item';
    $li.="
      <li class='mb2 ket_item'  id=source_edit_ket_item__$key>
        $item_ket_item 
        <span class='btn_aksi' id=$id_toggle>$img_edit</span>
        <span class='btn_aksi' id=$id_delete>$img_delete</span>
      </li>
      <li class='no-bullet $hideit' id=edit_ket_item__$key>
        <div class='border-merah gradasi-kuning br5 p2 flex-between mb4'>
          <textarea class='form-control' id=update_value__ket_item__$key>$item_ket_item</textarea>
          <div class='ml2' style='width: 60px'>
            <span class='btn_aksi' id=$id_save>$img_save</span>
            <span class='btn_aksi' id=$id_close>$img_close</span>
          </div>
        </div>
      </li>  
    ";
  }
}
$ket_po_show = "<ul>$li</ul>";  
?>
<div class='bordered mt1 p2 mb4'>
  <div>
    <div>Catatan item:</div>
    <div>
      <?=$ket_po_show?>
      <div class="">
        <div class='mb2'>
          <span class="btn_aksi" id="edit_ket_item__pra_tambah"><?=$img_add?></span>
        </div>
        <div id="edit_ket_item" class='border-merah gradasi-kuning br5 p2 mb2 hideit'>
          <div class="row">
            <div class="col-11">
              <h2 class='darkblue f16'>Catatan item</h2>
            </div>
            <div class="col-1 kanan">
              <span class="btn_aksi" id="edit_ket_item__close"><?=$img_close?></span>
            </div>
          </div>
          <form method=post>
            <input type="hidden" name=id_po value=<?=$id_po?>>
            <textarea class="form-control mt2 mb2 gradasi-merah hideit" id=ket_item name=ket_item rows=5></textarea>
            <textarea class="form-control mt2 mb2" id=new_ket_item></textarea>
            <script>$(function(){
              $('#new_ket_item').keyup(function(){
                //zzz here
                let z = document.getElementsByClassName('ket_item');
                // console.log(z);
                let kets = '';
                for (let i = 0; i < z.length; i++) {
                  kets += z[i].innerText + '___';
                  // console.log(z.innerHTML,z.text,z.innerText);
                }

                kets += $(this).val();


                $('#ket_item').val(kets.replace(/\s\s+/g, ' '));
              })
            })</script>
            <div class="flex-between">
              <button class="btn btn-primary btn-sm" name=btn_simpan_ket_item>Simpan</button> 
              <span class="btn btn-danger btn_aksi btn-sm" id=edit_ket_item__cancel>Cancel</span> 
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>