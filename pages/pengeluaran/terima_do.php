<style>
  .help{font-size:12px; color:white; background:blue; padding: 10px; border-radius: 5px; margin: 10px 0}
  .help ul, .blok_kode ul{margin:0;padding:0 0 0 15px}
</style>
<div class="pagetitle">
  <h1>Buat DO</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?pengeluaran">Pengeluaran</a></li>
      <li class="breadcrumb-item active">Buat DO</li>
    </ol>
  </nav>
</div>



<?php
# ======================================================
# PROCESSORS
# ======================================================
if(isset($_POST['btn_create_do'])){
  unset($_POST['btn_create_do']);

  echo '<pre>';
  var_dump($_POST);
  echo '</pre>';

  $keys = '';
  $values = '';
  $pairs = '';
  foreach ($_POST as $key => $value) {
    $value = clean_sql($value);
    $value = ($value==''||$value=='-') ? 'NULL' : "'$value'";

    if($keys!='') $keys.=',';
    if($values!='') $values.=',';
    if($pairs!='') $pairs.=',';
    $keys .= $key;
    $values .= $value;
    if($key!='kode_do') $pairs .= "$key=$value";
  }

  $s = "INSERT INTO tb_do ($keys) VALUES ($values) ON DUPLICATE KEY UPDATE $pairs";
  // echo $s;
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  jsurl("?pengeluaran&p=terima_do&kode_do=$_POST[kode_do]");
  exit;

}







# ======================================================
# GET DO DATA
# ======================================================
$kode_do = $_GET['kode_do'] ?? '';
$kode_delivery = '';
$kode_artikel = '';
$btn_caption = 'Create and Next';
$id_kategori = '';
$kode_do_type = 'text';
$kode_do_div = '';
$update_trigger = '';
if($kode_do!=''){
  $s = "SELECT * FROM tb_do WHERE kode_do='$kode_do'";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0) die(div_alert('danger',"Data DO tidak ditemukan. | <a href='?pengeluaran&p=terima_do'>Terima DO Baru</a>"));
  $d = mysqli_fetch_assoc($q);
  $update_trigger = 'update_trigger';
  $kode_do_type = 'hidden';
  $kode_do_div = "<div class='tebal mb2'>$d[kode_do]</div>";
  $kode_delivery = $d['kode_delivery'];
  $kode_artikel = $d['kode_artikel'];
  $id_kategori = $d['id_kategori'];
  $btn_caption = 'Update DO';
}

$radio_kategori_1_checked = $id_kategori==1 ? 'checked' : '';
$radio_kategori_2_checked = $id_kategori==2 ? 'checked' : '';



# ======================================================
# START
# ======================================================
$s = "SELECT * FROM tb_brand";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$brands = '';
$brand_help = '';
$li = '';
$help = '';
while($d=mysqli_fetch_assoc($q)){
  $kode = strtoupper($d['kode']);
  $brand_help .= "<li>$kode : $d[brand]</li>";
  $brands .= "$kode,";
  $li .= "<li class='brand hideit' id=brand__$kode><span class='abu '>Brand:</span> $d[brand]</li>";
}
echo "<span class=hideit id=brands>$brands</span>";
$help .= "<div class=flexy><div class='help hideit' id=brand_help><ul>$brand_help</ul></div></div>";

$li.= "
<li class='div_kode hideit' id='div_kode_urut'><span class='abu '>Kode Urut:</span> <span id=kode_urut></span></li>
<li class='div_kode hideit' id='div_kode_bulan'><span class='abu '>Bulan Proyeksi:</span> <span id=kode_bulan></span></li>
<li class='div_kode hideit' id='div_kode_tahun'><span class='abu '>Tahun Proyeksi:</span> <span id=kode_tahun></span></li>
";

$s = "SELECT * FROM tb_gender";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$genders = '';
$gender_help = '';
while($d=mysqli_fetch_assoc($q)){
  $kode = strtoupper($d['kode']);
  $gender_help .= "<li>$kode : $d[gender]</li>";
  $genders .= "$kode,";
  $li .= "<li class='gender hideit' id=gender__$kode><span class='abu '>Gender:</span> $d[gender]</li>";
}
echo "<span class=hideit id=genders>$genders</span>";
$help .= "<div class=flexy><div class='help hideit' id=gender_help><ul>$gender_help</ul></div></div>";

$s = "SELECT * FROM tb_apparel";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$apparels = '';
$apparel_help = '';
while($d=mysqli_fetch_assoc($q)){
  $kode = strtoupper($d['kode']);
  $apparel_help .= "<li>$kode : $d[apparel]</li>";
  $apparels .= "$kode,";
  $li .= "<li class='apparel hideit' id=apparel__$kode><span class='abu '>Apparel:</span> $d[apparel]</li>";
}
echo "<span class=hideit id=apparels>$apparels</span>";
$help .= "<div class=flexy><div class='help hideit' id=apparel_help><ul>$apparel_help</ul></div></div>";

