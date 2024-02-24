<?php
$cat = $_GET['cat'] ?? 'aks';
$nama_cat = $cat=='aks' ? 'Aksesoris' : 'Fabric';
echo "<span class=hideit id=cat>$cat</span>";

$cat2 = $cat=='aks' ? 'fab' : 'aks';
$nama_cat2 = $cat=='aks' ? 'Fabric' : 'Aksesoris';


?>
<div class="pagetitle">
  <h1>Stok Awal <?=$nama_cat?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?">Home Dashboard</a></li>
      <li class="breadcrumb-item"><a href="?kartu_stok">Kartu Stok</a></li>
      <li class="breadcrumb-item"><a href="?awal&p=set_stok_awal&cat=<?=$cat2?>">Stok Awal <?=$nama_cat2?></a></li>
      <li class="breadcrumb-item active">Stok Awal <?=$nama_cat?></li>
    </ol>
  </nav>
</div>

<?php

# =========================================
# SELECT MAIN DATA
# =========================================
$id_kategori = $cat=='aks' ? 1 : 2;
$keyword = $_GET['keyword'] ?? '';
$sql_keyword = $keyword=='' ? '1' : "(a.kode like '%$keyword%' OR a.nama like '%$keyword%' )";

$sql_from = "FROM tb_barang a 
JOIN tb_satuan b ON a.satuan=b.satuan 
WHERE status=1 
AND a.id_kategori = $id_kategori 
AND $sql_keyword 
";

$s = "SELECT 1 $sql_from ";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_row = mysqli_num_rows($q);

$s = "SELECT 
a.id as id_barang, 
a.kode as kode_barang, 
a.nama as nama_barang,
a.*,
b.step  

$sql_from 
ORDER BY a.kode 
LIMIT 5 
";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_show = mysqli_num_rows($q);

$tr = '';
$i = 0;
$tambah_id = '';
while($d=mysqli_fetch_assoc($q)){
  $i++;
  $id = $d['id_barang'];
  $kode = $d['kode_barang'];
  $nama = $d['nama_barang'];
  $step = $d['step'];
  $satuan = $d['satuan'];

  $s2 = "SELECT * FROM tb_stok_awal WHERE kode_barang='$kode'";
  $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
  if(mysqli_num_rows($q2)){

    $tr2 = '';
    $stok = 0;
    $total = 0;
    $cap_lot = '';
    $colspan_total_qty = 1;
    while($d2=mysqli_fetch_assoc($q2)){
      $kode_po=$d2['kode_po'];
      $kode_lokasi=$d2['kode_lokasi'];
      $stok=$d2['stok'];
      $tanggal_masuk=$d2['tanggal_masuk'];
      $no_lot = trim($d2['no_lot']);

      $stok = floatval($stok);
      $total += $stok;

      $td_lot = '';
      if($cat=='fab'){
        $td_lot = "<td><input type=text class='form-control form-control-sm' value='$no_lot'></td>";
        $cap_lot = "<td>No Lot</td>";
        $colspan_total_qty = 2;
      }
      

      $tr2.="
        <tr>
          <td>
            <input type=text class='form-control form-control-sm' value='$kode_po'>
          </td>
          $td_lot
          <td>
            <input type=number class='form-control form-control-sm' step=$step value='$stok'>
          </td>
          <td class='kecil abu'>
            <div style='padding:0 15px 0 5px'>$satuan</div>
          </td>
          <td>
            <input type=date class='form-control form-control-sm' value='$tanggal_masuk'>
          </td>
          <td>
            <input type=text class='form-control form-control-sm' value='$kode_lokasi'>
          </td>
        </tr>
      ";

    }

    if($total!=$stok){

      $tr_total = "
        <tr class='gradasi-kuning'>
          <td class='kecil darkblue p2 right' colspan=$colspan_total_qty>
            Total QTY
          </td>
          <td class='darkblue'>
            $total <span class='kecil abu'>$satuan</span>
          </td>
          <td colspan=3>
            &nbsp;
          </td>
        </tr>
      ";
    }else{
      $tr_total = '';
    }

    $stok_awal = "
      <table >
        <tr class='abu miring f12'>
          <td>PO</td>
          $cap_lot
          <td colspan=2>QTY Awal</td>
          <td>Tanggal Masuk</td>
          <td>Lokasi</td>
        </tr>
        $tr2
        $tr_total
      </table>    
    ";

  }else{
    $stok_awal = "<div class='kecil abu miring mb2'>Barang ini tidak punya Stok Awal</div>";
  }


  # ==============================================================
  # TAMBAH STOK AWAL 
  # ==============================================================
  // $id = id_barang
  $id_toggle = "form_tambah_$id".'__toggle';
  $stok_awal .= "<div class='pointer btn_aksi mt1' id=$id_toggle>$img_add</div>";
  $stok_awal .= "
    <div id=form_tambah_$id class='mt1 bordered br5 p2 bg-white hideit'>
      <form>
        <table>
          <tr>
            <td>
              <input type=text class='form-control form-control-sm' placeholder='PO' required>
            </td>
            <td>
              <input type=number class='form-control form-control-sm' step=$step placeholder='QTY' required>
            </td>
            <td class='kecil abu'>
              <div style='padding:0 15px 0 5px'>$satuan</div>
            </td>
            <td>
              <input type=date class='form-control form-control-sm' required>
            </td>
            <td>
              <input type=text class='form-control form-control-sm' placeholder='Lokasi' required>
            </td>
            <td>
              <button class='btn btn-sm btn-primary'>Save</button>
            </td>
          </tr>
        </table>
      </form>
    </div>
  ";



  # ==============================================================
  # FINAL OUTPUT TR 
  # ==============================================================
  $tr .= "
    <tr id=tr_$id >
      <td width=30px>$i</td>
      <td>
        <div>$kode</div>
        <div class='kecil abu'>
          $nama 
        </div>
      </td>
      <td>
        $img_detail
        $img_delete
      </td>
      <td>
        $stok_awal
      </td>

    </tr>
  ";
}

