<?php
if($is_login) die('<script>location.replace("?")</script>');
if(isset($_POST['btn_login'])){

  $username = $_POST['username'];
  $password = $_POST['password'];

  $s = "SELECT * FROM tb_user WHERE username='$username' AND password='$password'";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  if(mysqli_num_rows($q)){
    $d=mysqli_fetch_assoc($q);
    $_SESSION['lshop_username'] = $d['username'];
    // echo "$d[username]";
    die('<script>location.replace("?")</script>');
  }else{
    echo "<script>alert('Maaf, username atau password tidak tepat.')</script>";
  }

}
?>


<section class='section'>
  <div class="section-title">
    <h2>Login</h2>
    <p>Silahkan masukan username dan password Anda!</p>
  </div>
  <div id=blok_login>
    <form method=post>
      <input type="text" class="mb-2 form-control" name=username minlenth=3 maxlength=20 required placeholder=username value='<?=$username?>'>
      <input type="password" class="mb-2 form-control" name=password minlenth=3 maxlength=20 required placeholder=password>
      <button class="btn btn-primary w-100" name=btn_login>Submit</button>
      <div class="kecil tengah mt-2">Belum punya akun? <a href="?register">Register</a></div>
    </form>
  </div>
</section>