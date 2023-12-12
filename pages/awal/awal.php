<div class="pagetitle">
  <h1>Awal Proses WMS</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?">Home Dashboard</a></li>
      <li class="breadcrumb-item"><a href="?stock_opname">Stock Opname</a></li>
      <li class="breadcrumb-item active">Set Stok Awal dan Tanggal Terima</li>
    </ol>
  </nav>
</div>

<?php

# =========================================
# SELECT MAIN DATA
# =========================================
$keyword = $_GET['keyword'] ?? '';
$sql_keyword = $keyword=='' ? '1' : "(a.kode like '%$keyword%' OR a.nama like '%$keyword%' )";

$sql_from = "FROM tb_barang a 
JOIN tb_satuan b ON a.satuan=b.satuan 
WHERE status=1 
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
LIMIT 10
";
// echo "<pre>$s</pre>";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$jumlah_show = mysqli_num_rows($q);

$tr = '';
$i = 0;
$tambah_id = '';
while($d=mysqli_fetch_assoc($q)){
  $i++;
  $id_barang = $d['id_barang'];

  $kode = $d['kode_barang'];
  $nama = $d['nama_barang'];
  $step = $d['step'];

  # ==============================================================
  # FINAL OUTPUT TR 
  # ==============================================================
  $tr .= "
    <tr id=tr_$id_barang >
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
        <input type=number class='form-control form-control-sm' step=$step>
      </td>
      <td>
        <input type=date class='form-control form-control-sm'>
      </td>
      <td>
        <input type=text class='form-control form-control-sm'>
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
? "<span class='f20 darkblue'>$jumlah_show</span> of $jumlah_row records" 
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
    <div>
      <button class='btn btn-sm btn-success btn_aksi' id=import_export__toggle>Import / Export</button>
    </div>
  </div>
  <div id=import_export class='wadah mb4 mt2 gradasi-hijau'>
    <div class=row>
      <div class='col-6'>

        <div class='wadah bg-white'>
          <h3>Import Stock Awal</h3>
          <p>Untuk import stock awal silahkan download dahulu template file CSV, lalu buka dengan Microsoft Excel, isi dan simpan, lalu upload via form berikut: </p>
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
          <h3>Import Sub Item Awal</h3>
          <p>Jika Stock Awal punya Sub Item, Anda boleh download dahulu template file CSV Subitem, lalu buka dengan Microsoft Excel, isi dan simpan, lalu upload via form berikut: </p>
          <form method=post enctype='multipart/form-data'>
            <div class=flexy>
              <div>
                <input class='form-control form-control-sm' type=file name=file_csv_subitem required accept='.csv'>
              </div>
              <div>
                <button class='btn btn-sm btn-success' name=btn_import_subitem>Import Subitem</button>
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
          <h3>Export Subitem Awal</h3>
          <div>Berikut adalah Stock Data Awal per sub item barang dalam bentuk file CSV.</div>
          <div class='mt2 mb1 abu miring kecil'>999 records</div>
          <button class='btn btn-sm btn-success w-100' name=btn_export>Export Subitem</button>
        </div>
      </div>

    </div>

  </div>
  <table class='table table-hover mt2'>
    <thead class='upper gradasi-toska'>
      <th>NO</th>
      <th colspan=2>BARANG</th>
      <th>QTY AWAL</th>
      <th>TANGGAL TERIMA</th>
      <th>LOKASI RAK</th>
    </thead>
    $tr
  </table>
";


















?><script>
  $(function(){
    $('#input_cari').keyup(function(e){
      let keyword = $(this).val();
      let href = keyword.length>2 ? `?awal&p=set_stok_awal&keyword=${keyword}` : '';

      if(e.keyCode == 13 && keyword.length>2){
        // alert('Enter')
        location.replace(href);
      }
    })
  })
</script>