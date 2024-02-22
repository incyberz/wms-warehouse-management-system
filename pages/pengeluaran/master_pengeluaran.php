
<?php 
set_title('Master Pengeluaran');
// to do : fix decimal
include 'include/date_managements.php';
include 'include/arr_brand.php';
include 'include/arr_apparel.php';
include 'include/arr_gender.php';
include 'include/arr_pic.php';
include 'include/arr_assign_pic.php';
$p = 'pengeluaran'; // untuk navigasi
$cat= $_GET['cat'] ?? 'aks'; //default AKS
$jenis_barang = $cat=='aks' ? 'Aksesoris' : 'Fabric';

$arr_waktu = [
  'hari_ini' => 'Hari ini',
  'kemarin' => 'Kemarin',
  'minggu_ini' => 'Minggu ini',
  'bulan_ini' => 'Bulan ini',
  'tahun_ini' => 'Tahun ini',
  'all_time' => 'All time',
];

$filter_waktu = $_GET['waktu'] ?? 'all_time';
$opt_waktu = '';
foreach ($arr_waktu as $waktu => $nama_waktu) {
  $selected = $filter_waktu==$waktu ? 'selected' : '';
  $opt_waktu.= "<option value=$waktu $selected>$nama_waktu</option>";
}

$filter_do = $_GET['do'] ?? '';
$filter_id = $_GET['id'] ?? '';
$filter_ppic = $_GET['ppic'] ?? '';
if(isset($_POST['btn_cari'])){
  jsurl("?$parameter&cat=$cat&do=$_POST[filter_do]&id=$_POST[filter_id]&waktu=$_POST[filter_waktu]");
}

$clear_filter = 'Filter:';
if(
  $filter_do!=''||
  $filter_id!=''
)$clear_filter = "<a href='?$parameter&cat=$cat'>Clear Text Filter</a>";

$bg_waktu = $filter_waktu=='all_time' ? '' : 'bg-hijau';
$bg_do = $filter_do=='' ? '' : 'bg-hijau';
$bg_id = $filter_id=='' ? '' : 'bg-hijau';

$id_kategori = $cat=='aks' ? 1 : 2;

if($filter_waktu=='all_time'){$where_date = '1';}else 
if($filter_waktu=='hari_ini'){$where_date = "i.tanggal_delivery >= '$today' ";}else 
if($filter_waktu=='kemarin'){$where_date = "i.tanggal_delivery >= '$kemarin' AND i.tanggal_delivery < '$today' ";}else 
if($filter_waktu=='minggu_ini'){$where_date = "i.tanggal_delivery >= '$ahad_skg' AND i.tanggal_delivery < '$ahad_depan' ";}else 
if($filter_waktu=='bulan_ini'){$where_date = "i.tanggal_delivery >= '$awal_bulan' ";}else
if($filter_waktu=='tahun_ini'){$where_date = "i.tanggal_delivery >= '$awal_tahun' ";} 


$where_do = $filter_do=='' ? '1' : "(i.kode_do LIKE '%$filter_do%' OR i.kode_artikel LIKE '%$filter_do%') ";
$where_id = $filter_id=='' ? '1' : "(f.kode LIKE '%$filter_id%' OR f.nama LIKE '%$filter_id%' OR f.keterangan LIKE '%$filter_id%' )";



$sql_from = "FROM tb_pengeluaran a 
JOIN tb_barang b ON a.kode_barang=b.kode 
";

$sql_where = "
WHERE f.id_kategori=$id_kategori 
AND $where_date 
AND $where_do 
AND $where_id 
";

$s = "SELECT 1 $sql_from $sql_where ";