$today = $d_do['tanggal_delivery'] ?? date('Y-m-d');

echo "
  <form method=post>
    <table class=tablez>
      <tr>
        <td width=150px>Nomor DO</td>
        <td>
          <input type='$kode_do_type' class='mb2 form-control' id=kode_do name=kode_do minlength=9 maxlength=9 placeholder='K0112312K' required value='$kode_do'>
          $kode_do_div
        </td>
      </tr>
      <tr>
        <td>Nomor Delivery</td>
        <td>
          <input type='text' class='mb2 form-control $update_trigger' id=kode_delivery name=kode_delivery minlength=9 maxlength=9 placeholder='191401705' required value='$kode_delivery'>
        </td>
      </tr>
      <tr>
        <td valign=top class=pt2>Kode Artikel</td>
        <td>
          <input type='text' class='mb2 form-control $update_trigger consolas f24' id=kode_artikel name=kode_artikel style='letter-spacing:5px;' maxlength=9 required value='$kode_artikel' placeholder='K01122312'>
          <div class='blok_kode kecil mb2'><ul>$li</ul></div>
          $help
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
          <div class=flexy>
            <div><label><input type='radio' class='$update_trigger' id=id_kategori name=id_kategori value='1' required $radio_kategori_1_checked> Aksesoris</label></div>
            <div><label><input type='radio' class='$update_trigger' id=id_kategori name=id_kategori value='2' required $radio_kategori_2_checked> Fabric</label></div>
          </div>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>
          <button class='btn btn-sm btn-primary mt2' name=btn_create_do id=btn_create_do disabled>$btn_caption</button>
        </td>
      </tr>
    </table>
  </form>
";

# ======================================================
# INCLUDE PICKING LISTS
# ======================================================
if($btn_caption=='Update DO') include 'picking_list.php';

?>


<script>
  $(function(){
    let brands = $('#brands').text().split(',');
    let genders = $('#genders').text().split(',');
    let apparels = $('#apparels').text().split(',');

    $('.update_trigger').change(function(){
      $('#btn_create_do').prop('disabled',0);
    });
    $('#kode_artikel').keyup(function(){
      let val = $(this).val().trim().toUpperCase();
      $(this).val(val);

      let kode_brand = val.substring(0,1);
      let kode_urut = val.substring(1,3);
      let kode_bulan = val.substring(3,5);
      let kode_tahun = val.substring(5,7);
      let kode_gender = val.substring(7,8);
      let kode_apparel = val.substring(8,9);

      $('.help').hide();
      $('.brand').hide();
      $('.gender').hide();
      $('.apparel').hide();
      $('.div_kode').hide();
      $('#btn_create_do').prop('disabled',1);

      if(kode_brand){
        if(brands.includes(kode_brand)){
          $('#brand__'+kode_brand).show();
          if(kode_urut>=1 && kode_urut<=99){
            $('#kode_urut').text(kode_urut);
            $('#div_kode_urut').show();
            if(kode_bulan>=1 && kode_bulan<=12){
              $('#kode_bulan').text(kode_bulan);
              $('#div_kode_bulan').show();
              if(kode_tahun >=23 && kode_tahun <=24){
                $('#kode_tahun').text(kode_tahun);
                $('#div_kode_tahun').show();
                if(kode_gender){
                  if(genders.includes(kode_gender)){
                    $('#gender__'+kode_gender).show();
                    if(kode_apparel){
                      if(apparels.includes(kode_apparel)){
                        $('#apparel__'+kode_apparel).show();
                        $('#btn_create_do').prop('disabled',0);
                      }else{
                        $('#apparel_help').show();
                      }
                    }
                  }else{
                    $('#gender_help').show();
                  }
                }
              }else{
                if(kode_tahun){
                  $('#kode_tahun').html('<span class=red>Proyeksi tahun antara 23 s.d 24</span>');
                  $('#div_kode_tahun').show();
                }
              }
            }else{
              if(kode_bulan){
                $('#kode_bulan').html('<span class=red>Proyeksi bulan antara 1 s.d 12</span>');
                $('#div_kode_bulan').show();
              }
            }
          }else{
            if(kode_urut){
              $('#kode_urut').html('<span class=red>Kode urut antara 1 s.d 99</span>');
              $('#div_kode_urut').show();

            }
          } 
        }else{
          $('#brand_help').show();
        }
      }
    }) // end kode_artikel keyup
  })
</script>

