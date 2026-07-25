<?php
// =============================================
// FILE: backend/delete_ac_unit.php
// Hapus AC unit dari database
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
    $checkStmt = $pdo->prepare(
        "SELECT ac_status, device_id FROM ac_units WHERE id_ac_units = :id"
    );
    $checkStmt->execute([':id' => $acUnitId]);
    $acUnit = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$acUnit) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'AC unit tidak ditemukan.']);
        exit;
    }

    $isActive = ((int) $acUnit['ac_status']) === 1;

    $isConnected = !empty($acUnit['device_id']);

    if ($isActive || $isConnected) {
        http_response_code(409); // Conflict
        echo json_encode([
            'status'  => 'error',
            'message' => 'AC unit tidak dapat dihapus karena masih aktif atau terhubung dengan device. '
                       . 'Nonaktifkan atau putuskan koneksi device terlebih dahulu.'
        ]);
        exit;
    }

    $pdo->prepare("DELETE FROM schedules     WHERE ac_unit_id = :id")->execute([':id' => $acUnitId]);
    $pdo->prepare("DELETE FROM activity_logs WHERE ac_unit_id = :id")->execute([':id' => $acUnitId]);

    $stmt = $pdo->prepare("DELETE FROM ac_units WHERE id_ac_units = :id");
    $stmt->execute([':id' => $acUnitId]);

    echo json_encode(['status' => 'success', 'message' => 'AC unit berhasil dihapus.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}