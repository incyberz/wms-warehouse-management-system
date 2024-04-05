<div class="pagetitle">
  <h1>Data Delivery Order</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?pengeluaran">Pengeluaran</a></li>
      <li class="breadcrumb-item"><a href="?pengeluaran&p=buat_do">Buat DO Baru</a></li>
      <li class="breadcrumb-item active">Data DO</li>
    </ol>
  </nav>
</div>

<?php
set_title('Data DO');
// filter data
$today = date('Y-m-d');
$bg_hijau = 'style="background:#0f0"';
$jumlah_skip = 0;
$jumlah_unlock_only = 0;
if (isset($_POST['btn_filter'])) {
  $keyword = clean_sql($_POST['keyword']);
  $awal_tanggal = $_POST['awal_tanggal'];
  $akhir_tanggal = $_POST['akhir_tanggal'];
  $limit = $_POST['limit'];
  $unlock_only = $_POST['unlock_only'] ?? '';
  jsurl("?pengeluaran&p=data_do&keyword=$keyword&awal_tanggal=$awal_tanggal&akhir_tanggal=$akhir_tanggal&limit=$limit&unlock_only=$unlock_only");
  exit;
}
$keyword = $_GET['keyword'] ?? '';
$awal_tanggal = $_GET['awal_tanggal'] ?? '';
$akhir_tanggal = $_GET['akhir_tanggal'] ?? '';
$unlock_only = $_GET['unlock_only'] ?? '';
$limit = $_GET['limit'] ?? '10';
$bg_keyword = $keyword ? $bg_hijau : '';
$bg_awal = $awal_tanggal ? $bg_hijau : '';
$bg_akhir = $akhir_tanggal ? $bg_hijau : '';
$bg_limit = $limit != 10 ? $bg_hijau : '';
$hide_clear = ($keyword || $awal_tanggal || $akhir_tanggal || $unlock_only) ? '' : 'hideit';
$keyword = trim($keyword);

$awal_tanggal_sql = $awal_tanggal ? "a.date_created >= '$awal_tanggal'" : '1';
$akhir_tanggal_sql = $akhir_tanggal ? "a.date_created <= '$akhir_tanggal'" : '1';

$opt_limit = '';
$rlimit = [10, 50, 100, 500, 1000];
foreach ($rlimit as $item) {
  $selected = $item == $limit ? 'selected' : '';
  $opt_limit .= "<option value='$item' $selected>Show $item</option>";
}

$sql_filter = $keyword ? "
  (
    a.kode_do LIKE '%$keyword%' OR  
    a.kode_artikel LIKE '%$keyword%' 
  )
" : '1';

$s = "SELECT 
a.*,
a.date_created as tanggal_do,
b.*,
a.id as id_do,
(SELECT SUM(qty) FROM tb_pick WHERE id_do=a.id AND is_hutangan is null) sum_pick, 
(SELECT SUM(qty_allocate) FROM tb_pick WHERE id_do=a.id AND is_hutangan is null) sum_allocate, 
(SELECT COUNT(1) FROM tb_pick WHERE id_do=a.id) jumlah_pick, 
(SELECT COUNT(1) FROM tb_pick WHERE id_do=a.id AND boleh_allocate=1) jumlah_pick_unlock, 
(SELECT COUNT(1) FROM tb_pick WHERE id_do=a.id AND qty_allocate is not null) jumlah_allocate 
FROM tb_do a 
JOIN tb_permintaan_do b ON a.id_permintaan=b.id 
WHERE $sql_filter  
AND $awal_tanggal_sql 
AND $akhir_tanggal_sql 
ORDER BY a.date_created DESC
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_records = mysqli_num_rows($q);

$s .= "LIMIT $limit";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_tampil = mysqli_num_rows($q);

