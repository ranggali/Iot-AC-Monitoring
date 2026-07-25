<?php
// =============================================
// FILE: backend/get_sensor_data.php
// Ambil data sensor terbaru per device
// Dipanggil setiap 30 detik untuk auto refresh
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

    // Ambil sensor terbaru untuk setiap device yang terhubung ke AC
    $stmt = $pdo->prepare("
        SELECT
            ac.id_ac_units,
            ac.ac_status,
            ac.target_temp,
            ac.boost_active,
            sl.humidity,
            sl.room_temp,
            sl.air_pressure,
            sl.recorded_at
        FROM ac_units ac
        LEFT JOIN (
            SELECT s1.*
            FROM sensor_logs s1
            INNER JOIN (
                SELECT device_id, MAX(recorded_at) AS max_recorded
                FROM sensor_logs
                GROUP BY device_id
            ) s2 ON s1.device_id = s2.device_id AND s1.recorded_at = s2.max_recorded
        ) sl ON ac.device_id = sl.device_id
        ORDER BY ac.ac_name ASC
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $data = array_map(function($row) {
        return [
            'id_ac_units' => $row['id_ac_units'],
            'ac_status'   => (bool) $row['ac_status'],
            'target_temp' => $row['target_temp'],
            'boost_active'=> (bool) $row['boost_active'],
            'sensor'      => $row['room_temp'] !== null ? [
                'room_temp'    => (float) $row['room_temp'],
                'humidity'     => (float) $row['humidity'],
                'air_pressure' => (float) $row['air_pressure'],
                'recorded_at'  => $row['recorded_at'],
            ] : null,
        ];
    }, $rows);

    echo json_encode(['status' => 'success', 'data' => $data]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}