<?php
// =============================================
// FILE: backend/esp32/update_ac_status.php
// Terima update status AC dari ESP32 (JSON body)
//
// ac_status:
// 0 = OFF
// 1 = ON
// =============================================

header('Content-Type: application/json');
require_once '../../config/database.php';

// Hanya terima POST (konsisten dengan endpoint ESP32 lainnya)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan.']);
    exit;
}

// Ambil JSON body dari ESP32
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "JSON tidak valid"
    ]);

    exit;
}

// Validasi parameter
if (
    !isset($data['ac_unit_id']) ||
    !isset($data['ac_status'])
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Parameter tidak lengkap"
    ]);

    exit;
}

$ac_unit_id = intval($data['ac_unit_id']);
$ac_status  = intval($data['ac_status']);

// Pastikan status hanya 0 atau 1
if ($ac_status != 0 && $ac_status != 1) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Status AC tidak valid"
    ]);

    exit;
}

try {
    $pdo = getDB();

    // FIX: sebelumnya pakai mysqli ($conn) yang TIDAK PERNAH didefinisikan
    // di config/database.php (yang tersedia hanya getDB() via PDO) —
    // itu sebabnya update selalu gagal (Fatal Error) walau ESP32 log
    // seolah "berhasil". Sekarang pakai PDO, konsisten dengan
    // get_ac_config.php & save_schedule.php.
    $stmt = $pdo->prepare("
        UPDATE ac_units
        SET ac_status = :ac_status
        WHERE id_ac_units = :ac_unit_id
    ");
    $stmt->execute([
        ':ac_status'  => $ac_status,
        ':ac_unit_id' => $ac_unit_id,
    ]);

    if ($stmt->rowCount() === 0) {
        // Query sukses dijalankan, tapi tidak ada baris yang match —
        // ac_unit_id kemungkinan salah/tidak ada di tabel ac_units.
        http_response_code(404);
        echo json_encode([
            "success"    => false,
            "message"    => "ac_unit_id tidak ditemukan, tidak ada baris yang diupdate.",
            "ac_unit_id" => $ac_unit_id,
        ]);
        exit;
    }

    http_response_code(200);
    echo json_encode([
        "success"    => true,
        "message"    => "Status AC berhasil diperbarui",
        "ac_unit_id" => $ac_unit_id,
        "ac_status"  => $ac_status
    ]);

} catch (PDOException $e) {
    error_log('[update_ac_status] PDOException: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);
}
