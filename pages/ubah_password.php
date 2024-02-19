<?php
// echo "d_peserta[pass]: $d_peserta[password] post[password]: $_POST[password]";
$hideit='';
$password_baru='';
$cpassword_baru='';
$password_lama='';
// $depas_note = $is_depas?div_alert('warning','Password Anda masih default (masih kosong atau sama dengan Username). Anda wajib mengubahnya untuk meningkatkan keamanan akun Anda.'):'Silahkan Anda ubah password:';

if(isset($_POST['btn_ubah_password'])){
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';

  $password_baru = $_POST['password_baru'];
  $cpassword_baru = $_POST['cpassword_baru'];

  echo '<div class="consolas"><br>checking length of passwords... ';
  foreach ($_POST as $key => $value) if(strlen($value)>20) die("Format $key melebihi 20 karakter");
  echo 'accepted';

  echo "<br>checking validitas password baru dg username... ";
  $d = mysqli_fetch_assoc($q);
  if($_POST['password_baru']!=$username){
    echo 'valid';
    echo "<br>checking validitas password baru dg konfirmasinya... ";
    if($_POST['password_baru']==$_POST['cpassword_baru']){
      echo 'sama';
      echo '<br>checking current password (password lama) at database... ';
      $s = "SELECT password FROM tb_user WHERE kode='$username' AND password=md5('$_POST[password_lama]')";
      $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
      if(mysqli_num_rows($q)){
        echo 'benar';
        echo '<br>updating password (dengan password baru Anda)... ';
        
        $s = "UPDATE tb_user SET password=md5('$_POST[password_baru]') WHERE kode='$username'";
        // echo $s;
        $q=mysqli_query($cn,$s) or die(mysqli_error($cn));
        echo 'success';
        // echo '<script>location.replace("?")</script>';
        unset($_SESSION['wms_username']);
        echo '
          <div class="alert alert-success mt2">
            Ubah Password berhasil. Silahkan Anda relogin dengan password baru Anda!
            <hr>
            <a href="?login" class="btn btn-primary btn-block">Relogin</a>
          </div>
        ';
        exit;
        
      }else{
        echo div_alert('danger','Maaf, password lama Anda tidak sesuai ');
      }
    }else{
      echo div_alert('danger', 'Konfirmasi password tidak sama dengan password baru.');
    }
  }else{
    echo div_alert('danger', 'Password baru Anda tidak boleh sama dengan Username.');
  }




  echo '</div><hr>'; //end consolas
}


?>

<div class="section-title">
  <h1>Ubah Password</h1>
  <p>Silahkan ubah password Anda dengan yang mudah diingat.</p>
</div>

<div class="wadah">
  <form method="post">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" class="form-control" id="username" value="<?=$username?>" disabled>
      <input type="hidden" value="<?=$username?>" name="username">
    </div>

    <div class="form-group">
      <label for="password_lama">Password Anda saat ini</label>
      <input required type="password" minlength=2 maxlength=20 class="form-control" id="password_lama" name="password_lama" value="<?=$password_lama?>">
      <div class="f12 abu miring mb4">silahkan masukan password Anda saat ini</div>
    </div>

    <div class="wadah gradasi-kuning">
      <div class="form-group">
        <label for="password_baru">Password Baru</label>
        <input required type="password" minlength=3 maxlength=20 class="form-control" id="password_baru" name="password_baru" value="<?=$password_baru?>">
      </div>
  
      <div class="form-group">
        <label for="cpassword_baru">Konfirmasi Password Baru</label>
        <input required type="password" minlength=3 maxlength=20 class="form-control" id="cpassword_baru" name="cpassword_baru" value="<?=$cpassword_baru?>">
        <div class="f12 abu miring">harus sama dengan password baru Anda</div>
      </div>

    </div>

    <div class="form-group">
      <button class="btn btn-primary btn-block" name="btn_ubah_password">Ubah Password</button>
    </div>


  </form>
</div>