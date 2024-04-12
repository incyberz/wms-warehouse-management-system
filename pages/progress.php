<style>
  .sub_number {
    width: 30px;
    text-align: center
  }

  .td_status_subfitur {
    width: 110px
  }

  .btn_sm {
    padding: 0 4px;
    font-size: 10px;
  }

  .btn_sedang_dikerjakan {
    padding: 3px 8px;
  }

  .sedang_dikerjakan {
    border: solid 1px blue;
    background: #faf
  }
</style>
<?php
$judul = "Progress dan Request Fitur";
set_title($judul);

echo "<h2 class='mt4 darkblue mb4'>$judul</h2>";
$img_arti[1] = '<img src="assets/img/icons/check_brown.png" height=20px>';
$img_arti[2] = '<img src="assets/img/icons/check_pink.png" height=20px>';
$img_arti[3] = '<img src="assets/img/icons/check_blue.png" height=20px>';
$img_arti[4] = "$img_check";
$img_arti[5] = "$img_check $img_check";

$img_loading = "<img src='assets/img/gifs/loading.gif' height=25px>";

if (isset($_POST['btn_add_fitur'])) {
  $nama_fitur = $_POST['new_fitur'] ?? die(erid('new_fitur'));
  $nama_fitur = strtoupper($nama_fitur);
  $s = "INSERT INTO tb_fitur (nama, request_by,sub_divisi) VALUES ('$nama_fitur',$id_user,'NEW REQUEST')";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
}
if (isset($_POST['btn_add_subfitur'])) {
  $id_fitur = $_POST['btn_add_subfitur'] ?? die(erid('btn_add_subfitur'));
  $nama_subfitur = $_POST['new_subfitur'] ?? die(erid('new_subfitur'));
  $keterangan = $_POST['keterangan'] ?? die(erid('keterangan'));

  $nama_subfitur = strtoupper($nama_subfitur);

  $s = "INSERT INTO tb_subfitur 
  (id_fitur,nama,request_by,keterangan) VALUES 
  ($id_fitur,'$nama_subfitur',$id_user,'$keterangan')";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
}


if (isset($_POST['btn_update_fitur'])) {
  $id = $_POST['btn_update_fitur'];
  unset($_POST['btn_update_fitur']);

  $pairs = '__';
  foreach ($_POST as $key => $value) {
    $value = $value ? "'$value'" : 'NULL';
    $value = $key == 'nama' ? strtoupper($value) : $value;
    $pairs .= ",$key = $value";
  }
  $pairs = str_replace('__,', '', $pairs);

  $s = "UPDATE tb_fitur SET $pairs WHERE id=$id";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl();
}

if (isset($_POST['btn_set_status'])) {
  // echo div_alert('danger','Maaf, saat ini hanya DEVELOPER yang bisa mengubah status development.');
  $arr = explode('__', $_POST['btn_set_status']);
  $status = $arr[0];
  $id_subfitur = $arr[1];

  $s = "SELECT id_fitur FROM tb_subfitur WHERE id=$id_subfitur";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $d = mysqli_fetch_assoc($q);
  $id_fitur = $d['id_fitur'];

  $s = "UPDATE tb_subfitur SET status=$status WHERE id=$id_subfitur";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  jsurl("?progress&id_fitur=$id_fitur");
}

if (isset($_POST['btn_sedang_dikerjakan'])) {
  echo div_alert('danger', 'Maaf, hanya DEVELOPER yang bisa mengubah fitur mana yang sedang dikerjakan saat ini.');
}




























$s = "DESCRIBE tb_fitur";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$colField = [];
$colType = [];
$colLength = [];
$colNull = [];
$colKey = [];
$colDefault = [];
while ($d = mysqli_fetch_assoc($q)) {
  if (
    $d['Field'] == 'id'
    || $d['Field'] == 'date_created'
  ) continue;
  array_push($colField, $d['Field']);
  array_push($colNull, $d['Null']);
  array_push($colKey, $d['Key']);
  array_push($colDefault, $d['Default']);

  if ($d['Type'] == 'timestamp') {
    $Type = 'timestamp';
    $Length = 19;
  } else {
    $pos = strpos($d['Type'], '(');
    $pos2 = strpos($d['Type'], ')');
    $len = strlen($d['Type']);
    $len_type = $len - ($len - $pos);
    $len_length = $len - ($len - $pos2) - $len_type - 1;

    $Type = substr($d['Type'], 0, $len_type);
    $Length = intval(substr($d['Type'], $pos + 1, $len_length));
  }

  array_push($colType, $Type);
  array_push($colLength, $Length);
}


