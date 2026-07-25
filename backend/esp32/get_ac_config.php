<?php
// =============================================
// FILE: backend/get_ac_config.php
// Ambil konfigurasi AC berdasarkan device_id
// Diakses oleh ESP32 untuk fetch config
// =============================================

header('Content-Type: application/json');

require_once '../../config/database.php';

// Hanya terima GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']);
    exit;
}

// Ambil device_id dari query string
// Contoh: get_ac_config.php?device_id=1
$deviceId = isset($_GET['device_id']) ? (int) $_GET['device_id'] : 0;

if (!$deviceId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'device_id tidak valid.']);
    exit;
}

try {
    $pdo = getDB();

    // Ambil semua AC yang terhubung ke device ini
    // beserta konfigurasi suhu dan jadwalnya
    $stmt = $pdo->prepare("
        SELECT
            ac.id_ac_units,
            ac.ac_name,
            ac.ac_status,
            ac.target_temp,
            ac.temp_threshold,
            ac.boost_active,
            s.on_hour,
            s.on_minute,
            s.off_hour,
            s.off_minute,
            s.mon,
            s.tue,
            s.wed,
            s.thu,
            s.fri,
            s.sat,
            s.sun,
            s.is_active
        FROM ac_units ac
        LEFT JOIN schedules s ON s.ac_unit_id = ac.id_ac_units
        WHERE ac.device_id = :device_id
        ORDER BY ac.id_ac_units ASC
    ");
    $stmt->execute([':device_id' => $deviceId]);
    $rows = $stmt->fetchAll();

    if (empty($rows)) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada AC untuk device ini.']);
        exit;
    }

    $acList = array_map(function($row) {
        return [
            'id_ac_units'    => (int)   $row['id_ac_units'],
            'ac_name'        =>         $row['ac_name'],
            'ac_status'      => (bool)  $row['ac_status'],
            'target_temp'    => (float) $row['target_temp'],
            'temp_threshold' => (float) $row['temp_threshold'],
            'boost_active'   => (bool)  $row['boost_active'],
            'schedule'       => $row['on_hour'] !== null ? [
                'on_hour'    => (int) $row['on_hour'],
                'on_minute'  => (int) $row['on_minute'],
                'off_hour'   => (int) $row['off_hour'],
                'off_minute' => (int) $row['off_minute'],
                'mon'        => (bool) $row['mon'],
                'tue'        => (bool) $row['tue'],
                'wed'        => (bool) $row['wed'],
                'thu'        => (bool) $row['thu'],
                'fri'        => (bool) $row['fri'],
                'sat'        => (bool) $row['sat'],
                'sun'        => (bool) $row['sun'],
                'is_active'  => (bool) $row['is_active'],
            ] : null,
        ];
    }, $rows);

    http_response_code(200);
    echo json_encode([
        'status'    => 'success',
        'device_id' => $deviceId,
        'data'      => $acList
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}
