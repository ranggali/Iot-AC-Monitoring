<?php
// =============================================
// FILE: backend/update_target_temp.php
// ESP32 update target_temp di database
// saat proses turun bertahap
// =============================================

header('Content-Type: application/json');
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']);
    exit;
}

$raw        = file_get_contents('php://input');
$data       = json_decode($raw, true);
$acUnitId   = (int)   ($data['ac_unit_id']  ?? 0);
$targetTemp = (float) ($data['target_temp'] ?? 0);

if (!$acUnitId || $targetTemp < 16 || $targetTemp > 30) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid.']);
    exit;
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare("UPDATE ac_units SET target_temp = :temp WHERE id_ac_units = :id");
    $stmt->execute([':temp' => $targetTemp, ':id' => $acUnitId]);

    echo json_encode(['status' => 'success', 'message' => 'target_temp berhasil diperbarui.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}