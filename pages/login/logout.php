<?php
if(isset($_GET['logout'])){
  // delete cookie

  // echo '<pre>';
  // var_dump($_SESSION);
  // echo '</pre>';

  echo 'logging out...';

  unset($_SESSION['wms_username']);
  unset($_SESSION['wms_role']);


  echo '<script>location.replace("?")</script>';
  exit;
}