# ==============================================================
# MAIN SQL PENGELUARAN
# ==============================================================
$s = "SELECT 
a.qty as qty_pick,
a.id as id_pick,
a.id_sj_subitem,
a.qty_allocate,
b.kode_lokasi,
b.is_fs,
b.no_lot,
b.id_sj_item,
c.kode_sj,
d.kode_po,
f.kode as kode_barang,
f.nama as nama_barang,
f.keterangan as keterangan_barang,
f.satuan,
g.brand,
h.nama as nama_supplier,
i.tanggal_delivery,
i.kode_artikel,
i.kode_do,
(
  SELECT p.qty FROM tb_sj_subitem p 
  WHERE p.is_fs is not null 
  AND p.id=a.id_sj_subitem) qty_fs,
(
  SELECT p.qty FROM tb_sj_subitem p 
  JOIN tb_retur q ON p.id=q.id 
  WHERE p.is_fs is null 
  AND p.id=a.id_sj_subitem) qty_diterima_with_qc_no_fs,
(
  SELECT p.qty FROM tb_retur p 
  JOIN tb_sj_subitem q ON p.id=q.id 
  WHERE q.id= a.id_sj_subitem
  ) qty_retur,
(
  SELECT p.qty FROM tb_terima_retur p
  JOIN tb_retur q ON p.id=q.id 
  JOIN tb_sj_subitem r ON q.id=r.id 
  WHERE r.id=a.id_sj_subitem) qty_balik,
(
  SELECT SUM(p.qty) FROM tb_picking p 
  WHERE p.id != a.id 
  AND p.id_sj_subitem = a.id_sj_subitem) qty_pick_by,
(
  SELECT COUNT(1) FROM tb_roll  
  WHERE id_sj_subitem = b.id) count_roll


FROM tb_picking a 
JOIN tb_sj_subitem b ON a.id_sj_subitem=b.id 
JOIN tb_sj_item c ON b.id_sj_item=c.id 
JOIN tb_sj d ON c.kode_sj=d.kode 
JOIN tb_bbm e ON e.kode_sj=d.kode 
JOIN tb_barang f ON c.kode_barang=f.kode 
JOIN tb_lokasi g ON b.kode_lokasi=g.kode 
JOIN tb_supplier h ON d.id_supplier=h.id  
JOIN tb_do i ON a.id_do=i.id  

$sql_where 

--  ID - PO - LOT - LOKASI - FS
ORDER BY f.kode, d.kode_po, b.no_lot, g.kode, b.is_fs  

";

$q = mysqli_query($cn,$s) or die(mysqli_error($cn));

$total_row = mysqli_num_rows($q);
$jumlah_row = mysqli_num_rows($q);


