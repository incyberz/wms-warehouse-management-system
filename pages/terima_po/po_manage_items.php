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


$tr = "
  <tr class='alert alert-danger'>
    <td colspan=100%>Belum ada item PO.</td>
  </tr>
";

$s = "SELECT 
a.id as id_po_item,
a.qty,
a.harga_manual,
b.tmp_harga as harga,
b.satuan,
b.keterangan,
b.tmp_stok,  
b.id as id_barang,  
b.kode as kode_barang,  
b.nama as nama_barang,
(SELECT stok FROM tb_trx WHERE id_barang=b.id ORDER BY tanggal DESC LIMIT 1) stok,   
(SELECT tanggal FROM tb_trx WHERE id_barang=b.id ORDER BY tanggal DESC LIMIT 1) last_trx

FROM tb_po_item a 
JOIN tb_barang b ON a.id_barang=b.id 
WHERE a.id_po=$id_po";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)){
  $tr = '';
  $jumlah_item=0;
  while($d=mysqli_fetch_assoc($q)){
    $jumlah_item++;
    $id=$d['id_po_item'];
    $id_po_item=$d['id_po_item'];
    $qty=$d['qty'];
    $harga=$d['harga'];
    $satuan=$d['satuan'];
    $harga_manual=$d['harga_manual'];
    $keterangan=$d['keterangan'];
    $stok=$d['stok'];
    $tmp_stok=$d['tmp_stok'];

    $harga = $harga ?? $harga_manual;
    $harga = $harga ?? $null;

    $stok = $stok ?? $tmp_stok;
    $stok = $stok ?? 0;

    if($harga==$null || $qty==$null){
      $jumlah = $null;
      $total = $null;
    }else{
      $jumlah = $qty * $harga;
      $total = 'zzz';
    }

    $age = round((strtotime('now') - strtotime($d['last_trx'])) / (60*60*24),0);

    if($age<30){
      $age_show = "$age<span class='miring abu'>d</span>";
    }elseif($age<365){
      $age_show = round($age/30,0)."<span class='miring abu'>m</span>";
    }else{
      $age_show = round($age/365,0)."<span class='miring abu'>y</span>";
    }


    $tr .= "
      <tr id=source_edit_item_po__1>
        <td>$jumlah_item</td>
        <td>
          <div class=darkblue>
            $d[kode_barang]
            <span class='btn_aksi' id='edit_item_po__delete__$id'>$img_delete</span>
          </div>
          <div class=darkabu>$d[nama_barang]</div> 
        </td>
        <td>
          <div class='kecil'>
            <div><span class='abu miring'>Stok-lama:</span> <span id=stok_lama__$id>$stok</span> $satuan</div>
            <div><span class='abu miring'>Age:</span> $age_show $img_detail</div>
          </div>
        </td>
        <td class=kanan>
          <input class='form-control input input__$id input_qty' style=width:110px type=number step=0.01 id=qty__$id value='$qty'>
          <div class='kecil abu kiri mt1'>Stok-baru: <span id=stok_baru__$id>$stok</span></div>
        </td>
        <td class=kanan>
          <input class='form-control input input__$id input_harga' style=width:150px type=number step=0.01 id=harga__$id value='$harga'>
          <div class='kecil abu kiri mt1'>Harga lama: <span id=harga_lama__$id>$harga</span></div>
        </td>
        <td class=kanan id=jumlah__$id>$jumlah</td>
      </tr>
    ";

  }

}


?>
<table class="table table-striped">
  <thead class=gradasi-hijau>
    <th>NO</th>
    <th>KODE</th>
    <th>KETERANGAN</th>
    <th>QUANTITY</th>
    <th>HARGA</th>
    <th>JUMLAH</th>
  </thead>

  <?=$tr?>
  
  <tr>
    <td><span class='miring abu kecil'><?php echo ($jumlah_item+1);?></span></td>
    <td colspan=5 >
      <span class="green pointer">
        <span class="btn_aksi" id="edit_item_po__toggle">
        Tambah item <?=$img_add?></span>
      </span>
      <div id="edit_item_po" class="border-merah gradasi-kuning br5 p2 mb2 mt2 hideit" style="display: block;">
          <div class="row">
            <div class="col-11">
              <div class="flexy">
                <div class="darkblue">Cari barang:</div>
                <div class="darkblue">
                  <input type="text" class="form-control form-control-sm" id=keyword>
                </div>
              </div>
            </div>
            <div class="col-1 kanan">
              <span class="btn_aksi" id="edit_item_po__close"><img class="zoom pointer" src="assets/img/icons/close.png" alt="close" height="20px"></span>
            </div>
          </div>

          <!-- ======================================================== -->
          <!-- HASIL AJAX ITEM BARANG PO -->
          <!-- ======================================================== -->
          <div class='mt2' id=hasil_ajax></div>
        </div>
    </td>
  </tr> 
  <tfoot class=gradasi-kuning>
    <tr>
      <td colspan=3 class=kanan>Total</td>
      <td class=kanan id=total_qty>?</td>
      <td>&nbsp;</td>
      <td class=kanan id=total>?</td>
    </tr>
    <tr>
      <td colspan=3 class=kanan>PPN 11%</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td class=kanan id=ppn>?</td>
    </tr>
    <tr>
      <td colspan=3 class=kanan>Total + PPN</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td class=kanan id=total_ppn>?</td>
    </tr>
    <tr>
      <td colspan=100%>
        <button class="btn btn-success w-100 btn-sm" id="btn_simpan_item_po" disabled>Simpan</button>
      </td>
    </tr>
  </tfoot>

