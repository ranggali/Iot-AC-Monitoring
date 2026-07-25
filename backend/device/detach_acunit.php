<?php
// =============================================
// FILE: backend/detach_ac_unit.php
// Lepas AC dari device (device_id = NULL)
// Data AC tetap ada di database
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

if (!$acUnitId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ac_unit_id tidak valid.']);
    exit;
}

try {
    $pdo = getDB();

    // Cek apakah AC masih aktif
    $check = $pdo->prepare("SELECT ac_name, ac_status FROM ac_units WHERE id_ac_units = :id");
    $check->execute([':id' => $acUnitId]);
    $ac = $check->fetch();

    if (!$ac) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'AC tidak ditemukan.']);
        exit;
    }

    if ($ac['ac_status']) {
        http_response_code(409);
        echo json_encode([
            'status'  => 'error',
            'message' => $ac['ac_name'] . ' masih aktif! Matikan AC terlebih dahulu.'
        ]);
        exit;
    }

    // Lepas AC dari device (set device_id = NULL)
    $stmt = $pdo->prepare("UPDATE ac_units SET device_id = NULL WHERE id_ac_units = :id");
    $stmt->execute([':id' => $acUnitId]);

    echo json_encode([
        'status'  => 'success',
        'message' => $ac['ac_name'] . ' berhasil dilepas dari device.'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}