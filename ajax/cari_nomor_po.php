<?php
include 'harus_login.php';
include '../include/crud_icons.php';
// ONLY('WH');


// $keyword = $_GET['keyword'] ?? die(erid('keyword'));
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : die(erid('keyword'));

// $tr = "<tr><td colspan=100% class='alert alert-danger'>PO tidak ditemukan.</td></tr>";
$tr = '';
$s = "SELECT  
a.id as id_po,
a.kode as no_po ,
a.tanggal_pengiriman,
1
FROM tb_po a 
WHERE 1 
AND (a.kode LIKE '%$keyword%' ) 
AND a.kode NOT LIKE 'STOCK%' 
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_row = mysqli_num_rows($q);

if($jumlah_row==0){
  die('');
}else{
  $tr = '';
  $i = 0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $id=$d['id_po'];
    $id_po=$d['id_po'];
    $no_po=$d['no_po'];
    $age = round((strtotime('now') - strtotime($d['tanggal_pengiriman'])) / (60*60*24),0);

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
          <span class='kecil miring abu'>Nomor PO:</span> <span class='darkblue tebal'>$d[no_po]</span>
        </td>
        <td><a href='?po&p=terima_barang&no_po=$no_po' class='btn btn-success btn-sm'>Pilih PO ini</a></td>
      </tr>
    ";
    if($i==5) break;
  }
}

$limited = $jumlah_row>$i ? "<div class='alert alert-info bordered br5 p2'>$i data dari $jumlah_row records. <span class=blue>Silahkan perjelas keyword Anda!</span></div>" : '';

echo "<table class='table table-hover'>$limited$tr</table>";
?>
