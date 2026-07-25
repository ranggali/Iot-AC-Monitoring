<?php
// =============================================
// FILE: backend/add_acunit.php
// Tambah AC unit baru (BELUM terhubung ke device)
// Dipanggil dari halaman AC Units
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

$acName        = trim($_POST['ac_name']            ?? '');
$acLocation    = trim($_POST['ac_location']         ?? '');
$targetTemp    = (float) ($_POST['target_temp']     ?? 25.0);
$tempThreshold = (float) ($_POST['temp_threshold']  ?? 25.0);

// device_id TIDAK diwajibkan — AC baru selalu mulai tanpa device
if (empty($acName) || empty($acLocation)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Nama AC dan lokasi wajib diisi.']);
    exit;
}

if ($targetTemp < 16 || $targetTemp > 30 || $tempThreshold < 16 || $tempThreshold > 30) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Suhu harus antara 16-30°C.']);
    exit;
}

try {
    $pdo = getDB();

    // =============================================
    // CEK DUPLIKAT NAMA AC (case-insensitive)
    // =============================================
    $checkStmt = $pdo->prepare("
        SELECT COUNT(*) FROM ac_units WHERE LOWER(ac_name) = LOWER(:ac_name)
    ");
    $checkStmt->execute([':ac_name' => $acName]);

    if ((int) $checkStmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(['status' => 'error', 'message' => 'Nama AC sudah ada!']);
        exit;
    }

    // =============================================
    // INSERT AC UNIT BARU
    // =============================================
    $stmt = $pdo->prepare("
        INSERT INTO ac_units (device_id, ac_name, ac_location, ac_status, target_temp, temp_threshold, ac_mode, boost_active)
        VALUES (NULL, :ac_name, :ac_location, 0, :target_temp, :temp_threshold, 'cool', 0)
    ");
    $stmt->execute([
        ':ac_name'        => $acName,
        ':ac_location'    => $acLocation,
        ':target_temp'    => $targetTemp,
        ':temp_threshold' => $tempThreshold,
    ]);

    $newId = (int) $pdo->lastInsertId();

    echo json_encode([
        'status'      => 'success',
        'message'     => 'AC unit berhasil ditambahkan. Silakan hubungkan ke device di halaman Devices.',
        'id_ac_units' => $newId,
    ]);

} catch (PDOException $e) {
    // Jaga-jaga jika ada UNIQUE constraint di level database (MySQL error code 1062)
    if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1062) {
        http_response_code(409);
        echo json_encode(['status' => 'error', 'message' => 'Nama AC sudah ada!']);
        exit;
    }

    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}