$tr = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $id_do = $d['id_do'];
  $abu_items = $d['jumlah_pick'] ? 'abu' : 'tebal merah';
  $aksi_hapus = $d['jumlah_pick'] ? '' : "<span class='btn_aksi' id=do__delete__$id_do>$img_delete</span>";
  $untuk = $d['id_kategori'] == 1 ? 'Aksesoris' : 'Fabric';
  $cat = $d['id_kategori'] == 1 ? 'aks' : 'fab';
  $add_ro = $d['is_repeat'] ? '' : "<a target=_blank onclick='return confirm(\"Ingin menambah Repeat Order dari DO ini?\")' href='?pengeluaran&p=repeat_order&kode_do_awal=$d[kode_do]&id_kategori=$d[id_kategori]'>$img_add</a>";

  $jumlah_pick = $d['jumlah_pick'];
  $jumlah_pick_unlock = $d['jumlah_pick_unlock'];
  if ($unlock_only) {
    if ($jumlah_pick != $jumlah_pick_unlock)     $i++;
  } else {
    $i++;
  }
  $sum_pick = floatval($d['sum_pick']);
  $sum_allocate = floatval($d['sum_allocate']);

  $unlock_show = "<div class=mb1>$jumlah_pick_unlock of $jumlah_pick</div>";
  if ($jumlah_pick != $jumlah_pick_unlock and $jumlah_pick > 0) {
    if ($id_role == 7) {
      $unlock_show = "
        $unlock_show
        <a class='btn btn-primary btn-sm mt1' href='?pengeluaran&p=buat_do&kode_do=$d[kode_do]&cat=$cat'>Unlock</a> 
      ";
    } else {
      $unlock_show = "
        $unlock_show
        <span class='f12 darkred miring'>Masih ada item terkunci</span> 
      ";
    }
  }

  $persen = ($sum_pick and $sum_allocate) ? number_format($sum_allocate / $sum_pick * 100, 2) : 0;
  $primary = ($jumlah_pick == $jumlah_pick_unlock and $jumlah_pick > 0) ? 'primary' : 'secondary';

  if ($id_role == 3) {
    $allocate_show = ($sum_allocate != $sum_pick) ? "
      <div>$sum_allocate of $sum_pick ($persen%)</div>
      <a class='btn btn-$primary btn-sm mt1' href='?pengeluaran&p=buat_do&kode_do=$d[kode_do]&cat=$cat'>Allocate</a>
    " : "$persen%";
  } else {
    $allocate_show = "$persen%";
  }

  $tanggal_do = date('d-M-y', strtotime($d['tanggal_do']));
  $jam_do = date('H:i', strtotime($d['tanggal_do']));

  $this_tr = "
    <tr id=source_do__$id_do>
      <td>$i</td>
      <td>
        $tanggal_do
        <div class='abu f12'>$jam_do</div>
      </td>
      <td>
        <a href='?pengeluaran&p=buat_do&kode_do=$d[kode_do]&cat=$cat'>
          $d[kode_do]
          <div class='kecil $abu_items'>$d[jumlah_pick] picks</div>
          <div class='kecil $abu_items'>$d[jumlah_allocate] allocate</div>
        </a>
      </td>
      <td>$d[kode_artikel]</td>
      <td>$untuk</td>
      <td>$unlock_show</td>
      <td>$allocate_show</td>
      <td>$d[permintaan]</td>
      <td class=pic_only>
        $aksi_hapus 
        $add_ro
      </td>
    </tr>
  ";

  if ($unlock_only) {
    if ($jumlah_pick != $jumlah_pick_unlock) {
      $jumlah_unlock_only++;
      $tr .= $this_tr;
    } else {
      $jumlah_skip++;
    }
  } else {
    $tr .= $this_tr;
  }
}

$jumlah_tampil = $unlock_only ? $jumlah_unlock_only : $jumlah_tampil;
$checked_unlock_only = $unlock_only ? 'checked' : '';
$bg_unlock_only = $unlock_only ? $bg_hijau : '';

$tambah_do_baru = $id_role != 7 ? '' : "<a class='btn btn-sm btn-success' href='?pengeluaran&p=buat_do'>Buat DO Baru</a>";
if (!$tr) $tr = "<tr><td colspan=100%><div class='alert alert-danger'>Data tidak ditemukan</div></td></tr>";


echo
"
  <div class=flexy>
    <div>
      <form method=post>
        <div class=flexy>
          <div>
            <input class='form-control form-control-sm' placeholder='Filter ...' name=keyword id=keyword value='$keyword' maxlength=15 $bg_keyword>
          </div>
          <div>
            <input type=date class='form-control form-control-sm' name=awal_tanggal id=awal_tanggal value='$awal_tanggal' $bg_awal max='$today'>
          </div>
          <div>
            s.d
          </div>
          <div>
            <input type=date class='form-control form-control-sm' name=akhir_tanggal id=akhir_tanggal value='$akhir_tanggal' $bg_akhir max='$today'>
          </div>
          <div>
            <select name=limit class='form-control form-control-sm' $bg_limit>
              $opt_limit
            </select>
          </div>
          <div $bg_unlock_only class='pl1 pr1 br5'>
            <label>
              <input type=checkbox name=unlock_only value=1 $checked_unlock_only > 
              <span class='f12 '>Unlock Only</span>
            </label>
          </div>
          <div>
            <button class='btn btn-success btn-sm' name=btn_filter>Filter</button>
          </div>
        </div>
      </form>
    </div>
    <div class='$hide_clear'><a href='?pengeluaran&p=data_do' class=kecil>Clear<span class=f18> </span></a></div>
  </div>

  <div class='flexy flex-between mb2'>
    <div class='kecil abu'>Tampil <span class='darkblue f18'>$jumlah_tampil</span> data of $jumlah_records records</div>
    <div class='mb2 kanan'>$tambah_do_baru</div>
  </div>  

  <table class=table>
    <thead>
      <th>NO</th>
      <th>Tanggal</th>
      <th>NOMOR DO</th>
      <th>ARTIKEL</th>
      <th>OTP</th>
      <th>Unlock</th>
      <th>QTY Allocate</th>
      <th>Permintaan</th>
      <th class=pic_only>Delete / Add-RO</th>
    </thead>
    $tr
  </table>
";
