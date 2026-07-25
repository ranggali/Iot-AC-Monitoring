<?php
// =============================================
// FILE: backend/get_user_data.php
// Ambil data user berdasarkan session
// =============================================

header('Content-Type: application/json');

session_start();

require_once '../../config/database.php';

// Cek session
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Unauthorized.'
    ]);
    exit;
}

// Hanya terima method GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Method tidak diizinkan.'
    ]);
    exit;
}

try {
    $pdo = getDB();

    $stmt = $pdo->prepare("SELECT id_users, username, email FROM users WHERE id_users = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(404);
        echo json_encode([
            'status'  => 'error',
            'message' => 'User tidak ditemukan.'
        ]);
        exit;
    }

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'data'   => [
            'username' => $user['username'],
            'email'    => $user['email'],
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Terjadi kesalahan pada server.'
    ]);
}