$tr_hasil = $jumlah_row==0 ? "<tr><td colspan=100%><div class='alert alert-danger'>Data tidak ditemukan..</div></td></tr>" : '';
while($d=mysqli_fetch_assoc($q)){
  $id=$d['id_pick'];
  $satuan=$d['satuan'];
  $kode_sj=$d['kode_sj'];
  $kode_lokasi=$d['kode_lokasi'];
  $kode_do=$d['kode_do'];
  $is_fs=$d['is_fs'];

  $id_sj_item = $d['id_sj_item'];

  $tgl = date('d-m-y',strtotime($d['tanggal_delivery']));
  $awal_terima = $d['awal_terima'] ?? '';
  $akhir_terima = $d['akhir_terima'] ?? '';

  $no_lot = $d['no_lot'] ?? $unset;
  $qty_fs = $d['qty_fs'];
  $qty_retur = $d['qty_retur'];
  $qty_balik = $d['qty_balik'];
  $qty_pick = $d['qty_pick'];
  $qty_allocate = $d['qty_allocate'];
  $qty_pick_by = $d['qty_pick_by'];
  $qty_diterima_with_qc_no_fs = $d['qty_diterima_with_qc_no_fs'];
  
  $qty_fs = $qty_fs ? floatval($qty_fs) : '-';
  $qty_retur = $qty_retur ? floatval($qty_retur) : 0;
  $qty_balik = $qty_balik ? floatval($qty_balik) : 0;
  $qty_pick = $qty_pick ? floatval($qty_pick) : 0;
  $qty_allocate = $qty_allocate ? floatval($qty_allocate) : 0;
  $qty_pick_by = $qty_pick_by ? floatval($qty_pick_by) : 0;
  $qty_diterima_with_qc_no_fs = $qty_diterima_with_qc_no_fs ? floatval($d['qty_diterima_with_qc_no_fs']) : 0;

  $qty_real = $qty_diterima_with_qc_no_fs - $qty_retur + $qty_balik;
  $qty_real = $qty_real<$qty_fs ? $qty_fs : $qty_real;

  $stok_akhir = $qty_real - $qty_pick - $qty_pick_by;
  $stok_akhir_show = $stok_akhir ? "<span class='tebal darkblue'>$stok_akhir $satuan</span>" : '<span class=abu>0</span>';

  $fs_show = $is_fs ? ' <b class="f14 ml1 mr1 biru p1 pr2 br5" style="display:inline-block;background:green;color:white">FS</b>' : '';


  # =======================================================
  # DECODE KODE ARTIKEL
  # =======================================================
  $kode_artikel=$d['kode_artikel'];
  $kode_brand = substr($kode_artikel,0,1);
  $kode_gender = substr($kode_artikel,7,1);
  $kode_apparel = substr($kode_artikel,8,1);
  $kode_unik = "$kode_brand$kode_gender$kode_apparel";

  $brand = ucwords(strtolower($arr_brand[$kode_brand]));
  $gender = ucwords(strtolower($arr_gender[$kode_gender]));
  $apparel = ucwords(strtolower($arr_apparel[$kode_apparel]));
  $pic = $arr_assign_pic[$kode_unik] ?? $unset;

  $ul_artikel = "
  <ul class='f12 abu m0 p0 pl3' >
    <li>$brand</li>
    <li>$gender</li>
    <li>$apparel</li>
    <li>$kode_unik: $pic</li>
  </ul>
  ";

  $gradasi = $qty_pick ? '' : 'merah';

  if($qty_pick){

    if($id_role==3){
      if($qty_allocate==$qty_pick){
        $color = 'success';
        $caption = 'reAllocate';
      }else if($qty_allocate){
        $color = 'danger';
        $caption = 'Fix Allocate';
      }else{
        $color = 'primary';
        $caption = 'Allocate';
      }
      $btn = "<button class='btn btn-$color btn-sm'>$caption</button>";
    }else{
      $btn='';      
    }

    $pick_allocate = "
      <a target=_blank href='?pengeluaran&p=buat_do&kode_do=$kode_do&cat=$cat'>
        $qty_pick / $qty_allocate  $btn
      </a>
    ";
  }else{
    if($id_role==7){
      $pick_allocate = "
        <a target=_blank href='?pengeluaran&p=buat_do&kode_do=$kode_do&cat=$cat'>
          0 / 0  <button class='btn btn-danger btn-sm'>Pick</button>
        </a>
      ";
    }else{
      $pick_allocate = '0 / 0';
    }
  }
  // $pick_allocate = $id_role!=3 ? "$qty_pick / $qty_allocate" : "
  //   
  // ";

  $tr_hasil .= "
    <tr id=tr__$id class='gradasi-$gradasi'>
      <td>
        <div>
          <div class='kecil abu'>Tgl: $tgl</div>
          <div class='btn_aksi pointer' id=ket_artikel$id"."__toggle>$kode_artikel $img_detail</div>
          <div class=hideit id=ket_artikel$id>
            <div class='kecil abu'>DO: $kode_do</div>
            $ul_artikel
          </div>
        </div>
      </td>
      <td>
        <div>
          <a target=_blank href='?master_penerimaan&cat=$cat&do=$d[kode_po]&id=&waktu=all_time'>
            $d[kode_po]
          </a>
        </div>
        <div class='kecil abu'>$d[nama_supplier]</div>
      </td>
      <td>
        <div class='abu f12 mb1'>
          <span class=miring>id.$d[id_sj_subitem]</span> ~ 
          <a target=_blank href='?penerimaan&p=manage_sj_subitem&id_sj_item=$d[id_sj_item]&id_sj_subitem=$d[id_sj_subitem]'>
            Lot: $no_lot 
          </a> ~ $d[kode_lokasi]
        </div>
        <div>
          <a target=_blank href='?master&p=barang&keyword=$d[kode_barang]'>
            $d[kode_barang] 
          </a>
          $fs_show 
          <span class=btn_aksi id=id_ket$id"."__toggle>$img_detail</span>

        </div>
        <div id=id_ket$id class=hideit>
          <ul class='kecil abu m0 p0 pl3'>
            <li>$d[nama_barang]</li>
            <li>$d[keterangan_barang]</li>
            <li>Lokasi: $d[kode_lokasi]</li>
            <li>Brand: $d[brand]</li>
            <li>Roll count: $d[count_roll]</li>
          </li>
        </div>
      </td>
      <td>
        <div class=btn_aksi id=detail_qty$id"."__toggle>
          <a target=_blank href='?retur&id_sj_item=$id_sj_item'>$qty_real</a> $img_detail
        </div>
        <div class='hideit wadah br5 kecil mt1' id=detail_qty$id>
          <div>QC: $qty_diterima_with_qc_no_fs</div>
          <div>QC-FS: $qty_fs</div>
          <div>Retur: $qty_retur</div>
          <div>Balik: $qty_balik</div>
        </div>

      </td>
      <td>
        <div>$pick_allocate</div>
      </td>
      <td>
        <div class=darkred>$qty_pick_by</div>
      </td>
      <td>
        <div>$stok_akhir_show</div>
      </td>
    </tr>
  ";
}






