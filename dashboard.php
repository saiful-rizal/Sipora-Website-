<?php
require_once 'config/db.php';
require_once 'includes/home.php';

session_start();

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
 $stmt = $conn->prepare("SELECT id_user, username FROM users WHERE id_user = ?");
 $stmt->bind_param("i", $user_id);
 $stmt->execute();
 $result = $stmt->get_result();
 $user_data = $result->fetch_assoc();
 $stmt->close();

 $initials = '';
if (!empty($user_data['username'])) {
    $username_parts = explode('_', $user_data['username']);
    if (count($username_parts) > 1) {
        $initials = strtoupper(substr($username_parts[0], 0, 1) . substr(end($username_parts), 0, 1));
    } else {
        $initials = strtoupper(substr($user_data['username'], 0, 2));
    }
}

 $stats_data = [
    'total_users' => 1234,
    'total_buku' => 5432,
    'total_jurnal' => 892,
    'total_skripsi' => 456,
    'total_arsip' => 234
];

 $books_data = [
    ['id_buku' => 1, 'judul' => 'Struktur Data dan Algoritma', 'penulis' => 'Prof. Dr. Budi Santoso', 'jurusan' => 'ti', 'tahun' => '2024'],
    ['id_buku' => 2, 'judul' => 'Database Management Systems', 'penulis' => 'Dr. Ramesh & S. Sudarshan', 'jurusan' => 'si', 'tahun' => '2023'],
    ['id_buku' => 3, 'judul' => 'Pemrograman Web Modern', 'penulis' => 'Ahmad Fadillah', 'jurusan' => 'ti', 'tahun' => '2024'],
    ['id_buku' => 4, 'judul' => 'Machine Learning Fundamentals', 'penulis' => 'Dr. Sarah Johnson', 'jurusan' => 'si', 'tahun' => '2023'],
    ['id_buku' => 5, 'judul' => 'Keamanan Jaringan', 'penulis' => 'Ir. Bambang Sutrisno', 'jurusan' => 'te', 'tahun' => '2022'],
    ['id_buku' => 6, 'judul' => 'Sistem Operasi', 'penulis' => 'Prof. Dr. Andi Wijaya', 'jurusan' => 'ti', 'tahun' => '2023']
];

 $journals_data = [
    ['id_jurnal' => 1, 'judul' => 'International Journal of Machine Learning', 'volume' => 'Vol. 45 No. 3', 'jurusan' => 'si', 'tahun' => '2024'],
    ['id_jurnal' => 2, 'judul' => 'Journal of Internet of Things Research', 'volume' => 'Vol. 12 No. 2', 'jurusan' => 'te', 'tahun' => '2024'],
    ['id_jurnal' => 3, 'judul' => 'Journal of Software Engineering', 'volume' => 'Vol. 28 No. 1', 'jurusan' => 'ti', 'tahun' => '2023'],
    ['id_jurnal' => 4, 'judul' => 'International Journal of Data Science', 'volume' => 'Vol. 15 No. 4', 'jurusan' => 'si', 'tahun' => '2023'],
    ['id_jurnal' => 5, 'judul' => 'Journal of Computer Networks', 'volume' => 'Vol. 33 No. 2', 'jurusan' => 'te', 'tahun' => '2022'],
    ['id_jurnal' => 6, 'judul' => 'Artificial Intelligence Review', 'volume' => 'Vol. 55 No. 1', 'jurusan' => 'ti', 'tahun' => '2024']
];

 $theses_data = [
    ['id_skripsi' => 1, 'judul' => 'Analisis Sistem Informasi Akademik Berbasis Web', 'penulis' => 'Ahmad Rizki, S.Kom', 'jurusan' => 'ti', 'tahun' => '2023'],
    ['id_skripsi' => 2, 'judul' => 'Penerapan Machine Learning untuk Prediksi', 'penulis' => 'Siti Nurhaliza, S.Kom', 'jurusan' => 'si', 'tahun' => '2024'],
    ['id_skripsi' => 3, 'judul' => 'Rancang Bangun Smart Home System', 'penulis' => 'Budi Santoso, S.T.', 'jurusan' => 'te', 'tahun' => '2023'],
    ['id_skripsi' => 4, 'judul' => 'Sistem Monitoring Kesehatan Pasien', 'penulis' => 'Dewi Lestari, S.T.', 'jurusan' => 'te', 'tahun' => '2024'],
    ['id_skripsi' => 5, 'judul' => 'Aplikasi E-Learning Berbasis Mobile', 'penulis' => 'Rizky Pratama, S.Kom', 'jurusan' => 'ti', 'tahun' => '2022'],
    ['id_skripsi' => 6, 'judul' => 'Optimasi Jaringan dengan SDN', 'penulis' => 'Maya Sari, S.T.', 'jurusan' => 'te', 'tahun' => '2023']
];

 $ebooks_data = [
    ['id_ebook' => 1, 'judul' => 'Digital Marketing Strategy', 'penulis' => 'Dr. Michael Chen', 'jurusan' => 'mn', 'tahun' => '2024'],
    ['id_ebook' => 2, 'judul' => 'Financial Accounting Basics', 'penulis' => 'Prof. Dr. Lisa Anderson', 'jurusan' => 'ak', 'tahun' => '2023'],
    ['id_ebook' => 3, 'judul' => 'Business Intelligence Guide', 'penulis' => 'Robert Williams', 'jurusan' => 'si', 'tahun' => '2024'],
    ['id_ebook' => 4, 'judul' => 'Quality Management Systems', 'penulis' => 'Dr. James Brown', 'jurusan' => 'tm', 'tahun' => '2023'],
    ['id_ebook' => 5, 'judul' => 'Project Management Professional', 'penulis' => 'Sarah Davis', 'jurusan' => 'mn', 'tahun' => '2022'],
    ['id_ebook' => 6, 'judul' => 'Supply Chain Management', 'penulis' => 'Dr. David Miller', 'jurusan' => 'mn', 'tahun' => '2024']
];

 $news_data = [
    ['id_berita' => 1, 'judul' => 'Polije Meraih Peringkat Terbaik dalam Kompetisi Robotik Nasional', 'kategori' => 'Prestasi', 'tanggal_post' => '2024-03-15'],
    ['id_berita' => 2, 'judul' => 'Pembukaan Program Studi Baru: Teknologi Informasi Kesehatan', 'kategori' => 'Akademik', 'tanggal_post' => '2024-03-14'],
    ['id_berita' => 3, 'judul' => 'Seminar Nasional: Tantangan dan Peluang Industri 5.0', 'kategori' => 'Acara', 'tanggal_post' => '2024-03-12']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    $title = clean_input($_POST['title']);
    $author = clean_input($_POST['author']);
    $jurusan = clean_input($_POST['jurusan']);
    $year = clean_input($_POST['year']);
    $category = clean_input($_POST['category']);
    $description = clean_input($_POST['description']);
    
    $response = ['success' => false, 'message' => 'Terjadi kesalahan.'];

    if (!empty($title) && !empty($author) && !empty($jurusan) && !empty($year) && !empty($category)) {
        $response['success'] = true;
        $response['message'] = 'Dokumen berhasil diunggah! (Mode Demo)';
    } else {
        $response['message'] = 'Semua field wajib diisi!';
    }
    echo json_encode($response);
    exit();
}
 $conn->close();
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
    <div class="loading-text">SISTEM INFORMASI POLIJE REPOSITORY ASSETS</div>
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
        <button class="filter-chip" data-filter="thesis">Skripsi</button>
      </div>
      <div class="search-results" id="searchResults">
        <div class="search-result-item">
          <div class="search-result-icon">
            <i class="bi bi-person"></i>
          </div>
          <div class="search-result-content">
            <div class="search-result-title">Ahmad Rizki</div>
            <div class="search-result-subtitle">Teknik Informatika • 00123</div>
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
            <div class="search-result-title">International Journal of Machine Learning</div>
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
          <div class="notification-message">Jangan lupa logout setelah selesai</div>
          <div class="notification-time">1 menit yang lalu</div>
        </div>
      </div>
    </div>
  </div>

  <div class="profile-dropdown" id="profileDropdown">
    <div class="profile-dropdown-header">
      <div class="profile-dropdown-avatar"><?php echo $initials; ?></div>
      <div class="profile-dropdown-name"><?php echo htmlspecialchars($user_data['username']); ?></div>
      <div class="profile-dropdown-role">User</div>
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
      <div class="profile-role">User</div>
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
          Skripsi
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
          History
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
            <h2>Selamat Datang, <?php echo htmlspecialchars($user_data['username']); ?>!</h2>
            <p>Sistem Informasi Polije Repository Assets siap membantu Anda</p>
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
          <div class="stat-label">Member Sipora</div>
          <div class="stat-value"><?php echo number_format($stats_data['total_users']); ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">📚</div>
          <div class="stat-label">Buku</div>
          <div class="stat-value"><?php echo number_format($stats_data['total_buku']); ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">📄</div>
          <div class="stat-label">Jurnal</div>
          <div class="stat-value"><?php echo number_format($stats_data['total_jurnal']); ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">🎓</div>
          <div class="stat-label">Skripsi</div>
          <div class="stat-value"><?php echo number_format($stats_data['total_skripsi']); ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">📁</div>
          <div class="stat-label">Arsip</div>
          <div class="stat-value"><?php echo number_format($stats_data['total_arsip']); ?></div>
        </div>
      </div>
    </section>

    <div class="filter-bar">
      <div class="filter-group">
        <span class="filter-label">Jurusan:</span>
        <select class="filter-select" id="jurusanFilter">
          <option value="">Semua Jurusan</option>
          <option value="ti">Teknik Informatika</option>
          <option value="si">Sistem Informasi</option>
          <option value="te">Teknik Elektro</option>
          <option value="tm">Teknik Mesin</option>
          <option value="ts">Teknik Sipil</option>
          <option value="mi">Manajemen Informatika</option>
          <option value="ak">Akuntansi</option>
          <option value="mn">Manajemen</option>
        </select>
      </div>
      <div class="filter-group">
        <span class="filter-label">Tahun:</span>
        <select class="filter-select" id="tahunFilter">
          <option value="">Semua Tahun</option>
          <option value="2024">2024</option>
          <option value="2023">2023</option>
          <option value="2022">2022</option>
          <option value="2021">2021</option>
          <option value="2020">2020</option>
        </select>
      </div>
      <div class="filter-group">
        <span class="filter-label">Kategori:</span>
        <select class="filter-select" id="kategoriFilter">
          <option value="">Semua Kategori</option>
          <option value="buku">Buku</option>
          <option value="jurnal">Jurnal</option>
          <option value="skripsi">Skripsi</option>
          <option value="tugas-akhir">Tugas Akhir</option>
          <option value="penelitian">Penelitian</option>
        </select>
      </div>
      <div class="filter-group">
        <span class="filter-label">Urutkan:</span>
        <select class="filter-select" id="urutkanFilter">
          <option value="terbaru">Terbaru</option>
          <option value="terlama">Terlama</option>
          <option value="nama-az">Nama A-Z</option>
          <option value="nama-za">Nama Z-A</option>
          <option value="terpopuler">Terpopuler</option>
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
        <?php foreach ($books_data as $book): ?>
          <div class="card" data-jurusan="<?php echo $book['jurusan']; ?>" data-tahun="<?php echo $book['tahun']; ?>" data-kategori="buku">
            <div class="card-thumbnail">
              <img src="https://picsum.photos/seed/<?php echo $book['id_buku']; ?>/160/160.jpg" alt="Buku">
              <span class="card-badge">Buku</span>
              <div class="card-type-icon">
                <i class="bi bi-book"></i>
              </div>
            </div>
            <div class="card-info">
              <div class="card-title"><?php echo htmlspecialchars($book['judul']); ?></div>
              <div class="card-meta">
                <i class="bi bi-person"></i>
                <span><?php echo htmlspecialchars($book['penulis']); ?></span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="cards-section">
      <div class="section-header">
        <h2 class="section-title">Koleksi Skripsi</h2>
        <div class="section-actions">
          <a href="#" class="see-all-btn">Lihat Semua →</a>
          <button class="add-btn" id="uploadBtn2">
            <i class="bi bi-upload"></i>
            Unggah
          </button>
        </div>
      </div>
      <div class="cards-grid" id="cardsGrid2">
        <?php foreach ($theses_data as $thesis): ?>
          <div class="card" data-jurusan="<?php echo $thesis['jurusan']; ?>" data-tahun="<?php echo $thesis['tahun']; ?>" data-kategori="skripsi">
            <div class="card-thumbnail">
              <img src="https://picsum.photos/seed/<?php echo $thesis['id_skripsi']; ?>/160/160.jpg" alt="Skripsi">
              <span class="card-badge">Skripsi</span>
              <div class="card-type-icon">
                <i class="bi bi-file-earmark-text"></i>
              </div>
            </div>
            <div class="card-info">
              <div class="card-title"><?php echo htmlspecialchars($thesis['judul']); ?></div>
              <div class="card-meta">
                <i class="bi bi-person"></i>
                <span><?php echo htmlspecialchars($thesis['penulis']); ?></span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
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
        <?php foreach ($ebooks_data as $ebook): ?>
          <div class="card" data-jurusan="<?php echo $ebook['jurusan']; ?>" data-tahun="<?php echo $ebook['tahun']; ?>" data-kategori="ebook">
            <div class="card-thumbnail">
              <img src="https://picsum.photos/seed/<?php echo $ebook['id_ebook']; ?>/160/160.jpg" alt="E-Book">
              <span class="card-badge">E-Book</span>
              <div class="card-type-icon">
                <i class="bi bi-book"></i>
              </div>
            </div>
            <div class="card-info">
              <div class="card-title"><?php echo htmlspecialchars($ebook['judul']); ?></div>
              <div class="card-meta">
                <i class="bi bi-person"></i>
                <span><?php echo htmlspecialchars($ebook['penulis']); ?></span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
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
        <?php foreach ($journals_data as $journal): ?>
          <div class="card" data-jurusan="<?php echo $journal['jurusan']; ?>" data-tahun="<?php echo $journal['tahun']; ?>" data-kategori="jurnal">
            <div class="card-thumbnail">
              <img src="https://picsum.photos/seed/<?php echo $journal['id_jurnal']; ?>/160/160.jpg" alt="Jurnal">
              <span class="card-badge">Jurnal</span>
              <div class="card-type-icon">
                <i class="bi bi-journal-text"></i>
              </div>
            </div>
            <div class="card-info">
              <div class="card-title"><?php echo htmlspecialchars($journal['judul']); ?></div>
              <div class="card-meta">
                <i class="bi bi-calendar3"></i>
                <span><?php echo htmlspecialchars($journal['volume']); ?> • <?php echo htmlspecialchars($journal['tahun']); ?></span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="news-section">
      <div class="section-header">
        <h2 class="section-title">Berita Terkini</h2>
        <div class="section-actions">
          <a href="#" class="see-all-btn">Lihat Semua →</a>
        </div>
      </div>
      <?php foreach ($news_data as $news): ?>
        <div class="news-item">
          <div class="news-thumbnail">
            <img src="https://picsum.photos/seed/<?php echo $news['id_berita']; ?>/80/80.jpg" alt="Berita">
          </div>
          <div class="news-content">
            <div class="news-title"><?php echo htmlspecialchars($news['judul']); ?></div>
            <div class="news-meta">
              <span class="news-category"><?php echo htmlspecialchars($news['kategori']); ?></span>
              <span>•</span>
              <span><?php echo date('d M Y', strtotime($news['tanggal_post'])); ?></span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
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
          <div class="list-subtitle">Tambah buku, jurnal, atau skripsi</div>
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
          <div class="upload-form-group">
            <label class="upload-form-label">Judul Dokumen</label>
            <input type="text" class="upload-form-input" id="documentTitle" name="title" placeholder="Masukkan judul dokumen" required>
          </div>
          
          <div class="upload-form-group">
            <label class="upload-form-label">Penulis/Pengarang</label>
            <input type="text" class="upload-form-input" id="documentAuthor" name="author" placeholder="Masukkan nama penulis/pengarang" required>
          </div>
          
          <div class="upload-form-group">
            <label class="upload-form-label">Jurusan</label>
            <select class="upload-form-select" id="documentJurusan" name="jurusan" required>
              <option value="">Pilih jurusan</option>
              <option value="ti">Teknik Informatika</option>
              <option value="si">Sistem Informasi</option>
              <option value="te">Teknik Elektro</option>
              <option value="tm">Teknik Mesin</option>
              <option value="ts">Teknik Sipil</option>
              <option value="mi">Manajemen Informatika</option>
              <option value="ak">Akuntansi</option>
              <option value="mn">Manajemen</option>
            </select>
          </div>
          
          <div class="upload-form-group">
            <label class="upload-form-label">Tahun</label>
            <input type="number" class="upload-form-input" id="documentYear" name="year" placeholder="Contoh: 2024" min="2000" max="2030" required>
          </div>
          
          <div class="upload-form-group">
            <label class="upload-form-label">Kategori</label>
            <select class="upload-form-select" id="documentCategory" name="category" required>
              <option value="">Pilih kategori</option>
              <option value="buku">Buku</option>
              <option value="jurnal">Jurnal</option>
              <option value="skripsi">Skripsi</option>
              <option value="ebook">E-Book</option>
            </select>
          </div>
          
          <div class="upload-form-group">
            <label class="upload-form-label">Deskripsi</label>
            <textarea class="upload-form-textarea" id="documentDescription" name="description" placeholder="Masukkan deskripsi singkat dokumen"></textarea>
          </div>
          
          <div class="upload-form-group">
            <label class="upload-form-label">Unggah File</label>
            <div class="upload-area" id="uploadArea">
              <i class="bi bi-cloud-upload upload-icon"></i>
              <p class="upload-text">Seret dan lepas file di sini atau klik untuk memilih</p>
              <p class="upload-subtext">Mendukung PDF, DOC, DOCX, JPG, PNG (maks. 10MB)</p>
              <input type="file" id="fileInput" name="files[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" multiple style="display: none;">
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

  <script src="assets/js/script.js"></script>
</body>
</html>