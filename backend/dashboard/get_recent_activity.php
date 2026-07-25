<?php
// =============================================
// FILE: backend/get_recent_activity.php
// Ambil 3 aktivitas terakhir dari AC tertentu
// Dipanggil saat modal detail AC dibuka
// =============================================

header('Content-Type: application/json');
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']);
    exit;
}

$acUnitId = isset($_GET['ac_unit_id']) ? (int) $_GET['ac_unit_id'] : 0;

if (!$acUnitId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ac_unit_id tidak valid.']);
    exit;
}

try {
    $pdo = getDB();

    $stmt = $pdo->prepare("
        SELECT action, old_value, new_value, performed_by, created_at
        FROM activity_logs
        WHERE ac_unit_id = :ac_unit_id
        ORDER BY created_at DESC
        LIMIT 3
    ");
    $stmt->execute([':ac_unit_id' => $acUnitId]);
    $logs = $stmt->fetchAll();

    echo json_encode(['status' => 'success', 'data' => $logs]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}