<?php 
require_once __DIR__ . '/../config/db.php';
include 'header.php';
include 'sidebar.php';

// Ambil semua data mahasiswa (bukan admin)
$stmt = $pdo->query("SELECT * FROM users WHERE role_id != 1 ORDER BY created_at DESC");
$mahasiswa = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>SIPORA - Data Mahasiswa</title>
  <link rel="stylesheet" href="assets/vendors/feather/feather.css">
  <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="shortcut icon" href="assets/images/favicon.png" />
</head>

<body>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title mb-4">Data Mahasiswa Terdaftar</h4>
        <p class="card-description">Berikut adalah daftar mahasiswa yang telah melakukan registrasi akun SIPORA.</p>

        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle">
            <thead class="table-primary text-center">
              <tr>
                <th>#</th>
                <th>Nama Lengkap</th>
                <th>NIM</th>
                <th>Email</th>
                <th>Username</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($mahasiswa):
                $no = 1;
                foreach ($mahasiswa as $mhs):
              ?>
              <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= htmlspecialchars($mhs['nama_lengkap']); ?></td>
                <td><?= htmlspecialchars($mhs['nomor_induk']); ?></td>
                <td><?= htmlspecialchars($mhs['email']); ?></td>
                <td><?= htmlspecialchars($mhs['username']); ?></td>
                <td class="text-center">
                  <?php
                    if ($mhs['status'] === 'pending') echo "<span class='badge bg-warning'>Pending</span>";
                    elseif ($mhs['status'] === 'approved') echo "<span class='badge bg-success'>Approved</span>";
                    elseif ($mhs['status'] === 'rejected') echo "<span class='badge bg-danger'>Rejected</span>";
                  ?>
                </td>
              </tr>
              <?php
                endforeach;
              else:
              ?>
              <tr><td colspan="6" class="text-center text-muted">Belum ada mahasiswa terdaftar.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php include 'footer.php'; ?>
</div>

<script src="assets/vendors/js/vendor.bundle.base.js"></script>
<script src="assets/js/off-canvas.js"></script>
<script src="assets/js/template.js"></script>
<script src="assets/js/settings.js"></script>
<script src="assets/js/todolist.js"></script>
</body>
</html>
