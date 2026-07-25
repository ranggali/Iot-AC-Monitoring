<?php
// =============================================
// FILE: config/auth.php
// Proteksi halaman + session timeout 15 menit
// =============================================

session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Cek timeout 15 menit (900 detik)
if (isset($_SESSION['last_activity'])) {
    $elapsed = time() - $_SESSION['last_activity'];
    if ($elapsed > 900) {
        header('Location: ../backend/logout_data.php?timeout=1');
        exit;
    }
}

// Update waktu aktivitas terakhir
$_SESSION['last_activity'] = time();
