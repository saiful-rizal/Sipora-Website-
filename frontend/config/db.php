<?php
// config/db.php
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3306);           // sesuai dump SQL kamu
define('DB_NAME', 'db_sipora');
define('DB_USER', 'root');
define('DB_PASS', '');             // sesuaikan jika ada password

try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