if($keyword==''){
  $info = "Belum ada data barang pada database.";
  $clear = '';
  $gradasi = '';
}else{
  $info = "Data barang dengan <b>filter: $keyword</b> tidak ditemukan. | <a href='?awal&p=set_stok_awal'>Clear Filter</a>";
  $gradasi = 'gradasi-kuning';
  $clear = "<div class=kecil><a href='?awal&p=set_stok_awal'>Clear</a></div>";
}

$record_show = $jumlah_row>$jumlah_show 
? "<span class='f20 darkblue'>$jumlah_show</span> data of $jumlah_row records" 
: "<span class='f20 darkblue'>$jumlah_row</span> records found";

echo $tr=='' ? div_alert('danger mt-4', $info) : "
  <div class=flexy>
    <div class='kecil miring abu mb1 ml1'>
      $record_show
    </div>
    <div>
      <input class='form-control form-control-sm $gradasi' placeholder='Cari ...' id=input_cari value='$keyword'>
    </div>
    $clear
    <div style=width:40px>&nbsp;</div>
    <div class='hideit zzz'>
      <button class='btn btn-sm btn-success btn_aksi' id=import_export__toggle>Import / Export</button>
    </div>
  </div>
  <div id=import_export class='wadah mb4 mt2 gradasi-hijau hideit'>
    <div class=row>
      <div class='col-6'>

        <div class='wadah bg-white'>
          <h3>Import Stock Awal</h3>
          <p>Untuk import stok awal silahkan download dahulu template file CSV, lalu buka dengan Microsoft Excel, isi dan simpan, lalu upload via form berikut: </p>
          <form method=post enctype='multipart/form-data'>
            <div class=flexy>
              <div>
                <input class='form-control form-control-sm' type=file name=file_csv required accept='.csv'>
              </div>
              <div>
                <button class='btn btn-sm btn-success' name=btn_import>Import</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class='col-6'>
        <div class='wadah bg-white'>
          <h3>Import Item Kumulatif Awal</h3>
          <p>Jika Stock Awal punya Item Kumulatif, Anda boleh download dahulu template file CSV Item Kumulatif, lalu buka dengan Microsoft Excel, isi dan simpan, lalu upload via form berikut: </p>
          <form method=post enctype='multipart/form-data'>
            <div class=flexy>
              <div>
                <input class='form-control form-control-sm' type=file name=file_csv_kumulatif_item required accept='.csv'>
              </div>
              <div>
                <button class='btn btn-sm btn-success' name=btn_import_kumulatif_item>Import Item Kumulatif</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class='col-6'>
        <div class='wadah bg-white'>
          <h3>Export Stock Awal</h3>
          <div>Berikut adalah Stock Data Awal pada WMS ini dalam bentuk file CSV.</div>
          <div class='mt2 mb1 abu miring kecil'>777 records</div>
          <button class='btn btn-sm btn-success w-100' name=btn_export>Export</button>
        </div>
      </div>

      <div class='col-6'>
        <div class='wadah bg-white'>
          <h3>Export Item Kumulatif Awal</h3>
          <div>Berikut adalah Stock Data Awal per sub item barang dalam bentuk file CSV.</div>
          <div class='mt2 mb1 abu miring kecil'>999 records</div>
          <button class='btn btn-sm btn-success w-100' name=btn_export>Export Item Kumulatif</button>
        </div>
      </div>

    </div>

  </div>
  <table class='table mt2 table-striped'>
    <thead class='upper gradasi-toska'>
      <th>NO</th>
      <th colspan=2>BARANG</th>
      <th>STOK AWAL</th>
    </thead>
    $tr
  </table>
";


















?><script>
  $(function(){
    $('#input_cari').keyup(function(e){
      let keyword = $(this).val();
      let cat = $('#cat').text();
      let href = keyword.length>2 ? `?awal&p=set_stok_awal&keyword=${keyword}&cat=${cat}` : '';

      if(e.keyCode == 13 && keyword.length>2){
        // alert('Enter')
        location.replace(href);
      }
    })
  })
</script>