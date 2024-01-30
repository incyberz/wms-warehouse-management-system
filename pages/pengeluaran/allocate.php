<?php 
if(!in_array($id_role,[1,2,3,9])){
  // jika bukan petugas wh
  $pesan = div_alert('info',"<p>$img_locked Maaf, <u>hak akses Anda tidak sesuai</u> dengan fitur ini. Silahkan hubungi Pihak Warehouse jika ada kesalahan. terimakasih</p>");
  echo "
    <div class='pagetitle'>
      <h1>Proses Allocate</h1>
      <nav>
        <ol class='breadcrumb'>
          <li class='breadcrumb-item'><a href='?'>Home Dashboard</a></li>
        </ol>
      </nav>
    </div>
    $pesan
  ";

}else{


?>
<div class="pagetitle">
  <h1>Allocate/Packing</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?pengeluaran">Pengeluaran</a></li>
      <li class="breadcrumb-item"><a href="?pengeluaran&p=data_do">Data DO</a></li>
      <li class="breadcrumb-item"><a href="?pengeluaran&p=buat_do">Buat DO Baru</a></li>
      <li class="breadcrumb-item active">Allocate</li>
    </ol>
  </nav>
</div>

<?php
set_title('Allocate/Packing');
?>
<div class="alert alert-danger">Page ini masih dalam tahap pengembangan. Terimakasih.</div>


<?php } ?>