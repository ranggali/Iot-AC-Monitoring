<?php
// =============================================
// FILE: backend/update_device.php
// Update nama device
// =============================================

header('Content-Type: application/json');
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']);
    exit;
}

$deviceId   = (int)  ($_POST['id_devices']   ?? 0);
$deviceName = trim($_POST['device_name'] ?? '');

if (!$deviceId || empty($deviceName)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare("UPDATE devices SET device_name = :name WHERE id_devices = :id");
    $stmt->execute([':name' => $deviceName, ':id' => $deviceId]);

    echo json_encode(['status' => 'success', 'message' => 'Nama device berhasil diperbarui.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}