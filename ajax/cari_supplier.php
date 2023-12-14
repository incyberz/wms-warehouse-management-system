<?php
include 'harus_login.php';
include '../include/crud_icons.php';
// ONLY('WH');


// $keyword = $_GET['keyword'] ?? die(erid('keyword'));
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : die(erid('keyword'));

// $tr = "<tr><td colspan=100% class='alert alert-danger'>PO tidak ditemukan.</td></tr>";
$tr = '';
$s = "SELECT  
a.id as id_supplier,
a.nama as nama_supplier,
a.kode as kode_supplier 
FROM tb_supplier a 
WHERE 1 
AND (a.kode LIKE '%$keyword%' ) 
AND a.kode NOT LIKE 'STOCK%' 
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_row = mysqli_num_rows($q);

if($jumlah_row==0){
  die("<div class='alert alert-danger'>Data Supplier tidak ada. Silahkan pakai keyword lainnya.</div>");
}else{
  $tr = '';
  $i = 0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $id=$d['id_supplier'];
    $id_supplier=$d['id_supplier'];
    $kode_supplier=$d['kode_supplier'];

    $tr .= "
      <tr>
        <td>
          <div class='darkblue tebal'>$d[kode_supplier]</div>
          <div class='kecil miring abu' id=nama_supplier__$id>$d[nama_supplier]</div>

        </td>
        <td><span class='btn btn-success btn-sm pilih_supplier' id=pilih_supplier__$id>Pilih Supplier</span></td>
      </tr>
    ";
    if($i==5) break;
  }
}

$limited = $jumlah_row>$i ? "<div class='alert alert-info bordered br5 p2'>$i data dari $jumlah_row records. <span class=blue>Silahkan perjelas keyword Anda!</span></div>" : '';

echo "<table class='table table-hover'>$limited$tr</table>";
?>
