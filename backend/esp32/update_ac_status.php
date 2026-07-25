<?php
// =============================================
// FILE: backendserver/esp32/update_ac_status.php
// Terima update status AC dari ESP32 (JSON body)
//
// ac_status:
// 0 = OFF
// 1 = ON
// =============================================

header('Content-Type: application/json');
require_once '../../config/database.php';

// Ambil JSON body dari ESP32
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {

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

    echo json_encode([
        "success" => false,
        "message" => "Status AC tidak valid"
    ]);

    exit;
}

// Update status AC
$sql = "
UPDATE ac_units
SET ac_status = ?
WHERE id_ac_units = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $ac_status,
    $ac_unit_id
);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Status AC berhasil diperbarui",
        "ac_unit_id" => $ac_unit_id,
        "ac_status" => $ac_status
    ]);

}
else {

    echo json_encode([
        "success" => false,
        "message" => "Gagal update database"
    ]);

}

$stmt->close();
$conn->close();

?>