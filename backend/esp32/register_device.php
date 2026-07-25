<?php
// =============================================
// FILE: backend/register_device.php
// ESP32 register MAC + IP saat pertama boot
// Jika MAC sudah ada → UPDATE ip saja
// Jika belum ada → INSERT baru
// =============================================

header('Content-Type: application/json');
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method tidak diizinkan.']);
    exit;
}

$raw        = file_get_contents('php://input');
$data       = json_decode($raw, true);
$macAddress = trim($data['mac_address'] ?? '');
$ipAddress  = trim($data['ip_address']  ?? '');

if (empty($macAddress) || empty($ipAddress)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'mac_address dan ip_address wajib diisi.']);
    exit;
}

try {
    $pdo = getDB();

    // Cek apakah MAC sudah terdaftar
    $check = $pdo->prepare("SELECT id_devices FROM devices WHERE mac_address = :mac");
    $check->execute([':mac' => $macAddress]);
    $existing = $check->fetch();

    if ($existing) {
        // Update IP dan last_seen
        $stmt = $pdo->prepare("
            UPDATE devices
            SET ip_address = :ip, last_seen = NOW(), connection_status = 1
            WHERE mac_address = :mac
        ");
        $stmt->execute([':ip' => $ipAddress, ':mac' => $macAddress]);

        $deviceId = $existing['id_devices'];
        $message  = 'Device diperbarui.';
    } else {
        // Insert device baru (belum ada nama, admin yang isi nanti)
        $stmt = $pdo->prepare("
            INSERT INTO devices (user_id, device_name, ip_address, mac_address, connection_status, last_seen, created_at)
            VALUES (1, :device_name, :ip, :mac, 1, NOW(), NOW())
        ");
        $stmt->execute([
            ':device_name' => 'ESP32-' . strtoupper(substr(str_replace(':', '', $macAddress), -6)),
            ':ip'          => $ipAddress,
            ':mac'         => $macAddress,
        ]);
        $deviceId = (int) $pdo->lastInsertId();
        $message  = 'Device baru terdaftar.';
    }

    echo json_encode([
        'status'    => 'success',
        'message'   => $message,
        'device_id' => $deviceId,
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.']);
}
