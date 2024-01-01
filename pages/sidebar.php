<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-heading">Login as <b class="darkred"><?=$sebagai?></b></li>
    <!-- <li class="nav-item">
      <a class="nav-link collapsed" href="?stok_awal">
        <i class="bi bi-arrow-down-square"></i>
        <span>Stok Awal</span>
      </a>
    </li> -->
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
        <i class="bi bi-menu-button-wide"></i><span>Master Data</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="prebab-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <?php 
        foreach ($arr_master as $master) echo "
        <li><a href='?master&p=$master'><i class='bi bi-circle'></i><span class=proper>$master</span></a></li>";
        ?>
      </ul>
    </li>

    <li class="nav-heading">Transaksi</li>

    <!-- <li class="nav-item">
      <a class="nav-link collapsed" href="?terima_po">
        <i class="bi bi-arrow-down-square"></i>
        <span>Terima PO</span>
      </a>
    </li> -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="?penerimaan">
        <i class="bi bi-arrow-down-square"></i>
        <span>Penerimaan</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="?master_penerimaan">
        <i class="bi bi-arrow-down-square"></i>
        <span>Master Penerimaan</span>
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

    <!-- <li class="nav-item">
      <a class="nav-link collapsed" href="?penerimaan">
        <i class="bi bi-arrow-down-square"></i>
        <span>Penerimaan (PO)</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" href="?do">
        <i class="bi bi-send-check"></i>
        <span>Pengiriman (DO)</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="?retur_po">
        <i class="bi bi-arrow-return-left"></i>
        <span>Retur PO</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" href="?retur_do">
        <i class="bi bi-arrow-return-right"></i>
        <span>Retur DO</span>
      </a>
    </li> -->

    <li class="nav-heading">Laporan</li>

    <!-- <li class="nav-item">
      <a class="nav-link collapsed" href="?laporan">
        <i class="bi bi-file-earmark-spreadsheet"></i>
        <span>Laporan</span>
      </a>
    </li> -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="?stok">
        <i class="bi bi-file-earmark-spreadsheet"></i>
        <span>Stok Opname</span>
      </a>
    </li>
  </ul>

</aside>
