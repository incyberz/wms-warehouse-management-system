<?php

if(!in_array($id_role,[1,2,3,9])){
  // jika bukan petugas wh
  $pesan = div_alert('info',"<p>$img_locked Maaf, <u>hak akses Anda tidak sesuai</u> dengan fitur ini. Silahkan hubungi Pihak Warehouse jika ada kesalahan. terimakasih</p>");
  echo "
    <div class='pagetitle'>
      <h1>Proses Retur</h1>
      <nav>
        <ol class='breadcrumb'>
          <li class='breadcrumb-item'><a href='?'>Home Dashboard</a></li>
        </ol>
      </nav>
    </div>
    $pesan
  ";

}else{


# ========================================================
# PETUGAS WH ONLY
# ========================================================
$pesan_tambah = '';
$today = date('Y-m-d');
$now = date('Y-m-d H:i:s');
$now_eng = date('M d, Y, H:i:s');


if(isset($_POST['btn_terima_retur'])){
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';
  echo 'Processing penerimaan retur...<hr>';
  $koloms = 'id,qty,tanggal_terima';
  $values = "$_POST[id_retur],$_POST[qty_balik],CURRENT_TIMESTAMP";
  $pairs = "qty=$_POST[qty_balik],tanggal_terima=CURRENT_TIMESTAMP";
  $s = "INSERT INTO tb_terima_retur ($koloms) VALUES ($values) ON DUPLICATE KEY UPDATE $pairs ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $arr = explode('?',$_SERVER['REQUEST_URI']);
  jsurl("?$arr[1]");
  exit;
}

if(isset($_POST['btn_retur']) || isset($_POST['btn_hapus_qc'])){

  if(isset($_POST['btn_hapus_qc'])){
    // echo '<pre>';
    // var_dump($_POST);
    // echo '</pre>';
    echo 'Deleting data retur...<hr>';
    $s = "DELETE FROM tb_retur WHERE id = $_POST[id] ";
  }else{
    unset($_POST['btn_retur']);
    echo 'Processing data retur...<hr>';
  
    $pairs = '__';
    $koloms = '__';
    $values = '__';
    foreach ($_POST as $key => $value) {
      $value = clean_sql($value);
      $value = ($value==''||$value=='-') ? 'NULL' : "'$value'";
      if($key!='id') $pairs .= ",$key = $value";
      $koloms .= ",$key";
      $values .= ",$value";
    }
    $pairs = str_replace('__,','',$pairs);
    $koloms = str_replace('__,','',$koloms);
    $values = str_replace('__,','',$values);
  
    $s = "INSERT INTO tb_retur ($koloms) VALUES ($values) ON DUPLICATE KEY UPDATE $pairs ";
  }

  // echo $s;
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

  $arr = explode('?',$_SERVER['REQUEST_URI']);
  jsurl("?$arr[1]");
  exit;
}


$id_sj_item = $_GET['id_sj_item'] ?? die(erid('id_sj_item'));
$s = "SELECT a.*,
a.id as id_sj_item,
a.kode_sj,
b.kode_po,
a.qty as qty_po,
c.id as id_bbm,
c.kode as no_bbm,
c.tanggal_masuk,
d.nama as nama_barang,
d.kode as kode_barang,
d.keterangan as keterangan_barang,
d.satuan,
e.kode as kategori,
e.nama as nama_kategori,
f.step,
(
  SELECT SUM(qty) FROM tb_sj_kumulatif WHERE id_sj_item=a.id and is_fs is null) qty_kumulatif_item,
(
  SELECT SUM(qty) FROM tb_sj_kumulatif WHERE id_sj_item=a.id and is_fs is not null) qty_kumulatif_item_fs

FROM tb_sj_item a 
JOIN tb_sj b ON a.kode_sj=b.kode 
JOIN tb_bbm c ON b.kode=c.kode_sj 
JOIN tb_barang d ON a.kode_barang=d.kode 
JOIN tb_kategori e ON d.id_kategori=e.id 
JOIN tb_satuan f ON d.satuan=f.satuan 
WHERE a.id=$id_sj_item 
";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
if(mysqli_num_rows($q)==0) die(div_alert('danger',"Data item dg id_sj_item: $id_sj_item tidak ditemukan. | 
<a href='?master_penerimaan&id=&waktu=all_time'>Akses dari Master Penerimaan</a>"));
$d = mysqli_fetch_assoc($q);

$nama_barang = $d['nama_barang'];
$kode_barang = $d['kode_barang'];
$keterangan_barang = $d['keterangan_barang'];
$id_sj_item = $d['id_sj_item'];
$kode_po = $d['kode_po'];
$kode_sj = $d['kode_sj'];
$no_bbm = $d['no_bbm'];
$qty_po = $d['qty_po'];
$qty_datang = $d['qty_datang'];
$qty_kumulatif_item = $d['qty_kumulatif_item'];
$qty_kumulatif_item_fs = $d['qty_kumulatif_item_fs'];
$satuan = $d['satuan'];
$step = $d['step'];
// $pengiriman_ke = $d['pengiriman_ke'];
$kategori = $d['kategori'];
$nama_kategori = $d['nama_kategori'];
$tanggal_masuk = $d['tanggal_masuk'];

$qty_po = floatval($qty_po);
$qty_datang = floatval($qty_datang);
$qty_kumulatif_item = floatval($qty_kumulatif_item);
$qty_kumulatif_item_fs = floatval($qty_kumulatif_item_fs);


$nama_kategori = ucwords(strtolower($nama_kategori));

$is_lebih = $qty_po<$qty_datang ? 1 : 0;
$qty_fs = 0;
if($is_lebih){
  $qty_fs = $qty_datang-$qty_po;
  $tr_free_supplier = "
    <tr class=blue>
      <td>QTY Lebih (Free Supplier)</td>
      <td>$qty_fs $satuan</td>
    </tr>
  ";

}else{
  $tr_free_supplier = '';
}
# =======================================================================
# PAGE TITLE & BREADCRUMBS 
# =======================================================================
set_title('Retur Barang');
?>
<div class="pagetitle">
  <h1>Retur Barang</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?penerimaan">Penerimaan</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=data_sj">Data SJ</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=manage_sj&kode_sj=<?=$kode_sj?>">Manage SJ</a></li>
      <li class="breadcrumb-item"><a href="?penerimaan&p=bbm&kode_sj=<?=$kode_sj?>">BBM</a></li>
      <li class="breadcrumb-item"><a href="?master_penerimaan&&id=<?=$kode_barang?>&waktu=all_time">Master Penerimaan</a></li>
      <li class="breadcrumb-item active">Retur</li>
    </ol>
  </nav>
</div>
<p>Page ini digunakan untuk Proses Retur Barang.</p>


<?php
# =======================================================================
# ITEM BARANG INFO 
# =======================================================================
?>
<h2>Info Penerimaan</h2>
<table class="table table-hover">
  <tr>
    <td>Surat Jalan</td>
    <td><?=$kode_sj?></td>
  </tr>
  <tr>
    <td>Nomor BBM</td>
    <td><?=$no_bbm?></td>
  </tr>
  <?=$tr_free_supplier?>
  <tr>
    <td>QTY Item Kumulatif</td>
    <td>
      <span id="qty_kumulatif_item"><?=$qty_kumulatif_item?></span> <?=$satuan?> 
    </td>
  </tr>

</table>

<?php
# =======================================================================
# RETUR ITEM 
# =======================================================================
echo "<h2 class='mb3 mt4'>Retur Item: $kode_barang | $nama_kategori</h2>";

$get_id_sj_item = $_GET['id_sj_item'] ?? '';

if($get_id_sj_item!=''){

  $s = "SELECT a.*,
  c.kode as kode_barang,
  c.nama as nama_barang,
  c.keterangan as keterangan_barang,
  d.satuan,
  d.step,
  (SELECT qty FROM tb_retur WHERE id=a.id) qty_retur,
  (SELECT qty FROM tb_terima_retur WHERE id=a.id) qty_balik,
  (SELECT metode_qc FROM tb_retur WHERE id=a.id) metode_qc,
  (SELECT alasan_retur FROM tb_retur WHERE id=a.id) alasan_retur,
  (SELECT tanggal_retur FROM tb_retur WHERE id=a.id) tanggal_retur,
  1
  FROM tb_sj_kumulatif a 
  JOIN tb_sj_item b ON a.id_sj_item=b.id 
  JOIN tb_barang c ON b.kode_barang=c.kode   
  JOIN tb_satuan d ON c.satuan=d.satuan   
  WHERE a.id_sj_item = $id_sj_item
  -- AND a.is_fs is null
  ";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)==0) die('Item ini tidak punya subitem');
  $tr = '';
  $i = 0;
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $qty = floatval($d['qty']);
    $qty_retur = floatval($d['qty_retur']);
    $qty_balik = floatval($d['qty_balik']);
    $id = $d['id'];
    $satuan = $d['satuan'];
    $is_fs = $d['is_fs'];
    $kode_lokasi = $d['kode_lokasi'];
    $no_lot = $d['no_lot'] ?? '-';
    $no_roll = $d['no_roll'] ?? '-';
    $metode_qc = $d['metode_qc'] ?? '-';
    $alasan_retur = $d['alasan_retur'] ?? '-';
    $tanggal_retur = $d['tanggal_retur'] ?? $now;

    

    $qty_retur_show = $qty_retur ? "<div class='darkred miring'>Retur: $qty_retur </div>" : "<div class='green'>No Retur $img_check</div>";
    $qty_retur_show = $d['qty_retur']=='' ? "<div class='red'>Belum QC $img_warning</div>" : $qty_retur_show;

    $qty_balik_show = $qty_balik ? "<div class='darkred miring'>Balik: $qty_balik</div>" : '';
    $qty_stok = $qty - $qty_retur + $qty_balik;

    $gradasi = $qty_retur ? 'kuning' : '';
    $gradasi = $qty_retur==0 ? 'hijau' : $gradasi;
    $gradasi = $d['qty_retur']=='' ? 'merah' : $gradasi;

    $fs_show = $is_fs ? ' <b class="f14 ml1 mr1 biru p1 pr2 br5" style="display:inline-block;background:green;color:white">FS</b>' : '';

    $tr .= "
      <tr class='gradasi-$gradasi tr_retur' id=tr_retur__$id>
        <td>$i</td>
        <td>
          $d[kode_barang]
          <div class='abu f12'>$d[nama_barang]</div>
        </td>
        <td>$d[kode_lokasi] / $no_lot / $no_roll</td>
        <td>
          <span id=qty__$id>$qty</span> $satuan $fs_show
        </td>
        <td>
          $qty_retur_show
          $qty_balik_show
          <div class=hideit>qty_retur:<span id=qty_retur__$id>$d[qty_retur]</span></div>
          <div class=hideit>qty_balik:<span id=qty_balik__$id>$qty_balik</span></div>
          
        </td>
        <td>
          <span class='btn btn-warning btn-sm btn_retur' id=retur__$id>QC & Retur</span>
          <div class=hideit>
            <div>id: <span id=id__$id>$d[id]</span></div>
            <div>metode_qc: <span id=metode_qc__$id>$metode_qc</span></div>
            <div>alasan_retur: <span id=alasan_retur__$id>$alasan_retur</span></div>
            <div>tanggal_retur: <span id=tanggal_retur__$id>$tanggal_retur</span></div>
          </div>
        </td>
        <td>
          $qty_stok $satuan
        </td>
      </tr>
    ";
    // $id=$d['id'];
  }


  echo "
    <table class=table>
      <thead>
        <th>No</th>
        <th>ID</th>
        <th>Lokasi / Lot / Roll</th>
        <th>QTY Item Kumulatif</th>
        <th>QTY Retur</th>
        <th>QC & Retur</th>
        <th>Stok Terima</th>
      </thead>
      $tr
    </table>
  
    <div class='row'>
      <div class=col-md-6>
        <div class='wadah mt2 hideit gradasi-hijau' id=form_retur>
          <form method=post>
            <div class='flexy flex-between'>
              <div>
                <h3 class=f18 darkblue mb3>Hasil QC</h3>
              </div>
              <div>
                <span class=btn_cancel>$img_close</span>
              </div>
            </div>
            <input type='hidden' name=id id=id>
            QTY Retur ($satuan) $bintang 
            <input class='elemen_retur form-control mb1' type=number min=0 max=0 step=$step required name=qty id=qty>
            <div class='mb2 abu f12 mt1'><b>Catatan:</b> Masukan nilai 0 jika Hasil QC menyatakan item bagus semua</div>


            Metode QC
            <input class='elemen_retur form-control mb2' maxlength=100 name=metode_qc id=metode_qc value='-'>
            Alasan Retur
            <input class='elemen_retur form-control mb2' maxlength=100 name=alasan_retur id=alasan_retur value='-'>
            Tanggal QC / Retur
            <input type=datetime class='elemen_retur form-control mb2' value='' name=tanggal_retur id=tanggal_retur>
            <div class=mt3>
              <button class='elemen_retur btn btn-primary' name=btn_retur id=btn_retur >Simpan QC</button> 
              <button class='elemen_retur btn btn-danger' name=btn_hapus_qc id=btn_hapus_qc >Hapus Data QC</button> 
              <div class='mb2 abu f12 mt1'><b>Catatan:</b> Tidak bisa update retur jika sudah ada Penerimaan Retur untuk retur ini. Nol kan QTY balik terlebih dahulu untuk mengubah data retur ini.</div>
            </div>
          </form>
        </div>
      </div>


      <div class=col-md-6>
        <div class='wadah mt2 hideit' id=form_terima_retur>
          <form method=post>
            <input type='hidden' name=id_retur id=id_retur>
            QTY Terima Retur ($satuan) $bintang 
            <input class='form-control mb1' type=number min=0 max=0 step=$step required name=qty_balik id=qty_balik>
            <div class='mb2 abu f12'><b>Catatan:</b> Max QTY Terima Retur sama dengan QTY Retur.</div>
            Tanggal Terima Retur
            <input type=text class='form-control mb2' value='$now_eng' disabled>
            <div class=mt3>
              <button class='btn btn-primary' name=btn_terima_retur id=btn_terima_retur >Terima Retur</button> 
              <span class='btn btn-danger btn_cancel'>Cancel</span>
            </div>
          </form>

        </div>
      </div>
    </div>
  ";
}

?>
<script>
  $(function(){
    $('.btn_cancel').click(function(){
      $('.tr_retur').fadeIn();
      $('#form_retur').slideUp();
      $('#form_terima_retur').slideUp();
    })
    $('.btn_retur').click(function(){
      let tid = $(this).prop('id');
      let rid = tid.split('__');
      let aksi = rid[0];
      let id = rid[1];

      $('.tr_retur').hide();
      $('#tr_retur__'+id).show();
      $('#form_retur').slideDown();


      
      let this_qty = $('#qty__'+id).text();
      let qty_retur = $('#qty_retur__'+id).text();
      let qty_balik = $('#qty_balik__'+id).text();

      if(qty_retur>0){
        // console.log(qty_retur);
        $('#form_terima_retur').slideDown();
      }


      $('#qty').prop('max',this_qty)
      $('#qty_balik').prop('max',qty_retur)
      $('#id').val(id)
      $('#id_retur').val(id)
      $('#alasan_retur').val( $('#alasan_retur__'+id).text());
      $('#metode_qc').val( $('#metode_qc__'+id).text());
      $('#tanggal_retur').val( $('#tanggal_retur__'+id).text());

      $('#qty').val(qty_retur);
      if(qty_retur!=''){
        $('#btn_retur').text('Update QC');
      }else{
        // $('#qty').val('');
        $('#btn_retur').text('Simpan QC');
      }

      if(qty_balik>0){
        $('#qty_balik').val(qty_balik);
        // console.log(qty_balik,$('#qty_balik').val());

        // $('#qty').prop('disabled',1);
        $('.elemen_retur').prop('disabled',1);
        $('#btn_terima_retur').text('Update QTY Balik');
      }else{
        $('.elemen_retur').prop('disabled',0);
        $('#qty_balik').val('');

      }
    });
  })
</script>

<?php } ?>