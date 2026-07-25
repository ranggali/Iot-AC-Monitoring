<?php
// =============================================
// FILE: backend/check_password.php
// Cek apakah current password benar (untuk real-time validation)
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

$currentPassword = $_POST['current_password'] ?? '';

if (empty($currentPassword)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Password tidak boleh kosong.']);
    exit;
}

try {
    $pdo = getDB();

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id_users = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($currentPassword, $user['password'])) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Password saat ini tidak sesuai.']);
        exit;
    }

    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Password sesuai.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}
