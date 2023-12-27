<?php
# =======================================================================
# TOTAL QTY PENERIMAAN
# =======================================================================
echo "
  <h2 class=hide_cetak>BBM Info</h2>
";

$s = "SELECT 
a.id as id_bbm,
a.kode,
a.tanggal_masuk,
a.awal_masuk,
a.akhir_masuk,
a.tanggal_verifikasi,
(SELECT nama FROM tb_user WHERE id=a.diverifikasi_oleh) verifikator
FROM tb_bbm a 
WHERE a.kode_sj='$kode_sj'";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d_bbm = mysqli_fetch_assoc($q);

$id_bbm = $d_bbm['id_bbm'];
$awal_masuk = $d_bbm['awal_masuk'];
$akhir_masuk = $d_bbm['akhir_masuk'];

$tr = '';
foreach ($d_bbm as $key => $value) {
  $value_show = '';
  if(
    $key=='id'
    ||$key=='kode'
    ||$key=='awal_masuk'
    ||$key=='akhir_masuk'
    ) continue;
  $kolom = ucwords(str_replace('_',' ',$key));

  if($key=='verifikator'||$key=='tanggal_verifikasi'){
    $value_show = $value=='' ? $unverified : $value;
  }else{
    $value_show = $value=='' ? $unset : $value;

    $key_toggle = 'kolom_'.$key.'__toggle';
    $value_show = "
      <span class='btn_aksi pointer' id=$key_toggle>$value_show</span>
      <div id=kolom_$key class=hideit>
        <input class='form-control-sm edit_here' id=edit_here__$key value='$value'> 
        <button class='btn btn-sm btn-success btn_set_here' id=btn_set_here__$key >Set</button>
      </div>
    ";

  }

  $tr.= "
    <tr>
      <td>$kolom</td>
      <td>$value_show</td>
    </tr>
  ";
}

echo "
  <table class=table>
    <thead>
      <th width=200px>Nomor BBM</th>
      <th>
        $d_bbm[kode] 
        <span class='btn_aksi hide_cetak' id=sj_info__toggle>$img_detail</span> 
      </th>
    </thead>
    <tbody id=sj_info class=hideit>
      $tr
    </tbody>
  </table>
";


?>
<script>
  $(function(){
    $('.btn_set_here').click(function(){
      //zzz
      alert('Fitur Select and Save akan segera diaktifkan. Terimakasih sudah mencoba!');
    })
  })
</script>