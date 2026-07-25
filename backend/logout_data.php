<?php
// =============================================
// FILE: backend/logout_data.php
// Proses logout user (manual & timeout)
// =============================================

session_start();

require_once '../config/database.php';

// Cek apakah user memang sedang login
if (isset($_SESSION['user_id'])) {
    try {
        $pdo = getDB();

        // Catat ke auth_logs
        $stmt = $pdo->prepare("INSERT INTO auth_logs (user_id, action, created_at) VALUES (:user_id, 'logout', NOW())");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);

    } catch (PDOException $e) {
        // Tetap lanjutkan logout meskipun gagal catat log
    }
}

// Hapus semua data session
$_SESSION = [];

// Hapus cookie session
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Hancurkan session
session_destroy();

// Redirect ke login dengan pesan sesuai kondisi
$timeout = isset($_GET['timeout']) ? '?timeout=1' : '';
header('Location: ../../index.php' . $timeout);
exit;