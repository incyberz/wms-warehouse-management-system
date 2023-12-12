<style>
.nama_proses{transition: .2s}
.nama_proses:hover{letter-spacing: 1px; backgrouand: linear-gradient(#fef,#fcf)}
</style>
<?php
$sub_judul[1] = 'Tahap Awal WMS';
$sub_judul[2] = 'Daily Process Penerimaan';
$sub_judul[3] = 'Daily Process Pengeluaran';

$btn_type[1] = 'info';
$btn_type[2] = 'primary';
$btn_type[3] = 'warning';

$arr_proses = [
  'stocking' => [
    1,
    'Set Stok Awal',
    'Mengisi stok awal untuk tiap barang yang ada di gudang',
    '?awal&p=set_stok_awal'
  ],
  'aging' => [
    1,
    'Set Tanggal Terima',
    'Mengisi tanggal terima untuk tiap sub-item barang agar diketahui Aging of Goods',
    '?awal&p=set_tanggal_terima'
  ],
  'positioning' => [
    1,
    'Set Posisi Awal Barang',
    'Mengisi keterangan Rak dan Gudang untuk tiap barang agar diketahui lokasinya melalui searching',
    '?awal&p=set_tanggal_terima'
  ],
  'stock-opname' => [
    1,
    'Stock Opname Awal',
    'Melihat stock dan aging untuk setiap barang dan atau setiap sub item barang di awal proses',
    '?awal&p=stock_opname'
  ],
  'po' => [
    2,
    'Terima Surat Jalan',
    'Menambahkan data PO, barang, supplier, dan data lain jika belum tersedia di database WMS',
    '?po&p=terima_barang'
  ],
  'accepting' => [
    2,
    'Penerimaan Barang',
    'Melakukan penimbangan ulang QTY sesuai dengan Surat Jalan yang diterima hingga mencetak BBM',
    '?po&p=terima_barang'
  ],
  'managing' => [
    2,
    'Manage Sub Item',
    'Mengalokasikan QTY diterima menjadi Sub Item berdasarkan kemasan, roll, atau nomor lot',
    '?po&p=terima_barang'
  ],
  'putting' => [
    2,
    'Pemilihan Rak',
    'Memilih rak yang tersedia untuk tiap-tiap Sub Item',
    '?po&p=terima_barang'
  ],
  'labeling' => [
    2,
    'Cetak Label',
    'Mencetak label untuk tiap-tiap Sub Item',
    '?po&p=terima_barang'
  ],
  'picking' => [
    3,
    'Picking List',
    'Memilih barang-barang yang tersedia untuk dikirim ke proses produksi',
    '?zzz'
  ],
  'packing' => [
    3,
    'Proses Packing',
    'Recek QTY untuk setiap barang yang akan dikirimkan',
    '?zzz'
  ],
  'shipping' => [
    3,
    'Cetak Surat Jalan',
    'Mencetak Surat Jalan untuk setiap Delivery Order',
    '?zzz'
  ],
];

$div = '';
$last_group = '';
foreach ($arr_proses as $key => $proses) {
  if($last_group!=$proses[0]){
    $end_div = $proses[0]==1 ? '' : '</div><div class=row>';
    $sub = "$end_div<h2 class='mt4 darkblue mb4'>".$sub_judul[$proses[0]].'</h2>';
  }else{
    $sub = '';
  }

  $btn = $btn_type[$proses[0]];

  $div .= "
    $sub
    <div class='col-sm-3'>
      <div class='wadah tengah'>
        <a href='$proses[3]'>
          <img src='assets/img/icons/wms/$key.png' alt='$proses[1]' height=80px class=zoom>
          <button class=' btn btn-$btn w-100 btn-sm mt3 nama_proses'>$proses[1]</button>
        </a>
        <div class='kecil abu'>$proses[2]</div>
      </div>
    </div>
  "; 
  $last_group = $proses[0];
}

echo $div;
?>

