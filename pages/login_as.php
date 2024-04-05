
<?php
$judul = 'Login As';
set_title($judul);
echo "<h2>Login As</h2><p>Digunakan khusus oleh admin agar dapat login sebagai username lain. Admin tetap dibatasi hak aksesnya agar tidak mengubah properti pick_by, allocate_by, dan properti private lainnya. Fitur ini hanya diperuntukan pada Mode Testing dan khusus untuk admin.</p>";

if ($username == 'admin') {
  $get_username = $_GET['username'] ?? '';
  if ($get_username) {
    $s = "SELECT * FROM tb_user WHERE kode='$get_username' ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    if (mysqli_num_rows($q)) {
      // set session
      $_SESSION['wms_master_username'] = $username;
      $_SESSION['wms_username'] = $get_username;
      jsurl();
    } else {
      echo div_alert('danger', "Username $get_username tidak ada.");
    }
  } else { // get_username masih kosong
    $s = "SELECT * FROM tb_user WHERE kode!='admin' order by kode ";
    $q = mysqli_query($cn, $s) or die(mysqli_error($cn));
    while ($d = mysqli_fetch_assoc($q)) {
      echo "<a class='btn btn-success' href='?login_as&username=$d[kode]' onclick='return confirm(\"Yakin Login As $d[kode]?\")'>As $d[kode]</a> ";
    }
  }
} else {
  if (isset($_SESSION['wms_master_username'])) {
    $get_unlog = $_GET['unlog'] ?? '';
    if ($get_unlog) {
      echolog('unlogging as...');
      $_SESSION['wms_username'] = $_SESSION['wms_master_username'];
      unset($_SESSION['wms_master_username']);
      jsurl();
    } else {
      echo div_alert('info', "And sudah login As $username<hr>Unlog As berada pada Menu User > Unlog As | <a href='?login_as&unlog=1'>Unlog As Now</a>");
    }
  } else {
    echo div_alert('danger', 'Maaf, hanya admin yang berhak mengakses fitur ini.');
  }
}

?>