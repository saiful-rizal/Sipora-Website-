<?php
require_once __DIR__ . '/../config/db.php';
?>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SIPORA - Tambah Admin</title>
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
        <div class="row">
          <div class="col-lg-10 mx-auto grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title mb-4">Form Tambah Admin</h4>
                <p class="card-description">Isi data berikut untuk menambahkan admin baru</p>

                <?php if (!empty($_GET['success'])): ?>
                  <div class="alert alert-success">✅ Data admin berhasil ditambahkan!</div>
                <?php elseif (!empty($_GET['error'])): ?>
                  <div class="alert alert-danger">❌ Gagal menambahkan admin!</div>
                <?php endif; ?>

                <!-- FORM HORIZONTAL -->
                <form class="forms-sample" method="POST" action="proses_admin.php">
                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Nama Lengkap</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Nomor Induk (NIP)</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="nomor_induk" placeholder="Masukkan NIP admin" required>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Email</label>
                    <div class="col-sm-9">
                      <input type="email" class="form-control" name="email" placeholder="Masukkan email admin" required>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Username</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="username" placeholder="Masukkan username admin" required>
                    </div>
                  </div>

                  <div class="row mb-4">
                    <label class="col-sm-3 col-form-label">Password</label>
                    <div class="col-sm-9">
                      <input type="password" class="form-control" name="pasword_hash" placeholder="Masukkan password" required>
                    </div>
                  </div>

                  <div class="text-end">
                    <button type="submit" class="btn btn-primary me-2"><i class="mdi mdi-content-save"></i> Simpan</button>
                    <button type="reset" class="btn btn-light"><i class="mdi mdi-refresh"></i> Batal</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- TABEL DATA ADMIN -->
        <div class="row mt-2">
          <div class="col-lg-10 mx-auto grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title mb-4">Data Admin Terdaftar</h4>

                <div class="table-responsive">
                  <table class="table table-hover table-bordered align-middle">
                    <thead class="table-primary text-center">
                      <tr>
                        <th>#</th>
                        <th>Nama Lengkap</th>
                        <th>NIP</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                  <?php
                      // Ambil data dari tabel users
                     $query = $pdo->query("SELECT * FROM users WHERE role_id = 1 ORDER BY id_user DESC");
$users = $query->fetchAll(PDO::FETCH_ASSOC);

if ($users):
  $no = 1;
  foreach ($users as $row):
?>
  <tr>
    <td class="text-center"><?= $no++; ?></td>
    <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
    <td><?= htmlspecialchars($row['nomor_induk']); ?></td>
    <td><?= htmlspecialchars($row['email']); ?></td>
    <td><?= htmlspecialchars($row['username']); ?></td>
    <td class="text-center">
      <a href="edit_admin.php?id=<?= $row['id_user']; ?>" class="btn btn-sm btn-warning">
        <i class="mdi mdi-pencil"></i> Edit
      </a>
      <a href="hapus_admin.php?id=<?= $row['id_user']; ?>" class="btn btn-sm btn-danger"
         onclick="return confirm('Yakin ingin menghapus data ini?');">
        <i class="mdi mdi-delete"></i> Hapus
      </a>
    </td>
  </tr>
<?php
  endforeach;
else:
?>
  <tr>
    <td colspan="6" class="text-center text-muted">Belum ada data admin.</td>
  </tr>
<?php endif; ?>


                    </tbody>
                  </table>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

      <?php include 'footer.php'; ?>
    </div>

    <!-- JS FILES -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/template.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
  </body>
</html>
