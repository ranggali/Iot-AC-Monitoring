<?php
// =============================================
// FILE: backend/update_password.php
// Proses update password user
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
$newPassword     = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validasi kosong
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Semua field password harus diisi.']);
    exit;
}

// Validasi panjang password baru
if (strlen($newPassword) < 8) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Password baru minimal 8 karakter.']);
    exit;
}

// Validasi konfirmasi password
if ($newPassword !== $confirmPassword) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Konfirmasi password tidak cocok.']);
    exit;
}

try {
    $pdo = getDB();

    // Cek current password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id_users = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($currentPassword, $user['password'])) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Password saat ini tidak sesuai.']);
        exit;
    }

    // Hash password baru
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update password
    $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id_users = :id");
    $stmt->execute([
        ':password' => $hashedPassword,
        ':id'       => $_SESSION['user_id']
    ]);

    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Password berhasil diperbarui.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}