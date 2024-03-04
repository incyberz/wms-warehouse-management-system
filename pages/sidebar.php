<?php $locked_icon = $id_role == 7 ? $img_locked : ''; ?>
<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-heading">Login as <b class="darkred"><?= $sebagai ?></b></li>
    <li class="nav-item">
      <a class="nav-link " href="?">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <!-- ====================================== -->
    <!-- PRE BAB -->
    <!-- ====================================== -->
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#prebab-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-menu-button-wide"></i><span>Master Data <?= $locked_icon ?></span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="prebab-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <?php
        foreach ($arr_master as $master) echo "
        <li><a href='?master&p=$master'><i class='bi bi-circle'></i><span class=proper>$master</span></a></li>";
        ?>
      </ul>
    </li>

    <li class="nav-heading">Transaksi</li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="?penerimaan">
        <i class="bi bi-arrow-down-square"></i>
        <span>Penerimaan <?= $locked_icon ?></span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="?rekap_kumulatif">
        <i class="bi bi-arrow-down-square"></i>
        <span>Rekap Kumulatif <?= $locked_icon ?></span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="?rekap_item">
        <i class="bi bi-arrow-down-square"></i>
        <span>Rekap Item <?= $locked_icon ?></span>
      </a>
    </li>

    <!-- <li class="nav-item">
      <a class="nav-link collapsed" href="?master_penerimaan">
        <i class="bi bi-arrow-down-square"></i>
        <span>Master Penerimaan <?= $locked_icon ?></span>
      </a>
    </li> -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="?retur">
        <i class="bi bi-arrow-down-square"></i>
        <span>QC & Retur <?= $locked_icon ?></span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="?pengeluaran">
        <i class="bi bi-send-check"></i>
        <span>Pengeluaran</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="?master_pengeluaran">
        <i class="bi bi-send-check"></i>
        <span>Master Pengeluaran</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="?import_data">
        <i class="bi bi-send-check"></i>
        <span>Import Data</span>
      </a>
    </li>


    <li class="nav-heading">Laporan</li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="?stok">
        <i class="bi bi-file-earmark-spreadsheet"></i>
        <span>Stok Old</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" href="?stok_kumulatif">
        <i class="bi bi-file-earmark-spreadsheet"></i>
        <span>Stok Kumulatif</span>
      </a>
    </li>
  </ul>

</aside>