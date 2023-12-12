<?php
$pesan_login = '<p>Warehouse Management System </p>';
$username = '';
$password = '';


?>
<style>
  .full{
    display:flex;
    height: 100vh;
  }
  .form-login{
    max-width: 400px;
    margin:auto;
  }
</style>
<div class="full" data-aos='fade-up'>
  <div class="wadah gradasi-biru form-login p-4">
    <h3>Login</h3>
    <?php include 'login_process.php'; ?>
    <?=$pesan_login?>
    <hr>
    <form method="post">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" minlength=2 maxlength=50 required id="username" name="username" value="<?=$username?>">
      </div>

      <div class="form-group">
        <label for="username">Password</label>
        <input type="password" class="form-control" minlength=2 maxlength=50 required id="password" name="password" value="<?=$password?>">
      </div>

      <div class="form-group">
        <button class='btn btn-primary btn-block' name='btn_login_wms'>Login</button>
      </div>      
    </form>

    <div class="tengah mt3" data-aos="fade-up" data-aos-delay="300">Belum punya akun? Silahkan <a href="#" onclick='alert("Fitur Register belum tersedia. Silahkan hubungi developer.")'><b>Register</b></a></div>
    <div class="tengah mt3" data-aos="fade-up" data-aos-delay="300">Lupa password? <a href="#" onclick='alert("Fitur Reset Password belum tersedia. Silahkan hubungi developer.")'><b>Reset Password</b></a></div>

  </div>
</div>
