<?php
include 'harus_login.php';
include '../include/crud_icons.php';
ONLY('PROC');


$keyword = $_GET['keyword'] ?? die(erid('keyword'));

$tr = "<tr><td colspan=100% class='alert alert-danger'>Barang tidak ditemukan.</td></tr>";
$s = "SELECT  
a.id as id_barang,
a.kode as kode_barang,
a.satuan,
a.nama as nama_barang,
(SELECT stok FROM tb_trx WHERE id_barang = a.id ORDER BY tanggal DESC LIMIT 1) stok,
(SELECT tanggal FROM tb_trx WHERE id_barang = a.id ORDER BY tanggal DESC LIMIT 1) last_trx

FROM tb_barang a 
WHERE 1 
AND (a.kode LIKE '%$keyword%' OR a.nama LIKE '%$keyword%')
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
    $age = round((strtotime('now') - strtotime($d['last_trx'])) / (60*60*24),0);

    if($age<30){
      $age_show = "$age<span class='miring abu'>d</span>";
    }elseif($age<365){
      $age_show = round($age/30,0)."<span class='miring abu'>m</span>";
    }else{
      $age_show = round($age/365,0)."<span class='miring abu'>y</span>";
    }

    $tr .= "
      <tr>
        <td>
          <span class=darkblue>$d[kode_barang]</span>
          <br><span class=darkabu><span class='kecil miring abu'>$i.</span> $d[nama_barang]</span> 
          <a href='?master&p=barang&keyword=$d[kode_barang]' onclick='return confirm(\"Edit barang ini?\")'>$img_edit</a>
        </td>
        <td>
          <div><span class='abu miring'>Stok:</span> $d[stok] $d[satuan]</div>
          <div><span class='abu miring'>Age:</span> $age_show $img_detail</div>
        </td>
        <td><button class='btn btn-success btn-sm'>Add</button></td>
      </tr>
    ";
    if($i==10) break;
  }
}

$limited = $jumlah_row>$i ? "<div class='alert alert-info bordered br5 p2'>$i data dari $jumlah_row records. <span class=blue>Silahkan perjelas keyword Anda!</span></div>" : '';

echo "<table class='table'>$limited$tr</table>";
?>
