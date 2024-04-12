<?php
$p = $_GET['p'] ?? 'penerimaan';

$judul = 'Dashboard ' . ucwords($p);
set_title($judul);

$arr = ['penerimaan', 'pengeluaran', 'inventory'];
$breads = '';
foreach ($arr as $value) {
  if ($value != $p) {
    $breads .= "<li class='breadcrumb-item'><a href='?dashboard&p=$value' class='proper'>Dashboard $value</a></li>";
  } else {
    $breads .= "<li class='breadcrumb-item active proper'>Dashboard $value</li>";
  }
}
echo "
<div class='pagetitle'>
  <h1>$judul</h1>
  <nav>
    <ol class='breadcrumb'>
      $breads
    </ol>
  </nav>
</div>
";


if ($username == 'admin') {
  echo "
  <div class=wadah>
    <h2>Login As</h2>
    <p>Digunakan khusus oleh admin agar dapat login sebagai username lain.</p>
    <hr>
    <a class='btn btn-success' href='?login_as'>Login As</a>
  </div>
  ";
}

include "dashboard-$p.php";
