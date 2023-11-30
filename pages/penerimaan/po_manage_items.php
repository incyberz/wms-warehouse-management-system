<?php
# ==========================================
# KET ITEM PO
# ==========================================
$ket_item_po = 'Lorem ipsum dolor sit amet consectetur, adipisicing elit___Distinctio doloribus corporis ducimus, veniam officiis expedita quidem voluptates sed, magni veritatis odio officia___Praesentium ipsum iusto tenetur facilis odio accusamus temporibus';

$arr_ket_item_po = explode('___',$ket_item_po);
$li = '';
foreach ($arr_ket_item_po as $key => $item_ket_item_po) {
  $id_toggle = "edit_ket_item_po__toggle__$key";
  $id_save = "edit_ket_item_po__save__$key";
  $id_close = "edit_ket_item_po__close__$key";
  $id_delete = "edit_ket_item_po__delete__$key";
  $li.="
    <li class=mb2  id=source_edit_ket_item_po__$key>
      $item_ket_item_po 
      <span class='btn_aksi' id=$id_toggle>$img_edit</span>
      <span class='btn_aksi' id=$id_delete>$img_delete</span>
    </li>
    <li class='no-bullet $hideit' id=edit_ket_item_po__$key>
      <div class='border-merah gradasi-kuning br5 p2 flex-between mb4'>
        <textarea class='form-control'>$item_ket_item_po</textarea>
        <div class='ml2' style='width: 60px'>
          <span class='btn_aksi' id=$id_save>$img_save</span>
          <span class='btn_aksi' id=$id_close>$img_close</span>
        </div>
      </div>
    </li>  
  ";
}

$ket_item_po_show = "<ul>$li</ul>";

?>
<table class="table table-striped">
  <thead class=gradasi-hijau>
    <th>NO</th>
    <th>ITEM</th>
    <th>KETERANGAN ITEM</th>
    <th>SATUAN</th>
    <th>QUANTITY</th>
    <th>HARGA</th>
    <th>JUMLAH</th>
  </thead>
  
  <tr id=source_edit_item_po__1>
    <td>1</td>
    <td>K001</td>
    <td>
      KAIN KATUN JET BLACK 
      <span class="btn_aksi" id="edit_item_po__delete__1"><?=$img_delete?></span>
    </td>
    <td>KG</td>
    <td class=kanan>503.00</td>
    <td class=kanan>115.315.32</td>
    <td class=kanan>58.033.876.21</td>
  </tr>
  <tr id=source_edit_item_po__2>
    <td>2</td>
    <td>K027</td>
    <td>
      KAIN BATIK ORI CIREBON MEGA MENDUNG 
      <span class="btn_aksi" id="edit_item_po__delete__2"><?=$img_delete?></span>
    </td>
    <td>KG</td>
    <td class=kanan>19.00</td>
    <td class=kanan>116.315.32</td>
    <td class=kanan>2.208.108.21</td>
  </tr> 
  <tr>
    <td><span class='miring abu kecil'>3</span></td>
    <td colspan=6 >
      <span class="green pointer">
        Tambah item 
        <span class="btn_aksi" id="add_item_po__add"><?=$img_add?></span>
      </span>
    </td>
  </tr> 
  <tfoot class=gradasi-kuning>
    <tr>
      <td colspan=4 class=kanan>Total</td>
      <td class=kanan>522,00</td>
      <td>&nbsp;</td>
      <td class=kanan>60.211.711,11</td>
    </tr>
    <tr>
      <td colspan=4 class=kanan>PPN 11%</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td class=kanan>6.623.711,11</td>
    </tr>
    <tr>
      <td colspan=4 class=kanan>Total + PPN</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td class=kanan>66.835.711,11</td>
    </tr>
  </tfoot>

</table>
