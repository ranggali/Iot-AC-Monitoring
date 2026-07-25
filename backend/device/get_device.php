<?php
// =============================================
// FILE: backend/get_devices.php
// Ambil semua devices + AC units terhubung
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
    $pdo = getDB();

    // Ambil semua devices
    $stmt = $pdo->prepare("
        SELECT id_devices, device_name, ip_address, mac_address,
               connection_status, last_seen, created_at
        FROM devices
        ORDER BY id_devices ASC
    ");
    $stmt->execute();
    $devices = $stmt->fetchAll();

    // Untuk setiap device, ambil AC units yang terhubung
    $result = [];
    foreach ($devices as $device) {
        $acStmt = $pdo->prepare("
            SELECT id_ac_units, ac_name, ac_location, ac_status
            FROM ac_units
            WHERE device_id = :device_id
            ORDER BY ac_name ASC
        ");
        $acStmt->execute([':device_id' => $device['id_devices']]);
        $acUnits = $acStmt->fetchAll();

        // Hitung status online/offline (last_seen < 2 menit = online)
        $isOnline = false;
        if ($device['last_seen']) {
            $lastSeen = strtotime($device['last_seen']);
            $isOnline = (time() - $lastSeen) < 120;
        }

        $result[] = [
            'id_devices'        => (int)  $device['id_devices'],
            'device_name'       =>        $device['device_name'],
            'ip_address'        =>        $device['ip_address'],
            'mac_address'       =>        $device['mac_address'] ?? '-',
            'connection_status' => (bool) $isOnline,
            'last_seen'         =>        $device['last_seen'],
            'created_at'        =>        $device['created_at'],
            'ac_units'          =>        array_map(function($ac) {
                return [
                    'id_ac_units' => (int)  $ac['id_ac_units'],
                    'ac_name'     =>        $ac['ac_name'],
                    'ac_location' =>        $ac['ac_location'] ?? '-',
                    'ac_status'   => (bool) $ac['ac_status'],
                ];
            }, $acUnits),
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $result]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}