<?php
include 'pages/must_login.php';
// ONLY('ADMIN');


$tr = "<tr><td colspan=100% class='alert alert-danger'>Barang tidak ditemukan.</td></tr>";
$s = "SELECT  
a.id as id_barang,
a.kode as kode_barang,
a.satuan,
a.nama as nama_barang
FROM tb_barang a 
WHERE 1 
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_row = mysqli_num_rows($q);

if($jumlah_row==0){
  die("<div class='alert alert-danger'>Data barang tidak ditemukan. Silahkan memakai <i><u>keyword</u></i> lainnya.</div>");
}else{
  $tr = '';
  $i = 0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $id=$d['id_barang'];
    $id_barang=$d['id_barang'];
    $tr .= "
      <tr>
        <td>$d[kode_barang]</td>
        <td>$d[nama_barang]</td>
        <td><span class='abu miring'>Stok:</span> 321 $d[satuan]</td>
        <td><button class='btn btn-success btn-sm'>Add</button></td>
      </tr>
    ";
    if($i==10) break;
  }
}

$limited = $jumlah_row>$i ? "<div class='alert alert-info bordered br5 p2'>$i data dari $jumlah_row records. <span class=blue>Silahkan perjelas keyword Anda!</span></div>" : '';

echo "<table class='table'>$limited$tr</table>";
?>
