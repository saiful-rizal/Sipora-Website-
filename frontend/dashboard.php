<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/home.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    setcookie('username', '', time() - 3600, "/");
    header("Location: auth.php");
    exit();
}

 $user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT id_user, username, email FROM users WHERE id_user = :id LIMIT 1");
    $stmt->execute(['id' => $user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

 $initials = '';
if (!empty($user_data['username'])) {
    $username_parts = explode('_', $user_data['username']);
    if (count($username_parts) > 1) {
        $initials = strtoupper(substr($username_parts[0], 0, 1) . substr(end($username_parts), 0, 1));
    } else {
        $initials = strtoupper(substr($user_data['username'], 0, 2));
    }
}

// Mengambil data statistik dari database
try {
    $stats_data = [
        'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'total_books' => $pdo->query("SELECT COUNT(*) FROM dokumen WHERE tipe_dokumen = 'book'")->fetchColumn(),
        'total_journals' => $pdo->query("SELECT COUNT(*) FROM dokumen WHERE tipe_dokumen = 'journal'")->fetchColumn(),
        'total_theses' => $pdo->query("SELECT COUNT(*) FROM dokumen WHERE tipe_dokumen = 'thesis'")->fetchColumn(),
        'total_archives' => $pdo->query("SELECT COUNT(*) FROM dokumen")->fetchColumn()
    ];
} catch (PDOException $e) {
    $stats_data = [
        'total_users' => 0,
        'total_books' => 0,
        'total_journals' => 0,
        'total_theses' => 0,
        'total_archives' => 0
    ];
}

// Mengambil semua dokumen dari database
try {
    $stmt = $pdo->prepare("
        SELECT 
            d.dokumen_id AS id_book, 
            d.judul AS title, 
            d.tipe_dokumen AS type,
            d.abstrak AS abstract,
            (SELECT nama_jurusan FROM master_jurusan WHERE id_jurusan = d.id_jurusan) AS department,
            d.year_id AS year,
            d.file_path AS file_path,
            d.tgl_unggah AS upload_date
        FROM dokumen d
        ORDER BY d.tgl_unggah DESC 
        LIMIT 6
    ");
    $stmt->execute();
    $books_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $books_data = [];
}

// Mengambil data jurnal dari database
try {
    $stmt = $pdo->prepare("SELECT d.dokumen_id as id_journal, d.judul as title, 
                          CONCAT('Vol. ', d.year_id) as volume,
                          (SELECT nama_jurusan FROM master_jurusan WHERE id_jurusan = d.id_jurusan) as department, 
                          d.year_id as year 
                          FROM dokumen d WHERE d.tipe_dokumen = 'journal' AND d.status_id = 1 ORDER BY d.tgl_unggah DESC LIMIT 6");
    $stmt->execute();
    $journals_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $journals_data = [];
}

// Mengambil data tesis dari database
try {
    $stmt = $pdo->prepare("SELECT d.dokumen_id as id_thesis, d.judul as title, 
                          (SELECT nama_jurusan FROM master_jurusan WHERE id_jurusan = d.id_jurusan) as department, 
                          d.year_id as year 
                          FROM dokumen d WHERE d.tipe_dokumen = 'thesis' AND d.status_id = 1 ORDER BY d.tgl_unggah DESC LIMIT 6");
    $stmt->execute();
    $theses_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $theses_data = [];
}

// Mengambil data e-book dari database
try {
    $stmt = $pdo->prepare("SELECT d.dokumen_id as id_ebook, d.judul as title, 
                          (SELECT nama_jurusan FROM master_jurusan WHERE id_jurusan = d.id_jurusan) as department, 
                          d.year_id as year 
                          FROM dokumen d WHERE d.tipe_dokumen = 'ebook' AND d.status_id = 1 ORDER BY d.tgl_unggah DESC LIMIT 6");
    $stmt->execute();
    $ebooks_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $ebooks_data = [];
}

// Mengambil data berita dari database
try {
    $stmt = $pdo->prepare("SELECT id_news, title, category, post_date FROM news ORDER BY post_date DESC LIMIT 3");
    $stmt->execute();
    $news_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $news_data = [];
}

function handleFileUpload($file, $target_dir = "uploads/documents/") {
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = pathinfo($file["name"], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if ($file["size"] > 10000000) {
        return ["success" => false, "message" => "File terlalu besar, maksimal 10MB"];
    }
    
    $allowed_types = ["pdf", "doc", "docx", "jpg", "jpeg", "png"];
    if (!in_array(strtolower($file_extension), $allowed_types)) {
        return ["success" => false, "message" => "Tipe file tidak didukung"];
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return [
            "success" => true, 
            "file_path" => $target_file, 
            "file_size" => $file["size"],
            "original_name" => $file["name"]
        ];
    } else {
        return ["success" => false, "message" => "Gagal mengunggah file"];
    }
}

// --- PERUBAHAN PADA BAGIAN UPLOAD ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    $tipe_dokumen = htmlspecialchars($_POST['tipe_dokumen']);
    $judul = htmlspecialchars($_POST['judul']);
    $abstrak = htmlspecialchars($_POST['abstrak']);
    $id_tema = htmlspecialchars($_POST['id_tema']);
    $id_jurusan = htmlspecialchars($_POST['id_jurusan']);
    $id_prodi = htmlspecialchars($_POST['id_prodi']);
    $year_id = htmlspecialchars($_POST['year_id']);
    $status_id = htmlspecialchars($_POST['status_id']);
    $format_id = htmlspecialchars($_POST['format_id']);
    $policy_id = htmlspecialchars($_POST['policy_id']);
    
    $response = ['success' => false, 'message' => 'Terjadi kesalahan.'];
    
    if (!empty($tipe_dokumen) && !empty($judul) && !empty($id_tema) && !empty($id_jurusan) && !empty($id_prodi) && !empty($year_id) && !empty($status_id) && !empty($format_id) && !empty($policy_id)) {
        if (isset($_FILES['dokumen_file']) && $_FILES['dokumen_file']['error'] == 0) {
            $file_result = handleFileUpload($_FILES['dokumen_file']);
            
            if ($file_result['success']) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO dokumen 
    (tipe_dokumen, judul, abstrak, file_path, file_size, uploader_id, id_tema, id_jurusan, id_prodi, year_id, status_id, format_id, policy_id) 
    VALUES 
    (:tipe_dokumen, :judul, :abstrak, :file_path, :file_size, :uploader_id, :id_tema, :id_jurusan, :id_prodi, :year_id, :status_id, :format_id, :policy_id)");

                    $stmt->execute([
                        'tipe_dokumen' => $tipe_dokumen,
                        'judul' => $judul,
                        'abstrak' => $abstrak,
                        'file_path' => $file_result['file_path'],
                        'file_size' => $file_result['file_size'],
                        'uploader_id' => $user_id,
                        'id_tema' => $id_tema,
                        'id_jurusan' => $id_jurusan,
                        'id_prodi' => $id_prodi,
                        'year_id' => $year_id,
                        'status_id' => $status_id,
                        'format_id' => $format_id,
                        'policy_id' => $policy_id
                    ]);

                    // --- AMBIL DATA DOKUMEN BARU UNTUK DITAMPILKAN ---
                    $new_doc_id = $pdo->lastInsertId();
                    $stmt_new = $pdo->prepare("SELECT 
                        d.dokumen_id as id, 
                        d.judul as title, 
                        d.tipe_dokumen as type, 
                        d.tgl_unggah as upload_date,
                        d.year_id as year,
                        (SELECT nama_jurusan FROM master_jurusan WHERE id_jurusan = d.id_jurusan) as department
                        FROM dokumen d WHERE d.dokumen_id = :id");
                    $stmt_new->execute(['id' => $new_doc_id]);
                    $new_document_data = $stmt_new->fetch(PDO::FETCH_ASSOC);
                    
                    $response['success'] = true;
                    $response['message'] = 'Dokumen berhasil diunggah!';
                    $response['newDocument'] = $new_document_data; // Kirim data baru ke frontend

                } catch (PDOException $e) {
                    $response['message'] = 'Kesalahan database: ' . $e->getMessage();
                }
            } else {
                $response['message'] = $file_result['message'];
            }
        } else {
            $response['message'] = 'File tidak dipilih atau ada kesalahan saat mengunggah.';
        }
    } else {
        $response['message'] = 'Semua field yang ditandai * harus diisi!';
    }
    
    echo json_encode($response);
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIPORA POLIJE - Sistem Informasi Kampus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/home.css">
  <style>
    /* CSS Responsif Tambahan */
    
    /* Untuk Mobile (Layar kecil) */
    @media (max-width: 576px) {
      .modern-header {
        padding: 0.5rem 1rem;
      }
      
      .header-center {
        display: none;
      }
      
      .header-right {
        margin-left: auto;
      }
      
      .sidebar {
        width: 280px;
      }
      
      .banner-content h2 {
        font-size: 1.5rem;
      }
      
      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
      }
      
      .filter-bar {
        flex-direction: column;
        gap: 1rem;
      }
      
      .filter-group {
        width: 100%;
      }
      
      .cards-grid {
        grid-template-columns: repeat(1, 1fr);
      }
      
      .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }
      
      .upload-modal-content {
        width: 95%;
        margin: 0 auto;
        max-height: 90vh;
        overflow-y: auto;
      }
      
      .fab {
        bottom: 20px;
        right: 20px;
        width: 56px;
        height: 56px;
      }
      
      .quick-add-menu {
        bottom: 80px;
        right: 20px;
      }
    }
    
    /* Untuk Tablet (Layar sedang) */
    @media (min-width: 577px) and (max-width: 768px) {
      .header-center {
        width: 40%;
      }
      
      .stats-grid {
        grid-template-columns: repeat(3, 1fr);
      }
      
      .cards-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .filter-bar {
        flex-wrap: wrap;
      }
      
      .filter-group {
        flex: 1 0 40%;
      }
    }
    
    /* Untuk Desktop (Layar besar) */
    @media (min-width: 769px) {
      .cards-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }
    
    /* Untuk Desktop Besar (Layar sangat besar) */
    @media (min-width: 1200px) {
      .cards-grid {
        grid-template-columns: repeat(4, 1fr);
      }
    }
    
    /* Animasi dan Transisi */
    .card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    /* Touch-friendly untuk perangkat mobile */
    @media (hover: none) {
      .card:hover {
        transform: none;
        box-shadow: none;
      }
      
      .card:active {
        transform: scale(0.98);
      }
    }
    
    /* Perbaikan untuk navigasi mobile */
    .menu-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border-radius: 8px;
      background-color: rgba(255, 255, 255, 0.1);
      border: none;
      color: white;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    
    .menu-btn:hover {
      background-color: rgba(255, 255, 255, 0.2);
    }
    
    .menu-btn:active {
      background-color: rgba(255, 255, 255, 0.3);
    }
    
    /* Perbaikan untuk modal di mobile */
    @media (max-width: 576px) {
      .upload-modal-body {
        padding: 1rem;
      }
      
      .upload-form-group {
        margin-bottom: 1rem;
      }
      
      .upload-form-label {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
      }
      
      .upload-form-input, .upload-form-select, .upload-form-textarea {
        font-size: 0.9rem;
      }
    }
    
    /* Perbaikan untuk notifikasi di mobile */
    @media (max-width: 576px) {
      .notification-panel {
        width: 90%;
        right: 5%;
        left: 5%;
      }
      
      .profile-dropdown {
        width: 90%;
        right: 5%;
        left: 5%;
      }
    }
    
    /* Perbaikan untuk banner di mobile */
    @media (max-width: 576px) {
      .banner-section {
        padding: 1rem 0;
      }
      
      .banner-content {
        padding: 1.5rem;
      }
      
      .banner-btn {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
      }
    }
    
    /* Perbaikan untuk statistik di mobile */
    @media (max-width: 576px) {
      .stat-card {
        padding: 1rem;
      }
      
      .stat-icon {
        font-size: 1.5rem;
      }
      
      .stat-label {
        font-size: 0.8rem;
      }
      
      .stat-value {
        font-size: 1.2rem;
      }
    }
    
    /* Perbaikan untuk filter di mobile */
    @media (max-width: 576px) {
      .filter-label {
        font-size: 0.8rem;
      }
      
      .filter-select {
        font-size: 0.8rem;
        padding: 0.3rem;
      }
    }
    
    /* Perbaikan untuk tombol di mobile */
    @media (max-width: 576px) {
      .add-btn {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
      }
      
      .see-all-btn {
        font-size: 0.8rem;
      }
    }
    
    /* Perbaikan untuk card di mobile */
    @media (max-width: 576px) {
      .card {
        margin-bottom: 1rem;
      }
      
      .card-title {
        font-size: 0.9rem;
        line-height: 1.3;
      }
      
      .card-meta {
        font-size: 0.8rem;
      }
    }
    
    /* Perbaikan untuk news di mobile */
    @media (max-width: 576px) {
      .news-item {
        padding: 0.8rem;
      }
      
      .news-title {
        font-size: 0.9rem;
      }
      
      .news-meta {
        font-size: 0.8rem;
      }
    }
    
    /* Perbaikan untuk list-section di mobile */
    @media (max-width: 576px) {
      .list-item {
        padding: 0.8rem;
      }
      
      .list-title {
        font-size: 0.9rem;
      }
      
      .list-subtitle {
        font-size: 0.8rem;
      }
    }
    
    /* Perbaikan untuk FAB di mobile */
    @media (max-width: 576px) {
      .quick-add-menu {
        width: 90%;
        right: 5%;
        left: 5%;
      }
      
      .quick-add-item {
        padding: 0.8rem;
      }
      
      .quick-add-text {
        font-size: 0.9rem;
      }
    }
    
    /* Perbaikan untuk search di mobile */
    @media (max-width: 576px) {
      .search-overlay {
        padding: 1rem;
      }
      
      .search-modal {
        width: 95%;
      }
      
      .search-modal-input {
        font-size: 0.9rem;
      }
      
      .filter-chip {
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
      }
    }
    
    /* Perbaikan untuk loading screen di mobile */
    @media (max-width: 576px) {
      .loading-text {
        font-size: 0.9rem;
      }
    }
    
    /* Perbaikan untuk sidebar di mobile */
    @media (max-width: 576px) {
      .sidebar {
        width: 80%;
        max-width: 280px;
      }
      
      .sidebar-profile {
        padding: 1rem;
      }
      
      .profile-name {
        font-size: 0.9rem;
      }
      
      .profile-role {
        font-size: 0.8rem;
      }
      
      .menu-item {
        padding: 0.8rem 1rem;
        font-size: 0.9rem;
      }
    }
    
    /* Perbaikan untuk footer di mobile */
    @media (max-width: 576px) {
      .toast-container {
        width: 90%;
        right: 5%;
        left: 5%;
      }
    }
  </style>
</head>
<body>
  <div class="wave-container">
    <div class="wave"></div>
    <div class="wave"></div>
    <div class="wave"></div>
  </div>

  <div id="waterDropsContainer"></div>

  <div class="loading-screen" id="loadingScreen">
    <div class="loading-logo">
      <img src="assets/logo_polije.png" alt="Logo" class="logo-image">
    </div>
    <div class="loading-text">SISTEM INFORMASI ASET REPOSITORY POLIJE</div>
    <div class="loading-bar">
      <div class="loading-progress"></div>
    </div>
  </div>

  <header class="modern-header">
    <div class="header-left">
      <button class="menu-btn" id="menuBtn">
        <i class="bi bi-list"></i>
      </button>
      <a href="#" class="logo">
        <div class="logo-icon">P</div>
        <span class="logo-text">SIPORA</span>
      </a>
    </div>
    <div class="header-center">
      <div class="search-container">
        <input type="text" class="search-input" placeholder="Cari buku, jurnal..." id="searchInput">
        <button class="search-icon-btn" id="searchBtn">
          <i class="bi bi-search"></i>
        </button>
      </div>
    </div>
    <div class="header-right">
      <button class="notification-btn" id="notificationBtn">
        <i class="bi bi-bell"></i>
        <span class="notification-badge"></span>
      </button>
      <button class="profile-btn" id="profileBtn">
        <i class="bi bi-person-circle"></i>
      </button>
    </div>
  </header>

  <div class="search-overlay" id="searchOverlay">
    <div class="search-modal">
      <div class="search-modal-header">
        <h3 class="search-modal-title">Pencarian Lanjutan</h3>
        <button class="search-close-btn" id="searchCloseBtn">
          <i class="bi bi-x"></i>
        </button>
      </div>
      <input type="text" class="search-modal-input" placeholder="Ketik kata kunci pencarian..." id="searchModalInput">
      <div class="search-filters">
        <button class="filter-chip active" data-filter="all">Semua</button>
        <button class="filter-chip" data-filter="books">Buku</button>
        <button class="filter-chip" data-filter="journals">Jurnal</button>
        <button class="filter-chip" data-filter="thesis">Tesis</button>
      </div>
      <div class="search-results" id="searchResults">
        <div class="search-result-item">
          <div class="search-result-icon">
            <i class="bi bi-person"></i>
          </div>
          <div class="search-result-content">
            <div class="search-result-title">Ahmad Rizki</div>
            <div class="search-result-subtitle">Teknologi Informasi • 00123</div>
          </div>
        </div>
        <div class="search-result-item">
          <div class="search-result-icon">
            <i class="bi bi-book"></i>
          </div>
          <div class="search-result-content">
            <div class="search-result-title">Struktur Data dan Algoritma</div>
            <div class="search-result-subtitle">Prof. Dr. Budi Santoso</div>
          </div>
        </div>
        <div class="search-result-item">
          <div class="search-result-icon">
            <i class="bi bi-journal-text"></i>
          </div>
          <div class="search-result-content">
            <div class="search-result-title">Jurnal Internasional Machine Learning</div>
            <div class="search-result-subtitle">Vol. 45 No. 3 • Maret 2024</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="notification-panel" id="notificationPanel">
    <div class="notification-header">
      <h3 class="notification-title">Notifikasi</h3>
      <button class="notification-clear">Hapus semua</button>
    </div>
    <div class="notification-list">
      <div class="notification-item unread">
        <div class="notification-icon info">
          <i class="bi bi-info-circle"></i>
        </div>
        <div class="notification-content">
          <div class="notification-message">Selamat datang, <?php echo htmlspecialchars($user_data['username']); ?>!</div>
          <div class="notification-time">Baru saja</div>
        </div>
      </div>
      <div class="notification-item unread">
        <div class="notification-icon success">
          <i class="bi bi-check-circle"></i>
        </div>
        <div class="notification-content">
          <div class="notification-message">Login berhasil</div>
          <div class="notification-time">Baru saja</div>
        </div>
      </div>
      <div class="notification-item">
        <div class="notification-icon warning">
          <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div class="notification-content">
          <div class="notification-message">Jangan lupa logout saat selesai</div>
          <div class="notification-time">1 menit yang lalu</div>
        </div>
      </div>
    </div>
  </div>

  <div class="profile-dropdown" id="profileDropdown">
    <div class="profile-dropdown-header">
      <div class="profile-dropdown-avatar"><?php echo $initials; ?></div>
      <div class="profile-dropdown-name"><?php echo htmlspecialchars($user_data['username']); ?></div>
      <div class="profile-dropdown-role">Pengguna</div>
    </div>
    <div class="profile-dropdown-menu">
      <a href="#" class="profile-dropdown-item">
        <i class="bi bi-person profile-dropdown-icon"></i>
        Profil Saya
      </a>
      <a href="#" class="profile-dropdown-item">
        <i class="bi bi-gear profile-dropdown-icon"></i>
        Pengaturan
      </a>
      <a href="#" class="profile-dropdown-item">
        <i class="bi bi-moon profile-dropdown-icon"></i>
        Mode Gelap
      </a>
      <div class="profile-dropdown-divider"></div>
      <a href="#" class="profile-dropdown-item">
        <i class="bi bi-question-circle profile-dropdown-icon"></i>
        Bantuan
      </a>
      <a href="?logout=true" class="profile-dropdown-item">
        <i class="bi bi-box-arrow-right profile-dropdown-icon"></i>
        Keluar
      </a>
    </div>
  </div>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <button class="sidebar-close" id="sidebarClose">
        <i class="bi bi-x"></i>
      </button>
      <span>Menu</span>
    </div>
    <div class="sidebar-profile">
      <div class="profile-avatar"><?php echo $initials; ?></div>
      <div class="profile-name"><?php echo htmlspecialchars($user_data['username']); ?></div>
      <div class="profile-role">Pengguna</div>
    </div>
    <nav class="sidebar-menu">
      <div class="menu-section">
        <div class="menu-section-title">Perpustakaan</div>
        <a href="#" class="menu-item">
          <i class="bi bi-book menu-icon"></i>
          Buku
        </a>
        <a href="#" class="menu-item">
          <i class="bi bi-journal-text menu-icon"></i>
          Jurnal
        </a>
        <a href="#" class="menu-item">
          <i class="bi bi-file-earmark-text menu-icon"></i>
          Tesis
        </a>
      </div>
      <div class="menu-section">
        <div class="menu-section-title">Lainnya</div>
        <a href="#" class="menu-item">
          <i class="bi bi-newspaper menu-icon"></i>
          Berita
        </a>
        <a href="#" class="menu-item">
          <i class="bi bi-calendar-event menu-icon"></i>
          Riwayat
        </a>
        <a href="#" class="menu-item">
          <i class="bi bi-gear menu-icon"></i>
          Pengaturan
        </a>
        <a href="?logout=true" class="menu-item">
          <i class="bi bi-box-arrow-right menu-icon"></i>
          Keluar
        </a>
      </div>
    </nav>
  </aside>

  <main class="main-content">
    <section class="banner-section">
      <div class="banner-carousel">
        <div class="banner-slide active">
          <div class="banner-content">
            <h2>Selamat datang, <?php echo htmlspecialchars($user_data['username']); ?>!</h2>
            <p>Sistem Informasi Aset Repository Polije siap membantu Anda</p>
            <button class="banner-btn">Mulai Eksplorasi</button>
          </div>
        </div>
        <div class="banner-slide">
          <div class="banner-content">
            <h2>Perpustakaan Digital</h2>
            <p>Akses ribuan koleksi buku dan jurnal akademik</p>
            <button class="banner-btn">Buka Perpustakaan</button>
          </div>
        </div>
        <div class="banner-slide">
          <div class="banner-content">
            <h2>Kelola Data Akademik</h2>
            <p>Sistem informasi kampus yang terintegrasi dan mudah digunakan</p>
            <button class="banner-btn">Mulai Sekarang</button>
          </div>
        </div>
        <div class="banner-dots">
          <span class="banner-dot active" data-slide="0"></span>
          <span class="banner-dot" data-slide="1"></span>
          <span class="banner-dot" data-slide="2"></span>
        </div>
      </div>
    </section>

    <section class="stats-section">
      <div class="stats-header">
        <h3 class="stats-title">Statistik Hari Ini</h3>
        <div class="stats-timer">
          <i class="bi bi-clock"></i>
          <span>Update: <?php echo date('H:i'); ?></span>
        </div>
      </div>
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">👥</div>
          <div class="stat-label">Anggota Sipora</div>
          <div class="stat-value"><?php echo number_format($stats_data['total_users']); ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">📚</div>
          <div class="stat-label">Buku</div>
          <div class="stat-value"><?php echo number_format($stats_data['total_books']); ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">📄</div>
          <div class="stat-label">Jurnal</div>
          <div class="stat-value"><?php echo number_format($stats_data['total_journals']); ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">🎓</div>
          <div class="stat-label">Tesis</div>
          <div class="stat-value"><?php echo number_format($stats_data['total_theses']); ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">📁</div>
          <div class="stat-label">Arsip</div>
          <div class="stat-value"><?php echo number_format($stats_data['total_archives']); ?></div>
        </div>
      </div>
    </section>

    <div class="filter-bar">
      <div class="filter-group">
        <span class="filter-label">Jurusan:</span>
        <select class="filter-select" id="departmentFilter">
          <option value="">Semua Jurusan</option>
          <?php
          try {
            $stmt = $pdo->query("SELECT id_jurusan, nama_jurusan FROM master_jurusan ORDER BY nama_jurusan");
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo '<option value="' . $row['id_jurusan'] . '">' . htmlspecialchars($row['nama_jurusan']) . '</option>';
              }
          } catch (PDOException $e) {
              echo '<option value="">Error memuat jurusan</option>';
          }
          ?>
        </select>
      </div>
      <div class="filter-group">
        <span class="filter-label">Tahun:</span>
        <select class="filter-select" id="yearFilter">
          <option value="">Semua Tahun</option>
          <?php
          try {
              $stmt = $pdo->query("SELECT DISTINCT year_id FROM dokumen ORDER BY year_id DESC");
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo '<option value="' . $row['year_id'] . '">' . $row['year_id'] . '</option>';
              }
          } catch (PDOException $e) {
              echo '<option value="">Error memuat tahun</option>';
          }
          ?>
        </select>
      </div>
      <div class="filter-group">
        <span class="filter-label">Status:</span>
        <select class="filter-select" id="statusFilter">
          <option value="">Semua Status</option>
          <?php
          try {
              $stmt = $pdo->query("SELECT status_id, nama_status FROM status ORDER BY nama_status");
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo '<option value="' . $row['status_id'] . '">' . htmlspecialchars($row['nama_status']) . '</option>';
              }
          } catch (PDOException $e) {
              echo '<option value="">Error memuat status</option>';
          }
          ?>
        </select>
      </div>
      <div class="filter-group">
        <span class="filter-label">Urutkan:</span>
        <select class="filter-select" id="sortFilter">
          <option value="newest">Terbaru</option>
          <option value="oldest">Terlama</option>
          <option value="name-az">Nama A-Z</option>
          <option value="name-za">Nama Z-A</option>
          <option value="popular">Terpopuler</option>
        </select>
      </div>
      <div class="view-toggle">
        <button class="view-btn active" id="gridViewBtn">
          <i class="bi bi-grid-3x3-gap"></i>
        </button>
        <button class="view-btn" id="listViewBtn">
          <i class="bi bi-list-ul"></i>
        </button>
      </div>
    </div>

    <section class="cards-section">
      <div class="section-header">
        <h2 class="section-title">Koleksi Perpustakaan</h2>
        <div class="section-actions">
          <a href="#" class="see-all-btn">Lihat Semua →</a>
          <button class="add-btn" id="uploadBtn1">
            <i class="bi bi-upload"></i>
            Unggah
          </button>
        </div>
      </div>

      <div class="cards-grid" id="cardsGrid1">
        <?php if (empty($books_data)): ?>
          <div class="empty-state">
            <i class="bi bi-inbox empty-icon"></i>
            <p class="empty-message">Tidak ada dokumen ditemukan. Unggah dokumen pertama Anda!</p>
          </div>
        <?php else: ?>
          <?php foreach ($books_data as $book): ?>
            <div class="card" data-department="<?php echo htmlspecialchars($book['department']); ?>" data-year="<?php echo htmlspecialchars($book['year']); ?>">
              <div class="card-thumbnail">
                <img src="https://picsum.photos/seed/<?php echo $book['id_book']; ?>/160/160.jpg" alt="Dokumen">
                <span class="card-badge">Buku</span>
                <div class="card-type-icon">
                  <i class="bi bi-book"></i>
                </div>
              </div>
              <div class="card-info">
                <div class="card-title"><?php echo htmlspecialchars($book['title']); ?></div>
                <div class="card-meta">
                  <i class="bi bi-building"></i>
                  <span><?php echo htmlspecialchars($book['department']); ?></span>
                </div>
                <div class="card-meta">
                  <i class="bi bi-calendar"></i>
                  <span><?php echo htmlspecialchars($book['year']); ?></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <section class="cards-section">
      <div class="section-header">
        <h2 class="section-title">Koleksi Tesis</h2>
        <div class="section-actions">
          <a href="#" class="see-all-btn">Lihat Semua →</a>
          <button class="add-btn" id="uploadBtn2">
            <i class="bi bi-upload"></i>
            Unggah
          </button>
        </div>
      </div>
      <div class="cards-grid" id="cardsGrid2">
        <?php if (empty($theses_data)): ?>
          <div class="empty-state">
            <i class="bi bi-inbox empty-icon"></i>
            <p class="empty-message">Tidak ada tesis ditemukan. Unggah tesis pertama Anda!</p>
          </div>
        <?php else: ?>
          <?php foreach ($theses_data as $thesis): ?>
            <div class="card" data-department="<?php echo $thesis['department']; ?>" data-year="<?php echo $thesis['year']; ?>" data-category="thesis">
              <div class="card-thumbnail">
                <img src="https://picsum.photos/seed/<?php echo $thesis['id_thesis']; ?>/160/160.jpg" alt="Tesis">
                <span class="card-badge">Tesis</span>
                <div class="card-type-icon">
                  <i class="bi bi-file-earmark-text"></i>
                </div>
              </div>
              <div class="card-info">
                <div class="card-title"><?php echo htmlspecialchars($thesis['title']); ?></div>
                <div class="card-meta">
                  <i class="bi bi-building"></i>
                  <span><?php echo htmlspecialchars($thesis['department']); ?></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <section class="cards-section">
      <div class="section-header">
        <h2 class="section-title">Koleksi E-Book</h2>
        <div class="section-actions">
          <a href="#" class="see-all-btn">Lihat Semua →</a>
          <button class="add-btn" id="uploadBtn3">
            <i class="bi bi-upload"></i>
            Unggah
          </button>
        </div>
      </div>
      <div class="cards-grid" id="cardsGrid3">
        <?php if (empty($ebooks_data)): ?>
          <div class="empty-state">
            <i class="bi bi-inbox empty-icon"></i>
            <p class="empty-message">Tidak ada e-book ditemukan. Unggah e-book pertama Anda!</p>
          </div>
        <?php else: ?>
          <?php foreach ($ebooks_data as $ebook): ?>
            <div class="card" data-department="<?php echo $ebook['department']; ?>" data-year="<?php echo $ebook['year']; ?>" data-category="ebook">
              <div class="card-thumbnail">
                <img src="https://picsum.photos/seed/<?php echo $ebook['id_ebook']; ?>/160/160.jpg" alt="E-Book">
                <span class="card-badge">E-Book</span>
                <div class="card-type-icon">
                  <i class="bi bi-book"></i>
                </div>
              </div>
              <div class="card-info">
                <div class="card-title"><?php echo htmlspecialchars($ebook['title']); ?></div>
                <div class="card-meta">
                  <i class="bi bi-building"></i>
                  <span><?php echo htmlspecialchars($ebook['department']); ?></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <section class="cards-section">
      <div class="section-header">
        <h2 class="section-title">Koleksi Jurnal</h2>
        <div class="section-actions">
          <a href="#" class="see-all-btn">Lihat Semua →</a>
          <button class="add-btn" id="uploadBtn4">
            <i class="bi bi-upload"></i>
            Unggah
          </button>
        </div>
      </div>
      <div class="cards-grid" id="cardsGrid4">
        <?php if (empty($journals_data)): ?>
          <div class="empty-state">
            <i class="bi bi-inbox empty-icon"></i>
            <p class="empty-message">Tidak ada jurnal ditemukan. Unggah jurnal pertama Anda!</p>
          </div>
        <?php else: ?>
          <?php foreach ($journals_data as $journal): ?>
            <div class="card" data-department="<?php echo $journal['department']; ?>" data-year="<?php echo $journal['year']; ?>" data-category="journal">
              <div class="card-thumbnail">
                <img src="https://picsum.photos/seed/<?php echo $journal['id_journal']; ?>/160/160.jpg" alt="Jurnal">
                <span class="card-badge">Jurnal</span>
                <div class="card-type-icon">
                  <i class="bi bi-journal-text"></i>
                </div>
              </div>
              <div class="card-info">
                <div class="card-title"><?php echo htmlspecialchars($journal['title']); ?></div>
                <div class="card-meta">
                  <i class="bi bi-calendar3"></i>
                  <span><?php echo htmlspecialchars($journal['volume']); ?> • <?php echo htmlspecialchars($journal['year']); ?></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <section class="news-section">
      <div class="section-header">
        <h2 class="section-title">Berita Terbaru</h2>
        <div class="section-actions">
          <a href="#" class="see-all-btn">Lihat Semua →</a>
        </div>
      </div>
      <?php if (empty($news_data)): ?>
        <div class="empty-state">
          <i class="bi bi-inbox empty-icon"></i>
          <p class="empty-message">Tidak ada berita ditemukan.</p>
        </div>
      <?php else: ?>
        <?php foreach ($news_data as $news): ?>
          <div class="news-item">
            <div class="news-thumbnail">
              <img src="https://picsum.photos/seed/<?php echo $news['id_news']; ?>/80/80.jpg" alt="Berita">
            </div>
            <div class="news-content">
              <div class="news-title"><?php echo htmlspecialchars($news['title']); ?></div>
              <div class="news-meta">
                <span class="news-category"><?php echo htmlspecialchars($news['category']); ?></span>
                <span>•</span>
                <span><?php echo date('d M Y', strtotime($news['post_date'])); ?></span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>

    <section class="list-section">
      <div class="section-header">
        <h2 class="section-title">Aksi Cepat</h2>
      </div>
      <div class="list-item" id="quickUploadItem">
        <div class="list-icon">
          <i class="bi bi-upload"></i>
        </div>
        <div class="list-content">
          <div class="list-title">Unggah Dokumen</div>
          <div class="list-subtitle">Tambah buku, jurnal, atau tesis</div>
        </div>
        <i class="bi bi-chevron-right list-arrow"></i>
      </div>
      <div class="list-item">
        <div class="list-icon">
          <i class="bi bi-graph-up"></i>
        </div>
        <div class="list-content">
          <div class="list-title">Lihat Statistik</div>
          <div class="list-subtitle">Analisis data kampus</div>
        </div>
        <i class="bi bi-chevron-right list-arrow"></i>
      </div>
      <div class="list-item">
        <div class="list-icon">
          <i class="bi bi-envelope"></i>
        </div>
        <div class="list-content">
          <div class="list-title">Kirim Pengumuman</div>
          <div class="list-subtitle">Buat pengumuman untuk semua</div>
        </div>
        <i class="bi bi-chevron-right list-arrow"></i>
      </div>
    </section>
  </main>

  <div class="upload-modal" id="uploadModal">
    <div class="upload-modal-content">
      <div class="upload-modal-header">
        <h3 class="upload-modal-title">Unggah Dokumen</h3>
        <button class="upload-modal-close" id="uploadModalClose">
          <i class="bi bi-x"></i>
        </button>
      </div>
      <div class="upload-modal-body">
        <form id="uploadForm" enctype="multipart/form-data">
          <input type="hidden" name="action" value="upload">
          <div class="upload-form-group">
            <label class="upload-form-label">Tipe Dokumen *</label>
            <select class="upload-form-select" id="documentType" name="tipe_dokumen" required>
              <option value="">Pilih tipe dokumen</option>
              <option value="book">Buku</option>
              <option value="journal">Jurnal</option>
              <option value="thesis">Tesis</option>
              <option value="final_project">Tugas Akhir</option>
              <option value="research">Penelitian</option>
              <option value="ebook">E-Book</option>
              <option value="other">Lainnya</option>
            </select>
          </div>
          
          <div class="upload-form-group">
            <label class="upload-form-label">Judul Dokumen *</label>
            <input type="text" class="upload-form-input" id="documentTitle" name="judul" placeholder="Masukkan judul dokumen" required>
          </div>
          
          <div class="upload-form-group">
            <label class="upload-form-label">Abstrak</label>
            <textarea class="upload-form-textarea" id="documentAbstract" name="abstrak" placeholder="Masukkan abstrak dokumen" rows="4"></textarea>
          </div>
          
          <div class="upload-form-group">
            <label class="upload-form-label">Tema/Topik *</label>
            <select class="upload-form-select" id="documentTheme" name="id_tema" required>
              <option value="">Pilih tema</option>
              <?php
              try {
                $stmt = $pdo->query("SELECT id_tema, nama_tema FROM master_tema ORDER BY nama_tema");
                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      echo '<option value="' . $row['id_tema'] . '">' . htmlspecialchars($row['nama_tema']) . '</option>';
                  }
              } catch (PDOException $e) {
                  echo '<option value="">Error memuat tema</option>';
              }
              ?>
            </select>
          </div>
          
          <div class="upload-form-group">
            <label class="upload-form-label">Jurusan *</label>
            <select class="upload-form-select" id="documentDepartment" name="id_jurusan" required>
              <option value="">Pilih jurusan</option>
              <?php
              try {
                 $stmt = $pdo->query("SELECT id_jurusan, nama_jurusan FROM master_jurusan ORDER BY nama_jurusan");
                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      echo '<option value="' . $row['id_jurusan'] . '">' . htmlspecialchars($row['nama_jurusan']) . '</option>';
                  }
              } catch (PDOException $e) {
                  echo '<option value="">Error memuat jurusan</option>';
              }
              ?>
            </select>
          </div>
          
          <div class="upload-form-group">
            <label class="upload-form-label">Program Studi *</label>
            <select class="upload-form-select" id="documentProdi" name="id_prodi" required>
              <option value="">Pilih program studi</option>
              <?php
              try {
                  $stmt = $pdo->query("SELECT id_prodi, nama_prodi FROM master_prodi ORDER BY nama_prodi");
                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      echo '<option value="' . $row['id_prodi'] . '">' . htmlspecialchars($row['nama_prodi']) . '</option>';
                  }
              } catch (PDOException $e) {
                  echo '<option value="">Error memuat program studi</option>';
              }
              ?>
            </select>
          </div>
          
          <div class="upload-form-group">
            <label class="upload-form-label">Tahun *</label>
            <select class="upload-form-select" id="documentYear" name="year_id" required>
              <option value="">Pilih tahun</option>
              <?php
              try {
                 $stmt = $pdo->query("SELECT year_id, tahun FROM master_tahun ORDER BY tahun DESC");
                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      echo '<option value="' . $row['year_id'] . '">' . htmlspecialchars($row['tahun']) . '</option>';
                  }
              } catch (PDOException $e) {
                  for($i = date('Y'); $i >= 2020; $i--) {
                      echo '<option value="' . $i . '">' . $i . '</option>';
                  }
              }
              ?>
            </select>
          </div>
          
          <div class="upload-form-group">
            <label class="upload-form-label">Status *</label>
            <select class="upload-form-select" id="documentStatus" name="status_id" required>
              <option value="">Pilih status</option>
              <?php
              try {
                  $stmt = $pdo->query("SELECT status_id, nama_status FROM master_status_dokumen ORDER BY nama_status");
                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      echo '<option value="' . $row['status_id'] . '">' . htmlspecialchars($row['nama_status']) . '</option>';
                  }
              } catch (PDOException $e) {
                  echo '<option value="1">Aktif</option>';
                  echo '<option value="2">Tidak Aktif</option>';
                  echo '<option value="3">Menunggu</option>';
              }
              ?>
            </select>
          </div>
          
          <div class="upload-form-group">
            <label class="upload-form-label">Format *</label>
            <select class="upload-form-select" id="documentFormat" name="format_id" required>
              <option value="">Pilih format</option>
              <?php
              try {
                  $stmt = $pdo->query("SELECT format_id, nama_format FROM master_dokumen ORDER BY nama_format");
                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      echo '<option value="' . $row['format_id'] . '">' . htmlspecialchars($row['nama_format']) . '</option>';
                  }
              } catch (PDOException $e) {
                  echo '<option value="1">PDF</option>';
                  echo '<option value="2">DOC/DOCX</option>';
                  echo '<option value="3">PPT/PPTX</option>';
              }
              ?>
            </select>
          </div>
          
          <div class="upload-form-group">
            <label class="upload-form-label">Kebijakan *</label>
            <select class="upload-form-select" id="documentPolicy" name="policy_id" required>
              <option value="">Pilih kebijakan</option>
              <?php
              try {
                  $stmt = $pdo->query("SELECT policy_id, nama_policy FROM master_policy ORDER BY nama_policy");
                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      echo '<option value="' . $row['policy_id'] . '">' . htmlspecialchars($row['nama_policy']) . '</option>';
                  }
              } catch (PDOException $e) {
                  echo '<option value="1">Akses Terbuka</option>';
                  echo '<option value="2">Akses Terbatas</option>';
              }
              ?>
            </select>
          </div>
          
          <div class="upload-form-group">
            <label class="upload-form-label">Unggah File *</label>
            <div class="upload-area" id="uploadArea">
              <i class="bi bi-cloud-upload upload-icon"></i>
              <p class="upload-text">Seret dan lepas file di sini atau klik untuk memilih</p>
              <p class="upload-subtext">Mendukung PDF, DOC, DOCX, JPG, PNG (maks. 10MB)</p>
              <input type="file" id="fileInput" name="dokumen_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display: none;" required>
            </div>
          </div>
          
          <div class="file-list" id="fileList"></div>
          
          <div class="upload-progress" id="uploadProgress" style="display: none;">
            <div class="progress-bar">
              <div class="progress-fill" id="progressFill"></div>
            </div>
            <div class="progress-text">
              <span id="progressPercent">0%</span>
              <span id="progressStatus">Mengunggah...</span>
            </div>
          </div>
        </form>
      </div>
      <div class="upload-modal-footer">
        <button type="button" class="btn btn-secondary" id="cancelUploadBtn">Batal</button>
        <button type="button" class="btn btn-primary" id="submitUploadBtn">Unggah</button>
      </div>
    </div>
  </div>

  <div class="fab" id="fab">
    <i class="bi bi-plus"></i>
  </div>

  <div class="quick-add-menu" id="quickAddMenu">
    <div class="quick-add-item" id="quickUploadBtn">
      <div class="quick-add-icon">
        <i class="bi bi-upload"></i>
      </div>
      <div class="quick-add-text">Unggah Dokumen</div>
    </div>
    <div class="quick-add-item">
      <div class="quick-add-icon">
        <i class="bi bi-book"></i>
      </div>
      <div class="quick-add-text">Tambah Buku</div>
    </div>
    <div class="quick-add-item">
      <div class="quick-add-icon">
        <i class="bi bi-journal-text"></i>
      </div>
      <div class="quick-add-text">Tambah Jurnal</div>
    </div>
    <div class="quick-add-item">
      <div class="quick-add-icon">
        <i class="bi bi-calendar-plus"></i>
      </div>
      <div class="quick-add-text">Buat Acara</div>
    </div>
  </div>

  <!-- Simple Toast Notification -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <strong class="me-auto">Notifikasi</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body" id="toastMessage">
        Halo, dunia! Ini adalah pesan toast.
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/script.js"></script>
  
  <!-- --- TAMBAHKAN SCRIPT INI --- -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const uploadForm = document.getElementById('uploadForm');
        const submitBtn = document.getElementById('submitUploadBtn');
        const uploadModal = document.getElementById('uploadModal');
        const fileInput = document.getElementById('fileInput');
        const uploadArea = document.getElementById('uploadArea');
        const fileList = document.getElementById('fileList');
        const toastLiveExample = document.getElementById('liveToast');
        const toastMessage = document.getElementById('toastMessage');

        // Pemetaan tipe dokumen ke grid yang sesuai
        const gridMap = {
            'book': '#cardsGrid1',
            'ebook': '#cardsGrid3',
            'thesis': '#cardsGrid2',
            'journal': '#cardsGrid4',
            'final_project': '#cardsGrid2', // Asumsikan final project masuk thesis
            'research': '#cardsGrid1', // Asumsikan research masuk library collection
            'other': '#cardsGrid1' // Asumsikan other masuk library collection
        };

        // Fungsi untuk menampilkan notifikasi Toast
        function showToast(message, type = 'success') {
            toastMessage.textContent = message;
            const toast = new bootstrap.Toast(toastLiveExample);
            toast.show();
        }

        // Event listener untuk submit form
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Validasi sederhana
            if (!uploadForm.checkValidity()) {
                uploadForm.reportValidity();
                return;
            }

            const formData = new FormData(uploadForm);
            
            // Tampilkan progress
            document.getElementById('uploadProgress').style.display = 'block';
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Mengunggah...';

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Tutup modal
                    const modal = bootstrap.Modal.getInstance(uploadModal);
                    modal.hide();
                    
                    // Tampilkan pesan sukses
                    showToast(data.message, 'success');

                    // Tambahkan kartu baru ke grid
                    if (data.newDocument) {
                        addNewCardToGrid(data.newDocument);
                    }

                    // Reset form
                    uploadForm.reset();
                    fileList.innerHTML = '';
                } else {
                    // Tampilkan pesan error
                    showToast(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan yang tidak diharapkan.', 'danger');
            })
            .finally(() => {
                // Sembunyikan progress dan kembalikan tombol
                document.getElementById('uploadProgress').style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Unggah';
            });
        });

        function addNewCardToGrid(doc) {
            const gridSelector = gridMap[doc.type] || '#cardsGrid1';
            const grid = document.querySelector(gridSelector);
            
            if (!grid) return;

            // Hapus pesan "empty state" jika ada
            const emptyState = grid.querySelector('.empty-state');
            if (emptyState) {
                emptyState.remove();
            }
            
            // Tentukan ikon berdasarkan tipe
            let iconClass = 'bi-file-earmark-text';
            if (doc.type === 'book' || doc.type === 'ebook') iconClass = 'bi-book';
            if (doc.type === 'journal') iconClass = 'bi-journal-text';

            // Buat HTML untuk kartu baru
            const newCardHtml = `
                <div class="card" data-department="${doc.department}" data-year="${doc.year}" style="animation: fadeIn 0.5s;">
                    <div class="card-thumbnail">
                        <img src="https://picsum.photos/seed/${doc.id}/160/160.jpg" alt="${doc.title}">
                        <span class="card-badge">${doc.type.charAt(0).toUpperCase() + doc.type.slice(1)}</span>
                        <div class="card-type-icon">
                            <i class="bi ${iconClass}"></i>
                        </div>
                    </div>
                    <div class="card-info">
                        <div class="card-title">${doc.title}</div>
                        <div class="card-meta">
                            <i class="bi bi-building"></i>
                            <span>${doc.department}</span>
                        </div>
                        <div class="card-meta">
                            <i class="bi bi-calendar"></i>
                            <span>${doc.year}</span>
                        </div>
                    </div>
                </div>
            `;
            
            // Tambahkan kartu baru ke awal grid
            grid.insertAdjacentHTML('afterbegin', newCardHtml);
        }

        // Logika untuk drag and drop dan klik area upload (jika belum ada di script.js)
        uploadArea.addEventListener('click', () => fileInput.click());
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                updateFileList();
            }
        });

        fileInput.addEventListener('change', updateFileList);

        function updateFileList() {
            fileList.innerHTML = '';
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.innerHTML = `
                    <i class="bi bi-file-earmark"></i>
                    <span>${file.name}</span>
                    <span>(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                `;
                fileList.appendChild(fileItem);
            }
        }
        
        // Deteksi ukuran layar dan sesuaikan tampilan
        function adjustForScreenSize() {
            const width = window.innerWidth;
            
            // Sesuaikan grid berdasarkan ukuran layar
            const grids = document.querySelectorAll('.cards-grid');
            grids.forEach(grid => {
                if (width < 576) {
                    grid.style.gridTemplateColumns = 'repeat(1, 1fr)';
                } else if (width < 768) {
                    grid.style.gridTemplateColumns = 'repeat(2, 1fr)';
                } else if (width < 1200) {
                    grid.style.gridTemplateColumns = 'repeat(3, 1fr)';
                } else {
                    grid.style.gridTemplateColumns = 'repeat(4, 1fr)';
                }
            });
            
            // Sesuaikan statistik grid
            const statsGrid = document.querySelector('.stats-grid');
            if (statsGrid) {
                if (width < 576) {
                    statsGrid.style.gridTemplateColumns = 'repeat(2, 1fr)';
                } else if (width < 768) {
                    statsGrid.style.gridTemplateColumns = 'repeat(3, 1fr)';
                } else {
                    statsGrid.style.gridTemplateColumns = 'repeat(5, 1fr)';
                }
            }
            
            // Sesuaikan filter bar
            const filterBar = document.querySelector('.filter-bar');
            if (filterBar) {
                if (width < 576) {
                    filterBar.style.flexDirection = 'column';
                } else {
                    filterBar.style.flexDirection = 'row';
                }
            }
        }
        
        // Panggil fungsi saat halaman dimuat
        adjustForScreenSize();
        
        // Panggil fungsi saat ukuran layar berubah
        window.addEventListener('resize', adjustForScreenSize);
        
        // Perbaikan untuk sentuhan pada perangkat mobile
        if ('ontouchstart' in window) {
            document.body.classList.add('touch-device');
            
            // Tambahkan event listener untuk sentuhan
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });
                
                card.addEventListener('touchend', function() {
                    this.style.transform = '';
                });
            });
        }
    });
  </script>
</body>
</html>