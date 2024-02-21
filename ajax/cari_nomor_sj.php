<?php
include 'harus_login.php';
include '../include/crud_icons.php';
// ONLY('WH');


// $keyword = $_GET['keyword'] ?? die(erid('keyword'));
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : die(erid('keyword'));

// $tr = "<tr><td colspan=100% class='alert alert-danger'>PO tidak ditemukan.</td></tr>";
$tr = '';
$s = "SELECT  
a.id as id_sj,
a.kode as kode_sj ,
a.kode_po,
a.tanggal_terima,
a.id_supplier,
(SELECT SUM(qty) FROM tb_sj_item WHERE kode_sj=a.kode) as total_qty
FROM tb_sj a 
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
    $id=$d['id_sj'];
    $id_sj=$d['id_sj'];
    $kode_sj=$d['kode_sj'];
    $kode_po=$d['kode_po'];
    $total_qty=$d['total_qty'] ?? 0;

    $total_qty = floatval($total_qty);

    if($total_qty){
      # =================================================
      # JUMLAH SURAT JALAN
      # =================================================
      $s2 = "SELECT COUNT(1) as jumlah_sj FROM tb_sj WHERE kode_po='$d[kode_po]'";
      $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));

      $d2 = mysqli_fetch_assoc($q2);
      // $jumlah_sj = $d2['jumlah_sj'];
      // $jumlah_sj++; // surat jalan tidak boleh dihapus

      // if($jumlah_sj<10){
      //   $jumlah_sj_str = "00$jumlah_sj";
      // }elseif($jumlah_sj<100){
      //   $jumlah_sj_str = "0$jumlah_sj";
      // }else{
      //   $jumlah_sj_str = $jumlah_sj;
      // }

      $new_kode_sj = "$kode_po-new-$d[id_supplier]";

      $btn_add = "
      <form method=post style='display: inline-block'>
        <button class='btn btn-primary btn-sm' name=btn_tambah_sj_selanjutnya value='$new_kode_sj' onclick='return confirm(\"Tambah SJ Baru dari PO ini?\")'>Tambah SJ Baru</button>
      </form>
      ";
      
    }else{
      $btn_add = '<span class=abu>Total QTY masih nol</span>';
      
    }


    $age = round((strtotime('now') - strtotime($d['tanggal_terima'])) / (60*60*24),0);

    if($age<30){
      $age_show = "$age<span class='miring abu'>d</span>";
    }elseif($age<365){
      $age_show = round($age/30,0)."<span class='miring abu'>m</span>";
    }else{
      $age_show = round($age/365,0)."<span class='miring abu'>y</span>";
    }

    $merah = $total_qty==0 ? 'merah' : '';

    $tr .= "
      <tr class='gradasi-$merah'>
        <td>$d[kode_sj]</td>
        <td>$d[tanggal_terima]</td>
        <td>$total_qty</td>
        <td>
          <a href='?penerimaan&p=manage_sj&kode_sj=$kode_sj' class='btn btn-success btn-sm'>Manage SJ</a>
          $btn_add
        </td>
      </tr>
    ";
    if($i==5) break;
  }
}

$limited = $jumlah_row>$i ? "<div class='alert alert-info bordered br5 p2'>$i data dari $jumlah_row records. <span class=blue>Silahkan perjelas keyword Anda!</span></div>" : '';

echo "
<table class='table table-hover'>
  $limited
  <thead>
    <th>Surat Jalan</th>
    <th>Tanggal Terima</th>
    <th>Total QTY</th>
    <th>Aksi</th>
  </thead>
  $tr
</table>";
?>
