<?php
// =============================================
// FILE: backend/update_profile.php
// Proses update username & email user
// =============================================

header('Content-Type: application/json');

session_start();

require_once '../../config/database.php';

// Cek session
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

// Hanya terima method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');

// Validasi kosong
if (empty($username) || empty($email)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Username dan email tidak boleh kosong.']);
    exit;
}

// Validasi panjang username
if (strlen($username) < 3 || strlen($username) > 50) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Username harus antara 3-50 karakter.']);
    exit;
}

// Validasi format email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid.']);
    exit;
}

try {
    $pdo = getDB();

    // Cek username sudah dipakai user lain
    $stmt = $pdo->prepare("SELECT id_users FROM users WHERE username = :username AND id_users != :id");
    $stmt->execute([':username' => $username, ':id' => $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan.']);
        exit;
    }

    // Cek email sudah dipakai user lain
    $stmt = $pdo->prepare("SELECT id_users FROM users WHERE email = :email AND id_users != :id");
    $stmt->execute([':email' => $email, ':id' => $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['status' => 'error', 'message' => 'Email sudah digunakan.']);
        exit;
    }

    // Update data
    $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id_users = :id");
    $stmt->execute([
        ':username' => $username,
        ':email'    => $email,
        ':id'       => $_SESSION['user_id']
    ]);

    // Update session username
    $_SESSION['username'] = $username;

    http_response_code(200);
    echo json_encode([
        'status'  => 'success',
        'message' => 'Profile berhasil diperbarui.',
        'data'    => ['username' => $username, 'email' => $email]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}