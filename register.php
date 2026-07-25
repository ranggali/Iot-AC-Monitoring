<?php
// =============================================
// FILE: webserver/register.php
// Halaman registrasi user
// =============================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>AcController - Register</title>
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="public/css/login.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
  <!-- icon -->
    <link rel="icon" href="public/assets/icon.ico" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="public/assets/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/assets/icon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/assets/apple-icon.png">
  <!-- end icon -->
</head>
<body>

  <div class="card">

    <div class="brand">
      <div class="brand-dot"></div>
      <span class="brand-name">AcController</span>
    </div>

    <a href="index.php" class="btn-back">
      <i class="bx bx-arrow-back"></i>
      Kembali ke Login
    </a>

    <h1>Buat Akun</h1>
    <p class="subtitle">Daftarkan akun baru Anda dengan mengisi username, email, dan password.</p>

    <div id="alert-box"></div>

    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" id="username" placeholder="Masukkan username" autocomplete="username">
    </div>

    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" placeholder="Masukkan email" autocomplete="email">
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <div class="input-wrapper">
        <input type="password" id="password" placeholder="••••••••" autocomplete="new-password">
        <button type="button" class="toggle-btn" onclick="togglePassword()">
          <i id="eye-icon" class="bx bx-show" style="font-size: 1.4rem;"></i>
        </button>
      </div>
    </div>

    <button class="btn btn-primary" id="btn-register" onclick="handleRegister()">Register</button>

  </div>

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
          <i class='bx ${type === 'success' ? 'bx-check-circle' : 'bx-error-circle'}'></i>
          <span>${message}</span>
        </div>`;
    }

    async function handleRegister() {
      const username  = document.getElementById('username').value.trim();
      const email     = document.getElementById('email').value.trim();
      const password  = document.getElementById('password').value;
      const btn       = document.getElementById('btn-register');
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      // Validasi sisi client
      if (!username || !email || !password) {
        showAlert('error', 'Username, email, dan password tidak boleh kosong.');
        return;
      }

      if (!emailPattern.test(email)) {
        showAlert('error', 'Format email tidak valid.');
        return;
      }

      if (password.length < 8) {
        showAlert('error', 'Password minimal 8 karakter.');
        return;
      }

      // Kirim ke backend
      btn.disabled = true;
      btn.textContent = 'Memproses...';

      try {
        const formData = new FormData();
        formData.append('username', username);
        formData.append('email', email);
        formData.append('password', password);

        const response = await fetch('backend/register_data.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (result.status === 'success') {
          showAlert('success', result.message);
          setTimeout(() => {
            window.location.href = 'index.php';
          }, 1000);
        } else {
          showAlert('error', result.message);
        }

      } catch (error) {
        showAlert('error', 'Gagal terhubung ke server.');
      } finally {
        btn.disabled = false;
        btn.textContent = 'Register';
      }
    }

    // Enter key support
    document.addEventListener('keydown', e => {
      if (e.key === 'Enter') handleRegister();
    });
  </script>

</body>
</html>