<?php
include 'harus_login.php';
include '../include/crud_icons.php';
// ONLY('PROC');




$kode_sj = $_GET['kode_sj'] ?? die(erid('kode_sj')); if(!$kode_sj) die(erid('kode_sj::empty'));
$id_kategori = $_GET['id_kategori'] ?? die(erid('id_kategori')); if(!$id_kategori) die(erid('id_kategori::empty'));

$s = "SELECT kode_barang FROM tb_sj_item WHERE kode_sj='$kode_sj'";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$arr_kode_barang = [];
while($d=mysqli_fetch_assoc($q)){
  array_push($arr_kode_barang,$d['kode_barang']);
}


$keyword = $_GET['keyword'] ?? die(erid('keyword'));
$keyword = strtoupper($keyword);

$tr = "<tr><td colspan=100% class='alert alert-danger'>Barang tidak ditemukan.</td></tr>";
$s = "SELECT  
a.id as id_barang,
a.kode as kode_barang,
a.satuan,
a.nama as nama_barang,
(SELECT stok FROM tb_trx WHERE id_barang = a.id ORDER BY tanggal DESC LIMIT 1) stok,
(SELECT tanggal FROM tb_trx WHERE id_barang = a.id ORDER BY tanggal DESC LIMIT 1) last_trx

FROM tb_barang a 
-- LEFT JOIN tb_sj_item b ON a.kode=b.kode_barang 
-- WHERE b.id is null 
WHERE id_kategori=$id_kategori   
AND (a.kode LIKE '%$keyword%' OR a.nama LIKE '%$keyword%')
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_row = mysqli_num_rows($q);

if($jumlah_row==0){
  die("
    <div class='alert alert-info'>
      Data barang tidak ditemukan. Anda bisa menambah barang ini ke Master Barang. | 
      <a target=_blank href='?master&p=barang'>Lihat Master Barang</a>
    </div>
    <form method=post>
      <input type=hidden name=kode value='$keyword'>
      Kode Barang
      <input class='form-control mb2 mt1' disabled value='$keyword'>
      Nama Barang
      <input class='form-control mb2 mt1' name=nama required minlength=10 maxlength=100>
      <button class='btn btn-sm btn-primary' name=btn_simpan_dan_tambahkan>Simpan dan Tambahkan ke PO</button>

    </form>
  ");
}else{
  $tr = '';
  $i = 0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $kode_barang=$d['kode_barang'];
    if(in_array($kode_barang,$arr_kode_barang)) continue;
    
    $id=$d['id_barang'];
    $id_barang=$d['id_barang'];
    $stok=$d['stok'] ?? 0;

    $age = round((strtotime('now') - strtotime($d['last_trx'])) / (60*60*24),0);

    if($stok){
      if($age<30){
        $age_show = "$age<span class='miring abu'>d</span>";
      }elseif($age<365){
        $age_show = round($age/30,0)."<span class='miring abu'>m</span>";
      }else{
        $age_show = round($age/365,0)."<span class='miring abu'>y</span>";
      }
      
      $age_show = "<span class='abu miring'>Age:</span> $age_show";
    }else{
      $age_show = '';
    }

    $tr .= "
      <tr>
        <td>
          <span class=darkblue>$d[kode_barang]</span>
          <br><span class=darkabu><span class='kecil miring abu'>$i.</span> $d[nama_barang]</span> 
          <a target=_blank href='?master&p=barang&keyword=$d[kode_barang]' onclick='return confirm(\"Edit barang ini?\")'>$img_edit</a>
        </td>
        <td>
          <div><span class='abu miring'>Stok Gudang:</span> $stok $d[satuan]</div>
          <div>$age_show</div>
        </td>
        <td>
          <form method=post>
            <button class='btn btn-success btn-sm add_item' value=$kode_barang name=btn_add_sj_item>Add Item SJ</button>
          </form>
        </td>
      </tr>
    ";
    if($i==5) break;
  }
}

$limited = $jumlah_row>$i ? "<div class='alert alert-info bordered br5 p2'>$i data dari $jumlah_row records. <span class=blue>Silahkan perjelas keyword Anda!</span></div>" : '';

echo "<table class='table'>$limited$tr</table>";
?>
<script>
  $(document).on("click",".add_item",function(){
    let tid = $(this).prop('id');
    let rid = tid.split('__');
    let aksi = rid[0];
    let kode_barang = rid[1];
    let kode_po = $('#kode_po').text();
    console.log(kode_barang, kode_po);
  })
</script>