<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
    exit();
}

 $data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['token'])) {
    echo json_encode(['success' => false, 'message' => 'Token tidak ditemukan']);
    exit();
}

 $token = $data['token'];

// Verifikasi token dengan Google API langsung (tanpa library)
 $client_id = 'MASUKKAN_CLIENT_ID_ANDA_DISINI'; // Ganti dengan Client ID Anda
 $url = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=" . $token;

 $response = file_get_contents($url);
 $data = json_decode($response, true);

if ($data && isset($data['aud']) && $data['aud'] === $client_id) {
    $email = $data['email'];
    $name = $data['name'];
    
    // Periksa apakah email adalah SSO (.ac.id)
    if (!is_sso_email($email)) {
        echo json_encode(['success' => false, 'message' => 'Hanya email dengan domain .ac.id yang diizinkan']);
        exit();
    }
    
    // Periksa apakah pengguna sudah ada
    $check_user = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($check_user);
    
    if ($result->num_rows > 0) {
        // Pengguna sudah ada, login
        $user_data = $result->fetch_assoc();
        
        $_SESSION['user_id'] = $user_data['id_user'];
        $_SESSION['username'] = $user_data['username'];
        $_SESSION['email'] = $user_data['email'];
        $_SESSION['role_id'] = $user_data['role_id'];
        $_SESSION['login_time'] = time();
        
        echo json_encode(['success' => true]);
    } else {
        // Buat pengguna baru
        $username = explode('@', $email)[0];
        $nomor_induk = 'GOOGLE' . date('Y') . sprintf('%04d', rand(1, 9999));
        $role_id = 2;
        
        $insert_user = "INSERT INTO users (nomor_induk, email, username, role_id, status, created_at)
            VALUES ('$nomor_induk', '$email', '$username', '$role_id', 'approved', NOW())";
        
        if ($conn->query($insert_user) === TRUE) {
            $user_id = $conn->insert_id;
            
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['role_id'] = $role_id;
            $_SESSION['login_time'] = time();
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal membuat pengguna baru']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Token tidak valid']);
}

 $conn->close();
?>