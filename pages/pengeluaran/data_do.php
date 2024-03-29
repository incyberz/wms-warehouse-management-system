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
if (isset($_POST['keyword'])) {
  $keyword = clean_sql($_POST['keyword']);
  jsurl("?pengeluaran&p=data_do&keyword=$keyword");
  exit;
}
$keyword = $_GET['keyword'] ?? '';
$bg_keyword = $keyword ? 'style="background:#0f0"' : '';
$hide_clear = $keyword ? '' : 'hideit';
$keyword = trim($keyword);

$sql_filter = $keyword ? "
  (
    a.kode_do LIKE '%$keyword%' OR  
    a.kode_artikel LIKE '%$keyword%' 
  )
" : '1';

$s = "SELECT 
a.*,
b.*,
a.id as id_do,
(SELECT SUM(qty) FROM tb_pick WHERE id_do=a.id AND is_hutangan is null) sum_pick, 
(SELECT SUM(qty_allocate) FROM tb_pick WHERE id_do=a.id AND is_hutangan is null) sum_allocate, 
(SELECT COUNT(1) FROM tb_pick WHERE id_do=a.id) jumlah_pick, 
(SELECT COUNT(1) FROM tb_pick WHERE id_do=a.id AND qty_allocate is not null) jumlah_allocate 
FROM tb_do a 
JOIN tb_permintaan_do b ON a.id_permintaan=b.id 
WHERE $sql_filter  
ORDER BY tanggal_delivery DESC
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_records = mysqli_num_rows($q);

$s .= "LIMIT 10";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$jumlah_tampil = mysqli_num_rows($q);

$tr = '';
$i = 0;
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $id_do = $d['id_do'];
  $abu_items = $d['jumlah_pick'] ? 'abu' : 'tebal merah';
  $aksi_hapus = $d['jumlah_pick'] ? '' : "<span class='btn_aksi' id=do__delete__$id_do>$img_delete</span>";
  $untuk = $d['id_kategori'] == 1 ? 'Aksesoris' : 'Fabric';
  $cat = $d['id_kategori'] == 1 ? 'aks' : 'fab';
  $add_ro = $d['is_repeat'] ? '' : "<a target=_blank onclick='return confirm(\"Ingin menambah Repeat Order dari DO ini?\")' href='?pengeluaran&p=repeat_order&kode_do_awal=$d[kode_do]&id_kategori=$d[id_kategori]'>$img_add</a>";

  $jumlah_pick = floatval($d['jumlah_pick']);
  $sum_pick = floatval($d['sum_pick']);
  $sum_allocate = floatval($d['sum_allocate']);
  $persen = ($sum_pick and $sum_allocate) ? number_format($sum_allocate / $sum_pick * 100, 2) : 0;
  $allocate_show = ($sum_allocate != $sum_pick) ? "
    <div>$sum_allocate of $sum_pick ($persen%)</div>
    <a class='btn btn-primary btn-sm mt1' href='?pengeluaran&p=buat_do&kode_do=$d[kode_do]&cat=$cat'>Allocate</a>
  " : "$persen%";

  $tr .= "
    <tr id=source_do__$id_do>
      <td>$i</td>
      <td>
        <a href='?pengeluaran&p=buat_do&kode_do=$d[kode_do]&cat=$cat'>
          $d[kode_do]
          <div class='kecil $abu_items'>$d[jumlah_pick] picks</div>
          <div class='kecil $abu_items'>$d[jumlah_allocate] allocate</div>
        </a>
      </td>
      <td>$d[kode_artikel]</td>
      <td>$untuk</td>
      <td>$allocate_show</td>
      <td>$d[permintaan]</td>
      <td class=pic_only>
        $aksi_hapus 
        $add_ro
      </td>
    </tr>
  ";
}

$tambah_do_baru = $id_role != 7 ? '' : "<a class='btn btn-sm btn-success' href='?pengeluaran&p=buat_do'>Buat DO Baru</a>";
if (!$tr) $tr = "<tr><td colspan=100%><div class='alert alert-danger'>Data tidak ditemukan</div></td></tr>";

echo
"
  <div class='flexy flex-between mb2'>
    <div class=flexy>
      <div>
        <form method=post>
          <input class='form-control form-control-sm' placeholder='Filter ...' name=keyword id=keyword value='$keyword' maxlength=15 $bg_keyword>
          <button class=hideit>Filter</button>
        </form>
      </div>
      <div class='$hide_clear'><a href='?penerimaan&p=data_sj' class=kecil>Clear<span class=f18> </span></a></div>
      <div class='kecil abu'>Tampil <span class='darkblue f18'>$jumlah_tampil</span> data of $jumlah_records records</div>
    </div>
    <div class='mb2 kanan'>$tambah_do_baru</div>
  </div>  

  <table class=table>
    <thead>
      <th>NO</th>
      <th>NOMOR DO</th>
      <th>ARTIKEL</th>
      <th>OTP</th>
      <th>QTY Allocate</th>
      <th>Permintaan</th>
      <th class=pic_only>Delete / Add-RO</th>
    </thead>
    $tr
  </table>
";
