<div class="pagetitle">
  <h1>Bukti Barang Masuk</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?po">PO Home</a></li>
      <li class="breadcrumb-item"><a href="?po&p=terima_barang">Cari PO</a></li>
      <li class="breadcrumb-item active">BBM</li>
    </ol>
  </nav>
</div>


<?php
$tb_identitas_po = '';
$no_bbm = '';
$id_bbm = '';


$s = "SELECT 
a.id as id_po,
a.kode as nomor_PO,
a.tempat_pengiriman,
a.tanggal_pemesanan,
a.tanggal_pengiriman,
a.date_created tanggal_buat_PO,
b.id as id_supplier,
b.kode as kode_supplier,
b.nama as nama_supplier,
b.alamat as alamat_supplier,
b.contact_person as contact_person,
b.no_telfon as telp_supplier, 
(SELECT count(1) FROM tb_po_item WHERE id_po=a.id) jumlah_item_barang, 
(SELECT id FROM tb_bbm WHERE id_po=a.id) id_bbm 

FROM tb_po a 
JOIN tb_supplier b ON a.id_supplier=b.id 
WHERE a.kode='$no_po'
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0){
  die(div_alert('danger',"Nomor PO : $no_po tidak ditermukan."));
}else{
  $d = mysqli_fetch_assoc($q);

  $tr = '';
  foreach ($d as $key => $value) {
    if(!strpos("salt$key",'id_')){
      $kolom = ucwords(str_replace('_',' ',$key));
      $value = $value ?? '-';
      $tr .= "
        <tr>
          <td>$kolom</td>
          <td>$value</td>
        </tr>
      ";
    }
  }
  $tb_identitas_po = "<table class=table>$tr</table>";
}



$s = "SELECT *,
a.id as id_item,
b.id as id_barang,
b.kode as kode_barang,
b.nama as nama_barang 

FROM tb_po_item a 
JOIN tb_barang b ON a.id_barang=b.id 
WHERE a.id_po=$d[id_po]";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0){
  $tr = "<tr><td colspan=100% ><div class='alert alert-danger'>Belum ada item barang pada PO ini</div></td></tr>";
  
}else{
  $tr = '';
  $i=0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $id = $d['id_item'];
    $tr .= "
      <tr>
        <td>$i</td>
        <td>
          <span class=darkblue>$d[kode_barang]</span>
          <div class='darkabu f14'>$d[nama_barang]</div>
        </td>
        <td>
          <span id=qty_po__$id>$d[qty]</span> 
          <span id=satuan__$id>$d[satuan]</span>
        </td>
        <td>
          <div class=flexy>
            <div>
              <input id='qty_diterima__$id' class='form-control form-control-sm qty_diterima' type=number step=0.01>
              <div class='mt1 abu kecil' id=selisih__$id></div>
            </div>
            <div>
              <button class='btn btn-success btn-sm btn_sesuai' id=btn_sesuai__$id>Sesuai</button>
              <div class=hideit id=img_check__$id>$img_check</div>
            </div>
          </div>
          
        </td>
      </tr>
    ";
  }

}

echo "
<div class='flexy mb4 mt1'>
  <div>No BBM</div>
  <div><input type='text' class='form-control form-control-sm' value='$no_bbm'></div>
</div>

<h2>Identitas PO</h2>
$tb_identitas_po


<h2>Barang yang Diterima</h2>
<table class='table'>
    <thead>
      <th>No</th>
      <th>Kode / Item</th>
      <th>QTY-PO</th>
      <th>QTY Diterima</th>
    </thead>
    $tr
  </table>
";













?><script>
  $(function(){
    $('.btn_sesuai').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
     
      $('#qty_diterima__'+id).val($('#qty_po__'+id).text());
      $('#btn_sesuai__'+id).hide();
      $('#img_check__'+id).show();

    });


    $('.qty_diterima').keyup(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];
      
      let qty_diterima = $(this).val();
      if(qty_diterima==''){
        $('#btn_sesuai__'+id).show();
        $('#selisih__'+id).html('');
        return;
      }
      qty_diterima = parseFloat(qty_diterima);

      let satuan = $('#satuan__'+id).text();
      let qty_po = parseFloat($('#qty_po__'+id).text());

      $('#img_check__'+id).hide();
      if(isNaN(qty_diterima)||isNaN(qty_po)){
        $('#selisih__'+id).html('masukan QTY yang benar..');
        $('#btn_sesuai__'+id).fadeOut();
      }else{
        // $('#btn_sesuai__'+id).fadeIn();
        let selisih = Math.round((qty_po - qty_diterima)*100) / 100;
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

      console.log(qty_diterima,qty_po);


    })
  })
</script>