$s = "DESCRIBE tb_subfitur";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$subcolField = [];
$subcolType = [];
$subcolLength = [];
$subcolNull = [];
$subcolKey = [];
$subcolDefault = [];
while ($d = mysqli_fetch_assoc($q)) {
  if (
    $d['Field'] == 'id'
    || $d['Field'] == 'date_created'
  ) continue;
  array_push($subcolField, $d['Field']);
  array_push($subcolNull, $d['Null']);
  array_push($subcolKey, $d['Key']);
  array_push($subcolDefault, $d['Default']);

  if ($d['Type'] == 'timestamp') {
    $Type = 'timestamp';
    $Length = 19;
  } else {
    $pos = strpos($d['Type'], '(');
    $pos2 = strpos($d['Type'], ')');
    $len = strlen($d['Type']);
    $len_type = $len - ($len - $pos);
    $len_length = $len - ($len - $pos2) - $len_type - 1;

    $Type = substr($d['Type'], 0, $len_type);
    $Length = intval(substr($d['Type'], $pos + 1, $len_length));
  }

  array_push($subcolType, $Type);
  array_push($subcolLength, $Length);
}

// echo '<pre>';
// var_dump($colDefault);
// echo '</pre>';






















$get_id_fitur = $_GET['id_fitur'] ?? '';
$get_no = $_GET['no'] ?? '';
$sql_id_fitur = $get_id_fitur ? "a.id=$get_id_fitur" : '1';

# ======================================================
# NAV
# ======================================================
$s = "SELECT status FROM tb_subfitur";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$total_subfitur = mysqli_num_rows($q);

$s = "SELECT a.*,b.nama as nama_fitur,b.sub_divisi 
FROM tb_subfitur a 
JOIN tb_fitur b ON a.id_fitur=b.id 
WHERE a.sedang_dikerjakan=1";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$d_sedang_dikerjakan = mysqli_fetch_assoc($q);

$s = "SELECT a.*,
(SELECT count(1) FROM tb_subfitur WHERE status=a.status) count_status 
FROM tb_status_subfitur a";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
while ($d = mysqli_fetch_assoc($q)) {
  $arti_status[$d['status']] = $d['arti'];
  $count_status[$d['status']] = $d['count_status'];
  $percent_subfitur[$d['status']] = $d['count_status'] ?
    round($d['count_status'] / $total_subfitur * 100, 2) : 0;
}



$s = "SELECT a.id, a.nama
FROM tb_fitur a ORDER BY a.sub_divisi_no";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$nav = "<a class='btn btn-sm btn-info f12' href='?progress' >All Fitur</a> ";
$i = 0;
$no_next_fitur = mysqli_num_rows($q) + 1;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $btn = $d['id'] == $get_id_fitur ? "<span class='btn btn-sm btn-primary' style='display:inline-block;margin:0 10px 0 5px'>$i</span>" : "<a class='btn btn-sm btn-info f10 miring' href='?progress&id_fitur=$d[id]&no=$i' >$i</a> ";
  $nav .= $btn;
}
$total_subfitur_show = "<span id=subfitur_info__toggle class='btn btn-sm btn_aksi f12' style='display:inline-block;margin:0 10px 0 5px'>$total_subfitur Subfitur $img_detail</span>";
$tr_subfitur_count = '';
$bg = ['red', '#cc5', '#caf', '#aaf', '#5f5', '#0f0'];
foreach ($count_status as $key => $value) {
  $persen = $percent_subfitur[$key];
  $grafik = "<div class='p1 f10 br5' style='width:$persen%;background:$bg[$key]'>$persen%</div>";
  $tr_subfitur_count .= "
    <tr>
      <td>$key</td>
      <td>$arti_status[$key]</td>
      <td>$value</td>
      <td>$grafik</td>
    </tr>
  ";
}
echo "
  <div class=mb2>
    $nav
    $total_subfitur_show
  </div>
  <div id=subfitur_info class='hideit wadah gradasi-kuning'>
    <table class=table>
      <thead>
        <th>Status</th>
        <th>Arti Status</th>
        <th>Count</th>
        <th>Percent</th>
      </thead>
      $tr_subfitur_count
    </table>
    <div>Subfitur sedang dikerjakan: <b>$d_sedang_dikerjakan[nama]</b> $img_loading</div>
    <div class='f12 abu'>$d_sedang_dikerjakan[keterangan]</div>
    <div class='f12 abu'>Fitur : $d_sedang_dikerjakan[nama_fitur]</div>
    <div class='f12 abu'>Sub Divisi : $d_sedang_dikerjakan[sub_divisi]</div>
  </div>
