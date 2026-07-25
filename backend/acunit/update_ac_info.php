<?php
// =============================================
// FILE: backend/update_ac_info.php
// Update nama AC dan lokasi
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

$acUnitId   = (int) ($_POST['ac_unit_id'] ?? 0);
$acName     = trim($_POST['ac_name'] ?? '');
$acLocation = trim($_POST['ac_location'] ?? '');
$tempThreshold = (float) ($_POST['temp_threshold'] ?? 25.0);

if (!$acUnitId || empty($acName) || empty($acLocation)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare("
    UPDATE ac_units
    SET ac_name = :ac_name, ac_location = :ac_location, temp_threshold = :temp_threshold
    WHERE id_ac_units = :id
    ");
    $stmt->execute([
        ':ac_name'        => $acName,
        ':ac_location'    => $acLocation,
        ':temp_threshold' => $tempThreshold,
        ':id'             => $acUnitId,
    ]);

    // Catat activity log
    $log = $pdo->prepare("
        INSERT INTO activity_logs (user_id, ac_unit_id, action, old_value, new_value, performed_by, created_at)
        VALUES (:user_id, :ac_unit_id, 'update_info', :old_val, :new_val, :performed_by, NOW())
    ");
    $log->execute([
        ':user_id'      => $_SESSION['user_id'],
        ':ac_unit_id'   => $acUnitId,
        ':old_val'      => null,
        ':new_val'      => $acName . ' - ' . $acLocation,
        ':performed_by' => $_SESSION['username'],
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Info AC berhasil diperbarui.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}