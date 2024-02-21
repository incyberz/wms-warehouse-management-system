<?php
$debug .= "<br>id_kategori: <span id=id_kategori>$id_kategori</span>";
$is_valid_all_qty = true;

# ==========================================
# KET ITEM PO
# ==========================================
$count_item = 0;
$ket_sj_item = 'Lorem ipsum dolor sit amet consectetur, adipisicing elit___Distinctio doloribus corporis ducimus, veniam officiis expedita quidem voluptates sed, magni veritatis odio officia___Praesentium ipsum iusto tenetur facilis odio accusamus temporibus';

$arr_ket_sj_item = explode('___',$ket_sj_item);
$li = '';
foreach ($arr_ket_sj_item as $key => $item_ket_sj_item) {
  $id_toggle = "edit_ket_sj_item__toggle__$key";
  $id_save = "edit_ket_sj_item__save__$key";
  $id_close = "edit_ket_sj_item__close__$key";
  $id_delete = "edit_ket_sj_item__delete__$key";
  $li.="
    <li class=mb2  id=source_edit_ket_sj_item__$key>
      $item_ket_sj_item 
      <span class='btn_aksi' id=$id_toggle>$img_edit</span>
      <span class='btn_aksi' id=$id_delete>$img_delete</span>
    </li>
    <li class='no-bullet $hideit' id=edit_ket_sj_item__$key>
      <div class='border-merah gradasi-kuning br5 p2 flex-between mb4'>
        <textarea class='form-control'>$item_ket_sj_item</textarea>
        <div class='ml2' style='width: 60px'>
          <span class='btn_aksi' id=$id_save>$img_save</span>
          <span class='btn_aksi' id=$id_close>$img_close</span>
        </div>
      </div>
    </li>  
  ";
}

$ket_sj_item_show = "<ul>$li</ul>";


$tr = "
  <tr class='alert alert-danger'>
    <td colspan=100%>Belum ada item pada Surat Jalan | <a href='#' onclick='alert(\"Fitur ini masih dalam tahap pengembangan. Terimakasih.\")'>Get Item via API</a></td>
  </tr>
";

$s = "SELECT 
a.id as id_sj_item,
a.qty_po,
a.qty,
a.qty_diterima,
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

FROM tb_sj_item a 
JOIN tb_barang b ON a.kode_barang=b.kode 
WHERE a.kode_sj='$kode_sj'";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$count_item = mysqli_num_rows($q);
if($count_item){
  $tr = '';
  $no = 0;
  while($d=mysqli_fetch_assoc($q)){
    $no++;

    $id=$d['id_sj_item'];
    $id_sj_item=$d['id_sj_item'];
    $qty_po=floatval($d['qty_po']);
    $qty=floatval($d['qty']);
    $qty_diterima=floatval($d['qty_diterima']);
    $harga=$d['harga'];
    $satuan=$d['satuan'];
    $harga_manual=floatval($d['harga_manual']);
    $keterangan=$d['keterangan'];
    $stok=$d['stok'];
    $tmp_stok=$d['tmp_stok'];

    $harga = $harga ?? $harga_manual;
    $harga = $harga ?? 0;

    $stok = $stok ?? $tmp_stok;
    $stok = $stok ?? 0;

    if($harga==$null || $qty==$null){
      $jumlah = $null;
      $total = $null;
    }else{
      $jumlah = $qty * $harga;
      $total = 'zzz';
    }

    // update value is_valid_all_qty
    if(!$qty) $is_valid_all_qty = false;

    
    if($qty and $d['last_trx']){
      $age = round((strtotime('now') - strtotime($d['last_trx'])) / (60*60*24),0);
      if($age<30){
        $age_show = "$age<span class='miring abu'>d</span>";
      }elseif($age<365){
        $age_show = round($age/30,0)."<span class='miring abu'>m</span>";
      }else{
        $age_show = round($age/365,0)."<span class='miring abu'>y</span>";
      }
      $age_show = "<span class='abu miring'>Age:</span> $age_show";
    }else{
      $age_show = '';
    }

    $satuan = $satuan=='' ? "<a href='?master&p=barang&keyword=$d[kode_barang]' target=_blank class='merah tebal'>SATUAN UNSET</a>" : $satuan;
    $qty_diterima_show = $qty_diterima ? "<span>$qty_diterima $satuan<span>" : '<span class="kecil miring abu">(belum ada)</span>';
    $qty_disabled = $qty_diterima ? 'disabled' : '';

    $tr .= "
      <tr id=source_edit_sj_item__$id>
        <td>$no</td>
        <td>
          <div class=darkblue>
            $d[kode_barang]
            <span class='btn_aksi' id='edit_sj_item__delete__$id'>$img_delete</span>
          </div>
          <div class=darkabu>$d[nama_barang]</div> 
        </td>
        <td>
          <input class='form-control input input__$id input_qty_po' style=width:110px type=number step=0.01 id=qty_po__$id value='$qty_po' $qty_disabled>
        </td>
        <td class=kanan>
          <input class='form-control input input__$id input_qty' style=width:110px type=number step=0.01 id=qty__$id value='$qty' $qty_disabled>
        </td>
        <td>$qty_diterima_show</td>
        <td class=kanan>
          <input class='form-control input input__$id input_harga' style=width:150px type=number step=0.01 id=harga__$id value='$harga'>
        </td>
        <td class=kanan id=jumlah__$id>$jumlah</td>
      </tr>
    ";

  }

}

