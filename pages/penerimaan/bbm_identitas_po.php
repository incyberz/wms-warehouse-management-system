<?php
# =======================================================================
# GET IDENTITAS PO 
# =======================================================================
$s = "SELECT 
a.id as id_po,
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
(SELECT count(1) FROM tb_bbm WHERE id_po=a.id) id_bbm_count, 
(SELECT COUNT(1) FROM tb_bbm WHERE tanggal_terima > '$today')+1 as  id_counter,
(SELECT SUM(qty) FROM tb_po_item WHERE id_po=a.id) sum_qty_po, 
(
  SELECT SUM(p.qty_diterima) 
  FROM tb_bbm_item p 
  JOIN tb_bbm q ON p.id_bbm=q.id 
  WHERE q.id_po=a.id) sum_qty_diterima 

FROM tb_po a 
JOIN tb_supplier b ON a.id_supplier=b.id 
WHERE a.kode='$no_po'
";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0){
  die(div_alert('danger',"Nomor PO : $no_po tidak ditermukan."));
}else{
  $d = mysqli_fetch_assoc($q);

  $id_bbm_count = $d['id_bbm_count'];
  $id_po = $d['id_po'];

  if($id_bbm_count){
    # =======================================================================
    # LIST ID-BBM 
    # =======================================================================
    $s2 = "SELECT a.*, a.id as id_bbm,
    (SELECT SUM(qty_diterima) FROM tb_bbm_item WHERE id_bbm=a.id) qty_diterima_per_bbm 
    FROM tb_bbm a WHERE a.id_po=$id_po";
    $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));

    while($d2=mysqli_fetch_assoc($q2)){

      $arr_no_bbm[$d2['id']] = $d2['kode'];

      if($d2['id']==$id_bbm){
        $no_bbm = $d2['kode'];
        $secondary = 'success';
        $border = 'border-biru';
        $hide_cetak = '';
      }else{
        $hide_cetak = 'hide_cetak';
        $secondary = 'secondary';
        $border = 'bordered';
      }
      if($d2['qty_diterima_per_bbm']==0){
        $ada_bbm_kosong = 1;
        $btn_simpan_caption = "Update BBM ini";
      }else{
        $btn_simpan_caption = "Simpan BBM";

      }
      $qty_diterima_per_bbm = $d2['qty_diterima_per_bbm'] ?? 0;
      $merah = $qty_diterima_per_bbm ? '' : 'merah';
      $tanggal = $qty_diterima_per_bbm ? eta(strtotime($d2['tanggal_terima'])-strtotime('now')) : '-';

      $id = 'bbm__delete__'.$d2['id_bbm'].'__id';
      $delete = $qty_diterima_per_bbm 
        ? "<span onclick='alert(\"BBM ini punya sub-data-qty sehingga tidak bisa langsung dihapus.\")'>$img_check</span>" 
        : "<span class='btn_aksi' id=$id>$img_delete</span>";

      $id = 'bbm__'.$d2['id_bbm'].'__vvvv';

      $qty_diterima_per_bbm = str_replace('.0000','',$qty_diterima_per_bbm);

      $link_no_bbm.= "
        <div id=$id class='$border $hide_cetak br5 p2 gradasi-$merah'>
          <div class='flex-between' style=gap:10px>
            <div>
              <div class='f12 miring abu'>$d2[nomor]</div>
              <a class='btn btn-sm btn-$secondary' href='?po&p=terima_barang&no_po=$no_po&id_bbm=$d2[id]'>$d2[kode]</a>
            </div>
            <div>
              $delete
            </div>
          </div>
          <div class='kecil miring abu'>QTY diterima: $qty_diterima_per_bbm</div>
          <div class='kecil miring abu'>$tanggal</div>
        </div>
      ";
    }

  }


  
  if($id_bbm_count==0 || $penerimaan=='selanjutnya'){
    # =======================================================================
    # AUTO CREATED ID BBM
    # =======================================================================
    $id_counter = $d['id_counter'];
    echo "id_counter: $id_counter";
    if($id_counter>100){
      $counter = $id_counter;
    }elseif($id_counter>10){
      $counter = "0$id_counter";
    }else{
      $counter = "00$id_counter";
    }
    $no_bbm = date('ymd').'-'.$counter;
    $nomor = $id_bbm_count+1;

    $s = "INSERT INTO tb_bbm (id_po,kode,nomor) VALUES ($id_po,'$no_bbm',$nomor)";
    // echo $s;
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));



    jsurl("?po&p=terima_barang&no_po=$no_po");
    exit;
  }

  # =======================================================================
  # SISA QTY
  # =======================================================================
  $sisa_qty = $d['sum_qty_po'] - $d['sum_qty_diterima'];
  // echo "<h1>sisa_qty :  $sisa_qty = $d[sum_qty_po] - $d[sum_qty_diterima];</h1>";



  # =======================================================================
  # TAMPIL INFORMASI PO
  # =======================================================================
  $tr = '';
  foreach ($d as $key => $value) {
    if($key=='no_bbm'||$key=='tanggal_terima') continue;
    if(!strpos("salt$key",'id_')){
      $kolom = ucwords(str_replace('_',' ',$key));
      // $value = $value ?? '-';
      if($value=='') $value = '-';
      $tr .= "
        <tr>
          <td class='abu miring'>$kolom</td>
          <td>$value</td>
        </tr>
      ";
    }
  }
  $tb_identitas_po = "
    <table class=table>
      <thead>
        <th width=30%>Nomor PO</th>
        <th>$no_po <span id=identitas_po_toggle>$img_detail</span></th>
      </thead>
      <tbody class=hideit id=identitas_po>
        $tr
      </tbody>
    </table>
  ";
}

echo "
  <h2>Identitas PO</h2>
  $tb_identitas_po
";