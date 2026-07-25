<?php
// =============================================
// FILE: backend/get_activity_logs.php
// Ambil semua data activity_logs untuk client-side
// filtering, search, dan pagination
// =============================================

header('Content-Type: application/json');

session_start();

require_once '../../config/database.php';

// Cek session
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

// Hanya terima GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']);
    exit;
}

try {
    $pdo = getDB();

    // Ambil semua activity_logs beserta nama AC unit
    $stmt = $pdo->prepare("
        SELECT
            al.id_activity_logs,
            al.action,
            al.old_value,
            al.new_value,
            al.performed_by,
            al.created_at,
            ac.ac_name,
            ac.ac_location
        FROM activity_logs al
        LEFT JOIN ac_units ac ON al.ac_unit_id = ac.id_ac_units
        ORDER BY al.created_at DESC
    ");
    $stmt->execute();
    $logs = $stmt->fetchAll();

    // Format data untuk frontend
    $data = array_map(function($row) {
        return [
            'id'           => $row['id_activity_logs'],
            'timestamp'    => $row['created_at'],
            'ac_name'      => $row['ac_name'] ?? 'Unknown',
            'ac_location'  => $row['ac_location'] ?? '-',
            'action'       => $row['action'],
            'old_value'    => $row['old_value'],
            'new_value'    => $row['new_value'],
            'performed_by' => $row['performed_by'],
        ];
    }, $logs);

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'data'   => $data
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}
