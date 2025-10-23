<?php
/**
 * db.php
 * Koneksi database MySQL menggunakan PDO
 * --------------------------------------
 * Sesuaikan host, port, nama database, user, dan password
 */

$host = '127.0.0.1';   // alamat server MySQL
$port = '3307';        // port MySQL kamu (lihat di phpMyAdmin)
$dbname = 'db_sipora'; // nama database
$username = 'root';    // default user XAMPP
$password = '';        // kosong jika belum diset password

try {
    // Buat koneksi PDO
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    // Atur mode error agar tampil di browser (untuk debugging)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // (Opsional) Tes koneksi: hapus komentar di bawah jika ingin cek manual
    // echo "✅ Koneksi ke database berhasil!";
} catch (PDOException $e) {
    // Jika gagal, tampilkan pesan error
    die("❌ Database connection failed: " . $e->getMessage());
}
?>
