<div class="pagetitle">
  <h1>Assign PIC-CD</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?">Dashboard</a></li>
      <li class="breadcrumb-item active">Assign PIC-CD</li>
    </ol>
  </nav>
</div>

<?php
$kode_artikel = $_GET['kode_artikel'] ?? '';
if(strlen(trim($kode_artikel))!=9) die(div_alert('danger',"Dibutuhkan Kode Artikel yang valid (9 karakter)."));

$kode_brand = substr($kode_artikel,0,1);
$kode_gender = substr($kode_artikel,7,1);
$kode_apparel = substr($kode_artikel,8,1);
$kode_unik = "$kode_brand$kode_gender$kode_apparel";

$s = "SELECT kode_pic FROM tb_assign_pic WHERE kode_unik='$kode_unik'";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)){
  $d=mysqli_fetch_assoc($q);
  $kode_pic = $d['kode_pic'];
}else{
  $kode_pic = '';
}


include 'include/arr_brand.php';
include 'include/arr_gender.php';
include 'include/arr_apparel.php';
include 'include/arr_pic.php';

$brand = $arr_brand[$kode_brand];
$gender = $arr_gender[$kode_gender];
$apparel = $arr_apparel[$kode_apparel];

$radio_pic = '';
foreach ($arr_pic as $key => $value) {
  $biru = $key==$kode_pic ? 'tebal biru' : '';
  $checked = $key==$kode_pic ? 'checked' : '';
  $radio_pic.= "
    <div>
      <label class='$biru'>
        <input type=radio name=kode_pic value=$key required $checked> $key ~ $value 
      </label>
    </div>
  ";
}

echo "
<div class=mb2>Kode Artikel: <span class='consolas f24 darkblue' style='letter-spacing: 5px'>$kode_artikel</span></div>
<div>Assign PIC-CD untuk:</div>
<ul class=abu>
  <li>Brand: <b class=darkblue>$brand ~ $kode_brand</b></li>
  <li>Gender: <b class=darkblue>$gender ~ $kode_gender</b></li>
  <li>Apparel: <b class=darkblue>$apparel ~ $kode_apparel</b></li>
</ul>
<div class=mt3>PIC:</div>
<form method=post>
  <input type=hidden name=kode_unik value='$kode_unik'>
  <input type=hidden name=kode_brand value='$kode_brand'>
  <input type=hidden name=kode_gender value='$kode_gender'>
  <input type=hidden name=kode_apparel value='$kode_apparel'>
  $radio_pic
  <button class='btn btn-primary mt2' name=btn_assign_pic>Assign PIC-CD</button>
</form>
";
?>