</table>











<script>$(function(){
  let total = 0;

  function hitung(id){
    let harga = parseFloat($('#harga__'+id).val());
    let qty = parseFloat($('#qty__'+id).val());
    let stok_lama = parseFloat($('#stok_lama__'+id).text());
    let jumlah = 0;

    harga = isNaN(harga) ? 0 : harga;
    qty = isNaN(qty) ? 0 : qty;
    jumlah = harga * qty;

    if($('#qty__'+id).val()<=0){
      $('#qty__'+id).val('');
      return;
    } 
    if($('#harga__'+id).val()<=0){
      $('#harga__'+id).val('');
      return;
    } 

    $('#jumlah__'+id).text(rupiah(jumlah));
    $('#stok_baru__'+id).text(stok_lama+qty);

    total += jumlah;
  }

  function hitung_total(is_save = 0){
    let z = document.getElementsByClassName('input_qty');
    let jumlah = 0;
    total = 0;
    let ids = '';
    let qtys = '';
    let hargas = '';
    let is_lengkap = 1;
    let total_qty = 0;
    for (let i = 0; i < z.length; i++){
      let id = z[i].id.split('__')[1];
      hitung(id);
      total_qty += parseFloat($('#qty__'+id).val());
      ids += id + ';';
      qtys += $('#qty__'+id).val() + ';';
      hargas += $('#harga__'+id).val() + ';';

      if($('#harga__'+id).val()=='') is_lengkap=0;
      if($('#qty__'+id).val()=='') is_lengkap=0;

    }
    // $('#total_qty').text(Math.round(total_qty,2)); //zzz precission
    $('#total_qty').text(total_qty); //zzz precission
    $('#total').text(rupiah(total));
    $('#ppn').text(rupiah(total*.11));
    $('#total_ppn').text(rupiah(total*.89));

    $('#btn_simpan_item_po').prop("disabled",true);
    $('#btn_simpan_item_po').text('Terdapat QTY atau harga satuan yang masih kosong.');
    if(is_lengkap){
      $('#btn_simpan_item_po').text('Simpan');
      $('#btn_simpan_item_po').prop("disabled",false);
      if(is_save){
        console.log('sace to db', ids, qtys,hargas);
        link_ajax = `ajax/crud.php?tb=po_item&aksi=insert_item&id=array&ids=${ids}&qtys=${qtys}&hargas=${hargas}`;
        $.ajax({
          url:link_ajax,
          success:function(a){
            if(a.trim()=='sukses'){
              $('#btn_simpan_item_po').prop("disabled",true);
              $('#btn_simpan_item_po').text('Simpan berhasil.');
            }else{
              // alert('Tidak dapat menyimpan items.');
              console.log(a);
            }
          }
        })
      }else{
        console.log('without saveDB');
      }
    }else{
      if(is_save) alert('Terdapat QTY atau harga satuan yang masih kosong.');
    }

  }
  hitung_total();
  $('#btn_simpan_item_po').prop("disabled",true);


  $('.input').change(function(){
    let tid = $(this).prop('id');
    let rid = tid.split('__');
    let aksi = rid[0];
    let id = rid[1];
    // console.log(aksi,id);

    hitung(id);
    hitung_total();
  });

  $('#btn_simpan_item_po').click(function(){
    hitung_total(1);
  });

  $('#keyword').keyup(function(){
    let keyword = $(this).val().trim();

    if(keyword.length<3 || keyword.length>15){
        $('#hasil_ajax').html("<div class='alert alert-info'>Silahkan ketik keyword minimal 3 huruf, max 15 huruf.</div>");
        return;
    }

    link_ajax = "ajax/cari_barang_untuk_po.php?keyword="+keyword;
    $.ajax({
      url:link_ajax,
      success:function(a){
        $('#hasil_ajax').html(a);
      }
    })
  })



})</script>