<?php
// =============================================
// FILE: backend/delete_device.php
// Hapus device + semua AC units terkait
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

$deviceId = (int) ($_POST['id_devices'] ?? 0);

if (!$deviceId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'id_devices tidak valid.']);
    exit;
}

try {
    $pdo = getDB();

    // Cek apakah ada AC yang masih aktif
    $checkActive = $pdo->prepare("
        SELECT ac_name, ac_location FROM ac_units
        WHERE device_id = :id AND ac_status = 1
    ");
    $checkActive->execute([':id' => $deviceId]);
    $activeAcs = $checkActive->fetchAll();

    if (!empty($activeAcs)) {
        $names = implode(', ', array_map(fn($ac) => $ac['ac_name'] . ' (' . $ac['ac_location'] . ')', $activeAcs));
        http_response_code(409);
        echo json_encode([
            'status'  => 'error',
            'message' => 'AC berikut masih aktif: ' . $names . '. Matikan terlebih dahulu.'
        ]);
        exit;
    }
    $acStmt = $pdo->prepare("SELECT id_ac_units FROM ac_units WHERE device_id = :id");
    $acStmt->execute([':id' => $deviceId]);
    $acUnits = $acStmt->fetchAll();

    foreach ($acUnits as $ac) {
        $acId = $ac['id_ac_units'];
        $pdo->prepare("DELETE FROM schedules     WHERE ac_unit_id = :id")->execute([':id' => $acId]);
        $pdo->prepare("DELETE FROM activity_logs WHERE ac_unit_id = :id")->execute([':id' => $acId]);
    }

    // Hapus ac_units
    $pdo->prepare("DELETE FROM ac_units   WHERE device_id  = :id")->execute([':id' => $deviceId]);
    // Hapus sensor_logs
    $pdo->prepare("DELETE FROM sensor_logs WHERE device_id = :id")->execute([':id' => $deviceId]);
    // Hapus device
    $pdo->prepare("DELETE FROM devices    WHERE id_devices = :id")->execute([':id' => $deviceId]);

    echo json_encode(['status' => 'success', 'message' => 'Device berhasil dihapus.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}