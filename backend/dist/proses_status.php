<?php
require_once __DIR__ . '/../config/db.php';

if (isset($_GET['id']) && isset($_GET['aksi'])) {
  $id = intval($_GET['id']);
  $aksi = $_GET['aksi'];

  // Ambil data user
  $stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = ?");
  $stmt->execute([$id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    header("Location: data_mahasiswa.php?error=Data tidak ditemukan");
    exit;
  }

  if ($aksi === 'approve') {
    $update = $pdo->prepare("UPDATE users SET status = 'approved' WHERE id_user = ?");
    $update->execute([$id]);
    header("Location: data_mahasiswa.php?success=Mahasiswa berhasil di-ACC");
    exit;

  } elseif ($aksi === 'reject') {
    $update = $pdo->prepare("UPDATE users SET status = 'rejected' WHERE id_user = ?");
    $update->execute([$id]);

    // Kirim email penolakan
    $to = $user['email'];
    $subject = "Pendaftaran Akun SIPORA Ditolak";
    $message = "Halo " . $user['nama_lengkap'] . ",\n\n"
             . "Maaf, pendaftaran akun Anda di SIPORA tidak dapat disetujui.\n"
             . "Silakan periksa kembali data Anda dan coba daftar ulang.\n\n"
             . "Terima kasih.";
    $headers = "From: admin@sipora.ac.id";

    @mail($to, $subject, $message, $headers);

    header("Location: data_mahasiswa.php?success=Penolakan berhasil dikirim ke email mahasiswa");
    exit;
  }
}
header("Location: data_mahasiswa.php?error=Aksi tidak valid");
exit;
