<?php
# =======================================================================
# TOTAL QTY PENERIMAAN
# =======================================================================
echo "
  <h2>BBM Info</h2>
";

$s = "SELECT 
a.kode as nomor_BBM,
a.kode_sj as nomor_surat_jalan,
a.tanggal_masuk,
a.diverifikasi_oleh,
a.tanggal_verifikasi  
FROM tb_bbm a WHERE a.kode_sj='$kode_sj'";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);

$tr = '';
foreach ($d as $key => $value) {
  if($key=='id') continue;
  $kolom = ucwords(str_replace('_',' ',$key));
  $value = $value=='' ? $unset : $value;
  $tr.= "
    <tr>
      <td>$kolom</td>
      <td>$value</td>
    </tr>
  ";
}

echo "<table class=table>$tr</table>";