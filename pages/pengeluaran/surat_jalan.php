<style>
  #tb_do td {
    padding: 5px 10px;
    padding: 3px;
  }
</style>
<?php
$kode_do = $_GET['kode_do'] ?? die('Kode DO belum terdefinisi');
$cat = $_GET['cat'] ?? 'aks';
$id_kategori = $cat == 'aks' ? 1 : 2;

if (isset($_POST['btn_cetak'])) {
  $s = "UPDATE tb_do SET tanggal_delivery='$_POST[tanggal_delivery]' WHERE kode_do='$kode_do'";
  $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
  $arr = explode('?', $_SERVER['REQUEST_URI']);
  jsurl("?$arr[1]&view_mode=cetak");
}

$judul = 'Surat Jalan Pengeluaran';
set_title($judul);
echo "<h1 class='f20 tengah' style=color:black>$judul</h1>";

$tr = "<tr><td colspan=100%><div class='alert alert-danger'>Belum ada item</div></td></tr>";
$s = "SELECT 
a.id as id_pick,
a.qty_allocate,
a.stok_akhir_tmp,
b.no_lot,
b.kode_lokasi,
b.is_fs,
d.kode_po,
e.id as id_do,
e.tanggal_delivery,
f.brand,
g.satuan,
g.kode_lama,
g.kode as kode_barang,
g.nama as nama_barang,
g.keterangan as keterangan_barang,

(SELECT COUNT(1) FROM tb_roll WHERE id_kumulatif=b.id ) count_roll

FROM tb_pick a 
JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id 
JOIN tb_sj_item c ON b.id_sj_item=c.id 
JOIN tb_sj d ON c.kode_sj=d.kode 
JOIN tb_do e ON a.id_do=e.id 
JOIN tb_lokasi f ON b.kode_lokasi=f.kode  
JOIN tb_barang g ON c.kode_barang=g.kode  
AND e.kode_do='$kode_do' 
ORDER BY a.tanggal_allocate   
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$count_pick = mysqli_num_rows($q);

$tr = '';
$i = 0;
$id_do = '';
while ($d = mysqli_fetch_assoc($q)) {
  $i++;
  $id_pick = $d['id_pick'];
  $id_do = $d['id_do'];
  $no_lot = $d['no_lot'];
  $kode_lokasi = $d['kode_lokasi'];
  $brand = $d['brand'];
  $stok_akhir_tmp = $d['stok_akhir_tmp'];
  // $is_hutangan = $d['is_hutangan'];
  $count_roll = $d['count_roll'];
  $is_fs = $d['is_fs'];
  $satuan = $d['satuan'];
  $tanggal_delivery = $d['tanggal_delivery'];


  //pengeluaran
  $qty_allocate = floatval($d['qty_allocate']);

  //show
  $tanggal_delivery_show = date('d-M-Y', strtotime($tanggal_delivery));













  # =======================================================
  # FINAL TR LOOP
  # =======================================================
  $tr .= "
    <tr>
      <td>$i</td>
      <td>
      <div>
        $d[kode_po]
      </div>
      <div>
        $no_lot
      </div>
      <div>
        $kode_lokasi $brand
      </div>
      </td>

      <td>
        <div>$d[kode_barang]</div>
        <div class=' '>
          <div>Kode lama: $d[kode_lama]</div>
        </div>
      </td>
      <td>
          <div>$d[nama_barang]</div>
          <div>$d[keterangan_barang]</div>
      </td>
      <td>
        $qty_allocate
        <div>$satuan</div>
      </td>
      <td>
        $stok_akhir_tmp
      </td>
    </tr>
  ";
}

echo "

  <hr>
  <table id=tb_do style='color:black' width=100%>
    <tr>
      <td>Nomor DO</td>
      <td>:</td>
      <td>$kode_do</td>
      <td width=20%>&nbsp;</td>
      <td>Tanggal Delivery</td>
      <td>:</td>
      <td class=upper>$tanggal_delivery_show</td>
    </tr>
    <tr>
      <td>Jenis</td>
      <td>:</td>
      <td class=upper>$cat</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  
  <table class='table' style='border: solid 1px black'>
    <thead>
      <th>No</th>
      <th>PO-LOT-LOC</th>
      <th>ITEM</th>
      <th>DESKRIPSI</th>
      <th>ALLOCATE</th>
      <th>STOCK</th>
    </thead>
    $tr
  </table>
";

echo "
  <form method='post' class='view_mode'>
    <div class='wadah'>
      <div class='flexy mb2'>
        <div>Tanggal Delivery</div>
        <div>
          <input type='date' name='tanggal_delivery' class='form-control' value='$tanggal_delivery'>
        </div>
      </div>
      <button class='btn btn-primary' name=btn_cetak>Cetak Surat Jalan</button>
    </div>
  </form>
";
if ($view_mode == 'cetak') {
  echo "
    <script>
      $(function(){
        $('.view_mode').hide();
        window.print();
      })
    </script>
  ";
}
?>