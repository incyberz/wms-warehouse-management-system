<style>
  .help {
    font-size: 12px;
    color: white;
    background: blue;
    padding: 10px;
    border-radius: 5px;
    margin: 10px 0
  }

  .help ul,
  .blok_kode ul {
    margin: 0;
    padding: 0 0 0 15px
  }
</style>
<?php
set_title('Buat DO');

$kode_do = $_GET['kode_do'] ?? '';
$bread_terima_do = $kode_do == '' ? '' : '<li class="breadcrumb-item"><a href="?pengeluaran&p=buat_do">Buat DO Baru</a></li>';

$page_title = "
  <div class='pagetitle'>
    <h1>Buat DO</h1>
    <nav>
      <ol class='breadcrumb'>
        <li class='breadcrumb-item'><a href='?pengeluaran'>Pengeluaran</a></li>
        <li class='breadcrumb-item'><a href='?pengeluaran&p=data_do'>Data DO</a></li>
        $bread_terima_do
        <li class='breadcrumb-item active'>Buat DO</li>
      </ol>
    </nav>
  </div>
";

# ======================================================
# PIC only
# ======================================================
if ($id_role != 7 and !$kode_do) {
  echo $page_title;
  echo div_alert('danger', " Maaf, hanya login PIC yang dapat menggunakan fitur membuat DO.");
  exit;
}


# ======================================================
# PROCESSORS
# ======================================================
if (isset($_POST['btn_create_do'])) {
  unset($_POST['btn_create_do']);
  echo 'Processing Delivery Order ... <hr>';

  $keys = '';
  $values = '';

  // kode_do == kode_artikel
  $_POST['kode_do'] = $_POST['kode_artikel'];

  // kode_delivery auto = yymmdd-count(data, 4 digit)
  $s = "SELECT 1 FROM tb_do";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $count_do = mysqli_num_rows($q);
  $count_do = "000$count_do";
  $count_do = substr($count_do, strlen($count_do) - 4, 4);
  $_POST['kode_delivery'] = date('ymd') . "-$count_do";



  foreach ($_POST as $key => $value) {
    $value = clean_sql($value);
    $value = ($value == '' || $value == '-') ? 'NULL' : "'$value'";

    if ($keys != '') $keys .= ',';
    if ($values != '') $values .= ',';
    $keys .= $key;
    $values .= $value;
  }


  // duplicate check
  $s = "SELECT 1 FROM tb_do WHERE kode_do='$_POST[kode_do]' AND id_kategori=$_POST[id_kategori]";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    //duplicate handler

    $kategori = $_POST['id_kategori'] == 1 ? 'Aksesoris' : 'Fabric';

    die(div_alert('danger', "Kode DO <u>$_POST[kode_do]</u> untuk <u>$kategori</u> sudah ada. Jika repeat order silahkan  <a href='?pengeluaran&p=repeat_order&kode_do_awal=$_POST[kode_do]&id_kategori=$_POST[id_kategori]'>Buat Repeat Order</a>"));
  }


  // length validate of kode_do, must 9 || 15
  if (strlen($_POST['kode_do']) == 9) {
    //order pertama kali ... do nothing, passed

  } else if (strlen($_POST['kode_do']) == 15) {
    //repeat order
    $keys .= ',is_repeat';
    $values .= ',1';
  } else {
    die('Kode DO harus 9 digit atau 15 digit (Repeat Order)<hr>Format Kode Repeat Order: RO-[9 digit kode DO normal]-[2 digit bulan proyeksi]');
  }

  $s = "INSERT INTO tb_do ($keys) VALUES ($values)";
  // echo $s;
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $cat = $_POST['id_kategori'] == 1 ? 'aks' : 'fab';
  jsurl("?pengeluaran&p=buat_do&kode_do=$_POST[kode_do]&cat=$cat");
  exit;
}







