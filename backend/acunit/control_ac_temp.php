<?php
// =============================================
// FILE: backend/control_ac_temp.php
// Kirim perintah perubahan suhu ke ESP32
// lalu update target_temp di database
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

$acUnitId  = (int)   ($_POST['ac_unit_id'] ?? 0);
$temp      = (float) ($_POST['value']      ?? 0);
$ipAddress = trim($_POST['ip_address']     ?? '');

// Kirim perintah ke ESP32
$espUrl  = "http://{$ipAddress}/send-ir";
$payload = json_encode(['command' => 'temperature', 'value' => $temp, 'ac_unit_id' => $acUnitId]);

$ch = curl_init($espUrl);
curl_setopt($ch, CURLOPT_POST,           true);
curl_setopt($ch, CURLOPT_POSTFIELDS,     $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER,     ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT,        5);
$espResponse = curl_exec($ch);
$curlError   = curl_error($ch);
curl_close($ch);

if ($curlError) {
    http_response_code(503);
    echo json_encode(['status' => 'error', 'message' => 'Gagal terhubung ke perangkat: ' . $curlError]);
    exit;
}

// Wajib: cek isi balasan ESP32, bukan cuma status koneksi.
// ESP32 diharapkan membalas JSON, contoh saat ditolak:
// {"status":"error","message":"Boost mode aktif, perubahan suhu manual tidak diizinkan."}
$espData = json_decode($espResponse, true);

if (!is_array($espData) || !isset($espData['status'])) {
    http_response_code(502);
    echo json_encode(['status' => 'error', 'message' => 'Respons perangkat tidak valid: ' . $espResponse]);
    exit;
}

if ($espData['status'] !== 'success') {
    // ESP32 menolak perintah (mis. mode boost aktif) -> teruskan pesan aslinya, JANGAN update DB
    http_response_code(409);
    echo json_encode(['status' => 'error', 'message' => $espData['message'] ?? 'Perintah ditolak oleh perangkat.']);
    exit;
}

try {
    $pdo = getDB();

    // Ambil suhu lama
    $old = $pdo->prepare("SELECT target_temp FROM ac_units WHERE id_ac_units = :id");
    $old->execute([':id' => $acUnitId]);
    $oldTemp = $old->fetchColumn();

    // Update target_temp
    $stmt = $pdo->prepare("UPDATE ac_units SET target_temp = :temp WHERE id_ac_units = :id");
    $stmt->execute([':temp' => $temp, ':id' => $acUnitId]);

    // Catat activity log
    $log = $pdo->prepare("
        INSERT INTO activity_logs (user_id, ac_unit_id, action, old_value, new_value, performed_by, created_at)
        VALUES (:user_id, :ac_unit_id, 'temperature_change', :old_val, :new_val, :performed_by, NOW())
    ");
    $log->execute([
        ':user_id'      => $_SESSION['user_id'],
        ':ac_unit_id'   => $acUnitId,
        ':old_val'      => $oldTemp . '°C',
        ':new_val'      => $temp . '°C',
        ':performed_by' => $_SESSION['username'],
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Suhu berhasil diubah ke ' . $temp . '°C.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}