<?php 
require_once __DIR__ . '/../config/db.php';
include 'header.php';
include 'sidebar.php';

// Ambil semua data dokumen + uploader
$query = "
  SELECT d.*, 
         u.nama_lengkap AS uploader_name, 
         u.email AS uploader_email,
         j.nama_jurusan,
         p.nama_prodi,
         t.nama_tema,
         s.nama_status
  FROM dokumen d
  JOIN users u ON d.uploader_id = u.id_user
  JOIN master_jurusan j ON d.id_jurusan = j.id_jurusan
  JOIN master_prodi p ON d.id_prodi = p.id_prodi
  JOIN master_tema t ON d.id_tema = t.id_tema
  JOIN master_status_dokumen s ON d.status_id = s.status_id
  ORDER BY d.tgl_unggah DESC
";
$stmt = $pdo->query($query);
$dokumen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>SIPORA - Data Dokumen</title>
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
        <h4 class="card-title mb-4">Data Dokumen Mahasiswa</h4>
        <p class="card-description">Berikut adalah daftar dokumen yang diunggah oleh mahasiswa.</p>

        <?php if (!empty($_GET['success'])): ?>
          <div class="alert alert-success"><?= htmlspecialchars($_GET['success']); ?></div>
        <?php elseif (!empty($_GET['error'])): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle">
            <thead class="table-primary text-center">
              <tr>
                <th>#</th>
                <th>Judul</th>
                <th>Tema</th>
                <th>Jurusan</th>
                <th>Prodi</th>
                <th>Uploader</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($dokumen): $no=1; foreach ($dokumen as $d): ?>
              <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= htmlspecialchars($d['judul']); ?></td>
                <td><?= htmlspecialchars($d['nama_tema']); ?></td>
                <td><?= htmlspecialchars($d['nama_jurusan']); ?></td>
                <td><?= htmlspecialchars($d['nama_prodi']); ?></td>
                <td><?= htmlspecialchars($d['uploader_name']); ?></td>
                <td><?= date('d-m-Y', strtotime($d['tgl_unggah'])); ?></td>
                <td class="text-center">
                  <?php
                    if ($d['nama_status'] === 'Menunggu Review') echo "<span class='badge bg-warning'>Pending</span>";
                    elseif ($d['nama_status'] === 'Disetujui') echo "<span class='badge bg-success'>Approved</span>";
                    elseif ($d['nama_status'] === 'Ditolak') echo "<span class='badge bg-danger'>Rejected</span>";
                  ?>
                </td>
                <td class="text-center">
                  <a href="../<?= htmlspecialchars($d['file_path']); ?>" target="_blank" class="btn btn-info btn-sm">
                    <i class="mdi mdi-eye"></i> Lihat
                  </a>
                  <?php if ($d['nama_status'] === 'Menunggu Review'): ?>
                    <a href="proses_dokumen.php?id=<?= $d['dokumen_id']; ?>&aksi=approve" class="btn btn-success btn-sm">
                      <i class="mdi mdi-check"></i> Approve
                    </a>
                    <a href="proses_dokumen.php?id=<?= $d['dokumen_id']; ?>&aksi=reject" class="btn btn-danger btn-sm">
                      <i class="mdi mdi-close"></i> Tolak
                    </a>
                  <?php else: ?>
                    <span class="text-muted">Selesai</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; else: ?>
              <tr><td colspan="9" class="text-center text-muted">Belum ada dokumen yang diunggah.</td></tr>
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
