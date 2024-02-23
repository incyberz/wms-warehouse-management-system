<?php
session_start();
# ================================================
# PHP INDEX
# ================================================
$dm = 0;
$debug = '';
$unset = '<span class="kecil miring red consolas">unset</span>';
$unverified = '<span class="kecil miring red consolas" onclick="alert(\'Proses verifikasi harus mengikuti alur pengisisan data.\')">unverified</span>';
$null = '<span class="kecil miring red consolas">null</span>';
$hideit = 'hideit';
$view_mode = $_GET['view_mode'] ?? '';
$arr_kategori[1] = 'Aksesoris';
$arr_kategori[2] = 'Fabric';
$arr_cat[1] = 'aks';
$arr_cat[2] = 'fab';

// set auto login
$_SESSION['wms_username'] = 'wh';

// set logout
// unset($_SESSION['wms_username']);


// include 'pages/login.php';


# ================================================
# DATA SESSION
# ================================================
$id_user = '';
$is_login = 0;
$id_role = 0; // pengunjung
$sebagai = 'Pengunjung';
$username = '';
$nama_user = ''; 
$email = ''; 
$no_wa = ''; 

if(isset($_SESSION['wms_username'])){
  $is_login = 1;
  $username = $_SESSION['wms_username'];
}



# ================================================
# KONEKSI KE MYSQL SERVER
# ================================================
include 'conn.php';

# ================================================
# USER DATA IF LOGIN
# ================================================
include 'data_user.php';



# ================================================
# INCLUDES
# ================================================
include 'include/insho_functions.php';
include 'include/wms_functions.php';
include 'include/data_perusahaan.php';
include 'include/crud_icons.php';
include 'include/arr_master.php';
include 'include/arr_sheet.php';


# ================================================
# GET URL PARAMETER
# ================================================
$parameter = '';
foreach ($_GET as $key => $value) {
  $parameter = $key;
  break;
}

# ================================================
# LOGOUT
# ================================================
if($parameter=='logout'){
  include 'pages/login/logout.php';
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>WMS</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link rel="shortcut icon" href="assets/img/favicon2.png" type="image/x-icon">

  <!-- Google Fonts -->
  <!-- <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet"> -->

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <!-- <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet"> -->

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  <script src="assets/vendor/jquery/jquery-3.7.1.min.js"></script>

  <!-- =======================================================
  * Template Name: NiceAdmin - v2.5.0
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->

  <?php if(file_exists('../insho_styles.php')){
    include '../insho_styles.php'; 
  }else{
    include 'insho_styles.php'; 
  } 
  ?>
  <style>
    section{min-height: 100vh}
    h2{
      font-size: 16px; 
      background: linear-gradient(#efe,#cfc);
      font-weight: bold;
      color: #66a;
      padding: 7px;
      border-radius: 5px;
      box-shadow: 1px 1px 3px gray;
    }
    .formulir input,.formulir textarea {text-transform: uppercase}
    .bg-hijau{background:#0f0}
  </style>

</head>

<body>
  <?php 
  if(!$is_login){
    include 'pages/login/login.php';
  }else{
    if($view_mode!='cetak'){
      include 'pages/header.php'; 
      include 'pages/sidebar.php';
    } 
    ?>
    
    
    <main id="main" class="main">
      <?php 
        include 'routing.php';
        include 'debug.php';
      ?>
    </main>
    
    <?php if($view_mode!='cetak') include 'pages/footer.php'; ?>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="assets/vendor/chart.js/chart.umd.js"></script> -->
    <!-- <script src="assets/vendor/echarts/echarts.min.js"></script> -->
    <!-- <script src="assets/vendor/quill/quill.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script> -->

    <!-- Template Main JS File -->
    <script src="assets/js/main2.js"></script>
    <?php include 'include/js_btn_aksi.php'; ?>

  <?php 
  }

  # =======================================================
  # INCLUDES
  # =======================================================
  include 'include/editable_js.php';
  ?>

</body>

</html>