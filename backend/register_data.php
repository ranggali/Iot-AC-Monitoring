<?php
// =============================================
// FILE: backend/register_data.php
// Proses registrasi user baru
// =============================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

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
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi input kosong
if (empty($username) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Username, email, dan password tidak boleh kosong.'
    ]);
    exit;
}

// Validasi format email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Format email tidak valid.'
    ]);
    exit;
}

// Validasi panjang username
if (strlen($username) < 3 || strlen($username) > 50) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Username harus antara 3-50 karakter.'
    ]);
    exit;
}

// Validasi panjang password
if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Password minimal 8 karakter.'
    ]);
    exit;
}

try {
    $pdo = getDB();

    // Cek apakah username sudah digunakan
    $stmt = $pdo->prepare("SELECT id_users FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Username sudah digunakan.'
        ]);
        exit;
    }

    // Cek apakah email sudah digunakan
    $stmt = $pdo->prepare("SELECT id_users FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Email sudah digunakan.'
        ]);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Simpan user baru
    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password, created_at)
        VALUES (:username, :email, :password, NOW())
    ");
    $stmt->execute([
        ':username' => $username,
        ':email'    => $email,
        ':password' => $hashedPassword,
    ]);

    http_response_code(201);
    echo json_encode([
        'status'  => 'success',
        'message' => 'Registrasi berhasil.'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Terjadi kesalahan pada server.'
    ]);
}