# ======================================================
# GET DO DATA
# ======================================================
$kode_delivery = '';
$kode_artikel = '';
$btn_caption = 'Create and Next';
$kode_do_tr_hide = '';
$kode_do_div = '';
$update_trigger = '';
$id_kategori = '';
$jumlah_item = 0;
$pic_info = '';
$form_do_hide = '';
if ($kode_do != '') {

  $cat = $_GET['cat'] ?? 'aks';
  $id_kategori = $cat == 'aks' ? 1 : 2;
  $jenis_barang = $cat == 'aks' ? 'Aksesoris' : 'Fabric';
  $jenis_barang_lainnya = $cat == 'aks' ? 'Fabric' : 'Aksesoris';
  $cat_lainnya = $cat == 'aks' ? 'fab' : 'aks';

  echo "<span class=hideit id=id_kategori>$id_kategori</span>";

  $Manage = $id_role == 7 ? 'Manage' : '';
  $Allocate = $id_role == 3 ? 'Allocate' : '';
  $judul = "$Manage$Allocate Picking List $jenis_barang";
  $retur_do = $_GET['retur_do'] ?? '';
  if ($retur_do) $judul = 'Retur DO';
  set_title($judul);
  $page_title = "
    <div class='pagetitle'>
      <h1>$judul</h1>
      <nav>
        <ol class='breadcrumb'>
          <li class='breadcrumb-item'><a href='?pengeluaran'>Pengeluaran</a></li>
          <li class='breadcrumb-item'><a href='?pengeluaran&p=data_do'>Data DO</a></li>
          $bread_terima_do
          <li class='breadcrumb-item'><a href='?pengeluaran&p=buat_do&kode_do=$kode_do&cat=$cat_lainnya'>PL $jenis_barang_lainnya</a></li>
          <li class='breadcrumb-item active'>PL $jenis_barang</li>
        </ol>
      </nav>
    </div>
  ";





  $s = "SELECT a.*, a.id as id_do,
  (SELECT COUNT(1) FROM tb_pick WHERE id_do=a.id) jumlah_item 
  FROM tb_do a 
  WHERE a.kode_do='$kode_do' 
  AND a.id_kategori=$id_kategori 
  ";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q) == 0) die(div_alert('danger', "Data DO $kode_do untuk $jenis_barang tidak ditemukan. | <a href='?pengeluaran&p=buat_do'>Buat DO Baru</a>"));
  $d = mysqli_fetch_assoc($q);
  $update_trigger = 'update_trigger';
  $kode_do_tr_hide = 'hideit';
  $form_do_hide = 'hideit';

  $detail = $id_role != 7 ? '' : "<div><span class=btn_aksi id=form_do__toggle>$img_detail</span></div>";

  $kode_do_div = "
    <div class=flexy>
      <div class='tebal mb2'>Nomor DO : $d[kode_do] </div>
      <div>|</div>
      <div class='tebal mb2'>Artikel : $d[kode_artikel] </div>
      $detail
    </div>
  ";
  $kode_delivery = $d['kode_delivery'];
  $kode_artikel = $d['kode_artikel'];
  $id_kategori = $d['id_kategori'];
  $jumlah_item = $d['jumlah_item'];
  $btn_caption = 'Update DO';

  $id_do = $d['id_do'];
  echo "<span class=hideit id=id_do>$id_do</span>";

  // 012345678
  $kode_brand = substr($kode_artikel, 0, 1);
  $kode_gender = substr($kode_artikel, 7, 1);
  $kode_apparel = substr($kode_artikel, 8, 1);
  $kode_unik = "$kode_brand$kode_gender$kode_apparel";

  $s = "SELECT *, e.nama as nama_pic 
  FROM tb_assign_pic a 
  JOIN tb_brand b ON a.kode_brand=b.kode 
  JOIN tb_gender c ON a.kode_gender=c.kode 
  JOIN tb_apparel d ON a.kode_apparel=d.kode 
  JOIN tb_pic e ON a.kode_pic=e.kode 
  WHERE a.kode_unik='$kode_unik'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  if (mysqli_num_rows($q)) {
    $d = mysqli_fetch_assoc($q);
    $brand = $d['brand'];
    $gender = $d['gender'];
    $apparel = $d['apparel'];
    $nama_pic = $d['nama_pic'];
    $pic_info = "
      <ul>
        <li>Brand: $brand</li>
        <li>Gender: $gender</li>
        <li>Apparel: $apparel</li>
        <li>PIC: $nama_pic</li>
      </ul>
    ";
  } else {
    $pic_info = "<div class='mb2 pic_only'>Assign PIC-CD: $unset | <a href='?assign_pic&kode_artikel=$kode_artikel'>Assign PIC-CD</a></div>";
  }
}

