<?php
$ket_po = $keterangan;
//'Lorem ipsum dolor sit amet consectetur, adipisicing elit___Distinctio doloribus corporis ducimus, veniam officiis expedita quidem voluptates sed, magni veritatis odio officia___Praesentium ipsum iusto tenetur facilis odio accusamus temporibus';

$arr_ket_po = explode('___',$ket_po);
$li = '';
foreach ($arr_ket_po as $key => $item_ket_po) {
  if(strlen($item_ket_po)>1){
    $id_toggle = "edit_ket_po__toggle__$key";
    $id_save = "edit_ket_po__save__$key";
    $id_close = "edit_ket_po__close__$key";
    $id_delete = "edit_ket_po__delete__$key";
    $li.="
      <li class='mb2 keterangan'  id=source_edit_ket_po__$key>
        $item_ket_po 
        <span class='btn_aksi' id=$id_toggle>$img_edit</span>
        <span class='btn_aksi' id=$id_delete>$img_delete</span>
      </li>
      <li class='no-bullet $hideit' id=edit_ket_po__$key>
        <div class='border-merah gradasi-kuning br5 p2 flex-between mb4'>
          <textarea class='form-control'>$item_ket_po</textarea>
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
<div class='bordered mt1 p1 mb4'>
  <div style='display:grid; grid-template-columns: 50px auto'>
    <div>Ket:</div>
    <div>
      <?=$ket_po_show?>
      <div class="">
        <div class='mb2'>
          <span class="btn_aksi" id="edit_ket_po__pra_tambah"><?=$img_add?></span>
        </div>
        <div id="edit_ket_po" class='border-merah gradasi-kuning br5 p2 mb2'>
          <div class="row">
            <div class="col-11">
              <h2 class='darkblue f16'>Tambah Keterangan PO</h2>
            </div>
            <div class="col-1 kanan">
              <span class="btn_aksi" id="edit_ket_po__close"><?=$img_close?></span>
            </div>
          </div>
          <form method=post>
            <input type="hidden" name=id_po value=<?=$id_po?>>
            <textarea class="form-control mt2 mb2 gradasi-merah debug" id=keterangan nama=keterangan></textarea>
            <textarea class="form-control mt2 mb2" id=new_ket></textarea>
            <script>$(function(){
              $('#new_ket')
            })</script>
            <div class="flex-between">
              <button class="btn btn-primary btn_aksi btn-sm" name=btn_simpan_ket_po>Simpan</button> 
              <span class="btn btn-danger btn_aksi btn-sm" id=edit_ket_po__cancel>Cancel</span> 
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>