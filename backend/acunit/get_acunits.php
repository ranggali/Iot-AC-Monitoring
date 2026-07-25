<?php
// =============================================
// FILE: backend/get_ac_units.php
// Ambil semua AC units untuk tabel di acunit.php
// =============================================

header('Content-Type: application/json');
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare("
        SELECT
            ac.id_ac_units,
            ac.ac_name,
            ac.ac_location,
            ac.ac_status,
            ac.target_temp,
            ac.temp_threshold,
            ac.ac_mode,
            ac.boost_active,
            ac.update_at,
            ac.device_id,
            d.ip_address,
            s.id_schedules,
            s.on_hour,
            s.on_minute,
            s.off_hour,
            s.off_minute,
            s.mon, s.tue, s.wed, s.thu, s.fri, s.sat, s.sun,
            s.is_active AS sched_active
        FROM ac_units ac
        LEFT JOIN devices d ON ac.device_id = d.id_devices
        LEFT JOIN schedules s ON s.ac_unit_id = ac.id_ac_units
        ORDER BY ac.ac_name ASC
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $data = array_map(function($row) {
        return [
            'id_ac_units'    => (int)   $row['id_ac_units'],
            'ac_name'        =>         $row['ac_name'],
            'ac_location'    =>         $row['ac_location'] ?? '-',
            'ac_status'      => (bool)  $row['ac_status'],
            'target_temp'    => (float) $row['target_temp'],
            'temp_threshold' => (float) $row['temp_threshold'],
            'ac_mode'        => strtoupper($row['ac_mode'] ?? 'COOL'),
            'boost_active'   => (bool)  $row['boost_active'],
            'update_at'      =>         $row['update_at'],
            'device_id'      => (int)   $row['device_id'],
            'ip_address'     =>         $row['ip_address'] ?? null,
            'schedule'       => $row['id_schedules'] ? [
                'id_schedules' => (int)  $row['id_schedules'],
                'on_hour'      => (int)  $row['on_hour'],
                'on_minute'    => (int)  $row['on_minute'],
                'off_hour'     => (int)  $row['off_hour'],
                'off_minute'   => (int)  $row['off_minute'],
                'mon'          => (bool) $row['mon'],
                'tue'          => (bool) $row['tue'],
                'wed'          => (bool) $row['wed'],
                'thu'          => (bool) $row['thu'],
                'fri'          => (bool) $row['fri'],
                'sat'          => (bool) $row['sat'],
                'sun'          => (bool) $row['sun'],
                'is_active'    => (bool) $row['sched_active'],
            ] : null,
        ];
    }, $rows);

    echo json_encode(['status' => 'success', 'data' => $data]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}