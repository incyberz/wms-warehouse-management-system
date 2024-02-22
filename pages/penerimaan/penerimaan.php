<span id="tb" class=hideit>po</span>
<?php
include 'podo_styles.php';
include 'pages/must_login.php';

$p = $_GET['p'] ?? '';
set_title('Penerimaan');

$kode_sj = $_GET['kode_sj'] ?? '';
$debug .= "<br>kode_sj: $kode_sj";

if(!in_array($id_role,[1,2,3,9])){
  // jika bukan petugas wh
  $pesan = div_alert('info',"<p>$img_locked Maaf, <u>hak akses Anda tidak sesuai</u> dengan fitur ini. Silahkan hubungi Pihak Warehouse jika ada kesalahan. terimakasih</p>");
  echo "
    <div class='pagetitle'>
      <h1>Proses Penerimaan</h1>
      <nav>
        <ol class='breadcrumb'>
          <li class='breadcrumb-item'><a href='?'>Home Dashboard</a></li>
        </ol>
      </nav>
    </div>
    $pesan
  ";

}else{
  // petugas WH only
  if($p!=''){
    if(file_exists("pages/penerimaan/$p.php")){
      include "$p.php";
    }else{
      include 'na.php';
    }
  }else{


?>

<!-- for btn_aksi -->

<div class="pagetitle">
  <h1>Proses Penerimaan</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?">Home Dashboard</a></li>
      <li class="breadcrumb-item active">Terima Purchase Order</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  <style type="text/css">
  #blok_step{
    /*background-color: #afa;*/
    /*background-image: url('img/alur_pmb.png');*/
  }

  #blok_steps{
    width: 100%;
    display: grid;
    grid-template-columns: 25% 25% 25% 25%;
  }

  #blok_steps div{
    /*border: solid 1px red;*/
    text-align: center;
    /* font-size: 14px; */
    /*margin-bottom: 20px;*/
  }

  .jumlah_podo{
    font-family: verdana;
    font-size: 25px;
    color: #555;
  }

  .jumlah_item_podo{
    color: #888;
    font-size: 14px;
  }
  .satuan_podo{
    color: #aaa;
    font-size: 12px;
  }
  </style>

  <div style='max-width:900px'>
    <div id="blok_steps">
      <?php 
      $arr[0] = ['Terima Barang','accepting','?penerimaan&p=terima_barang'];
      $arr[1] = ['Data Surat Jalan','po','?penerimaan&p=data_sj'];
      $arr[2] = ['Penempatan','putting','?penerimaan&p=penempatan'];
      $arr[3] = ['Manajemen Lokasi','positioning','?penerimaan&p=stok_gudang'];

      foreach ($arr as $key => $r) {
        echo "
          <div class='tengah'>
            <a href='$r[2]'>
            <div class=mb2>
              <img src='assets/img/icons/wms/$r[1].png' alt='terima-barang' height=80px>
            </div>
            $r[0]</a> 
          </div>
        ";
      }

      ?>
    </div>
  </div>
</section>

<?php }} ?>