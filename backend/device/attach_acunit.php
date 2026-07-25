<?php
// =============================================
// FILE: backend/attach_ac_unit.php
// Hubungkan AC yang belum punya device ke device tertentu
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

$acUnitId = (int) ($_POST['ac_unit_id'] ?? 0);
$deviceId = (int) ($_POST['device_id']  ?? 0);

if (!$acUnitId || !$deviceId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

try {
    $pdo = getDB();

    // Pastikan AC ini belum terhubung ke device manapun
    $check = $pdo->prepare("SELECT device_id, ac_name FROM ac_units WHERE id_ac_units = :id");
    $check->execute([':id' => $acUnitId]);
    $ac = $check->fetch();

    if (!$ac) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'AC tidak ditemukan.']);
        exit;
    }

    if ($ac['device_id'] !== null) {
        http_response_code(409);
        echo json_encode([
            'status'  => 'error',
            'message' => $ac['ac_name'] . ' sudah terhubung ke device lain. Lepas terlebih dahulu.'
        ]);
        exit;
    }

    // Hubungkan AC ke device
    $stmt = $pdo->prepare("UPDATE ac_units SET device_id = :device_id WHERE id_ac_units = :id");
    $stmt->execute([':device_id' => $deviceId, ':id' => $acUnitId]);

    // Ambil data AC terbaru untuk response
    $acData = $pdo->prepare("SELECT id_ac_units, ac_name, ac_location, ac_status FROM ac_units WHERE id_ac_units = :id");
    $acData->execute([':id' => $acUnitId]);
    $acResult = $acData->fetch();

    echo json_encode([
        'status'  => 'success',
        'message' => $acResult['ac_name'] . ' berhasil dihubungkan ke device.',
        'data'    => [
            'id_ac_units' => (int)  $acResult['id_ac_units'],
            'ac_name'     =>        $acResult['ac_name'],
            'ac_location' =>        $acResult['ac_location'],
            'ac_status'   => (bool) $acResult['ac_status'],
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}
