<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $nomor_induk = trim($_POST['nomor_induk']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password_hash = password_hash($_POST['pasword_hash'], PASSWORD_DEFAULT); // Enkripsi password

    $role_id = 1; // 1 = Admin
    $status = 'approved'; // langsung aktif

    try {
        // Periksa apakah username atau email sudah digunakan
        $cek = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
        $cek->execute([
            ':username' => $username,
            ':email' => $email
        ]);

        if ($cek->fetchColumn() > 0) {
            header("Location: form_admin.php?error=duplicate");
            exit;
        }

        // Masukkan data admin baru ke tabel users
        $stmt = $pdo->prepare("INSERT INTO users (nama_lengkap, nomor_induk, email, username, pasword_hash, role_id, status)
                               VALUES (:nama_lengkap, :nomor_induk, :email, :username, :pasword_hash, :role_id, :status)");

        $stmt->execute([
            ':nama_lengkap' => $nama_lengkap,
            ':nomor_induk' => $nomor_induk,
            ':email' => $email,
            ':username' => $username,
            ':pasword_hash' => $password_hash,
            ':role_id' => $role_id,
            ':status' => $status
        ]);

        // Redirect dengan pesan sukses
        header("Location: form_admin.php?success=1");
        exit;

    } catch (PDOException $e) {
        // Jika ada error koneksi atau query
        header("Location: form_admin.php?error=db");
        exit;
    }
} else {
    // Jika akses langsung tanpa POST
    header("Location: form_admin.php");
    exit;
}
