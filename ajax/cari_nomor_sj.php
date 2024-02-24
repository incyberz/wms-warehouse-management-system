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
a.kode_supplier,
a.id_kategori,
(
  SELECT SUM(p.qty_po) FROM tb_sj_item p 
  WHERE p.kode_sj=a.kode) as qty_po,
(
  SELECT SUM(p.tmp_qty) FROM tb_sj_kumulatif p 
  JOIN tb_sj_item q ON p.id_sj_item=q.id  
  WHERE q.kode_sj=a.kode) as sum_qty_datang,
  -- per item surat jalan
(
  SELECT SUM(p.tmp_qty) FROM tb_sj_kumulatif p 
  JOIN tb_sj_item q ON p.id_sj_item=q.id  
  WHERE q.kode_sj!=a.kode) as qty_parsial,
  -- qty di surat jalan lain di satu PO
(
  SELECT SUM(p.tmp_qty) FROM tb_sj_kumulatif p 
  JOIN tb_sj_item q ON p.id_sj_item=q.id  
  JOIN tb_sj r ON q.kode_sj=r.kode  
  WHERE r.kode_po=a.kode_po) as total_qty_datang
  -- per PO

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
    $id_kategori=$d['id_kategori'];
    $kode_supplier=$d['kode_supplier'];
    $kode_sj=$d['kode_sj'];
    $kode_po=$d['kode_po'];
    $qty_po=$d['qty_po'] ?? 0;
    $qty_parsial=$d['qty_parsial'] ?? 0;
    $sum_qty_datang=$d['sum_qty_datang'] ?? 0;
    $total_qty_datang=$d['total_qty_datang'] ?? 0;

    $sum_qty_datang = floatval($sum_qty_datang);

    if($sum_qty_datang){
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

      $new_kode_sj = "$kode_po-new-$d[kode_supplier]";

      $btn_add_parsial = "
      <form method=post style='display: inline-block'>
        <input type=hidden name=id_kategori value=$id_kategori>
        <input type=hidden name=kode_supplier value=$kode_supplier>
        <button class='btn btn-primary btn-sm' name=btn_add_parsial value='$new_kode_sj' onclick='return confirm(\"Tambah Penerimaan Parsial dari PO ini?\")'>Add Parsial</button>
      </form>
      ";
      
    }else{
      $btn_add_parsial = '<span class=abu>Total QTY masih nol</span>';
    }


    $age = round((strtotime('now') - strtotime($d['tanggal_terima'])) / (60*60*24),0);

    if($age<30){
      $age_show = "$age<span class='miring abu'>d</span>";
    }elseif($age<365){
      $age_show = round($age/30,0)."<span class='miring abu'>m</span>";
    }else{
      $age_show = round($age/365,0)."<span class='miring abu'>y</span>";
    }

    $merah = $sum_qty_datang==0 ? 'merah' : '';

    $qty_kurang = $qty_po - $qty_parsial - $sum_qty_datang;

    $qty_po_show = number_format($qty_po,0);
    $sum_qty_datang_show = number_format($sum_qty_datang,0);
    $qty_parsial_show = number_format($qty_parsial,0);
    $qty_kurang_show = number_format($qty_kurang,0);
    $tanggal_terima_show = date('d-M-y',strtotime($d['tanggal_terima']));

    $tr .= "
      <tr class='gradasi-$merah'>
        <td>$d[kode_sj]</td>
        <td>$tanggal_terima_show</td>
        <td>$qty_po_show</td>
        <td>$sum_qty_datang_show</td>
        <td>$qty_parsial_show</td>
        <td>$qty_kurang_show</td>
        <td>
          <a href='?penerimaan&p=manage_sj&kode_sj=$kode_sj' class='btn btn-success btn-sm'>Manage SJ</a>
          $btn_add_parsial
        </td>
      </tr>
    ";
    if($i==5) break;
  }
}

$limited = $jumlah_row>$i ? "<div class='alert alert-info bordered br5 p2'>$i data dari $jumlah_row records. <span class=blue>Silahkan perjelas keyword Anda!</span></div>" : '';

echo "
<div class='abu f12 mb2'>Nomor PO diatas sudah ada pada database. Silahkan Pilih Manage atau Penerimaan Parsial</div>
<div class='hasil_ajax'>Hasil AJAX Pencarian Nomor SJ</div>
<table class='table table-hover'>
  $limited
  <thead>
    <th>Surat Jalan</th>
    <th>Tanggal Terima</th>
    <th>QTY PO</th>
    <th>QTY Datang</th>
    <th>QTY Parsial</th>
    <th>QTY Kurang</th>
    <th>Aksi</th>
  </thead>
  $tr
</table>";
?>
