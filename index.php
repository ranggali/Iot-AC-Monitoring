<?php
// =============================================
// FILE: webserver/login.php
// Halaman login user
// =============================================

session_start();

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: frontend/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>AcController - Login</title>
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="public/css/login.css">
  <link rel="stylesheet" href="public/css/toast.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
  <!-- icon -->
    <link rel="icon" href="public/assets/icon.ico" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="public/assets/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/assets/icon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/assets/apple-icon.png">
  <!-- end icon -->
</head>
<body>

  <!-- Toast Notification -->
  <div class="success-message" id="successMessage">
    <i class="bx bx-check-circle"></i>
    <span></span>
  </div>

  <div class="card">

    <div class="brand">
      <div class="brand-dot"></div>
      <span class="brand-name">AcController</span>
    </div>

    <h1>Selamat Datang</h1>
    <p class="subtitle">Masukkan username dan password Anda untuk mengakses dashboard.</p>

    <div id="alert-box">
      <?php if (isset($_GET['timeout'])): ?>
        <div class="alert alert-error">
          <i class='bx bx-error-circle'></i>
          <span>Sesi Anda telah berakhir, silahkan login kembali!</span>
        </div>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" id="username" placeholder="Masukkan username" autocomplete="username">
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <div class="input-wrapper">
        <input type="password" id="password" placeholder="••••••••" autocomplete="current-password">
        <button type="button" class="toggle-btn" onclick="togglePassword()">
          <i id="eye-icon" class="bx bx-show"></i>
        </button>
      </div>
    </div>

    <div class="row-between">
      <a href="register.php" class="forgot-link">Daftar Akun</a>
    </div>

    <button class="btn btn-primary" id="btn-login" onclick="handleLogin()">Login</button>

  </div>

  <script src="public/js/toast.js"></script>
  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      const icon  = document.getElementById('eye-icon');
      if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bx bx-hide';
      } else {
        input.type = 'password';
        icon.className = 'bx bx-show';
      }
    }

    function showAlert(type, message) {
      const alertBox = document.getElementById('alert-box');
      alertBox.innerHTML = `
        <div class="alert alert-${type}">
          <i class='bx bx-error-circle'></i>
          <span>${message}</span>
        </div>`;
    }

    async function handleLogin() {
      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value;
      const btn      = document.getElementById('btn-login');

      // Validasi sisi client
      if (!username || !password) {
        showAlert('error', 'Username dan password tidak boleh kosong.');
        return;
      }

      // Kirim ke backend
      btn.disabled    = true;
      btn.textContent = 'Memproses...';

      try {
        const formData = new FormData();
        formData.append('username', username);
        formData.append('password', password);

        const response = await fetch('backend/login_data.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (result.status === 'success') {
          // Login berhasil → tampilkan toast lalu redirect
          showSuccessMessage('Login berhasil! Mengalihkan ke dashboard...');
          setTimeout(() => {
            window.location.href = 'frontend/dashboard.php';
          }, 1200);
        } else {
          // Login gagal → tampilkan alert-box dalam card
          showAlert('error', result.message);
        }

      } catch (error) {
        showAlert('error', 'Gagal terhubung ke server.');
      } finally {
        btn.disabled    = false;
        btn.textContent = 'Login';
      }
    }

    // Enter key support
    document.addEventListener('keydown', e => {
      if (e.key === 'Enter') handleLogin();
    });
  </script>

</body>
</html>