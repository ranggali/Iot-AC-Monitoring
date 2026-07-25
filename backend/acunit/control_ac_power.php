<?php
// =============================================
// FILE: backend/control_ac_power.php
// Kirim perintah power ON/OFF ke ESP32
// lalu update ac_status di database
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

$acUnitId  = (int) ($_POST['ac_unit_id'] ?? 0);
$value     = $_POST['value'] ?? ''; // 'on' atau 'off'
$ipAddress = trim($_POST['ip_address'] ?? '');

if (!$acUnitId || !in_array($value, ['on', 'off']) || empty($ipAddress)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

// Kirim perintah ke ESP32
$espUrl  = "http://{$ipAddress}/send-ir";
$payload = json_encode(['command' => 'power', 'value' => $value, 'ac_unit_id' => $acUnitId]);

$ch = curl_init($espUrl);
curl_setopt($ch, CURLOPT_POST,           true);
curl_setopt($ch, CURLOPT_POSTFIELDS,     $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER,     ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT,        5); // timeout 5 detik
$espResponse = curl_exec($ch);
$curlError   = curl_error($ch);
curl_close($ch);

if ($curlError) {
    http_response_code(503);
    echo json_encode(['status' => 'error', 'message' => 'Gagal terhubung ke perangkat: ' . $curlError]);
    exit;
}

try {
    $pdo       = getDB();
    $newStatus = $value === 'on' ? 1 : 0;

    // Ambil status lama
    $old = $pdo->prepare("SELECT ac_status FROM ac_units WHERE id_ac_units = :id");
    $old->execute([':id' => $acUnitId]);
    $oldStatus = $old->fetchColumn() ? 'on' : 'off';

    // Update status
    $stmt = $pdo->prepare("UPDATE ac_units SET ac_status = :status WHERE id_ac_units = :id");
    $stmt->execute([':status' => $newStatus, ':id' => $acUnitId]);

    // Catat activity log
    $log = $pdo->prepare("
        INSERT INTO activity_logs (user_id, ac_unit_id, action, old_value, new_value, performed_by, created_at)
        VALUES (:user_id, :ac_unit_id, :action, :old_val, :new_val, :performed_by, NOW())
    ");
    $log->execute([
        ':user_id'      => $_SESSION['user_id'],
        ':ac_unit_id'   => $acUnitId,
        ':action'       => $value === 'on' ? 'power_on' : 'power_off',
        ':old_val'      => $oldStatus,
        ':new_val'      => $value,
        ':performed_by' => $_SESSION['username'],
    ]);

    echo json_encode(['status' => 'success', 'message' => 'AC berhasil ' . ($value === 'on' ? 'dinyalakan' : 'dimatikan') . '.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}