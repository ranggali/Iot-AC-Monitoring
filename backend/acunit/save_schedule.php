<?php
// =============================================
// FILE: backend/save_schedule.php
// Simpan/update jadwal AC ke database
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

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

$acUnitId = (int) ($data['ac_unit_id'] ?? 0);
$onHour   = (int) ($data['on_hour']    ?? 0);
$onMin    = (int) ($data['on_minute']  ?? 0);
$offHour  = (int) ($data['off_hour']   ?? 0);
$offMin   = (int) ($data['off_minute'] ?? 0);
$mon      = (int) ($data['mon']        ?? 0);
$tue      = (int) ($data['tue']        ?? 0);
$wed      = (int) ($data['wed']        ?? 0);
$thu      = (int) ($data['thu']        ?? 0);
$fri      = (int) ($data['fri']        ?? 0);
$sat      = (int) ($data['sat']        ?? 0);
$sun      = (int) ($data['sun']        ?? 0);
// FIX: is_active dihapus dari variabel karena kolom tidak ada di tabel schedules
// Jika ingin menggunakan is_active, tambahkan kolom dulu ke tabel (lihat SQL di bawah)

if (!$acUnitId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ac_unit_id tidak valid.']);
    exit;
}

try {
    $pdo = getDB();

    // Cek apakah jadwal sudah ada untuk AC ini
    $check = $pdo->prepare("SELECT id_schedules FROM schedules WHERE ac_unit_id = :id");
    $check->execute([':id' => $acUnitId]);
    $existing = $check->fetch();

    if ($existing) {
        // UPDATE jadwal yang sudah ada
        // FIX: Hapus is_active dari query UPDATE karena kolom tidak ada di tabel
        $stmt = $pdo->prepare("
            UPDATE schedules SET
                on_hour    = :on_hour,
                on_minute  = :on_minute,
                off_hour   = :off_hour,
                off_minute = :off_minute,
                mon        = :mon,
                tue        = :tue,
                wed        = :wed,
                thu        = :thu,
                fri        = :fri,
                sat        = :sat,
                sun        = :sun,
                update_by  = :update_by,
                update_at  = NOW()
            WHERE ac_unit_id = :ac_unit_id
        ");

        $stmt->execute([
            ':ac_unit_id' => $acUnitId,
            ':on_hour'    => $onHour,
            ':on_minute'  => $onMin,
            ':off_hour'   => $offHour,
            ':off_minute' => $offMin,
            ':mon'        => $mon,
            ':tue'        => $tue,
            ':wed'        => $wed,
            ':thu'        => $thu,
            ':fri'        => $fri,
            ':sat'        => $sat,
            ':sun'        => $sun,
            ':update_by'  => $_SESSION['username'],
        ]);

    } else {
        // INSERT jadwal baru
        // FIX: Hapus is_active dari query INSERT karena kolom tidak ada di tabel
        // FIX: user_id di-bind langsung di array execute (bukan pakai bindValue terpisah)
        $stmt = $pdo->prepare("
            INSERT INTO schedules
                (ac_unit_id, user_id, on_hour, on_minute, off_hour, off_minute,
                 mon, tue, wed, thu, fri, sat, sun, update_by, update_at)
            VALUES
                (:ac_unit_id, :user_id, :on_hour, :on_minute, :off_hour, :off_minute,
                 :mon, :tue, :wed, :thu, :fri, :sat, :sun, :update_by, NOW())
        ");

        $stmt->execute([
            ':ac_unit_id' => $acUnitId,
            ':user_id'    => $_SESSION['user_id'],  // FIX: pindah ke sini agar tidak konflik
            ':on_hour'    => $onHour,
            ':on_minute'  => $onMin,
            ':off_hour'   => $offHour,
            ':off_minute' => $offMin,
            ':mon'        => $mon,
            ':tue'        => $tue,
            ':wed'        => $wed,
            ':thu'        => $thu,
            ':fri'        => $fri,
            ':sat'        => $sat,
            ':sun'        => $sun,
            ':update_by'  => $_SESSION['username'],
        ]);
    }

    // Format jadwal untuk activity log
    $onTime  = sprintf('%02d:%02d', $onHour, $onMin);
    $offTime = sprintf('%02d:%02d', $offHour, $offMin);

    // Catat activity log
    $log = $pdo->prepare("
        INSERT INTO activity_logs (user_id, ac_unit_id, action, old_value, new_value, performed_by, created_at)
        VALUES (:user_id, :ac_unit_id, 'schedule_update', NULL, :new_val, :performed_by, NOW())
    ");
    $log->execute([
        ':user_id'      => $_SESSION['user_id'],
        ':ac_unit_id'   => $acUnitId,
        ':new_val'      => "ON: {$onTime}, OFF: {$offTime}",
        ':performed_by' => $_SESSION['username'],
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil disimpan.']);

} catch (PDOException $e) {
    // FIX: Log error ke file server agar bisa di-debug, tapi jangan tampilkan ke client
    error_log('[save_schedule] PDOException: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()]);
}