$radio_kategori_1_checked = $id_kategori == 1 ? 'checked' : '';
$radio_kategori_2_checked = $id_kategori == 2 ? 'checked' : '';



# ======================================================
# START
# ======================================================
$s = "SELECT * FROM tb_brand";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$brands = '';
$brand_help = '';
$li = '';
$help = '';
while ($d = mysqli_fetch_assoc($q)) {
  $kode = strtoupper($d['kode']);
  $brand_help .= "<li>$kode : $d[brand]</li>";
  $brands .= "$kode,";
  $li .= "<li class='brand hideit' id=brand__$kode><span class='abu '>Brand:</span> $d[brand]</li>";
}
$debug .= "<br>brands: <span id=brands>$brands</span>";
$help .= "<div class=flexy><div class='help hideit' id=brand_help><ul>$brand_help</ul></div></div>";

$li .= "
<li class='div_kode hideit' id='div_kode_urut'><span class='abu '>Kode Urut:</span> <span id=kode_urut></span></li>
<li class='div_kode hideit' id='div_kode_bulan'><span class='abu '>Bulan Proyeksi:</span> <span id=kode_bulan></span></li>
<li class='div_kode hideit' id='div_kode_tahun'><span class='abu '>Tahun Proyeksi:</span> <span id=kode_tahun></span></li>
";

$s = "SELECT * FROM tb_gender";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$genders = '';
$gender_help = '';
while ($d = mysqli_fetch_assoc($q)) {
  $kode = strtoupper($d['kode']);
  $gender_help .= "<li>$kode : $d[gender]</li>";
  $genders .= "$kode,";
  $li .= "<li class='gender hideit' id=gender__$kode><span class='abu '>Gender:</span> $d[gender]</li>";
}
$debug .= "<br>genders: <span id=genders>$genders</span>";
$help .= "<div class=flexy><div class='help hideit' id=gender_help><ul>$gender_help</ul></div></div>";

$s = "SELECT * FROM tb_apparel";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$apparels = '';
$apparel_help = '';
while ($d = mysqli_fetch_assoc($q)) {
  $kode = strtoupper($d['kode']);
  $apparel_help .= "<li>$kode : $d[apparel]</li>";
  $apparels .= "$kode,";
  $li .= "<li class='apparel hideit' id=apparel__$kode><span class='abu '>Apparel:</span> $d[apparel]</li>";
}
$debug .= "<br>apparels: <span id=apparels>$apparels</span>";
$help .= "<div class=flexy><div class='help hideit' id=apparel_help><ul>$apparel_help</ul></div></div>";

$today = $d_do['tanggal_delivery'] ?? date('Y-m-d');

































# =========================================================
# FINAL ECHO
# =========================================================
echo "
  $page_title
  $kode_do_div
  $pic_info
  <form method=post id=form_do class='$form_do_hide wadah'>
    <div class=sub_form>Form Buat DO</div>
    <table>
      <tr>
        <td valign=top class=pt2>Kode Artikel</td>
        <td>
          <input type='text' class='mb2 form-control $update_trigger consolas f24' id=kode_artikel name=kode_artikel style='letter-spacing:5px;' maxlength=9 required value='$kode_artikel' placeholder='.........'>
          <div class='blok_kode kecil mb2'><ul>$li</ul></div>
          $help
        </td>
      </tr>
      <tr class='$kode_do_tr_hide'>
        <td width=150px>Nomor DO</td>
        <td>
          <input disabled type='text' class='mb2 form-control' id=kode_do name=kode_do minlength=9 maxlength=9 placeholder='auto' required value='$kode_do'>
          
        </td>
      </tr>
      <tr class=hideit>
        <td>Nomor Delivery</td>
        <td>
          <input disabled type='text' class='mb2 form-control $update_trigger' id=kode_delivery name=kode_delivery minlength=9 maxlength=9 placeholder='auto' value='$kode_delivery'>
        </td>
      </tr>
      <tr>
        <td>Tanggal Delivery</td>
        <td>
          <input type='date' class='mb2 form-control $update_trigger' id=tanggal_delivery name=tanggal_delivery value='$today' required>
        </td>
      </tr>
      <tr>
        <td>OTP</td>
        <td>
          <div class='flexy mt2 mb4'>
            <div><label><input type='radio' class='$update_trigger' id=id_kategori name=id_kategori value='1' required $radio_kategori_1_checked> Aksesoris</label></div>
            <div><label><input type='radio' class='$update_trigger' id=id_kategori name=id_kategori value='2' required $radio_kategori_2_checked> Fabric</label></div>
          </div>
        </td>
      </tr>
      <tr>
        <td>QTY Apparel</td>
        <td>
          <input required type=number min=0 max=9999 placeholder='QTY Apparel' name=qty_apparel class='form-control mb2'>
        </td>
      </tr>
      <tr>
        <td>Jenis Permintaan</td>
        <td>
          <select name=id_permintaan class='form-control mb2'>
            <option value=1>LPKP (reguler)</option>
            <option value=2>BAKB (Berita Acara Kehilangan Barang)</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>
          <button class='btn btn-primary mt2' name=btn_create_do id=btn_create_do disabled>$btn_caption</button>
        </td>
      </tr>
    </table>
  </form>
