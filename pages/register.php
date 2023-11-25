<?php
if(isset($_POST['btn_register'])){

  $nama_user = $_POST['nama_user'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $cpassword = $_POST['cpassword'];

  if($password!=$cpassword){
    echo div_alert('danger', 'Konfirmasi password tidak sama dengan password. Silahkan coba lagi!');
  }else{

    $s = "INSERT INTO tb_user (nama_user, username, password) VALUES ('$nama_user', '$username', '$password') ";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));

    echo div_alert('success',"Register berhasil.<hr>Silahkan <a href='?login'>login</a> menggunakan username $username");
    exit;
  }


}
?>


<section class='section'>
  <div class="section-title">
    <h2>Register</h2>
    <p>Silahkan Register untuk melanjutkan berbelanja!</p>
  </div>
  <div id="blok_register">

    <form method=post>
      <input type="text" class="mb-2 form-control" name=nama_user minlenth=3 maxlength=50 required placeholder="Nama Anda" value='<?=$nama_user?>'>
      <input type="text" class="mb-2 form-control" name=username minlenth=3 maxlength=20 required placeholder="Username" value='<?=$username?>'>
      <input type="password" class="mb-2 form-control" name=password minlenth=3 maxlength=20 required placeholder="Password">
      <input type="password" class="mb-2 form-control" name=cpassword minlenth=3 maxlength=20 required placeholder="Konfirmasi Password">
      <button class="btn btn-primary w-100" name=btn_register>Register</button>
      <div class="kecil tengah mt-2">Sudah punya akun? <a href="?login">Login</a></div>
    </form>
  </div>
</section>