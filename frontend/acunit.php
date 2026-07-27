<?php require_once '../config/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AcController - AC Units</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/basic/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/acunit.css">
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
    <button class="mobile-toggle" onclick="toggleMobileSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="mobile-overlay" onclick="toggleMobileSidebar()"></div>

    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="logo-text">
                        <h1>IT <span style="color: #3b82f6;">AC</span> CONTROLLER</h1>
                        <p>Ac Unit</p>
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
                        <a class="menu-link active">
                            <div class="menu-link-content">
                                <i class='bx bx-air-conditioner'></i>
                                <span class="menu-text">AC Units</span>
                            </div>
                        </a>
                    </div>
                    <!-- <div class="menu-item">
                        <a class="menu-link" href="ircommands.html">
                            <div class="menu-link-content">
                                <i class="fas fa-signal menu-icon"></i>
                                <span class="menu-text">IR Commands</span>
                            </div>
                        </a>
                    </div> -->
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
                        <a class="menu-link" href="account.php">
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
                        <div class="profile-name"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                        <div class="profile-role">Administrator</div>
                    </div>
                    <button class="logout-btn" title="Logout" onclick="showLogoutModal()">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                    <button class="logout-btn-collapsed" title="Logout" onclick="showLogoutModal()">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <div class="success-message" id="successMessage">
                <i class="bx bx-check-circle"></i>
                <span></span>
            </div>
            <div class="error-message" id="errorMessage">
                <i class="bx bx-error-circle"></i>
                <span></span>
            </div>

            <div id="acUnitsPage" class="page-content">
                <div class="content-header-acunit">
                    <h2>AC Units</h2>
                    <p>Manage all your AC units</p>
                </div>

                <div class="page-actions">
                    <div class="search-filter">
                        <input type="text" class="search-input" placeholder="Search AC units..." id="searchInput" onkeyup="searchTable()">
                    </div>
                    <button class="btn-add" onclick="openAddModal()">
                        <i class="fas fa-plus"></i>
                        Add New AC
                    </button>
                </div>

                <div class="table-container">
                    <div class="table-wrapper">
                        <table class="data-table" id="acTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Temperature</th>
                                    <th>Status</th>
                                    <th>Mode</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="acTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Detail AC -->
    <div class="modal-overlay" id="acModal" onclick="closeModalOnOverlay(event)">
        <div class="modal-content" id="detailModalContent">
            <div class="modal-header">
                <h2>
                    <div class="modal-header-icon">
                        <i class='bx bx-air-conditioner'></i>
                    </div>
                    <span id="modalTitle">AC Details</span>
                    <div id="modeIndicator" class="mode-indicator">
                        🔒 View Mode
                    </div>
                </h2>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">

                <!-- Info AC (Nama & Lokasi) -->
                <div class="detail-section" id="infoSection">
                    <div class="section-title">
                        <i class="fas fa-info-circle" style="margin-right:6px;"></i> Info AC
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Nama AC</span>
                        <input type="text" class="form-input" id="modalAcName" placeholder="Nama AC" disabled>
                    </div>
                    <div class="detail-row" style="margin-top:10px;">
                        <span class="detail-label">Lokasi</span>
                        <input type="text" class="form-input" id="modalAcLocation" placeholder="Lokasi" disabled>
                    </div>
                    <div class="detail-row" style="margin-top:10px;">
                        <span class="detail-label">Batas Suhu (°C)</span>
                        <input type="number" class="form-input" id="modalTempThreshold" placeholder="Batas suhu ruangan" disabled>
                    </div>
                </div>

                <!-- Kontrol Langsung — ikut terkunci sampai Unlock Control -->
                <div class="detail-section power-section" id="powerSection">
                    <div class="section-title">
                        <i class="fas fa-bolt" style="margin-right: 6px;"></i> Kontrol Langsung
                    </div>
                    <button class="power-ac-btn" id="powerAcBtn" onclick="togglePower()">
                        <div class="power-ac-icon-wrap" id="powerAcIconWrap">
                            <i class='bx bx-air-conditioner power-ac-icon' id="powerAcIcon"></i>
                        </div>
                        <div class="power-ac-state">
                            <span class="power-ac-label" id="powerAcLabel">Hidup</span>
                            <span class="power-ac-hint">Ketuk untuk matikan</span>
                        </div>
                    </button>
                </div>

                <!-- Wrapper konten yang bisa di-lock (suhu, mode, jadwal) -->
                <div style="position: relative;">
                <div id="readOnlyOverlay" class="read-only-overlay"></div>

                <!-- Status Section -->
                <div class="detail-section">
                    <div class="section-title">Status & Information</div>
                    <div class="detail-row">
                        <span class="detail-label">Location</span>
                        <span class="detail-value" id="modalLocation">-</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status</span>
                        <span class="status-badge active" id="modalStatus">Active</span>
                    </div>
                </div>

                <!-- Temperature Control -->
                <div class="detail-section">
                    <div class="section-title">Temperature Control</div>
                    <div class="temp-control">
                        <div class="temp-display" id="tempDisplay">22°C</div>
                        <div class="temp-buttons">
                            <button class="temp-btn" id="decreaseTempBtn" onclick="decreaseTemp()">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button class="temp-btn" id="increaseTempBtn" onclick="increaseTemp()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mode Selection -->
                <div class="detail-section">
                    <div class="section-title">Mode Selection</div>
                    <div class="mode-selection">
                        <div class="mode-btn active" onclick="selectMode(this, 'cool')">
                            <i class="fas fa-snowflake"></i>
                            <span>Cool</span>
                        </div>
                        <!-- <div class="mode-btn" onclick="selectMode(this, 'fan')">
                            <i class="fas fa-wind"></i>
                            <span>Fan</span>
                        </div>
                        <div class="mode-btn" onclick="selectMode(this, 'dry')">
                            <i class="fas fa-tint"></i>
                            <span>Dry</span>
                        </div> -->
                    </div>
                </div>

                <!-- Command Summary Section -->
                <!-- <div class="detail-section">
                    <div class="section-title">Command Summary</div>
                    <div class="cmd-summary-wrapper"> -->
                        <!-- Current Command -->
                        <!-- <div class="cmd-card current">
                            <div class="cmd-card-icon"><i class="fas fa-broadcast-tower"></i></div>
                            <div class="cmd-card-body">
                                <div class="cmd-card-label">Current Command</div>
                                <div class="cmd-tags" id="currentTags"></div>
                            </div>
                        </div> -->

                        <!-- Hex Collapsible (Current only) -->
                        <!-- <div class="hex-collapsible">
                            <button class="hex-toggle" id="hexToggleBtn" onclick="toggleHex()">
                                <span>Hex Details</span>
                                <div class="hex-toggle-right">
                                    <span class="hex-toggle-hint">tap to expand</span>
                                    <i class="fas fa-chevron-down arrow"></i>
                                </div>
                            </button>
                            <div class="hex-body" id="hexBody">
                                <div class="hex-rows" id="currentHexRows"></div>
                                <div class="hex-raw-row">
                                    <span class="hex-raw-label">RAW</span>
                                    <span class="hex-raw-val" id="currentRaw"></span>
                                    <button class="hex-copy-btn" onclick="copyRaw('current')"><i class="fas fa-copy"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Schedule Section -->
                <div class="detail-section">
                    <div class="section-title">
                        <i class="fas fa-clock" style="margin-right: 6px;"></i> Penjadwalan AC
                    </div>

                    <!-- Toggle aktifkan jadwal -->
                    <div class="sched-master-toggle-row">
                        <span class="sched-master-label">Aktifkan Jadwal</span>
                        <div class="toggle-switch green" id="schedMasterToggle" onclick="toggleSchedMaster()">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>

                    <!-- Preview ringkasan -->
                    <div class="sched-preview-bar" id="schedPreviewBar">Jadwal nonaktif</div>

                    <!-- Dua kolom waktu ON dan OFF -->
                    <div class="sched-time-row" id="schedTimeRow">
                        <!-- ON -->
                        <div class="sched-time-card on">
                            <div class="sched-time-card-header">
                                <div class="sched-icon on"><i class="fas fa-power-off"></i></div>
                                <span class="sched-title">Hidup (ON)</span>
                            </div>
                            <input type="time" class="sched-time-input" id="schedOnTime" value="07:00">
                        </div>
                        <!-- OFF -->
                        <div class="sched-time-card off">
                            <div class="sched-time-card-header">
                                <div class="sched-icon off"><i class="fas fa-power-off"></i></div>
                                <span class="sched-title">Mati (OFF)</span>
                            </div>
                            <input type="time" class="sched-time-input" id="schedOffTime" value="18:00">
                        </div>
                    </div>

                    <!-- Satu set hari aktif -->
                    <div class="sched-days-section" id="schedDaysSection">
                        <label class="sched-field-label">Hari Aktif</label>
                        <div class="day-chips" id="schedDayChips">
                            <span class="day-chip active" data-day="1" onclick="toggleDay(this)">Sen</span>
                            <span class="day-chip active" data-day="2" onclick="toggleDay(this)">Sel</span>
                            <span class="day-chip active" data-day="3" onclick="toggleDay(this)">Rab</span>
                            <span class="day-chip active" data-day="4" onclick="toggleDay(this)">Kam</span>
                            <span class="day-chip active" data-day="5" onclick="toggleDay(this)">Jum</span>
                            <span class="day-chip" data-day="6" onclick="toggleDay(this)">Sab</span>
                            <span class="day-chip" data-day="0" onclick="toggleDay(this)">Min</span>
                        </div>
                    </div>
                </div>
                </div><!-- end lockable wrapper -->
            </div>

            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeModal()">
                    Close
                </button>
                <button class="btn btn-primary" id="editActionBtn" onclick="handleEditAction()">
                    Unlock Control
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Add AC -->
    <div class="modal-overlay" id="formModal" onclick="closeFormModalOnOverlay(event)">
        <div class="modal-content read-only" id="formModalContent">
            <div class="modal-header">
                <h2>
                    <div class="modal-header-icon">
                        <i class='bx bx-air-conditioner'></i>
                    </div>
                    <span id="formModalTitle">Add New AC Unit</span>
                </h2>
                <button class="modal-close" onclick="closeFormModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <form id="acForm">
                    <div class="form-group">
                        <label class="form-label">AC Name</label>
                        <input type="text" class="form-input" id="acName" placeholder="e.g., Conference Room AC" required oninput="checkAcNameDuplicate()">
                        <span class="field-error-message" id="acNameError" style="display:none; color:#ef4444; font-size:12px; margin-top:4px; align-items:center; gap:4px;">
                            <i class="fas fa-exclamation-circle"></i> Nama AC sudah ada!
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-input" id="acLocation" placeholder="e.g., Conference Room B" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Default Temperature (°C)</label>
                        <input type="number" class="form-input" id="acTemp" min="16" max="30" value="24">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Default Mode</label>
                        <select class="form-select" id="acMode">
                            <option value="cool">Cool</option>
                            <option value="fan">Fan</option>
                            <option value="dry">Dry</option>
                            <option value="auto">Auto</option>
                        </select>
                    </div>
                </form>
            </div>

            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeFormModal()">
                    Cancel
                </button>
                <button class="btn btn-primary" id="submitAcBtn" onclick="submitACForm()"><
                    <i class="fas fa-save"></i> <span id="formSubmitText">Add AC Unit</span>
                </button>
            </div>
        </div>
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
        // STATE
        // =============================================
        let allAcUnits    = [];
        let activeAc      = null; // AC yang sedang dibuka di modal
        let currentTemp   = 22;
        let isPowerOn     = false;
        let isEditUnlocked = false;
        let currentMode   = 'cool';
        let isEditMode    = false;

        const DAY_NAMES = { 0:'Min', 1:'Sen', 2:'Sel', 3:'Rab', 4:'Kam', 5:'Jum', 6:'Sab' };

        // =============================================
        // LOAD AC UNITS
        // =============================================
        async function loadAcUnits() {
            try {
                const response = await fetch('../backend/acunit/get_acunits.php');
                const result   = await response.json();
                if (result.status === 'success') {
                    allAcUnits = result.data;
                    renderTable(allAcUnits);
                } else {
                    showErrorMessage('Gagal memuat data AC units.');
                }
            } catch (error) {
                showErrorMessage('Gagal terhubung ke server.');
            }
        }

        // =============================================
        // RENDER TABLE
        // =============================================
        function renderTable(data) {
            const tbody = document.getElementById('acTableBody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#64748b;">Tidak ada unit AC.</td></tr>';
                return;
            }
            tbody.innerHTML = data.map(ac => `
                <tr>
                    <td>${ac.ac_name}</td>
                    <td>${ac.ac_location}</td>
                    <td>${ac.target_temp}°C</td>
                    <td><span class="status-badge ${ac.ac_status ? 'active' : 'inactive'}">${ac.ac_status ? 'Active' : 'Inactive'}</span></td>
                    <td>${ac.ac_mode}</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-icon btn-edit" onclick="openModal(event, ${ac.id_ac_units})" title="Kontrol">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-icon btn-delete" onclick="confirmDelete(${ac.id_ac_units}, '${ac.ac_name}')" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`).join('');
        }

        // =============================================
        // SEARCH
        // =============================================
        function searchTable() {
            const filter = document.getElementById('searchInput').value.toLowerCase();
            const filtered = allAcUnits.filter(ac =>
                ac.ac_name.toLowerCase().includes(filter) ||
                ac.ac_location.toLowerCase().includes(filter) ||
                ac.ac_mode.toLowerCase().includes(filter)
            );
            renderTable(filtered);
        }

        // =============================================
        // MODAL OPEN / CLOSE
        // =============================================
        function openModal(event, acUnitId) {
            event.preventDefault();
            activeAc = allAcUnits.find(ac => ac.id_ac_units === acUnitId);
            if (!activeAc) return;

            // Isi info AC
            document.getElementById('modalTitle')     .textContent = activeAc.ac_name;
            document.getElementById('modalAcName')    .value       = activeAc.ac_name;
            document.getElementById('modalAcLocation').value       = activeAc.ac_location;

            // Status & suhu
            currentTemp = activeAc.target_temp;
            document.getElementById('tempDisplay').textContent = currentTemp + '°C';
            document.getElementById('modalLocation').textContent = activeAc.ac_location;

            //Batasan Suhu
            document.getElementById('modalTempThreshold').value = activeAc.temp_threshold ?? 25;

            isPowerOn = activeAc.ac_status;
            const statusBadge = document.getElementById('modalStatus');
            statusBadge.textContent = isPowerOn ? 'Active' : 'Inactive';
            statusBadge.className   = 'status-badge ' + (isPowerOn ? 'active' : 'inactive');
            setPowerUI(isPowerOn);

            // Jadwal
            loadScheduleToUI(activeAc.schedule);

            document.getElementById('acModal').classList.add('active');
            document.body.style.overflow = 'hidden';
            setReadOnlyMode();
        }

        function closeModal() {
            document.getElementById('acModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            activeAc = null;
        }

        function closeModalOnOverlay(event) {
            if (event.target.id === 'acModal') closeModal();
        }

        // =============================================
        // TEMPERATURE
        // =============================================
        function increaseTemp() {
            if (!isPowerOn) {
                showErrorMessage('AC sedang dalam keadaan mati, hidupkan untuk merubah suhu.');
                return;
            }
            if (currentTemp < 30) {
                currentTemp++;
                document.getElementById('tempDisplay').textContent = currentTemp + '°C';
            }
        }

        function decreaseTemp() {
            if (!isPowerOn) {
                showErrorMessage('AC sedang dalam keadaan mati, hidupkan untuk merubah suhu.');
                return;
            }
            if (currentTemp > 16) {
                currentTemp--;
                document.getElementById('tempDisplay').textContent = currentTemp + '°C';
            }
        }

        // =============================================
        // POWER
        // =============================================
        function setPowerUI(on) {
            const btn   = document.getElementById('powerAcBtn');
            const label = document.getElementById('powerAcLabel');
            const hint  = btn.querySelector('.power-ac-hint');
            const badge = document.getElementById('modalStatus');
            btn.classList.toggle('power-on',  on);
            btn.classList.toggle('power-off', !on);
            label.textContent = on ? 'Hidup' : 'Mati';
            hint.textContent  = on ? 'Ketuk untuk matikan' : 'Ketuk untuk hidupkan';
            badge.textContent = on ? 'Active' : 'Inactive';
            badge.className   = 'status-badge ' + (on ? 'active' : 'inactive');

            // Kunci visual tombol suhu (+/-) saat AC mati.
            // Sengaja TIDAK memakai disabled/pointer-events:none, supaya klik
            // tetap tertangkap oleh increaseTemp()/decreaseTemp() dan
            // memunculkan pesan error alih-alih diam saja.
            const decBtn = document.getElementById('decreaseTempBtn');
            const incBtn = document.getElementById('increaseTempBtn');
            [decBtn, incBtn].forEach(b => {
                if (!b) return;
                b.style.opacity = on ? '1'   : '0.4';
                b.style.cursor  = on ? 'pointer' : 'not-allowed';
            });
        }

        async function togglePower() {
            if (!isEditUnlocked) {
                showErrorMessage('Klik Unlock Control dulu.');
                return;
            }
            if (!activeAc || !activeAc.ip_address) {
                showErrorMessage('IP perangkat tidak ditemukan.');
                return;
            }

            const newValue = isPowerOn ? 'off' : 'on';
            const btn      = document.getElementById('powerAcBtn');
            btn.disabled   = true;

            try {
                const formData = new FormData();
                formData.append('ac_unit_id', activeAc.id_ac_units);
                formData.append('value',      newValue);
                formData.append('ip_address', activeAc.ip_address);

                const response = await fetch('../backend/acunit/control_ac_power.php', {
                    method: 'POST', body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    isPowerOn = !isPowerOn;
                    setPowerUI(isPowerOn);
                    // Update local data
                    activeAc.ac_status = isPowerOn;
                    const row = allAcUnits.find(a => a.id_ac_units === activeAc.id_ac_units);
                    if (row) row.ac_status = isPowerOn;
                    renderTable(allAcUnits);
                    showSuccessMessage(result.message);
                } else {
                    showErrorMessage(result.message);
                }
            } catch (error) {
                showErrorMessage('Gagal terhubung ke server.');
            } finally {
                btn.disabled = false;
            }
        }

        // =============================================
        // MODE
        // =============================================
        function selectMode(element, mode) {
            document.querySelectorAll('.mode-btn').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');
            currentMode = mode;
        }

        // =============================================
        // READ ONLY / EDIT MODE
        // =============================================
        function setReadOnlyMode() {
            isEditUnlocked = false;
            document.getElementById('detailModalContent').classList.add('read-only');
            document.getElementById('readOnlyOverlay').style.display = 'block';
            document.getElementById('powerAcBtn').disabled = true;
            document.getElementById('modalAcName').disabled = true;
            document.getElementById('modalAcLocation').disabled = true;
            document.getElementById('modeIndicator').textContent = '🔒 View Mode';
            document.getElementById('modeIndicator').classList.remove('edit');
            document.getElementById('modalTempThreshold').disabled = true;
            document.getElementById('editActionBtn').textContent = 'Unlock Control';
        }

        function setEditMode() {
            isEditUnlocked = true;
            document.getElementById('detailModalContent').classList.remove('read-only');
            document.getElementById('readOnlyOverlay').style.display = 'none';
            document.getElementById('powerAcBtn').disabled = false;
            document.getElementById('modalAcName').disabled = false;
            document.getElementById('modalAcLocation').disabled = false;
            document.getElementById('modeIndicator').textContent = '✏️ Edit Mode';
            document.getElementById('modeIndicator').classList.add('edit');
            document.getElementById('modalTempThreshold').disabled = false;
            document.getElementById('editActionBtn').textContent = 'Save Changes';
        }

        async function handleEditAction() {
            if (!isEditUnlocked) {
                setEditMode();
                return;
            }

            const sched      = getScheduleFromUI();
            const acName     = document.getElementById('modalAcName').value.trim();
            const acLocation = document.getElementById('modalAcLocation').value.trim();

            if (!acName || !acLocation) {
                showErrorMessage('Nama AC dan lokasi tidak boleh kosong.');
                return;
            }
            if (sched.enabled && sched.days.length === 0) {
                showErrorMessage('Jadwal aktif: pilih minimal satu hari!');
                return;
            }
            if (!isPowerOn && currentTemp !== activeAc.target_temp) {
                showErrorMessage('Perubahan suhu tidak dapat dilakukan, AC sedang mati.');
                return;
            }

            const btn     = document.getElementById('editActionBtn');
            btn.disabled  = true;
            btn.textContent = 'Menyimpan...';

            try {
                // 1. Update info AC (nama & lokasi)
                const infoForm = new FormData();
                infoForm.append('ac_unit_id',  activeAc.id_ac_units);
                infoForm.append('ac_name',     acName);
                infoForm.append('ac_location', acLocation);
                infoForm.append('temp_threshold', document.getElementById('modalTempThreshold').value);
                const infoRes = await fetch('../backend/acunit/update_ac_info.php', { method: 'POST', body: infoForm });
                const infoResult = await infoRes.json();
                if (infoResult.status !== 'success') throw new Error(infoResult.message);

                // 2. Update suhu target
                if (currentTemp !== activeAc.target_temp) {
                    if (!activeAc.ip_address) throw new Error('IP perangkat tidak ditemukan.');
                    const tempForm = new FormData();
                    tempForm.append('ac_unit_id', activeAc.id_ac_units);
                    tempForm.append('value',      currentTemp);
                    tempForm.append('ip_address', activeAc.ip_address);
                    const tempRes = await fetch('../backend/acunit/control_ac_temp.php', { method: 'POST', body: tempForm });
                    const tempResult = await tempRes.json();
                    if (tempResult.status !== 'success') throw new Error(tempResult.message);
                }

                // 3. Simpan jadwal
                const [onHour, onMin]   = sched.onTime.split(':').map(Number);
                const [offHour, offMin] = sched.offTime.split(':').map(Number);
                const schedPayload = {
                    ac_unit_id : activeAc.id_ac_units,
                    enabled    : sched.enabled,
                    on_hour    : onHour,  on_minute  : onMin,
                    off_hour   : offHour, off_minute : offMin,
                    mon: sched.days.includes(1) ? 1 : 0,
                    tue: sched.days.includes(2) ? 1 : 0,
                    wed: sched.days.includes(3) ? 1 : 0,
                    thu: sched.days.includes(4) ? 1 : 0,
                    fri: sched.days.includes(5) ? 1 : 0,
                    sat: sched.days.includes(6) ? 1 : 0,
                    sun: sched.days.includes(0) ? 1 : 0,
                };
                const schedRes = await fetch('../backend/acunit/save_schedule.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(schedPayload)
                });
                const schedResult = await schedRes.json();
                if (schedResult.status !== 'success') throw new Error(schedResult.message);

                // Update local data
                const updatedSchedule = {
                    id_schedules : activeAc.schedule?.id_schedules ?? null,
                    on_hour      : onHour,  on_minute  : onMin,
                    off_hour     : offHour, off_minute : offMin,
                    mon: sched.days.includes(1), tue: sched.days.includes(2),
                    wed: sched.days.includes(3), thu: sched.days.includes(4),
                    fri: sched.days.includes(5), sat: sched.days.includes(6),
                    sun: sched.days.includes(0),
                    is_active: sched.enabled,
                };

                const row = allAcUnits.find(a => a.id_ac_units === activeAc.id_ac_units);
                if (row) {
                    row.ac_name        = acName;
                    row.ac_location    = acLocation;
                    row.target_temp    = currentTemp;
                    row.temp_threshold = parseFloat(document.getElementById('modalTempThreshold').value);
                    row.schedule       = updatedSchedule;
                }
                activeAc.target_temp    = currentTemp;
                activeAc.temp_threshold = parseFloat(document.getElementById('modalTempThreshold').value);
                activeAc.schedule       = updatedSchedule;
                renderTable(allAcUnits);

                showSuccessMessage('Semua perubahan berhasil disimpan.');
                setReadOnlyMode();

            } catch (error) {
                showErrorMessage(error.message || 'Terjadi kesalahan.');
            } finally {
                btn.disabled = false;
            }
        }

        // =============================================
        // ADD NEW AC (Form Modal)
        // =============================================
        function openAddModal() {
            isEditMode = false;
            document.getElementById('formModalTitle').textContent = 'Add New AC Unit';
            document.getElementById('formSubmitText').textContent = 'Add AC Unit';
            document.getElementById('acForm').reset();
            document.getElementById('acNameError').style.display = 'none';
            document.getElementById('submitAcBtn').disabled = false;
            document.getElementById('formModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeFormModal() {
            document.getElementById('formModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function closeFormModalOnOverlay(event) {
            if (event.target.id === 'formModal') {
                closeFormModal();
            }
        }

        async function submitACForm() {
            const name     = document.getElementById('acName').value.trim();
            const location = document.getElementById('acLocation').value.trim();
            const temp     = document.getElementById('acTemp').value;
            const mode     = document.getElementById('acMode').value;

            if (!name || !location) {
                showErrorMessage('Please fill all required fields!');
                return;
            }
        
            if (checkAcNameDuplicate()) {
                showErrorMessage('Nama AC sudah ada!');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('ac_name',        name);
                formData.append('ac_location',    location);
                formData.append('target_temp',    temp);
                formData.append('ac_mode',        mode);

                const response = await fetch('../backend/acunit/add_acunit.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    showSuccessMessage(result.message || 'AC Unit berhasil ditambahkan!');
                    closeFormModal();
                    await loadAcUnits(); // Refresh tabel
                } else {
                    showErrorMessage(result.message || 'Gagal menambahkan AC Unit.');
                }
            } catch (error) {
                showErrorMessage('Gagal terhubung ke server.');
            }
        }

        function checkAcNameDuplicate() {
            const nameInput = document.getElementById('acName');
            const errorEl   = document.getElementById('acNameError');
            const submitBtn = document.getElementById('submitAcBtn');
            const name      = nameInput.value.trim().toLowerCase();

            const isDuplicate = name !== '' && allAcUnits.some(ac => ac.ac_name.trim().toLowerCase() === name);

            errorEl.style.display = isDuplicate ? 'flex' : 'none';
            nameInput.style.borderColor = isDuplicate ? '#ef4444' : '';
            submitBtn.disabled = isDuplicate;

            return isDuplicate;
        }

        // =============================================
        // DELETE
        // =============================================
        async function confirmDelete(acUnitId, acName) {
            const acUnit = allAcUnits.find(a => a.id_ac_units === acUnitId);

            if (acUnit) {
                const isActive    = !!acUnit.ac_status;
                const isConnected = !!acUnit.device_id;

                if (isActive || isConnected) {
                    showErrorMessage(
                        `${acName} tidak dapat dihapus karena masih aktif atau terhubung dengan device. ` +
                        `Nonaktifkan atau putuskan koneksi device terlebih dahulu.`
                    );
                    return;
                }
            }

            if (!confirm(`Yakin ingin menghapus ${acName}? Semua data jadwal dan aktivitas terkait akan ikut terhapus.`)) return;

            try {
                const formData = new FormData();
                formData.append('ac_unit_id', acUnitId);
                const response = await fetch('../backend/acunit/delete_acunit.php', { method: 'POST', body: formData });
                const result   = await response.json();

                if (result.status === 'success') {
                    allAcUnits = allAcUnits.filter(a => a.id_ac_units !== acUnitId);
                    renderTable(allAcUnits);
                    showSuccessMessage(result.message);
                } else {
                    showErrorMessage(result.message);
                }
            } catch (error) {
                showErrorMessage('Gagal terhubung ke server.');
            }
        }

        // =============================================
        // SCHEDULE — Unified (satu set hari untuk ON & OFF)
        // =============================================
        function loadScheduleToUI(sched) {
            // Jika belum ada jadwal di DB, pakai default Senin–Jumat, nonaktif
            const s = sched || {
                on_hour: 7, on_minute: 0, off_hour: 18, off_minute: 0,
                mon: 1, tue: 1, wed: 1, thu: 1, fri: 1, sat: 0, sun: 0
            };

            // Status aktif/nonaktif jadwal diambil dari kolom is_active.
            // Jika belum pernah ada baris schedule sama sekali, default nonaktif.
            const enabled = sched ? !!sched.is_active : false;

            document.getElementById('schedOnTime').value  = String(s.on_hour).padStart(2,'0')  + ':' + String(s.on_minute).padStart(2,'0');
            document.getElementById('schedOffTime').value = String(s.off_hour).padStart(2,'0') + ':' + String(s.off_minute).padStart(2,'0');

            // Set master toggle
            const masterToggle = document.getElementById('schedMasterToggle');
            masterToggle.classList.toggle('active', enabled);

            // Tampilkan / sembunyikan konten jadwal
            _applySchedEnabledUI(enabled);

            // Set hari chips dari satu set hari
            const dayMap = { 1: !!s.mon, 2: !!s.tue, 3: !!s.wed, 4: !!s.thu, 5: !!s.fri, 6: !!s.sat, 0: !!s.sun };
            document.getElementById('schedDayChips').querySelectorAll('.day-chip').forEach(chip => {
                chip.classList.toggle('active', !!dayMap[parseInt(chip.dataset.day)]);
            });

            updateSchedPreview();
        }

        function getScheduleFromUI() {
            const isEnabled = document.getElementById('schedMasterToggle').classList.contains('active');
            const days = [];
            document.getElementById('schedDayChips').querySelectorAll('.day-chip.active').forEach(chip => {
                days.push(parseInt(chip.dataset.day));
            });
            return {
                enabled  : isEnabled,
                onTime   : document.getElementById('schedOnTime').value,
                offTime  : document.getElementById('schedOffTime').value,
                days     : days
            };
        }

        function toggleSchedMaster() {
            const toggle  = document.getElementById('schedMasterToggle');
            const enabled = toggle.classList.toggle('active');
            _applySchedEnabledUI(enabled);
            updateSchedPreview();
        }

        function _applySchedEnabledUI(enabled) {
            const timeRow    = document.getElementById('schedTimeRow');
            const daysSection = document.getElementById('schedDaysSection');
            const opacity    = enabled ? '1' : '0.4';
            const pointer    = enabled ? 'auto' : 'none';
            timeRow.style.opacity       = opacity;
            timeRow.style.pointerEvents = pointer;
            daysSection.style.opacity       = opacity;
            daysSection.style.pointerEvents = pointer;
        }

        function toggleDay(el) {
            el.classList.toggle('active');
            updateSchedPreview();
        }

        function updateSchedPreview() {
            const isEnabled  = document.getElementById('schedMasterToggle').classList.contains('active');
            const preview    = document.getElementById('schedPreviewBar');
            if (!isEnabled) { preview.textContent = 'Jadwal nonaktif'; return; }

            const onTime  = document.getElementById('schedOnTime').value;
            const offTime = document.getElementById('schedOffTime').value;
            const activeDays = [];
            document.getElementById('schedDayChips').querySelectorAll('.day-chip.active').forEach(chip => {
                activeDays.push(DAY_NAMES[parseInt(chip.dataset.day)]);
            });

            if (activeDays.length === 0) { preview.textContent = 'Pilih minimal satu hari'; return; }

            let dayLabel;
            if (activeDays.length === 7) {
                dayLabel = 'Setiap hari';
            } else if (activeDays.length === 5 && ['Sen','Sel','Rab','Kam','Jum'].every(d => activeDays.includes(d))) {
                dayLabel = 'Senin–Jumat';
            } else if (activeDays.length === 2 && ['Sab','Min'].every(d => activeDays.includes(d))) {
                dayLabel = 'Sabtu–Minggu';
            } else {
                dayLabel = activeDays.join(', ');
            }
            preview.textContent = `${dayLabel} • Hidup ${onTime} — Mati ${offTime}`;
        }

        // =============================================
        // SIDEBAR
        // =============================================
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }

        function toggleMobileSidebar() {
            document.getElementById('sidebar').classList.toggle('mobile-open');
            document.querySelector('.mobile-overlay').classList.toggle('active');
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

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') { closeModal(); closeFormModal(); }
        });

        // Listener preview jadwal saat waktu berubah
        document.getElementById('schedOnTime') .addEventListener('change', updateSchedPreview);
        document.getElementById('schedOffTime').addEventListener('change', updateSchedPreview);

        // =============================================
        // BOOT
        // =============================================
        window.addEventListener('DOMContentLoaded', loadAcUnits);
    </script>
</body>
</html>
