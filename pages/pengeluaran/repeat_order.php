<?php
set_title('Repeat Order');
$kode_do_awal = $_GET['kode_do_awal'] ?? die(erid('kode_do_awal'));
$id_kategori = $_GET['id_kategori'] ?? die(erid('id_kategori'));
$cat = $id_kategori==1 ? 'aks' : 'fab';


$page_title = "
  <div class='pagetitle'>
    <h1>Repeat Order</h1>
    <nav>
      <ol class='breadcrumb'>
        <li class='breadcrumb-item'><a href='?pengeluaran'>Pengeluaran</a></li>
        <li class='breadcrumb-item'><a href='?pengeluaran&p=data_do'>Data DO</a></li>
        <li class='breadcrumb-item'><a href='?pengeluaran&p=buat_do&kode_do=$kode_do_awal&cat=$cat'>DO Awal</a></li>
        <li class='breadcrumb-item active'>Repeat Order</li>
      </ol>
    </nav>
  </div>
";

# ======================================================
# PROCESSORS
# ======================================================
if(isset($_POST['btn_repeat_order'])){
  unset($_POST['btn_repeat_order']);
  echo 'Processing Repeat Order ... <hr>';
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';

  $keys = '';
  $values = '';

  // kode_artikel == kode_do_awal
  $_POST['kode_artikel'] = $_POST['kode_do_awal'];
  unset($_POST['kode_do_awal']);

  //bulan proyeksi
  $bulan_proyeksi = $_POST['bulan_proyeksi'] < 10 ? "0$_POST[bulan_proyeksi]" : "$_POST[bulan_proyeksi]";
  unset($_POST['bulan_proyeksi']);

  //kode_do == kode repeat order
  $_POST['kode_do'] = "RO-$_POST[kode_artikel]-$bulan_proyeksi";

  // kode_delivery auto = yymmdd-count(data, 4 digit)
  $s = "SELECT 1 FROM tb_do";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $count_do = mysqli_num_rows($q);
  $count_do = "000$count_do";
  $count_do = substr($count_do,strlen($count_do)-4,4);
  $_POST['kode_delivery'] = date('ymd')."-$count_do";



  foreach ($_POST as $key => $value) {
    $value = clean_sql($value);
    $value = ($value==''||$value=='-') ? 'NULL' : "'$value'";

    if($keys!='') $keys.=',';
    if($values!='') $values.=',';
    $keys .= $key;
    $values .= $value;
  }


  // duplicate check
  $s = "SELECT 1 FROM tb_do WHERE kode_do='$_POST[kode_do]' AND id_kategori=$_POST[id_kategori]";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $cat = $_POST['id_kategori']==1 ? 'aks' : 'fab';
  $kategori = $_POST['id_kategori']==1 ? 'Aksesoris' : 'Fabric';

  //duplicate handler
  if(mysqli_num_rows($q)){
    die(div_alert('danger',"Kode DO <u>$_POST[kode_do]</u> untuk <u>$kategori</u> sudah ada. | <a href='?pengeluaran&p=buat_do&kode_do=$_POST[kode_do]&cat=$cat'>Lihat Item</a>"));
  }


  // length validate of id_do, must 10 || 16
  echo '<hr>'.strlen($_POST['kode_do']);

  if(strlen($_POST['kode_do'])==15){
    //repeat order
    $keys.= ',is_repeat';
    $values.= ',1';

  }else{
    die('Kode Repeat Order harus 15 digit <hr>Format Kode Repeat Order: RO-[9 digit kode DO normal]-[2 digit bulan proyeksi]');
  } 

  $s = "INSERT INTO tb_do ($keys) VALUES ($values)";
  // echo $s;
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  jsurl("?pengeluaran&p=buat_do&kode_do=$_POST[kode_do]&cat=$cat");
  exit;

}




$today = $d_do['tanggal_delivery'] ?? date('Y-m-d');
$kategori = $id_kategori==1 ? 'Aksesoris' : 'Fabric';

echo "
  $page_title
  <form method=post id=form_do>
    <input type=hidden name=kode_do_awal value='$kode_do_awal'>
    <input type=hidden name=id_kategori value='$id_kategori'>
    <table class=tablez>
      <tr class=''>
        <td width=150px valign=top class=pt3>Kode Repeat Order</td>
        <td>
          <div class=flexy>
            <div>
              <input disabled class='mb2 form-control consolas f24 kanan' value='RO-$kode_do_awal' style='width: 240px;letter-spacing:5px;' minlength=15 maxlength=15 placeholder='RO-$kode_do_awal-XX'>
            </div>
            <div class='f24 pt2'>-</div>
            <div>
              <input type=number required class='mb2 form-control consolas f24' value='' style='width: 90px;letter-spacing:5px;' min=1 max=12 placeholder='MM' name=bulan_proyeksi>
            </div>
          </div>
          <div class='kecil biru mb4'>Silahkan masukan bulan proyeksi!</div>
        </td>
      </tr>
      <tr>
        <td>Nomor Delivery</td>
        <td>
          <input disabled class='mb2 form-control' value='auto'>
        </td>
      </tr>
      <tr>
        <td>Tanggal Delivery</td>
        <td>
          <input type='date' class='mb2 form-control' id=tanggal_delivery name=tanggal_delivery value='$today' required>
        </td>
      </tr>
      <tr>
        <td>OTP / Jenis Bahan</td>
        <td>
          <input disabled class='mb2 form-control' value='$kategori'>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>
          <button class='btn btn-primary mt2' name=btn_repeat_order id=btn_repeat_order >Create Repeat Order</button>
        </td>
      </tr>
    </table>
  </form>
";
