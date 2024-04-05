<?php
$li_unlog = '';
$login_as_info = '';
if (isset($_SESSION['wms_master_username'])) {
  $login_as_info = '<span class=red>Login As</span> ';
  $li_unlog = "
    <li>
      <a class='dropdown-item d-flex align-items-center' href='?login_as&unlog=1' onclick='return confirm(\"Unlog As $username?\")'>
        <i class='bi bi-box-arrow-right'></i>
        <span>Unlog As</span>
      </a>
    </li>
  ";
}

?>
<li class="nav-item dropdown pe-3">

  <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
    <img src="<?= $src_profile ?>" alt="Profile" class="rounded-circle" style='height:30px;width:30px;object-fit:cover'>
    <span class="d-none d-md-block dropdown-toggle ps-2"><?= $login_as_info ?><?= $nama_user ?> | <?= $sebagai ?></span>
  </a><!-- End Profile Iamge Icon -->

  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
    <li class="dropdown-header">
      <h6><?= $nama_user ?></h6>
      <span><?= $jabatan ?></span>
    </li>
    <li>
      <hr class="dropdown-divider">
    </li>

    <li>
      <a class="dropdown-item d-flex align-items-center" href="?my_profile">
        <i class="bi bi-person"></i>
        <span>My Profile</span>
      </a>
    </li>
    <li>
      <hr class="dropdown-divider">
    </li>

    <!-- <li>
      <a class="dropdown-item d-flex align-items-center" href="#">
        <i class="bi bi-gear"></i>
        <span>Account Settings</span>
      </a>
    </li>
    <li>
      <hr class="dropdown-divider">
    </li>

    <li>
      <a class="dropdown-item d-flex align-items-center" href="#">
        <i class="bi bi-question-circle"></i>
        <span>Need Help?</span>
      </a>
    </li>
    <li>
      <hr class="dropdown-divider">
    </li> -->

    <?= $li_unlog ?>

    <li>
      <a class="dropdown-item d-flex align-items-center" href="?logout" onclick='return confirm("Yakin untuk Logout?")'>
        <i class="bi bi-box-arrow-right"></i>
        <span>Sign Out</span>
      </a>
    </li>

  </ul>
</li>