<?php
// =============================================
// FILE: backend/get_dashboard_data.php
// Ambil semua AC units + sensor terbaru per device
// untuk dashboard cards (load sekali)
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

try {
    $pdo = getDB();

    // Ambil semua AC units beserta device_id nya
    $stmt = $pdo->prepare("
        SELECT
            ac.id_ac_units,
            ac.ac_name,
            ac.ac_location,
            ac.ac_status,
            ac.target_temp,
            ac.ac_mode,
            ac.boost_active,
            ac.update_at,
            ac.temp_threshold,
            ac.device_id
        FROM ac_units ac
        ORDER BY ac.ac_name ASC
    ");
    $stmt->execute();
    $acUnits = $stmt->fetchAll();

    // Untuk setiap AC, ambil sensor terbaru dari device yang terhubung
    $result = [];
    foreach ($acUnits as $ac) {
        $sensorStmt = $pdo->prepare("
            SELECT humidity, room_temp, air_pressure, recorded_at
            FROM sensor_logs
            WHERE device_id = :device_id
            ORDER BY recorded_at DESC
            LIMIT 1
        ");
        $sensorStmt->execute([':device_id' => $ac['device_id']]);
        $sensor = $sensorStmt->fetch();

        $result[] = [
            'id_ac_units' => $ac['id_ac_units'],
            'ac_name'     => $ac['ac_name'],
            'ac_location' => $ac['ac_location'] ?? '-',
            'ac_status'   => (bool) $ac['ac_status'],
            'target_temp' => $ac['target_temp'],
            'ac_mode'        => strtoupper($ac['ac_mode'] ?? 'COOL'),
            'boost_active'   => (bool) $ac['boost_active'],
            'temp_threshold' => (float) $ac['temp_threshold'],
            'update_at'      => $ac['update_at'],
            'sensor'      => $sensor ? [
                'room_temp'    => (float) $sensor['room_temp'],
                'humidity'     => (float) $sensor['humidity'],
                'air_pressure' => (float) $sensor['air_pressure'],
                'recorded_at'  => $sensor['recorded_at'],
            ] : null,
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $result]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}