$form_cari = "
  <form method=post>
    <tr>
      <td class=kecil>$clear_filter</td>
      <td><input type='text' class='form-control form-control-sm $bg_do' placeholder='DO / Artikel' name=filter_do value='$filter_do' ></td>
      <td><input type='text' class='form-control form-control-sm $bg_id' placeholder='PO / ID' name=filter_id value='$filter_id' ></td>
      <td>
        <select class='form-control form-control-sm $bg_waktu' name=filter_waktu>$opt_waktu</select>
      </td>
      <td>
        <button class='btn btn-success btn-sm' name=btn_cari>Cari</button>
      </td>
    </tr>
  </form>

";

$bread = "<li class='breadcrumb-item'><a href='?master_pengeluaran&cat=fab'>Fabric</a></li><li class='breadcrumb-item active'>Aksesoris</li>";
if($cat=='fab')
$bread = "<li class='breadcrumb-item'><a href='?master_pengeluaran&cat=aks'>Aksesoris</a></li><li class='breadcrumb-item active'>Fabric</li>";
?>

<div class="pagetitle">
  <h1>Master Pengeluaran <?=$jenis_barang?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?pengeluaran">Pengeluaran</a></li>
      <?=$bread?>
    </ol>
  </nav>
</div>


<section class='section'>
  <div id="blok_pengeluaran" class='mt2'>

    <table class='table'>
      <?=$form_cari?>
      <tr>
        <td colspan=100%><span class="darkblue"><?=$jumlah_row?></span> <span class="abu kecil">data dari <b><?=$total_row ?></b> records</span></td>
      </tr>
      <tr class='tebal gradasi-hijau '>
        <td>Artikel</td>
        <td>PO / Supplier</td>
        <td>ID / Item / Keterangan</td>
        <td>QTY Subitem</td>
        <td>Pick/Allocate</td>
        <td class=darkred>Pick by<br><span class=f12>Other DO</span></td>
        <td>Stok Akhir</td>
      </tr>
      <?=$tr_hasil?>



    </table>

  </div>
</section>