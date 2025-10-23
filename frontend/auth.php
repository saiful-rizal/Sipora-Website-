<?php
session_start();
require_once __DIR__ . '/config/db.php'; // koneksi PDO

// Fungsi pembersih input sederhana
function clean_input($data) {
  return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Fungsi cek email SSO (.ac.id)
function is_sso_email($email) {
  return preg_match('/\.ac\.id$/', $email);
}

// ====================== LOGIN LOGIC ====================== //
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'login') {
  $username = clean_input($_POST['username']);
  $password = clean_input($_POST['password']);
  $remember = isset($_POST['remember']) ? 1 : 0;

  try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND status = 'approved' LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      if (!is_sso_email($user['email'])) {
        $login_error = "Akses ditolak! Hanya pengguna dengan email SSO (.ac.id) yang diizinkan.";
      } elseif (password_verify($password, $user['pasword_hash'])) {
        // Simpan sesi
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['login_time'] = time();

        // Remember me
        if ($remember) {
          setcookie('username', $username, time() + (86400 * 30), "/");
        }

        // Redirect sesuai role
        if ($user['role_id'] == 1) {
          header("Location: ../backend/dist/index.php");
        } elseif ($user['role_id'] == 2) {
          header("Location: dashboard.php");
        } else {
          header("Location: home.php");
        }
        exit();
      } else {
        $login_error = "Username atau password salah.";
      }
    } else {
      $login_error = "Akun tidak ditemukan atau belum disetujui.";
    }
  } catch (PDOException $e) {
    $login_error = "Terjadi kesalahan koneksi ke database.";
  }
}

// ====================== REGISTER LOGIC ====================== //
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'register') {
  $nama = trim($_POST['nama_lengkap']);
  $nim = trim($_POST['nomor_induk']);
  $username = clean_input($_POST['username']);
  $email = clean_input($_POST['email']);
  $password = clean_input($_POST['password']);
  $confirm_password = clean_input($_POST['confirmPassword']);

  if (!is_sso_email($email)) {
    $register_error = "Gunakan email kampus (.ac.id) untuk mendaftar.";
  } elseif ($password !== $confirm_password) {
    $register_error = "Password dan konfirmasi tidak cocok.";
  } else {
    try {
      $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR username = :username");
      $stmt->execute(['email' => $email, 'username' => $username]);
      $existing = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($existing) {
        $register_error = "Email atau username sudah terdaftar.";
      } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role_id = 2;
        $status = "approved";

        $insert = $pdo->prepare("
          INSERT INTO users (nama_lengkap, nomor_induk, email, username, pasword_hash, role_id, status, created_at)
          VALUES (:nama_lengkap, :nomor_induk, :email, :username, :password, :role_id, :status, NOW())
        ");
        $insert->execute([
          'nama_lengkap' => $nama,
          'nomor_induk' => $nim,
          'email' => $email,
          'username' => $username,
          'password' => $hashed_password,
          'role_id' => $role_id,
          'status' => $status
        ]);

        $register_success = "Registrasi berhasil! Silakan login menggunakan username: $username";
      }
    } catch (PDOException $e) {
      $register_error = "Terjadi kesalahan database: " . $e->getMessage();
    }
  }
}


// ====================== LOGOUT LOGIC ====================== //
if (isset($_GET['logout'])) {
  session_destroy();
  setcookie('username', '', time() - 3600, "/");
  header("Location: index.php");
  exit();
}

