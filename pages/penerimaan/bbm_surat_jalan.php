<?php
# =======================================================================
# GET IDENTITAS SJ 
# =======================================================================
$s = "SELECT 
a.id as id_sj,
a.*,
b.id as id_supplier,
b.kode as kode_supplier,
b.nama as nama_supplier,
b.alamat as alamat_supplier,
b.contact_person as contact_person,
b.no_telfon as telp_supplier, 
(SELECT count(1) FROM tb_sj_item WHERE kode_sj='$kode_sj') jumlah_item_barang, 
(SELECT count(1) FROM tb_bbm WHERE kode_sj='$kode_sj') id_bbm_count, 
(SELECT COUNT(1) FROM tb_bbm WHERE tanggal_terima > '$today')+1 as  id_counter,
(SELECT SUM(qty) FROM tb_sj_item WHERE kode_sj='$kode_sj') sum_qty_po, 
(
  SELECT SUM(p.qty) 
  FROM tb_sj_item p 
  JOIN tb_sj q ON p.kode_sj=q.kode 
  WHERE q.kode='$kode_sj') sum_qty_diterima 

FROM tb_sj a 
JOIN tb_supplier b ON a.kode_supplier=b.kode 
WHERE a.kode='$kode_sj'
";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0){
  die(div_alert('danger',"Nomor SJ : $kode_sj tidak ditermukan."));
}else{
  $d = mysqli_fetch_assoc($q);

  $kode_sj_supplier = $d['kode_sj_supplier'] ?? $unset;
  $id_bbm_count = $d['id_bbm_count'];
  if($id_bbm_count==0){
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

    $s = "INSERT INTO tb_bbm (kode_sj,kode) VALUES ('$kode_sj','$no_bbm')";
    // echo $s;
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

    jsurl("?penerimaan&p=bbm&kode_sj=$kode_sj");
    exit;
  }

  $kode_po = $d['kode_po'];
  $tanggal_terima = $d['tanggal_terima'];
  $awal_terima = date('H:i',strtotime($d['awal_terima']));
  $akhir_terima = date('H:i',strtotime($d['akhir_terima']));
  $kode_supplier = $d['kode_supplier'];
  $sum_qty_po = $d['sum_qty_po'];
  $sum_qty_diterima = $d['sum_qty_diterima'];
  $id_kategori = $d['id_kategori'];

  $kategori = $arr_kategori[$id_kategori];
  $cat = $arr_cat[$id_kategori];

  # =======================================================================
  # SISA QTY
  # =======================================================================
  $sisa_qty = $sum_qty_po - $sum_qty_diterima;

  # =======================================================================
  # TAMPIL INFORMASI SJ
  # =======================================================================
  $tr = '';
  foreach ($d as $key => $value) {
    if(
      $key=='id'
      ||$key=='kode'
      ||$key=='status'
    ) continue;
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

  $tanggal_terima_show = date('d M Y', strtotime($tanggal_terima));
  $tanggal_terima_show = $awal_terima ? "$tanggal_terima_show ~ Pukul: $awal_terima" : $tanggal_terima_show;
  $tanggal_terima_show = ($awal_terima && $akhir_terima) ? "$tanggal_terima_show s.d $akhir_terima" : $tanggal_terima_show;

  $tb_sj = "
    <table class=table>
      <tr>
        <td width=200px>Surat Jalan From Supplier</td>
        <td>$kode_sj_supplier </td>
      </tr>
      <tr>
        <td width=200px>Tanggal Terima</td>
        <td>$tanggal_terima_show </td>
      </tr>
    </table>
    <table class=table>
      <thead>
        <th width=200px>Nomor Terima Barang</th>
        <th>$kode_sj <span id=identitas_po_toggle  class=hide_cetak>$img_detail</span></th>
      </thead>
      <tbody class=hideit id=identitas_po>
        $tr
        <tr>
          <td colspan=100% class='kecil tengah'>
            <a href='?master&p=supplier&keyword=$kode_supplier'>Ubah Data Supplier</a> | 
            <a href='?penerimaan&p=manage_sj&kode_sj=201500001-001'>Manage SJ</a>

          </td>
        </tr>
      </tbody>
    </table>
  ";
}

echo "
  <h2 class=hide_cetak>Surat Jalan</h2>
  $tb_sj
";