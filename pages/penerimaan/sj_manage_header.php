<?php
# ==========================================
# SELECT SUPPLIER
# ==========================================
// $opt = '';
// $ket_supplier = '';
// $s = "SELECT * FROM tb_supplier";
// $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
// while($d=mysqli_fetch_assoc($q)){
//   $alamat = $d['alamat']=='' ? "Alamat: $unset" : "Alamat: $d[alamat]";
//   $no_telfon = $d['no_telfon']=='' ? "Telfon: $unset" : "Telp. $d[no_telfon]";
//   $no_hp = $d['no_hp']=='' ? '' : ", HP. $d[no_hp]";
//   $no_wa = $d['no_wa']=='' ? '' : ", WA. $d[no_wa]";
//   $no_fax = $d['no_fax']=='' ? '' : ", Fax. $d[no_fax]";

//   $hideit = $id_supplier==$d['id'] ? '' : 'hideit';
//   $ket_supplier .= "
//   <div class='ket_supplier $hideit' id=ket_supplier__$d[id]>
//     $alamat $no_telfon $no_hp $no_wa $no_fax 
//   </div>
//   ";

//   $selected = $id_supplier==$d['id'] ? 'selected' : '';
//   $opt.= "<option value='$d[id]' $selected>$d[nama]</option>";
// }

// $select_supplier = "<select class='form-control' name=id_supplier>$opt</select>";


?>
<div class="mb2 bordered p2">
  <div class="f24 darkblue tebal mb2">Surat Jalan</div>

  Nomor PO 
  <input type="text" class="form-control mt1 mb2" value='<?=$kode_po?>' disabled>
  Supplier 
  <input type="text" class="form-control mt1 mb2" value='<?=$nama_supplier?>' disabled>
  Tanggal Terima 
  <input type="date" class="form-control mt1 mb2">
</div>

<?php
// zzz lewat tanggal terima save