// ====================== AUTO LOGIN COOKIE ====================== //
if (!isset($_SESSION['user_id']) && isset($_COOKIE['username'])) {
  $username = $_COOKIE['username'];
  $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND status = 'approved'");
  $stmt->execute(['username' => $username]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && is_sso_email($user['email'])) {
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role_id'] = $user['role_id'];
    $_SESSION['login_time'] = time();
    header("Location: home.php");
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login & Register - SIPORA POLIJE</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/auth.css">
  <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>
  <div class="wave-container">
    <div class="wave"></div>
    <div class="wave"></div>
    <div class="wave"></div>
  </div>

  <div id="waterDropsContainer"></div>
  <div id="particlesContainer"></div>

  <div class="main-container min-h-screen flex items-center justify-center p-4">
    <div class="auth-card glass-card animate-fadeInUp overflow-hidden">
      <div class="flex h-full">
        <div class="hidden lg:flex lg:w-2/5 bg-gradient-to-br from-sky-50 to-blue-50 flex-col justify-center items-center p-10 relative">
          <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-10 left-10 w-32 h-32 bg-sky-200 rounded-full opacity-20 animate-float"></div>
            <div class="absolute bottom-10 right-10 w-40 h-40 bg-blue-200 rounded-full opacity-20 animate-float" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/3 left-1/4 w-24 h-24 bg-sky-300 rounded-full opacity-20 animate-float" style="animation-delay: 2s;"></div>
          </div>
          
          <div class="relative z-10 text-center">
            <div class="mb-6 animate-pulse">
              <img src="assets/logo_polije.png" alt="Logo Polije" class="w-28 h-auto mx-auto drop-shadow-lg">
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-3">Welcome to SIPORA</h1>
            <p class="text-base text-gray-600 mb-4">Sistem Informasi Polije Repository Assets</p>
            <p class="text-sm text-gray-500 max-w-xs mx-auto leading-relaxed">
              Platform terpadu untuk mengelola aset digital POLIJE
            </p>
          </div>
        </div>

        <div class="w-full lg:w-3/5 flex flex-col form-container">
          <div class="p-4 sm:p-6 lg:p-8">
            <div class="lg:hidden text-center mb-6">
              <img src="assets/images/logo_polije.png" alt="Logo Polije" class="w-20 h-auto mx-auto mb-3">
              <h1 class="text-2xl font-bold text-gray-800">SIPORA</h1>
            </div>

            <div class="flex mb-6 sm:mb-8 border-b border-gray-100">
              <button id="loginTab" class="flex-1 pb-3 text-center font-medium tab-active transition-all duration-300" onclick="switchTab('login')">
                Masuk
              </button>
              <button id="registerTab" class="flex-1 pb-3 text-center font-medium text-gray-500 hover:text-gray-700 transition-all duration-300" onclick="switchTab('register')">
                Daftar
              </button>
            </div>

            <div id="loginForm" class="space-y-4">
              <?php if (isset($login_error)): ?>
                <div class="alert alert-error">
                  <i class="fas fa-exclamation-circle"></i>
                  <?php echo $login_error; ?>
                </div>
              <?php endif; ?>
              
              <form method="POST" action="" id="loginFormElement">
                <input type="hidden" name="action" value="login">
                <div class="space-y-4">
                  <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input 
                      type="text" 
                      name="username"
                      required
                      class="input-field w-full px-4 py-3 rounded-lg"
                      placeholder="Masukan username"
                      onfocus="animateInput(this)"
                      onclick="createRipple(event, this)"
                      value="<?php echo isset($_COOKIE['username']) ? $_COOKIE['username'] : ''; ?>"
                    >
                  </div>

                  <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi</label>
                    <div class="relative">
                      <input 
                        type="password" 
                        name="password"
                        required
                        class="input-field w-full px-4 py-3 pr-12 rounded-lg"
                        placeholder="•••••••••"
                        onfocus="animateInput(this)"
                        onclick="createRipple(event, this)"
                      >
                      <button 
                        type="button" 
                        onclick="togglePassword(this)"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                      >
                        <i class="fas fa-eye text-sm"></i>
                      </button>
                    </div>
                  </div>

                  <div class="flex items-center justify-between">
                    <div class="flex items-center">
                      <input type="checkbox" id="rememberMe" name="remember" class="custom-checkbox" <?php echo isset($_COOKIE['username']) ? 'checked' : ''; ?>>
                      <label for="rememberMe" class="ml-2 text-sm text-gray-600">Ingat saya</label>
                    </div>
                    <a href="#" class="text-sm text-sky-600 hover:text-sky-500 transition-colors">Lupa kata sandi?</a>
                  </div>

                  <button type="submit" class="btn-primary w-full py-3 px-4 text-white font-medium rounded-lg relative wave-button">
                    <span class="btn-text">Masuk</span>
                    <div class="loading-spinner hidden">
                      <i class="fas fa-spinner animate-rotate"></i>
                    </div>
                  </button>
                </div>
              </form>

              <div class="divider">
                <span>Atau masuk dengan</span>
              </div>

              <div class="flex justify-center">
                <div id="googleSignInButton"></div>
              </div>
            </div>

            <div id="registerForm" class="space-y-4 hidden">
              <?php if (isset($register_error)): ?>
                <div class="alert alert-error">
                  <i class="fas fa-exclamation-circle"></i>
                  <?php echo $register_error; ?>
                </div>
              <?php endif; ?>

  <form method="POST" action="" id="registerFormElement" onsubmit="return validateRegisterForm()">
    <input type="hidden" name="action" value="register">
    <div class="space-y-4">
      
      <!-- 🔹 Nama Lengkap -->
      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
        <input 
          type="text" 
          name="nama_lengkap"
          id="namaLengkapInput"
          required
          class="input-field w-full px-4 py-3 rounded-lg"
          placeholder="Masukan nama lengkap"
          onfocus="animateInput(this)"
          onclick="createRipple(event, this)"
        >
      </div>

      <!-- 🔹 Nomor Induk -->
      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Induk</label>
        <input 
          type="text" 
          name="nomor_induk"
          id="nomorIndukInput"
          required
          class="input-field w-full px-4 py-3 rounded-lg"
          placeholder="Masukan NIM / NIP / Nomor Pegawai"
          onfocus="animateInput(this)"
          onclick="createRipple(event, this)"
        >
      </div>

      <!-- 🔹 Username -->
      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
        <input 
          type="text" 
          name="username"
          id="usernameInput"
          required
          class="input-field w-full px-4 py-3 rounded-lg"
          placeholder="Masukan username"
          onfocus="animateInput(this)"
          onclick="createRipple(event, this)"
          onblur="validateUsername()"
        >
        <div id="usernameWarning" class="email-warning hidden">
          <i class="fas fa-exclamation-triangle"></i>
          <span>Username minimal 3 karakter</span>
        </div>
      </div>

      <!-- 🔹 Email SSO -->
      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Email SSO <span class="text-red-500">*</span>
        </label>
        <input 
          type="email" 
          name="email"
          id="emailInput"
          required
          class="input-field w-full px-4 py-3 rounded-lg"
          placeholder="Masukan Akun SSO"
          onfocus="animateInput(this)"
          onclick="createRipple(event, this)"
          onblur="validateEmail()"
        >
        <div id="emailWarning" class="email-warning hidden">
          <i class="fas fa-exclamation-triangle"></i>
          <span>Hanya email dengan domain .ac.id yang diizinkan</span>
        </div>
      </div>

      <!-- 🔹 Password -->
      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi</label>
        <div class="relative">
          <input 
            type="password" 
            name="password"
            id="passwordInput"
            required
            minlength="8"
            class="input-field w-full px-4 py-3 pr-12 rounded-lg"
            placeholder="Minimal 8 karakter"
            onfocus="animateInput(this)"
            onclick="createRipple(event, this)"
          >
          <button 
            type="button" 
            onclick="togglePassword(this)"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
          >
            <i class="fas fa-eye text-sm"></i>
          </button>
        </div>
      </div>

      <!-- 🔹 Konfirmasi Password -->
      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Kata Sandi</label>
        <div class="relative">
          <input 
            type="password" 
            name="confirmPassword"
            id="confirmPasswordInput"
            required
            minlength="8"
            class="input-field w-full px-4 py-3 pr-12 rounded-lg"
            placeholder="Ulangi kata sandi"
            onfocus="animateInput(this)"
            onclick="createRipple(event, this)"
          >
          <button 
            type="button" 
            onclick="togglePassword(this)"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
          >
            <i class="fas fa-eye text-sm"></i>
          </button>
        </div>
      </div>

      <!-- 🔹 Persetujuan -->
      <div class="flex items-start">
        <input type="checkbox" id="agreeTerms" class="custom-checkbox mt-1" required>
        <label for="agreeTerms" class="ml-2 text-sm text-gray-600">
          Saya setuju dengan <a href="#" class="text-sky-600 hover:text-sky-500">syarat dan ketentuan</a>
        </label>
      </div>

      <!-- 🔹 Tombol Daftar -->
      <button type="submit" class="btn-primary w-full py-3 px-4 text-white font-medium rounded-lg relative wave-button">
        <span class="btn-text">Daftar</span>
        <div class="loading-spinner hidden">
          <i class="fas fa-spinner animate-rotate"></i>
        </div>
      </button>
    </div>
  </form>
</div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="successModal" class="fixed inset-0 bg-black bg-opacity-30 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 max-w-sm mx-4 shadow-xl animate-fadeInUp">
      <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-50 mb-4">
          <i class="fas fa-check text-green-500 text-xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Berhasil!</h3>
        <p class="text-sm text-gray-600" id="successMessage">Operasi berhasil dilakukan.</p>
        <button onclick="closeModal()" class="mt-4 w-full bg-sky-500 text-white py-2 px-4 rounded-lg hover:bg-sky-600 transition-colors">
          OK
        </button>
      </div>
    </div>
  </div>

  <script>
    // Initialize Google Sign-In when page loads
    window.onload = function() {
      // Ganti dengan Client ID Anda dari Google Cloud Console
      const clientId = 'MASUKKAN_CLIENT_ID_ANDA_DISINI';
      
      google.accounts.id.initialize({
        client_id: clientId,
        callback: handleGoogleSignIn,
        auto_select: false,
        cancel_on_tap_outside: false
      });
      
      // Render the Google Sign-In button
      google.accounts.id.renderButton(
        document.getElementById("googleSignInButton"),
        { 
          theme: "outline", 
          size: "large",
          text: "signin_with",
          width: 250,
          logo_alignment: "center"
        }
      );
      
      // Display the One Tap dialog
      setTimeout(function() {
        google.accounts.id.prompt();
      }, 1000);
    }

    // Function to handle Google Sign-In response
    function handleGoogleSignIn(response) {
      console.log('Google Sign-In response:', response);
      
      // Send the token to your server for verification
      fetch('google_auth.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          token: response.credential
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          window.location.href = 'home.php';
        } else {
          alert('Login gagal: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat login dengan Google');
      });
    }
  </script>
  <script src="assets/js/auth.js"></script>
</body>
</html>

<?php include 'includes/auth_template.php'; ?>