";

# ======================================================
# MAIN SELECT FITUR
# ======================================================
$s = "SELECT a.id as id_fitur, a.* 
FROM tb_fitur a 
WHERE $sql_id_fitur 
ORDER BY sub_divisi_no";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
$i = $get_no ? ($get_no - 1) : 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $id_fitur = $d['id_fitur'];
  $id_toggle = "fitur$id_fitur" . '__toggle';

  # ======================================================
  # FOREACH FITUR KOLOM FOR EDITING FORM
  # ======================================================
  $tr_form = '';
  foreach ($colField as $key => $kolom) {
    if ($colNull[$key] == 'NO') {
      $sty_kolom = 'darkblue consolas f12 kanan';
      $required = 'required';
    } else {
      $required = '';
      $sty_kolom = 'gray consolas f12 kanan';
    }

    $sty_isi = '';
    $kolom_show = str_replace('_', ' ', $kolom);

    $tr_form .= "
      <tr>
        <td class='$sty_kolom' style='padding-right:10px'>$kolom_show</td>
        <td class='$sty_isi'>
          <input $required name=$kolom value='$d[$kolom]' class='form-control form-control-sm'>
        </td>
      </tr>
    ";
  }

  # ======================================================
  # SELECT SUB FITUR
  # ======================================================
  $s2 = "SELECT a.*,
  a.id as id_subfitur,
  (
    SELECT arti FROM tb_status_subfitur 
    WHERE status=a.status) arti_subfitur, 
  (
    SELECT keterangan FROM tb_status_subfitur 
    WHERE status=a.status) ket_status_subfitur 
  FROM tb_subfitur a 
  WHERE a.id_fitur=$id_fitur";
  $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));
  $tr_sub = '<tr><td colspan=100% class="consolas f12 miring red">Belum ada subfitur</td></tr>';
  $j = 0;


  if (mysqli_num_rows($q2)) {
    $tr_sub = '';
    while ($d2 = mysqli_fetch_assoc($q2)) {
      $j++;
      $id_subfitur = $d2['id_subfitur'];
      $status_show = $d2['status'] ? $img_arti[$d2['status']] : $img_warning;
      $status_show = $d2['sedang_dikerjakan'] ? $img_loading : $status_show;

      $arti_subfitur = $d2['arti_subfitur'] ? $d2['arti_subfitur'] : 'Belum dikerjakan';
      $arti_subfitur = $d2['sedang_dikerjakan'] ? 'Sedang dikerjakan' : $arti_subfitur;
      $sedang_dikerjakan = $d2['sedang_dikerjakan'] ? 'sedang_dikerjakan' : '';

      for ($k = 0; $k <= 5; $k++) $dis[$k] = '';
      $dis[$d2['status']] = 'disabled';

      $tr_sub .= "
        <tr class='$sedang_dikerjakan'>
          <td class='sub_number'>$i.$j</td>
          <td>
            $d2[nama]
            <div class='abu f12 mt1'>$d2[keterangan]</div>
          </td>
          <td class='td_status_subfitur'>
            <div class='btn_aksi pointer' id=keterangan_subfitur_$id_subfitur" . "__toggle>
              $status_show
              <div class='abu f10 miring mt1'>$arti_subfitur</div>
            </div>
            <div class='hideit f10 mt2' id=keterangan_subfitur_$id_subfitur>
              $d2[ket_status_subfitur]
              <form method=post class='mt2 f10'>
                <div class=mb1>Set status:</div>
                <button class='btn btn-danger btn_sm' name=btn_set_status value=0__$id_subfitur $dis[0]>0</button>
                <button class='btn btn-warning btn_sm' name=btn_set_status value=1__$id_subfitur $dis[1]>1</button>
                <button class='btn btn-warning btn_sm' name=btn_set_status value=2__$id_subfitur $dis[2]>2</button>
                <button class='btn btn-info btn_sm' name=btn_set_status value=3__$id_subfitur $dis[3]>3</button>
                <button class='btn btn-success btn_sm' name=btn_set_status value=4__$id_subfitur $dis[4]>4</button>
                <button class='btn btn-success btn_sm' name=btn_set_status value=5__$id_subfitur $dis[5]>5</button>
              </form>
              <form method=post class='mt2 f10'>
                <div class=mb1>Set:</div>
                <button class='btn btn-success btn_sm btn_sedang_dikerjakan' name=btn_sedang_dikerjakan value=$id_subfitur>Sedang dikerjakan</button>
              </form>
            </div>
          </td>
        </tr>
      ";
    }
  }

  # ======================================================
  # TR ADD SUB FITUR
  # ======================================================
  $j++;
  $tr_add_sub = "
    <tr>
      <td class='abu miring consolas f12 sub_number'>*$i.$j</td>
      <td colspan=100%>
        <span class='consolas green f12 bold btn_aksi pointer' id=form_subfitur$id_fitur" . "__toggle>+ Add Subfitur</span>
        <form method=post id=form_subfitur$id_fitur class='hideit mt1'>
          <table width=100%>
            <tr>
              <td>
                <input class='form-control form-control-sm' name=new_subfitur required minlength=5 maxlength=30 placeholder='Subfitur Baru...'/>
              </td>
              <td>
                <button class='btn btn-success btn-sm ml1' name=btn_add_subfitur value=$id_fitur>Add</button>
              </td>
            </tr>
            <tr>
              <td>
                <textarea class='form-control form-control-sm' name=keterangan required minlength=20 maxlength=1000 placeholder='Keterangan...'></textarea>
              </td>
            </tr>
          </table>
        </form>
      </td>
    </tr>
  ";

  # ======================================================
  # FINAL TB-SUB OF LOOP OUTPUT
  # ======================================================
  $tb_sub = "
    <table class='table table-bordered gradasi-kuning'>
      $tr_sub 
      $tr_add_sub
    </table>
  ";

  # ======================================================
  # FINAL LOOP OUTPUT
  # ======================================================
  $tr .= "
    <tr>
      <td>$i</td>
      <td>
        <div class='darkblue miring f12 mb1'>$d[sub_divisi]</div>
        $d[nama] <span class=btn_aksi id=$id_toggle>$img_detail</span>
        <form method=post class='hideit wadah gradasi-kuning mt2' id=fitur$id_fitur>
          <div class='f10 abu consolas mb2'>FORM EDIT FITUR</div>
          <table>
            $tr_form
            <tr><td>&nbsp;</td><td colspan=100%><button class='btn btn-info btn-sm' name=btn_update_fitur value=$id_fitur>Update</button></td></tr>
          </table>
        </form>
      </td>
      <td>$tb_sub</td>
    </tr>
  ";
}

# ======================================================
# TR ADD FITUR
# ======================================================
$tr_add = "
  <tr>
    <td class='abu f12 miring tengah'>*$no_next_fitur</td>
    <td colspan=100%>
      <form method=post>
        <div class=flexy>
          <div>
            <input class='form-control' name=new_fitur required minlength=5 maxlength=30/>
          </div>
          <div>
            <button class='btn btn-success' name=btn_add_fitur>Add Fitur</button>
          </div>
        </div>
      </form>
    </td>
  </tr>
";

# ======================================================
# FINAL TABLE OUTPUT
# ======================================================
echo "
  <table class='table'>
    <thead>
      <th>No</th>
      <th>Sub Divisi / Fitur</th>
      <th>Subfitur dan Status</th>
    </thead>
    $tr
    $tr_add
  </table>  
";

?>
<?php