$tr_tambah = "
  <tr>
    <td><span class='miring abu kecil'>*</td>
    <td colspan=100% >
      <span class='green pointer'>
        <span class='btn_aksi' id='edit_sj_item__toggle'>
          Tambah item $img_add
        </span>
      </span>
      <div id='edit_sj_item' class='border-merah gradasi-kuning br5 p2 mb2 mt2 hideit' style='display: blocasdk;'>
          <div class='row'>
            <div class='col-11'>
              <div class='flexy'>
                <div class='darkblue'>Cari $kategori:</div>
                <div class='darkblue'>
                  <input type='text' class='form-control form-control-sm' id=keyword>
                </div>
              </div>
            </div>
            <div class='col-1 kanan'>
              <span class='btn_aksi' id='edit_sj_item__close'><img class='zoom pointer' src='assets/img/icons/close.png' alt='close' height='20px'></span>
            </div>
          </div>

          <!-- ======================================================== -->
          <!-- HASIL AJAX ITEM BARANG PO -->
          <!-- ======================================================== -->
          <div class='mt2' id=hasil_ajax></div>
        </div>
    </td>
  </tr>
";

if($tanggal_verifikasi_bbm){
  $tgl = date('d M Y H:i:s',strtotime($tanggal_verifikasi_bbm));
  $tr_tambah = "
    <tr>
      <td colspan=100% class='kecil'>
        <span class='hijau miring'>)* BBM sudah terverifikasi pada tanggal $tgl</span>
      </td>
    </tr>
  ";
}

?>
<div class="wadah">
  <div class="f20 darkblue tebal mb2">Item Surat Jalan</div>
  <table class="table table-striped">
    <thead class=gradasi-hijau>
      <th>NO</th>
      <th>KODE</th>
      <th>QTY PO</th>
      <th>QTY Adjusted</th>
      <th>QTY Diterima</th>
      <th>INFO HARGA</th>
      <th>JUMLAH</th>
    </thead>
  
    <?=$tr?>
    <?=$tr_tambah?> 
    <tfoot class=gradasi-kuning>
      <tr>
        <td colspan=4 class=kanan>Total</td>
        <td class=kanan id=total_qty>?</td>
        <td>&nbsp;</td>
        <td class=kanan id=total>?</td>
      </tr>
      <tr>
        <td colspan=4 class=kanan>PPN 11%</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class=kanan id=ppn>?</td>
      </tr>
      <tr>
        <td colspan=4 class=kanan>Total + PPN</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class=kanan id=total_ppn>?</td>
      </tr>
      <tr>
        <td colspan=100%>
          <button class="btn btn-success w-100 btn-sm" id="btn_simpan_sj_item" disabled>Simpan</button>
        </td>
      </tr>
    </tfoot>
  
  </table>
  <table class='darkabu f12 flexy'>
    <tr>
      <td valign=top><b>Catatan:</b></td> 
      <td valign=top>
        <ul>
          <li>QTY PO tidak bisa diubah jika sudah ada QTY Diterima pada BBM</li>
          <li>Item Surat Jalan tidak bisa ditambah jika BBM sudah diverifikasi</li>
        </ul>
      </td>
    </tr>
  </table>

