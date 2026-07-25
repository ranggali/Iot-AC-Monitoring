<?php
// =============================================
// FILE: backend/login_data.php
// Proses login user
// =============================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

session_start();

require_once '../config/database.php';

// Hanya terima method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Method tidak diizinkan.'
    ]);
    exit;
}

// Ambil dan sanitasi input
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi input kosong
if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Username dan password tidak boleh kosong.'
    ]);
    exit;
}

try {
    $pdo = getDB();

    // Cari user berdasarkan username
    $stmt = $pdo->prepare("SELECT id_users, username, password FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    // Cek user ada dan password cocok
    if (!$user || !password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Username atau password salah.'
        ]);
        exit;
    }

    // Set session
    $_SESSION['user_id']  = $user['id_users'];
    $_SESSION['username'] = $user['username'];

    // Update last_login
    $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id_users = :id");
    $stmt->execute([':id' => $user['id_users']]);

    // Catat ke auth_logs
    $stmt = $pdo->prepare("INSERT INTO auth_logs (user_id, action, created_at) VALUES (:user_id, 'login', NOW())");
    $stmt->execute([':user_id' => $user['id_users']]);

    http_response_code(200);
    echo json_encode([
        'status'  => 'success',
        'message' => 'Login berhasil.'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Terjadi kesalahan pada server.'
    ]);
}
