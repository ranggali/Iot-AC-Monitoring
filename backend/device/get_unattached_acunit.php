<?php
// =============================================
// FILE: backend/get_unattached_ac.php
// Ambil semua AC yang belum terhubung ke device manapun
// Untuk dropdown di form tambah AC
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
        SELECT id_ac_units, ac_name, ac_location, target_temp, temp_threshold
        FROM ac_units
        WHERE device_id IS NULL
        ORDER BY ac_name ASC
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $data = array_map(function($row) {
        return [
            'id_ac_units'    => (int)   $row['id_ac_units'],
            'ac_name'        =>         $row['ac_name'],
            'ac_location'    =>         $row['ac_location'] ?? '-',
            'target_temp'    => (float) $row['target_temp'],
            'temp_threshold' => (float) $row['temp_threshold'],
        ];
    }, $rows);

    echo json_encode(['status' => 'success', 'data' => $data]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}