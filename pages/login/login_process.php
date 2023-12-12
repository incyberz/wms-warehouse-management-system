<?php
if(isset($_POST['btn_login_wms'])){
  $username = clean_sql($_POST['username']);
  $password = clean_sql($_POST['password']);

  if(strlen($username)>20 || strlen($password)>20){
    $pesan_login = div_alert('danger','Maaf, format username dan password invalid. Silahkan coba kembali!');
  }else{
    // $sql_password = $username==$password ? 'password is null' : "password=md5('$password')";
    // $sql_password = $username==$password ? 'password is null' : "password='$password'";
    $sql_password = "password='$password'";
    $s = "SELECT 1 from tb_user WHERE kode='$username' and $sql_password";
    // echo $s;
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    if(mysqli_num_rows($q)==1){
      $d=mysqli_fetch_assoc($q);
      $_SESSION['wms_username'] = $username;
  
  
      echo 'Processing login...<script>location.replace("?")</script>';
      exit;
    }else{
      $pesan_login = div_alert('danger','Maaf, username dan password tidak tepat. Silahkan coba kembali!');
    }

  }

}