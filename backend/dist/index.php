

<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Redirect jika belum login
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

// ====== AMBIL DATA DARI DATABASE ======
try {
  $stmt1 = $pdo->query("SELECT COUNT(*) AS total FROM dokumen");
  $total_dokumen = $stmt1->fetch(PDO::FETCH_ASSOC)['total'];

  $stmt2 = $pdo->query("SELECT COUNT(*) AS total FROM master_author");
  $total_author = $stmt2->fetch(PDO::FETCH_ASSOC)['total'];

  $stmt3 = $pdo->query("SELECT COUNT(*) AS total FROM users");
  $total_user = $stmt3->fetch(PDO::FETCH_ASSOC)['total'];

  $stmt4 = $pdo->query("SELECT COUNT(*) AS total FROM log_review");
  $total_review = $stmt4->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
  $total_dokumen = $total_author = $total_user = $total_review = "Error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Skydash Admin</title>
  <link rel="stylesheet" href="assets/vendors/feather/feather.css">
  <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="assets/vendors/datatables.net-bs5/dataTables.bootstrap5.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="shortcut icon" href="assets/images/favicon.png" />
</head>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

    <div class="main-panel">
      <div class="content-wrapper">
        <div class="row">
          <div class="col-md-12 grid-margin">
            <div class="row">
              <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <h3 class="font-weight-bold">
                Hallo <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Pengguna'; ?>
              </h3>
              <h6 class="font-weight-normal mb-0">Selamat Datang di SIPORA</h6>

              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 grid-margin stretch-card">
            <div class="card tale-bg">
              <div class="card-people mt-auto">
                <img src="assets/images/dashboard/people.svg" alt="people">
                <div class="weather-info">
                  <div class="d-flex">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 grid-margin transparent">
            <div class="row">
              <div class="col-md-6 mb-4 stretch-card transparent">
                <div class="card card-tale">
                  <div class="card-body">
                    <p class="mb-4">Total Dokumen</p>
                    <p class="fs-30 mb-2"><?php echo $total_dokumen; ?></p>
                  </div>
                </div>
              </div>
              <div class="col-md-6 mb-4 stretch-card transparent">
                <div class="card card-dark-blue">
                  <div class="card-body">
                    <p class="mb-4">Total Author</p>
                    <p class="fs-30 mb-2"><?php echo $total_author; ?></p>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                <div class="card card-light-blue">
                  <div class="card-body">
                    <p class="mb-4">Total Review</p>
                    <p class="fs-30 mb-2"><?php echo $total_review; ?></p>
                  </div>
                </div>
              </div>
              <div class="col-md-6 stretch-card transparent">
                <div class="card card-light-danger">
                  <div class="card-body">
                    <p class="mb-4">Number of Clients</p>
                    <p class="fs-30 mb-2">47033</p>
                    <p>0.22% (30 days)</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <p class="card-title mb-0">Daftar Dokumen Terbaru</p>
                <div class="table-responsive">
                  <table class="table table-striped table-borderless">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Tahun</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      try {
                        $stmt = $pdo->query("SELECT dokumen_id, judul, tahun, status FROM dokumen ORDER BY dokumen_id DESC LIMIT 10");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          echo "<tr>
                                          <td>{$row['dokumen_id']}</td>
                                          <td>{$row['judul']}</td>
                                          <td>{$row['tahun']}</td>
                                          <td><span class='badge badge-success'>{$row['status']}</span></td>
                                        </tr>";
                        }
                      } catch (PDOException $e) {
                        echo "<tr><td colspan='4'>Error loading data</td></tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
                <div class="row">
          <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <p class="card-title">Sales Report</p>
                  <a href="#" class="text-info">View all</a>
                </div>
                <p class="font-weight-500">The total number of sessions within the date range. It is the period time a user is actively engaged with your website, page or app, etc</p>
                <div id="sales-chart-legend" class="chartjs-legend mt-4 mb-2"></div>
                <canvas id="sales-chart"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 grid-margin stretch-card">
            <div class="card position-relative">
              <div class="card-body">
                <div id="detailedReports" class="carousel slide detailed-report-carousel position-static pt-2" data-bs-ride="carousel">
                  <div class="carousel-inner">
                    <div class="carousel-item active">
                      <div class="row">
                        <div class="col-md-12 col-xl-3 d-flex flex-column justify-content-start">
                          <div class="ml-xl-4 mt-3">
                            <p class="card-title">Detailed Reports</p>
                            <h1 class="text-primary">$34040</h1>
                            <h3 class="font-weight-500 mb-xl-4 text-primary">North America</h3>
                            <p class="mb-2 mb-xl-0">The total number of sessions within the date range. It is the period time a user is actively engaged with your website, page or app, etc</p>
                          </div>
                        </div>
                        <div class="col-md-12 col-xl-9">
                          <div class="row">
                            <div class="col-md-6 border-right">
                              <div class="table-responsive mb-3 mb-md-0 mt-3">
                                <table class="table table-borderless report-table">
                                  <tr>
                                    <td class="text-muted">Illinois</td>
                                    <td class="w-100 px-0">
                                      <div class="progress progress-md mx-4">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                    </td>
                                    <td>
                                      <h5 class="font-weight-bold mb-0">713</h5>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td class="text-muted">Washington</td>
                                    <td class="w-100 px-0">
                                      <div class="progress progress-md mx-4">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                    </td>
                                    <td>
                                      <h5 class="font-weight-bold mb-0">583</h5>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td class="text-muted">Mississippi</td>
                                    <td class="w-100 px-0">
                                      <div class="progress progress-md mx-4">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                    </td>
                                    <td>
                                      <h5 class="font-weight-bold mb-0">924</h5>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td class="text-muted">California</td>
                                    <td class="w-100 px-0">
                                      <div class="progress progress-md mx-4">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                    </td>
                                    <td>
                                      <h5 class="font-weight-bold mb-0">664</h5>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td class="text-muted">Maryland</td>
                                    <td class="w-100 px-0">
                                      <div class="progress progress-md mx-4">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                    </td>
                                    <td>
                                      <h5 class="font-weight-bold mb-0">560</h5>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td class="text-muted">Alaska</td>
                                    <td class="w-100 px-0">
                                      <div class="progress progress-md mx-4">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                    </td>
                                    <td>
                                      <h5 class="font-weight-bold mb-0">793</h5>
                                    </td>
                                  </tr>
                                </table>
                              </div>
                            </div>
                            <div class="col-md-6 mt-3">
                              <div class="daoughnutchart-wrapper">
                                <canvas id="north-america-chart"></canvas>
                              </div>
                              <div id="north-america-chart-legend">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="carousel-item">
                      <div class="row">
                        <div class="col-md-12 col-xl-3 d-flex flex-column justify-content-start">
                          <div class="ml-xl-4 mt-3">
                            <p class="card-title">Detailed Reports</p>
                            <h1 class="text-primary">$34040</h1>
                            <h3 class="font-weight-500 mb-xl-4 text-primary">North America</h3>
                            <p class="mb-2 mb-xl-0">The total number of sessions within the date range. It is the period time a user is actively engaged with your website, page or app, etc</p>
                          </div>
                        </div>
                        <div class="col-md-12 col-xl-9">
                          <div class="row">
                            <div class="col-md-6 border-right">
                              <div class="table-responsive mb-3 mb-md-0 mt-3">
                                <table class="table table-borderless report-table">
                                  <tr>
                                    <td class="text-muted">Illinois</td>
                                    <td class="w-100 px-0">
                                      <div class="progress progress-md mx-4">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                    </td>
                                    <td>
                                      <h5 class="font-weight-bold mb-0">713</h5>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td class="text-muted">Washington</td>
                                    <td class="w-100 px-0">
                                      <div class="progress progress-md mx-4">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                    </td>
                                    <td>
                                      <h5 class="font-weight-bold mb-0">583</h5>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td class="text-muted">Mississippi</td>
                                    <td class="w-100 px-0">
                                      <div class="progress progress-md mx-4">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                    </td>
                                    <td>
                                      <h5 class="font-weight-bold mb-0">924</h5>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td class="text-muted">California</td>
                                    <td class="w-100 px-0">
                                      <div class="progress progress-md mx-4">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                    </td>
                                    <td>
                                      <h5 class="font-weight-bold mb-0">664</h5>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td class="text-muted">Maryland</td>
                                    <td class="w-100 px-0">
                                      <div class="progress progress-md mx-4">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                    </td>
                                    <td>
                                      <h5 class="font-weight-bold mb-0">560</h5>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td class="text-muted">Alaska</td>
                                    <td class="w-100 px-0">
                                      <div class="progress progress-md mx-4">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                    </td>
                                    <td>
                                      <h5 class="font-weight-bold mb-0">793</h5>
                                    </td>
                                  </tr>
                                </table>
                              </div>
                            </div>
                            <div class="col-md-6 mt-3">
                              <div class="daoughnutchart-wrapper">
                                <canvas id="south-america-chart"></canvas>
                              </div>
                              <div id="south-america-chart-legend"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <a class="carousel-control-prev" href="#detailedReports" role="button" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  </a>
                  <a class="carousel-control-next" href="#detailedReports" role="button" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- FOOTER -->

    </div>
  </div>
  </div>
  </div>
  <!-- content-wrapper ends -->
  <!-- partial:partials/_footer.html -->
 <?php include 'footer.php'; ?>
  <!-- partial -->
  </div>
  <!-- main-panel ends -->
  </div>
  <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="assets/vendors/chart.js/chart.umd.js"></script>
  <script src="assets/vendors/datatables.net/jquery.dataTables.js"></script>
  <!-- <script src="assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script> -->
  <script src="assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="assets/js/off-canvas.js"></script>
  <script src="assets/js/template.js"></script>
  <script src="assets/js/settings.js"></script>
  <script src="assets/js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="assets/js/jquery.cookie.js" type="text/javascript"></script>
  <script src="assets/js/dashboard.js"></script>
  <!-- <script src="assets/js/Chart.roundedBarCharts.js"></script> -->
  <!-- End custom js for this page-->
</body>

</html>