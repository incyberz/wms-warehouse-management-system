<div class="pagetitle">
  <h1>Dashboard</h1>
  <!-- <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?">Home</a></li>
      <li class="breadcrumb-item active">Proses Bisnis WMS</li>
    </ol>
  </nav> -->
</div>

<section class="section dashboard">
  <div class="row">

    <!-- Left side columns -->
    <div class="col-lg-12">
      <div class="row">

        <?php
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
        include 'dashboard-progress.php';
        // include 'dashboard-proses-bisnis.php';
        // include 'dashboard-penerimaan.php';
        // include 'dashboard-pengiriman.php';
        // include 'dashboard-retur-po.php';
        // include 'dashboard-retur-do.php';
        // include 'dashboard-stok-menipis.php'; 
        ?>

        <!-- <div class="col-12">
          <div class="card top-selling overflow-auto">
            
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>

                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>

            <div class="card-body pb-0">
              <h5 class="card-title">Top Selling <span>| Today</span></h5>

              <table class="table table-borderless">
                <thead>
                  <tr>
                    <th scope="col">Preview</th>
                    <th scope="col">Product</th>
                    <th scope="col">Price</th>
                    <th scope="col">Sold</th>
                    <th scope="col">PO</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row"><a href="#"><img src="assets/img/product-1.jpg" alt=""></a></th>
                    <td><a href="#" class="text-primary fw-bold">Ut inventore ipsa voluptas nulla</a></td>
                    <td>$64</td>
                    <td class="fw-bold">124</td>
                    <td>$5,828</td>
                  </tr>
                  <tr>
                    <th scope="row"><a href="#"><img src="assets/img/product-2.jpg" alt=""></a></th>
                    <td><a href="#" class="text-primary fw-bold">Exercitationem similique doloremque</a></td>
                    <td>$46</td>
                    <td class="fw-bold">98</td>
                    <td>$4,508</td>
                  </tr>
                  <tr>
                    <th scope="row"><a href="#"><img src="assets/img/product-3.jpg" alt=""></a></th>
                    <td><a href="#" class="text-primary fw-bold">Doloribus nisi exercitationem</a></td>
                    <td>$59</td>
                    <td class="fw-bold">74</td>
                    <td>$4,366</td>
                  </tr>
                  <tr>
                    <th scope="row"><a href="#"><img src="assets/img/product-4.jpg" alt=""></a></th>
                    <td><a href="#" class="text-primary fw-bold">Officiis quaerat sint rerum error</a></td>
                    <td>$32</td>
                    <td class="fw-bold">63</td>
                    <td>$2,016</td>
                  </tr>
                  <tr>
                    <th scope="row"><a href="#"><img src="assets/img/product-5.jpg" alt=""></a></th>
                    <td><a href="#" class="text-primary fw-bold">Sit unde debitis delectus repellendus</a></td>
                    <td>$79</td>
                    <td class="fw-bold">41</td>
                    <td>$3,239</td>
                  </tr>
                </tbody>
              </table>

            </div>

          </div>
        </div> -->
      </div>

      <?php // include 'dashboard-stok-gudang.php'; 
      ?>

    </div>

    <div class="col-lg-4">
      <?php //include 'pages/recent_activity.php'; 
      ?>
    </div>

  </div>
</section>