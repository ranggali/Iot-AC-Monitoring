<?php require_once '../config/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AcController - Account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/basic/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/account.css">
    <link rel="stylesheet" href="../public/css/toast.css">
    <link rel="stylesheet" href="../public/css/logout.css">
    <!-- icon -->
    <link rel="icon" href="../public/assets/icon.ico" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="../public/assets/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../public/assets/icon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../public/assets/apple-icon.png">
  <!-- end icon -->
</head>
<body>
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" onclick="toggleMobileSidebar()"></div>
    
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" onclick="toggleMobileSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="logo-text">
                        <h1>IT <span style="color: #3b82f6;">AC</span> CONTROLLER</h1>
                        <p>Account</p>
                    </div>
                </div>
                <button class="toggle-btn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="nav-menu">
                <div class="menu-items-top">
                    <div class="menu-item">
                        <a class="menu-link" href="dashboard.php">
                            <div class="menu-link-content">
                                <i class="fas fa-home menu-icon"></i>
                                <span class="menu-text">Dashboard</span>
                            </div>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="acunit.php">
                            <div class="menu-link-content">
                                <i class='bx bx-air-conditioner'></i>
                                <span class="menu-text">AC Units</span>
                            </div>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="device.php">
                            <div class="menu-link-content">
                                <i class="fas fa-microchip menu-icon"></i>
                                <span class="menu-text">Devices</span>
                            </div>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="history.php">
                            <div class="menu-link-content">
                                <i class="fas fa-clock-rotate-left"></i>
                                <span class="menu-text">History</span>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="menu-items-bottom">
                    <div class="menu-item">
                        <a class="menu-link active">
                            <div class="menu-link-content">
                                <i class="fas fa-user-cog menu-icon"></i>
                                <span class="menu-text">Account</span>
                            </div>
                        </a>
                    </div>
                </div>
            </nav>

            <div class="user-profile">
                <div class="profile-container">
                    <div class="profile-info">
                        <div class="profile-name" id="sidebarUsername"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                        <div class="profile-role">Administrator</div>
                    </div>
                    <button class="logout-btn" title="Logout" onclick="showLogoutModal()">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-header">
                <h2>Account</h2>
                <p>Manage your account and preferences</p>
            </div>

            <!-- Toast Notification -->
            <div class="success-message" id="successMessage">
                <i class="bx bx-check-circle"></i>
                <span></span>
            </div>
            <div class="error-message" id="errorMessage">
                <i class="bx bx-error-circle"></i>
                <span></span>
            </div>

            <div class="account-container">

                <!-- Profile Account Card -->
                <div class="account-card primary">
                    <div class="card-header">
                        <div class="card-header-info">
                            <h3>
                                <i class="fas fa-user-circle"></i>
                                Profile Information
                            </h3>
                            <p>Update your personal information</p>
                        </div>
                    </div>
                    <div class="card-content">
                        <form id="profileForm">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="username">Username <span>*</span></label>
                                    <input type="text" id="username" placeholder="Enter your username" required disabled>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address <span>*</span></label>
                                    <input type="email" id="email" placeholder="Enter your email address" required disabled>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary" id="cancelProfileBtn" onclick="cancelProfileEdit()" style="display: none;">
                                    <i class="fas fa-times"></i>
                                    Cancel
                                </button>
                                <button type="button" class="btn btn-primary" id="editProfileBtn" onclick="enableProfileEdit()">
                                    <i class="fas fa-edit"></i>
                                    Edit Profile
                                </button>
                                <button type="button" class="btn btn-primary" id="saveProfileBtn" onclick="saveProfile()" style="display: none;">
                                    <i class="fas fa-save"></i>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password Account Card -->
                <div class="account-card secondary collapsed" id="passwordCard">
                    <div class="card-header">
                        <div class="card-header-info">
                            <h3>
                                <i class="fas fa-lock"></i>
                                Security & Password
                            </h3>
                            <p>Update your password to keep your account secure</p>
                        </div>
                        <button class="collapse-toggle" onclick="togglePasswordCard()">
                            <span>Expand</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="card-content">
                        <form id="passwordForm">
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label for="currentPassword">Current Password <span>*</span></label>
                                    <div class="password-input-wrapper">
                                        <input type="password" id="currentPassword" placeholder="Enter current password" required disabled>
                                        <button type="button" class="password-toggle" onclick="togglePassword('currentPassword')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <span class="field-error" id="currentPasswordError">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span></span>
                                    </span>
                                    <span class="field-success" id="currentPasswordSuccess">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Password sesuai.</span>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="newPassword">New Password <span>*</span></label>
                                    <div class="password-input-wrapper">
                                        <input type="password" id="newPassword" placeholder="Enter new password" required disabled>
                                        <button type="button" class="password-toggle" onclick="togglePassword('newPassword')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <span class="field-error" id="newPasswordError">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span>Password minimal 8 karakter.</span>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="confirmPassword">Confirm Password <span>*</span></label>
                                    <div class="password-input-wrapper">
                                        <input type="password" id="confirmPassword" placeholder="Confirm new password" required disabled>
                                        <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <span class="field-error" id="confirmPasswordError">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span>Password tidak cocok.</span>
                                    </span>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary" id="cancelPasswordBtn" onclick="cancelPasswordEdit()" style="display: none;">
                                    <i class="fas fa-times"></i>
                                    Cancel
                                </button>
                                <button type="button" class="btn btn-primary" id="editPasswordBtn" onclick="enablePasswordEdit()">
                                    <i class="fas fa-edit"></i>
                                    Change Password
                                </button>
                                <button type="button" class="btn btn-primary" id="savePasswordBtn" onclick="savePassword()" style="display: none;">
                                    <i class="fas fa-key"></i>
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

     <!-- Logout Confirmation Modal -->
    <div class="logout-modal-overlay" id="logoutModal">
        <div class="logout-modal-box">
            <div class="logout-modal-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h3>Confirm Logout</h3>
            <p>Are you sure you want to log out of this account?</p>
            <div class="logout-modal-actions">
                <button type="button" class="logout-modal-btn-cancel" onclick="hideLogoutModal()">
                    <!-- <i class="fas fa-times"></i> -->
                    Cancel
                </button>
                <button type="button" class="logout-modal-btn-confirm" id="confirmLogoutBtn" onclick="confirmLogout()">
                    <!-- <i class="fas fa-sign-out-alt"></i> -->
                    Logout
                </button>
            </div>
        </div>
    </div>

    <script src="../public/js/logout_modal.js"></script>
    <script src="../public/js/toast.js"></script>
    <script>
        // =============================================
        // Load data user saat halaman dibuka
        // =============================================
        async function loadUserData() {
            try {
                const response = await fetch('../backend/account/get_user_data.php');
                const result   = await response.json();
                if (result.status === 'success') {
                    document.getElementById('username').value             = result.data.username;
                    document.getElementById('email').value                = result.data.email;
                    document.getElementById('sidebarUsername').textContent = result.data.username;
                } else {
                    showErrorMessage('Gagal memuat data akun.');
                }
            } catch (error) {
                showErrorMessage('Gagal terhubung ke server.');
            }
        }

        document.addEventListener('DOMContentLoaded', loadUserData);

        // =============================================
        // Profile Edit
        // =============================================
        function enableProfileEdit() {
            document.querySelectorAll('#profileForm input').forEach(i => i.disabled = false);
            document.getElementById('editProfileBtn').style.display   = 'none';
            document.getElementById('saveProfileBtn').style.display   = 'inline-flex';
            document.getElementById('cancelProfileBtn').style.display = 'inline-flex';
        }

        function cancelProfileEdit() {
            document.querySelectorAll('#profileForm input').forEach(i => i.disabled = true);
            document.getElementById('editProfileBtn').style.display   = 'inline-flex';
            document.getElementById('saveProfileBtn').style.display   = 'none';
            document.getElementById('cancelProfileBtn').style.display = 'none';
            loadUserData(); // Kembalikan data ke nilai awal dari database
        }

        async function saveProfile() {
            const username = document.getElementById('username').value.trim();
            const email    = document.getElementById('email').value.trim();
            const btn      = document.getElementById('saveProfileBtn');

            if (!username || !email) {
                showErrorMessage('Username dan email tidak boleh kosong.');
                return;
            }

            btn.disabled    = true;
            btn.innerHTML   = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

            try {
                const formData = new FormData();
                formData.append('username', username);
                formData.append('email', email);

                const response = await fetch('../backend/account/update_profile.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    showSuccessMessage(result.message);
                    document.getElementById('sidebarUsername').textContent = result.data.username;
                    cancelProfileEdit();
                } else {
                    showErrorMessage(result.message);
                }
            } catch (error) {
                showErrorMessage('Gagal terhubung ke server.');
            } finally {
                btn.disabled  = false;
                btn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
            }
        }

        // =============================================
        // Password Edit + Real-time Validation
        // =============================================
        let currentPasswordValid = false;
        let debounceTimer        = null;

        function enablePasswordEdit() {
            document.querySelectorAll('#passwordForm input').forEach(i => i.disabled = false);
            document.getElementById('editPasswordBtn').style.display   = 'none';
            document.getElementById('savePasswordBtn').style.display   = 'inline-flex';
            document.getElementById('cancelPasswordBtn').style.display = 'inline-flex';

            // Pasang event listener real-time validation
            document.getElementById('currentPassword').addEventListener('input', onCurrentPasswordInput);
            document.getElementById('newPassword').addEventListener('input', onNewPasswordInput);
            document.getElementById('confirmPassword').addEventListener('input', onConfirmPasswordInput);
        }

        function cancelPasswordEdit() {
            document.querySelectorAll('#passwordForm input').forEach(i => {
                i.disabled = true;
                i.value    = '';
                i.classList.remove('input-error', 'input-success');
            });
            hideFieldMessage('currentPasswordError');
            hideFieldMessage('currentPasswordSuccess');
            hideFieldMessage('newPasswordError');
            hideFieldMessage('confirmPasswordError');
            currentPasswordValid = false;

            document.getElementById('editPasswordBtn').style.display   = 'inline-flex';
            document.getElementById('savePasswordBtn').style.display   = 'none';
            document.getElementById('cancelPasswordBtn').style.display = 'none';

            // Hapus event listener
            document.getElementById('currentPassword').removeEventListener('input', onCurrentPasswordInput);
            document.getElementById('newPassword').removeEventListener('input', onNewPasswordInput);
            document.getElementById('confirmPassword').removeEventListener('input', onConfirmPasswordInput);
        }

        // Real-time: current password dengan debounce 600ms
        function onCurrentPasswordInput() {
            clearTimeout(debounceTimer);
            const val = document.getElementById('currentPassword').value;

            if (!val) {
                resetFieldMessage('currentPassword', 'currentPasswordError', 'currentPasswordSuccess');
                currentPasswordValid = false;
                return;
            }

            debounceTimer = setTimeout(async () => {
                try {
                    const formData = new FormData();
                    formData.append('current_password', val);

                    const response = await fetch('../backend/account/check_password.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();

                    if (result.status === 'success') {
                        currentPasswordValid = true;
                        showFieldSuccess('currentPassword', 'currentPasswordError', 'currentPasswordSuccess');
                    } else {
                        currentPasswordValid = false;
                        showFieldError('currentPassword', 'currentPasswordError', result.message);
                        hideFieldMessage('currentPasswordSuccess');
                    }
                } catch (error) {
                    currentPasswordValid = false;
                }
            }, 600);
        }

        // Real-time: new password
        function onNewPasswordInput() {
            const val = document.getElementById('newPassword').value;
            if (val.length > 0 && val.length < 8) {
                showFieldError('newPassword', 'newPasswordError', 'Password minimal 8 karakter.');
            } else {
                resetFieldMessage('newPassword', 'newPasswordError', null);
            }
            // Cek ulang confirm password jika sudah ada isinya
            if (document.getElementById('confirmPassword').value) {
                onConfirmPasswordInput();
            }
        }

        // Real-time: confirm password
        function onConfirmPasswordInput() {
            const newPass     = document.getElementById('newPassword').value;
            const confirmPass = document.getElementById('confirmPassword').value;
            if (confirmPass && newPass !== confirmPass) {
                showFieldError('confirmPassword', 'confirmPasswordError', 'Password tidak cocok.');
            } else {
                resetFieldMessage('confirmPassword', 'confirmPasswordError', null);
            }
        }

        // =============================================
        // Helper field message
        // =============================================
        function showFieldError(inputId, errorId, message) {
            const input = document.getElementById(inputId);
            const error = document.getElementById(errorId);
            input.classList.add('input-error');
            input.classList.remove('input-success');
            error.querySelector('span:last-child').textContent = message;
            error.classList.add('show');
        }

        function showFieldSuccess(inputId, errorId, successId) {
            const input   = document.getElementById(inputId);
            const error   = document.getElementById(errorId);
            const success = document.getElementById(successId);
            input.classList.remove('input-error');
            input.classList.add('input-success');
            error.classList.remove('show');
            if (success) success.classList.add('show');
        }

        function hideFieldMessage(elementId) {
            const el = document.getElementById(elementId);
            if (el) el.classList.remove('show');
        }

        function resetFieldMessage(inputId, errorId, successId) {
            const input = document.getElementById(inputId);
            input.classList.remove('input-error', 'input-success');
            hideFieldMessage(errorId);
            if (successId) hideFieldMessage(successId);
        }

        // =============================================
        // Save Password
        // =============================================
        async function savePassword() {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword     = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const btn             = document.getElementById('savePasswordBtn');

            if (!currentPassword || !newPassword || !confirmPassword) {
                showErrorMessage('Semua field password harus diisi.');
                return;
            }
            if (!currentPasswordValid) {
                showErrorMessage('Pastikan password saat ini sudah benar.');
                return;
            }
            if (newPassword.length < 8) {
                showErrorMessage('Password baru minimal 8 karakter.');
                return;
            }
            if (newPassword !== confirmPassword) {
                showErrorMessage('Konfirmasi password tidak cocok.');
                return;
            }

            btn.disabled  = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memperbarui...';

            try {
                const formData = new FormData();
                formData.append('current_password', currentPassword);
                formData.append('new_password', newPassword);
                formData.append('confirm_password', confirmPassword);

                const response = await fetch('../backend/account/update_password.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    showSuccessMessage(result.message);
                    cancelPasswordEdit();
                } else {
                    showErrorMessage(result.message);
                }
            } catch (error) {
                showErrorMessage('Gagal terhubung ke server.');
            } finally {
                btn.disabled  = false;
                btn.innerHTML = '<i class="fas fa-key"></i> Update Password';
            }
        }

        // =============================================
        // Logout Confirmation Modal
        // =============================================
        function showLogoutModal() {
            document.getElementById('logoutModal').classList.add('show');
        }

        function hideLogoutModal() {
            document.getElementById('logoutModal').classList.remove('show');
        }

        function confirmLogout() {
            const btn = document.getElementById('confirmLogoutBtn');
            btn.disabled  = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging out...';
            window.location.href = '../backend/logout_data.php';
        }

        // Tutup modal jika klik area luar box
        document.getElementById('logoutModal').addEventListener('click', function(event) {
            if (event.target === this) {
                hideLogoutModal();
            }
        });

        // Tutup modal dengan tombol Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideLogoutModal();
            }
        });

        // =============================================
        // UI Helpers
        // =============================================
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }

        function toggleMobileSidebar() {
            document.getElementById('sidebar').classList.toggle('mobile-open');
            document.querySelector('.mobile-overlay').classList.toggle('active');
        }

        function togglePasswordCard() {
            const card   = document.getElementById('passwordCard');
            const button = card.querySelector('.collapse-toggle span');
            card.classList.toggle('collapsed');
            button.textContent = card.classList.contains('collapsed') ? 'Expand' : 'Collapse';
        }

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon  = event.target.closest('button').querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        document.addEventListener('click', function(event) {
            const sidebar      = document.getElementById('sidebar');
            const mobileToggle = document.querySelector('.mobile-toggle');
            if (window.innerWidth <= 768 &&
                !sidebar.contains(event.target) &&
                !mobileToggle.contains(event.target) &&
                sidebar.classList.contains('mobile-open')) {
                toggleMobileSidebar();
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                document.getElementById('sidebar').classList.remove('mobile-open');
                document.querySelector('.mobile-overlay').classList.remove('active');
            }
        });
    </script>
</body>
</html>