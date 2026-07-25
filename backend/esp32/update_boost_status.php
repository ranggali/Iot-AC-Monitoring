<?php
// =============================================
// FILE: backendserver/esp32/update_boost_status.php
// Terima update status mode boost dari ESP32 (JSON body)
//
// Dipanggil oleh ESP32: updateBoostStatus(acUnitId, active)
// Body: {"ac_unit_id": <int>, "boost_active": 0|1}
// =============================================

header('Content-Type: application/json');
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']);
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'JSON tidak valid.']);
    exit;
}

$acUnitId    = (int) ($data['ac_unit_id'] ?? 0);
$boostActive = isset($data['boost_active']) ? (int) $data['boost_active'] : null;

if (!$acUnitId || $boostActive === null || !in_array($boostActive, [0, 1], true)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap atau tidak valid.']);
    exit;
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare("
        UPDATE ac_units
        SET boost_active = :boost_active
        WHERE id_ac_units = :id
    ");
    $stmt->execute([
        ':boost_active' => $boostActive,
        ':id'           => $acUnitId,
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Status boost berhasil diperbarui.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}