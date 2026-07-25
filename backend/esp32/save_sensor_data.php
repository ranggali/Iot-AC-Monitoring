<?php
// =============================================
// FILE: backend/save_sensor_data.php
// ESP32 kirim data BME280 setiap 30 detik
// Sekaligus update last_seen di devices
// =============================================

header('Content-Type: application/json');
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']);
    exit;
}

$raw      = file_get_contents('php://input');
$data     = json_decode($raw, true);

$deviceId    = (int)   ($data['device_id']   ?? 0);
$humidity    = (float) ($data['humidity']    ?? 0);
$roomTemp    = (float) ($data['room_temp']   ?? 0);
$airPressure = (float) ($data['air_pressure'] ?? 0);

if (!$deviceId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'device_id tidak valid.']);
    exit;
}

try {
    $pdo = getDB();

    // Simpan data sensor
    $stmt = $pdo->prepare("
        INSERT INTO sensor_logs (device_id, humidity, room_temp, air_pressure, recorded_at)
        VALUES (:device_id, :humidity, :room_temp, :air_pressure, NOW())
    ");
    $stmt->execute([
        ':device_id'    => $deviceId,
        ':humidity'     => $humidity,
        ':room_temp'    => $roomTemp,
        ':air_pressure' => $airPressure,
    ]);

    // Update last_seen & connection_status di devices
    $update = $pdo->prepare("
        UPDATE devices
        SET last_seen = NOW(), connection_status = 1
        WHERE id_devices = :device_id
    ");
    $update->execute([':device_id' => $deviceId]);

    echo json_encode(['status' => 'success', 'message' => 'Data sensor tersimpan.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}