</div>

<?php
if($count_item){
  if($is_valid_all_qty){
    // Next Process
    echo "
      <div class=' mb2'>Next: <a href='?penerimaan&p=bbm&kode_sj=$kode_sj'>Proses Bukti Barang Masuk</a></div>
    ";
  }else{
    echo '<div class="abu consolas miring f12">Belum bisa ke proses BBM, masih terdapat QTY PO yang kosong.</div>';
  }
}else{
  echo '<div class="abu consolas miring f12">Belum ada item pada Surat Jalan.</div>';
} 
?>









<script>$(function(){
  let total = 0;

  function hitung(id){
    let harga = parseFloat($('#harga__'+id).val());
    let qty = parseFloat($('#qty__'+id).val());
    // let stok_lama = parseFloat($('#stok_lama__'+id).text());
    let jumlah = 0;

    harga = isNaN(harga) ? 0 : harga;
    qty = isNaN(qty) ? 0 : qty;
    jumlah = harga * qty;

    if($('#qty__'+id).val()<=0){
      $('#qty__'+id).val('');
      return;
    } 
    if($('#harga__'+id).val()<=0){
      $('#harga__'+id).val(0);
      return;
    } 

    $('#jumlah__'+id).text(rupiah(jumlah));
    // $('#stok_baru__'+id).text(stok_lama+qty);

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

      if($('#harga__'+id).val()=='') $('#harga__'+id).val(0);
      if($('#qty__'+id).val()=='') is_lengkap=0;

    }
    // $('#total_qty').text(Math.round(total_qty,2)); //zzz precission
    $('#total_qty').text(total_qty); //zzz precission
    $('#total').text(rupiah(total));
    $('#ppn').text(rupiah(total*.11));
    $('#total_ppn').text(rupiah(total*.89));

    $('#btn_simpan_sj_item').prop("disabled",true);
    $('#btn_simpan_sj_item').text('Terdapat QTY atau harga satuan yang masih kosong.');
    if(is_lengkap){
      $('#btn_simpan_sj_item').text('Simpan');
      $('#btn_simpan_sj_item').prop("disabled",false);
      if(is_save){
        link_ajax = `ajax/crud.php?tb=sj_item&aksi=insert_item&id=array&ids=${ids}&qtys=${qtys}&hargas=${hargas}`;
        console.log('save to db link_ajax :: ', link_ajax);
        $.ajax({
          url:link_ajax,
          success:function(a){
            if(a.trim()=='sukses'){
              $('#btn_simpan_sj_item').prop("disabled",true);
              $('#btn_simpan_sj_item').text('Simpan berhasil.');
              // location.reload();
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
  $('#btn_simpan_sj_item').prop("disabled",true);


  $('.input').change(function(){
    let tid = $(this).prop('id');
    let rid = tid.split('__');
    let aksi = rid[0];
    let id = rid[1];
    // console.log(aksi,id);

    hitung(id);
    hitung_total();
  });

  $('#btn_simpan_sj_item').click(function(){
    hitung_total(1);
  });

  $('#keyword').keyup(function(){
    let keyword = $(this).val().trim();
    let kode_sj = $('#kode_sj').text();
    let id_kategori = $('#id_kategori').text();

    if(keyword.length<3 || keyword.length>15){
        $('#hasil_ajax').html("<div class='alert alert-info'>Silahkan ketik keyword minimal 3 huruf, max 15 huruf.</div>");
        return;
    }

    link_ajax = "ajax/cari_barang_untuk_sj.php?keyword="+keyword+"&kode_sj="+kode_sj+"&id_kategori="+id_kategori;
    $.ajax({
      url:link_ajax,
      success:function(a){
        $('#hasil_ajax').html(a);
      }
    })
  })



})</script>