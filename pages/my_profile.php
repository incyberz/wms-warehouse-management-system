<div class="pagetitle">
  <h1>My Profile</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?">Dashboard</a></li>
      <li class="breadcrumb-item active">My Profile</li>
    </ol>
  </nav>
</div>

<?php include 'my_profile_upload_process.php'; ?>

<div class="wadah">
  <img src="<?=$src_profile?>" alt="user profile" class='mt-2 mb-2' style='height:100px;width:100px;object-fit:cover; border-radius:50%; box-shadow: 0 0 9px gray; padding: 3px'>
  <?php
  if($src_profile==$profile_na){
    $hide_form = '';
    $toggler = '';
  }else{
    $toggler = "<div class='pointer btn_aksi miring darkblue mb1' id=form_upload_profile__toggle>re-upload</div>";
    $hide_form = 'hideit';
  }
  
  ?>
  <?=$toggler?>
  <form method=post enctype='multipart/form-data' class='<?=$hide_form?>' id=form_upload_profile>
    <div class="flexy">
      <div><input type="file" class="form-control form-control-sm" name=profile required accept='image/jpeg'></div>
      <div><button class="btn btn-info btn-sm" name=btn_upload_profile>Upload</button></div>
    </div>
    
  </form>

</div>

<?php 
$s = "SELECT * FROM tb_user WHERE kode='$username'";
$q = mysqli_query($cn,$s) or die(mysqli_error($cn));
$d = mysqli_fetch_assoc($q);

$tr='';
foreach ($d as $key => $value) {
  if($key=='id'||$key=='password'||$key=='id_role') continue;
  $kolom = ucwords(str_replace('_',' ',$key));
  $value = $value ?? $null;
  $tr .= "
    <tr>
      <td>$kolom</td>
      <td>$value</td>
    </tr>
  ";
}

$tr_ha = '';
$resourcess = ['data master','penerimaan PO','PO item','bukti barang masuk','BBM subitem', 'cetak label'];
foreach ($resourcess as $key => $rsc) {
  $no = $key+1;
  $tr_ha .= "
    <tr>
      <td>$no</td>
      <td class=proper>$rsc</td>
      <td><input type=checkbox checked disabled></td>
      <td><input type=checkbox checked disabled></td>
      <td><input type=checkbox checked disabled></td>
      <td><input type=checkbox checked disabled></td>
      <td><input type=checkbox checked disabled></td>
      <td><input type=checkbox checked disabled></td>
    </tr>
  ";

}


?>

<div class="flexy mb2">
  <div class='miring pt1'>Role:</div>
  <div class='f20 tebal darkblue'><?=strtoupper($username)?> | <?=$jabatan?></div>
</div>

<div class="mb2 wadah">
  <div class="lead">Hak Akses</div>
  <table class="table">
    <thead>
      <th>No</th>
      <th>Resourcess</th>
      <th>Create</th>
      <th>Read</th>
      <th>Update</th>
      <th>Delete</th>
      <th>Upload</th>
      <th>Verifikasi</th>
    </thead>
    <?=$tr_ha?>
  </table>
</div>

<div class='alert alert-info'>
  
  <table class="table table-striped"><?=$tr?></table>
  
  <hr>
  <a href="?master&p=user&keyword=<?=$nama_user?>">Ubah Data User</a> | <a href="?ubah_password">Ubah Password</a>
</div>