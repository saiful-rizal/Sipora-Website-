<?php
require_once __DIR__ . '/../config/db.php';

if (isset($_GET['id']) && isset($_GET['aksi'])) {
  $id = intval($_GET['id']);
  $aksi = $_GET['aksi'];

  // Ambil data dokumen + email pengunggah
  $stmt = $pdo->prepare("
    SELECT d.*, u.email, u.nama_lengkap, s.nama_status
    FROM dokumen d
    JOIN users u ON d.uploader_id = u.id_user
    JOIN master_status_dokumen s ON d.status_id = s.status_id
    WHERE dokumen_id = ?
  ");
  $stmt->execute([$id]);
  $dokumen = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$dokumen) {
    header("Location: tabel_dokumen.php?error=Dokumen tidak ditemukan");
    exit;
  }

  // Ambil ID status dari tabel master_status_dokumen
  if ($aksi === 'approve') {
    $statusBaru = $pdo->query("SELECT status_id FROM master_status_dokumen WHERE nama_status = 'Disetujui'")->fetchColumn();
  } elseif ($aksi === 'reject') {
    $statusBaru = $pdo->query("SELECT status_id FROM master_status_dokumen WHERE nama_status = 'Ditolak'")->fetchColumn();
  } else {
    header("Location: tabel_dokumen.php?error=Aksi tidak valid");
    exit;
  }

  // Update status dokumen
  $update = $pdo->prepare("UPDATE dokumen SET status_id = ? WHERE dokumen_id = ?");
  $update->execute([$statusBaru, $id]);

  // Simpan ke log_review
  $insertLog = $pdo->prepare("
    INSERT INTO log_review (dokumen_id, reviewer_id, catatan_review, status_sebelum, status_sesudah)
    VALUES (?, ?, ?, ?, ?)
  ");
  $insertLog->execute([
    $id,
    1, // reviewer_id = admin (bisa diganti dengan session id admin)
    ($aksi === 'reject' ? 'Dokumen ditolak oleh admin.' : 'Dokumen disetujui oleh admin.'),
    $dokumen['status_id'],
    $statusBaru
  ]);

  // Kirim email notifikasi
  $to = $dokumen['email'];
  $subject = "Status Dokumen Anda di SIPORA";
  if ($aksi === 'approve') {
    $message = "Halo " . $dokumen['nama_lengkap'] . ",\n\n"
             . "Dokumen Anda dengan judul \"" . $dokumen['judul'] . "\" telah DISETUJUI oleh admin.\n\n"
             . "Terima kasih telah mengunggah dokumen di SIPORA.";
  } else {
    $message = "Halo " . $dokumen['nama_lengkap'] . ",\n\n"
             . "Dokumen Anda dengan judul \"" . $dokumen['judul'] . "\" telah DITOLAK oleh admin.\n\n"
             . "Silakan periksa kembali dan unggah ulang setelah diperbaiki.";
  }

  $headers = "From: admin@sipora.ac.id";
  @mail($to, $subject, $message, $headers);

  header("Location: tabel_dokumen.php?success=Aksi berhasil dilakukan");
  exit;
}
header("Location: tabel_dokumen.php?error=Permintaan tidak valid");
exit;