";

# ======================================================
# INCLUDE PICKING LISTS OR RETUR
# ======================================================
// btn_caption as step process
if ($btn_caption == 'Update DO') {
  $get_retur_do = $_GET['retur_do'] ?? '';
  if ($get_retur_do) {
    if ($sebagai == 'WH') {
      include 'retur_do2.php';
    } else {
      die('Yang berhak retur DO adalah WH');
    }
  } else {
    include 'picking_list.php';
  }
}











































?>
<script>
  $(function() {
    let brands = $('#brands').text().split(',');
    let genders = $('#genders').text().split(',');
    let apparels = $('#apparels').text().split(',');

    $('.update_trigger').change(function() {
      $('#btn_create_do').prop('disabled', 0);
    });
    $('#kode_artikel').keyup(function() {
      let val = $(this).val().trim().toUpperCase();
      $(this).val(val);

      let kode_brand = val.substring(0, 1);
      let kode_urut = val.substring(1, 3);
      let kode_bulan = val.substring(3, 5);
      let kode_tahun = val.substring(5, 7);
      let kode_gender = val.substring(7, 8);
      let kode_apparel = val.substring(8, 9);

      $('.help').hide();
      $('.brand').hide();
      $('.gender').hide();
      $('.apparel').hide();
      $('.div_kode').hide();
      $('#btn_create_do').prop('disabled', 1);

      if (kode_brand) {
        if (brands.includes(kode_brand)) {
          $('#brand__' + kode_brand).show();
          if (kode_urut >= 1 && kode_urut <= 99) {
            $('#kode_urut').text(kode_urut);
            $('#div_kode_urut').show();
            if (kode_bulan >= 1 && kode_bulan <= 12) {
              $('#kode_bulan').text(kode_bulan);
              $('#div_kode_bulan').show();
              if (kode_tahun >= 21 && kode_tahun <= 24) {
                $('#kode_tahun').text(kode_tahun);
                $('#div_kode_tahun').show();
                if (kode_gender) {
                  if (genders.includes(kode_gender)) {
                    $('#gender__' + kode_gender).show();
                    if (kode_apparel) {
                      if (apparels.includes(kode_apparel)) {
                        $('#apparel__' + kode_apparel).show();
                        $('#btn_create_do').prop('disabled', 0);
                      } else {
                        $('#apparel_help').show();
                      }
                    }
                  } else {
                    $('#gender_help').show();
                  }
                }
              } else {
                if (kode_tahun) {
                  $('#kode_tahun').html('<span class=red>Proyeksi tahun antara 21 s.d 24</span>');
                  $('#div_kode_tahun').show();
                }
              }
            } else {
              if (kode_bulan) {
                $('#kode_bulan').html('<span class=red>Proyeksi bulan antara 1 s.d 12</span>');
                $('#div_kode_bulan').show();
              }
            }
          } else {
            if (kode_urut) {
              $('#kode_urut').html('<span class=red>Kode urut antara 1 s.d 99</span>');
              $('#div_kode_urut').show();

            }
          }
        } else {
          $('#brand_help').show();
        }
      }
    }) // end kode_artikel keyup
  })
</script>