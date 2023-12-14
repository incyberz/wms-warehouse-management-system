<?php
$input_select = "<select class='form-control form-control-sm'><option>--pilih--</option></select>";
$input_text = "<input type=text class='form-control form-control-sm' />";
$input_date = "<input type=date class='form-control form-control-sm' />";
$input_number = "<input type=number class='form-control form-control-sm' />";

$new_lokasi = $input_select;
$new_po = $input_text;
$new_id = $input_text;
$new_tgl = $input_date;
$new_qty = $input_number;
$new_proyeksi = $input_text;




$s = "SELECT * FROM tb_lokasi ";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$opt = '';
$brands = '';
while($d=mysqli_fetch_assoc($q)){
  $kode=$d['kode'];
  $opt.= "<option>$d[kode]</option>";
  $kode2 = str_replace('.','_',$kode);
  $kode2 = str_replace(' ','_',$kode2);
  $brand = $d['brand'] ?? '<span class=miring>no-brand</span>';
  $brands.="<div class='brand kecil abu hideit' id=brand__$kode2>$brand</div>";
}

$select_lokasi = "
<select class='form-control mb1' name=kode_lokasi id=kode_lokasi>
  $opt
</select>
";

$new_po = "
<input type=text class='form-control' name=kode_po id=kode_po maxlength=9 />
";




echo "
  <tr>
    <td>
      $select_lokasi
      $brands
    </td>
    <td>$new_po</td>
    <td>$new_id</td>
    <td>$new_tgl</td>
    <td>$new_qty</td>
    <td>$new_proyeksi</td>
  </tr>
";

?><script>
  $(function(){
    $('#kode_lokasi').change(function(){
      let kode = $(this).val();
      let kode2 = kode.replaceAll('.', '_');
      kode2 = kode2.replaceAll(' ', '_');

      $('.brand').slideUp();
      $('#brand__'+kode2).slideDown();

    });

    $('#kode_po').keyup(function(){
      
      let kode = $(this).val();
      let kode2 = kode.replaceAll('.', '_');
      kode2 = kode2.replaceAll(' ', '_');

      $('.brand').slideUp();
      $('#brand__'+kode2).slideDown();

    })
  })
</script>