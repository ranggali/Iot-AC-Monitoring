<?php require_once '../config/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AcController - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/basic/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/toast.css">
    <link rel="stylesheet" href="../public/css/logout.css">

    <!-- icon -->
    <link rel="icon" href="../public/assets/icon.ico" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="../public/assets/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../public/assets/icon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../public/assets/apple-icon.png">
    <link rel="stylesheet" href="../public/css/dashboard.css">
</head>
<body>
    <button class="mobile-toggle" onclick="toggleMobileSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="mobile-overlay" onclick="toggleMobileSidebar()"></div>

    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <!-- Header -->
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="logo-text">
                        <h1>IT <span style="color: #3b82f6;">AC</span> CONTROLLER</h1>
                        <p>Dashboard IOT AC MONITORING</p>
                    </div>
                </div>
                <button class="toggle-btn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Navigation Menu -->
            <nav class="nav-menu">
                <!-- Menu Items Top -->
                <div class="menu-items-top">
                    <div class="menu-item">
                        <a class="menu-link active">
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

                    <!-- <div class="menu-item">
                        <a class="menu-link" href="ircommands.php">
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

                <!-- Menu Items Bottom -->
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

            <!-- User Profile -->
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
            <!-- Home Page -->
            <div id="homePage" class="page-content">
                <div class="content-header">
                    <h2>Dashboard</h2>
                    <p>Welcome to your AC control dashboard</p>
                </div>

                <!-- Stats Row -->
                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class='bx bx-air-conditioner'></i></div>
                        <div class="stat-info">
                            <div class="stat-val" id="statTotal">6</div>
                            <div class="stat-lbl">Total Unit AC</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="fas fa-circle-check"></i></div>
                        <div class="stat-info">
                            <div class="stat-val" id="statActive">4</div>
                            <div class="stat-lbl">Unit Aktif</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon red"><i class="fas fa-circle-xmark"></i></div>
                        <div class="stat-info">
                            <div class="stat-val" id="statInactive">2</div>
                            <div class="stat-lbl">Unit Tidak Aktif</div>
                        </div>
                    </div>
                </div>

                <!-- Toast -->
                <div class="success-message" id="successMessage"><i class="bx bx-check-circle"></i><span></span></div>
                <div class="error-message" id="errorMessage"><i class="bx bx-error-circle"></i><span></span></div>

                <!-- search dashboard -->
                <div class="dashboard-search">
                    <div class="dashboard-search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search AC units by name or location..." id="dashboardSearch" onkeyup="searchDashboard()">
                    </div>
                </div>

                <div class="cards-grid" id="cardsGrid">
                    <!-- AC cards di-render oleh JavaScript -->
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper" id="paginationWrapper">
                    <div class="pagination-info" id="paginationInfo"></div>
                    <div class="pagination-controls" id="paginationControls"></div>
                </div>
            </div>

        </main>
    </div>

    <!-- Modal Detail AC -->
    <div class="modal-overlay" id="acModal" onclick="closeModalOnOverlay(event)">
        <div class="modal-content">
            <div class="modal-header">
                <h2>
                    <div class="modal-header-icon">
                        <i class='bx  bx-air-conditioner'></i> 
                    </div>
                    <span id="modalTitle">AC Details</span>
                </h2>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
    <!-- Info Cards Grid -->
    <div class="info-grid">

        <!-- Suhu Ruangan (BME280) — full width -->
        <div class="info-card temperature-card">
            <div class="temp-boost">
                <div class="temp-boost-label">Boost</div>
                <div class="temp-boost-val inactive" id="boostStatus">Nonaktif</div>
            </div>
            <div class="temp-main">
                <div class="info-card-icon"><i class="fas fa-thermometer-half"></i></div>
                <div class="info-card-label">Suhu Ruangan</div>
                <div class="info-card-value">
                    <span id="sensorTemp">--</span>°C
                    <span class="sensor-fresh" id="tempFresh"></span>
                </div>
            </div>
            <div class="temp-side">
                <div class="temp-side-label">Set AC</div>
                <div class="temp-side-val" id="tempDisplay">--°C</div>
            </div>
        </div>

        <!-- Kelembapan (BME280) -->
        <div class="info-card humidity-card">
            <div class="info-card-icon"><i class="fas fa-tint"></i></div>
            <div class="info-card-label">Kelembapan</div>
            <div class="humidity-arc-wrap">
                <svg class="humidity-mini-arc" viewBox="0 0 42 42">
                    <circle cx="21" cy="21" r="17" fill="none" stroke="rgba(255,255,255,0.07)" stroke-width="4"/>
                    <circle cx="21" cy="21" r="17" fill="none" stroke="#4fc3f7" stroke-width="4"
                            stroke-dasharray="106.8" stroke-dashoffset="106.8"
                            stroke-linecap="round" transform="rotate(-90 21 21)"
                            id="humidityArc" style="transition:stroke-dashoffset 0.8s ease;"/>
                </svg>
                <div>
                    <div class="info-card-value" style="font-size:20px;">
                        <span id="sensorHumidity">--</span>%
                    </div>
                    <div class="info-card-subtext" id="humidityStatus">--</div>
                </div>
            </div>
        </div>

        <!-- Status AC -->
        <div class="info-card status-card">
            <div class="info-card-icon"><i class="fas fa-power-off"></i></div>
            <div class="info-card-label">Status AC</div>
            <div class="info-card-value">
                <div class="status-indicator">
                    <span class="status-dot" id="statusDot"></span>
                    <span id="modalStatus" style="font-size:18px;">--</span>
                </div>
            </div>
            <div class="info-card-subtext" id="statusTime">--</div>
        </div>

        <!-- Mode AC -->
        <div class="info-card mode-card">
            <div class="info-card-icon"><i class="fas fa-snowflake" id="modeIcon"></i></div>
            <div class="info-card-label">Mode AC</div>
            <div class="info-card-value" style="font-size:15px;">
                <span class="mode-badge cool" id="modeBadge">
                    <span id="acMode">COOL</span>
                </span>
            </div>
            <div class="info-card-subtext">Lokasi: <span id="modalLocation">--</span></div>
        </div>

        <!-- Fan Speed -->
        <div class="info-card fan-card">
            <div class="info-card-icon"><i class="fas fa-fan"></i></div>
            <div class="info-card-label">Fan Speed</div>
            <div class="info-card-value" style="font-size:20px;" id="acFanSpeed">3 Bar</div>
            <div class="fan-bar-wrap">
                <div class="fan-bar">
                    <div class="fan-bar-fill" id="fanBarFill" style="width:50%;"></div>
                </div>
                <span class="fan-bar-label" id="fanBarLabel">3 Bar</span>
            </div>
        </div>

        <!-- Tekanan Udara (BME280) -->
        <div class="info-card pressure-card">
            <div class="info-card-icon"><i class="fas fa-compress-alt"></i></div>
            <div class="info-card-label">Tekanan Udara</div>
            <div class="info-card-value">
                <span id="sensorPressure">--</span>
                <span style="font-size:13px; opacity:0.5;"> hPa</span>
            </div>
            <div class="info-card-subtext">Sensor BME280</div>
        </div>

        <!-- Batas Suhu Ruangan -->
        <div class="info-card">
            <div class="info-card-icon"><i class="fas fa-temperature-high"></i></div>
            <div class="info-card-label">Batas Suhu Ruangan</div>
            <div class="info-card-value">
                <span id="modalTempThreshold">--</span>
                <span style="font-size:13px; opacity:0.5;">°C</span>
            </div>
            <div class="info-card-subtext">Jika suhu &ge; nilai ini, sinyal IR dikirim</div>
        </div>

    </div>

    <!-- Activity Timeline -->
    <div class="detail-section">
        <div class="section-title">Recent Activity</div>
        <div class="detail-timeline">
            <div class="timeline-item">
                <div class="timeline-label">Last Mode Change</div>
                <div class="timeline-value">Cool Mode - 2 hours ago</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-label">Temperature Adjusted</div>
                <div class="timeline-value">22°C - 3 hours ago</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-label">Last Maintenance</div>
                <div class="timeline-value">15 Dec 2024</div>
            </div>
        </div>
    </div>
</div>

            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeModal()">
                    Close
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
                    Batal
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
        let allAcData     = [];
        let filteredCards = [];
        let currentPage   = 1;
        let activeAcId    = null;
        const CARDS_PER_PAGE = 8;
        const REFRESH_INTERVAL = 30000; // 30 detik

        const ACTION_LABELS = {
            temperature_change : 'Temperature Change',
            power_on           : 'Power On',
            power_off          : 'Power Off',
            boost_on           : 'Boost On',
            boost_off          : 'Boost Off',
            schedule_on        : 'Schedule On',
            schedule_off       : 'Schedule Off',
            schedule_update    : 'Schedule Update',
        };

        const MODE_CONFIG = {
            COOL: { icon: 'fa-snowflake', cls: 'cool', label: 'COOL' },
            HEAT: { icon: 'fa-fire',      cls: 'heat', label: 'HEAT' },
            DRY:  { icon: 'fa-tint',      cls: 'dry',  label: 'DRY'  },
            AUTO: { icon: 'fa-sync-alt',  cls: 'auto', label: 'AUTO' },
            FAN:  { icon: 'fa-fan',       cls: 'fan',  label: 'FAN'  },
        };

        const FAN_LEVEL = {
            AUTO: { pct: 45, label: 'AUTO' },
            LOW:  { pct: 25, label: 'LOW'  },
            MED:  { pct: 60, label: 'MED'  },
            HIGH: { pct: 90, label: 'HIGH' },
        };

        // =============================================
        // LOAD AC CARDS (sekali saat halaman dibuka)
        // =============================================
        async function loadDashboardData() {
            try {
                const response = await fetch('../backend/dashboard/get_dashboard_data.php');
                const result   = await response.json();

                if (result.status === 'success') {
                    allAcData = result.data;
                    renderAllCards();
                    updateStats();
                    filteredCards = getAllCards();
                    renderPage();
                } else {
                    showErrorMessage('Gagal memuat data dashboard.');
                }
            } catch (error) {
                showErrorMessage('Gagal terhubung ke server.');
            }
        }

        // =============================================
        // RENDER CARDS
        // =============================================
        function renderAllCards() {
            const grid = document.getElementById('cardsGrid');
            grid.innerHTML = allAcData.map(ac => {
                const statusClass = ac.ac_status ? 'active' : 'inactive';
                const statusLabel = ac.ac_status ? 'Aktif' : 'Tidak Aktif';
                const modeVal     = ac.ac_status ? `<div class="m-val" style="font-size:13px;color:#4fc3f7;">${ac.ac_mode}</div>` :
                                                   `<div class="m-val" style="font-size:13px;color:#64748b;">—</div>`;
                const fanVal      = ac.ac_status ? `<div class="m-val" style="font-size:13px;color:#80cbc4;">AUTO</div>` :
                                                   `<div class="m-val" style="font-size:13px;color:#64748b;">—</div>`;
                return `
                    <div class="card" data-id="${ac.id_ac_units}" data-name="${ac.ac_name}" data-loc="${ac.ac_location}">
                        <div class="card-top">
                            <div class="card-icon"><i class='bx bx-air-conditioner'></i></div>
                            <div class="card-status-badge ${statusClass}">
                                <span class="card-status-dot"></span>${statusLabel}
                            </div>
                        </div>
                        <h3>${ac.ac_name}</h3>
                        <div class="card-location-text">
                            <i class="fas fa-location-dot"></i>&nbsp;${ac.ac_location}
                        </div>
                        <div class="card-metrics">
                            <div class="card-metric">
                                <div class="m-val" id="card-temp-${ac.id_ac_units}">${ac.sensor ? ac.sensor.room_temp.toFixed(1) : '--'}°C</div>
                                <div class="m-lbl">Suhu Ruangan</div>
                            </div>
                            <div class="card-metric-divider"></div>
                            <div class="card-metric">${modeVal}<div class="m-lbl">Mode</div></div>
                            <div class="card-metric-divider"></div>
                            <div class="card-metric">${fanVal}<div class="m-lbl">Fan</div></div>
                        </div>
                        <a href="#" class="card-link" onclick="openModal(event, ${ac.id_ac_units})">View Details →</a>
                    </div>`;
            }).join('');
        }

        // =============================================
        // AUTO REFRESH SENSOR (setiap 30 detik)
        // =============================================
        async function refreshSensorData() {
            try {
                const response = await fetch('../backend/dashboard/get_sensor_data.php');
                const result   = await response.json();

                if (result.status === 'success') {
                    // Update data di allAcData
                    result.data.forEach(updated => {
                        const ac = allAcData.find(a => a.id_ac_units === updated.id_ac_units);
                        if (ac) {
                            ac.sensor      = updated.sensor;
                            ac.ac_status   = updated.ac_status;
                            ac.target_temp = updated.target_temp;
                            ac.boost_active= updated.boost_active;

                            // Update card status badge
                            const card = document.querySelector(`.card[data-id="${ac.id_ac_units}"]`);
                            if (card) {
                                const badge = card.querySelector('.card-status-badge');
                                badge.className = `card-status-badge ${ac.ac_status ? 'active' : 'inactive'}`;
                                badge.innerHTML = `<span class="card-status-dot"></span>${ac.ac_status ? 'Aktif' : 'Tidak Aktif'}`;
                                const tempEl = document.getElementById(`card-temp-${ac.id_ac_units}`);
                                if (tempEl) tempEl.textContent = (ac.sensor ? ac.sensor.room_temp.toFixed(1) : '--') + '°C';
                            }
                        }
                    });

                    // Update stats
                    updateStats();

                    // Update modal jika sedang terbuka
                    if (activeAcId) {
                        const ac = allAcData.find(a => a.id_ac_units === activeAcId);
                        if (ac) updateModalSensorCards(ac);
                    }
                }
            } catch (error) {
                // Diam saja saat auto refresh gagal, tidak tampilkan toast
            }
        }

        // =============================================
        // STATS
        // =============================================
        function updateStats() {
            const total    = allAcData.length;
            const active   = allAcData.filter(a => a.ac_status).length;
            const inactive = total - active;
            document.getElementById('statTotal')   .textContent = total;
            document.getElementById('statActive')  .textContent = active;
            document.getElementById('statInactive').textContent = inactive;
        }

        // =============================================
        // MODAL
        // =============================================
        function updateBoostBadge(ac) {
            const el = document.getElementById('boostStatus');
            const isActive = !!ac.boost_active;
            el.textContent = isActive ? 'Aktif' : 'Nonaktif';
            el.className = 'temp-boost-val ' + (isActive ? 'active' : 'inactive');
        }

        async function openModal(event, acId) {
            event.preventDefault();
            activeAcId = acId;
            const ac   = allAcData.find(a => a.id_ac_units === acId);
            if (!ac) return;

            const modal        = document.getElementById('acModal');
            const modalContent = modal.querySelector('.modal-content');
            modalContent.classList.add('detail-view');

            document.getElementById('modalTitle')   .textContent = ac.ac_name;
            document.getElementById('modalLocation').textContent = ac.ac_location;
            document.getElementById('tempDisplay')  .textContent = ac.target_temp + '°C';
            document.getElementById('modalTempThreshold').textContent = ac.temp_threshold ?? '--';
            updateBoostBadge(ac);

            // Status
            const statusText = document.getElementById('modalStatus');
            const statusDot  = document.getElementById('statusDot');
            statusText.textContent = ac.ac_status ? 'Active' : 'Inactive';
            statusDot.classList.toggle('inactive', !ac.ac_status);
            document.getElementById('statusTime').textContent = ac.update_at ? 'Update: ' + ac.update_at : '';

            // Mode & Fan
            const modeKey = ac.ac_mode || 'COOL';
            const modeCfg = MODE_CONFIG[modeKey] || MODE_CONFIG.COOL;
            const badge   = document.getElementById('modeBadge');
            badge.className = 'mode-badge ' + modeCfg.cls;
            document.getElementById('acMode')   .textContent = modeCfg.label;
            document.getElementById('modeIcon') .className   = 'fas ' + modeCfg.icon;

            // Fan selalu AUTO
            const fanCfg = FAN_LEVEL.AUTO;
            document.getElementById('acFanSpeed')  .textContent  = fanCfg.label;
            document.getElementById('fanBarFill')  .style.width   = fanCfg.pct + '%';
            document.getElementById('fanBarLabel') .textContent   = fanCfg.label;

            // Sensor data
            updateModalSensorCards(ac);

            // Recent activity
            loadRecentActivity(acId);

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function updateModalSensorCards(ac) {
            updateBoostBadge(ac);
            if (ac.sensor) {
                document.getElementById('sensorTemp')    .textContent = ac.sensor.room_temp.toFixed(1);
                document.getElementById('sensorPressure').textContent= ac.sensor.air_pressure.toFixed(1);

                const hum = ac.sensor.humidity;
                document.getElementById('sensorHumidity').textContent = hum.toFixed(1);
                const arc = document.getElementById('humidityArc');
                arc.style.strokeDashoffset = (106.8 - (hum / 100 * 106.8)).toString();
                document.getElementById('humidityStatus').textContent =
                    hum < 30 ? '🌵 Terlalu Kering' : hum > 70 ? '💧 Terlalu Lembap' : '✅ Ideal';

                // Freshness indicator
                const freshEl  = document.getElementById('tempFresh');
                const recorded = new Date(ac.sensor.recorded_at).getTime();
                const age      = Date.now() - recorded;
                freshEl.className = 'sensor-fresh' + (age > 60000 ? ' stale' : '');
            } else {
                ['sensorTemp','sensorPressure','sensorHumidity'].forEach(id => {
                    document.getElementById(id).textContent = '--';
                });
                document.getElementById('humidityStatus').textContent = 'Tidak ada data';
            }
        }

        // =============================================
        // RECENT ACTIVITY
        // =============================================
        async function loadRecentActivity(acId) {
            const timeline = document.querySelector('.detail-timeline');
            timeline.innerHTML = '<div class="timeline-item"><div class="timeline-label">Memuat...</div></div>';

            try {
                const response = await fetch(`../backend/dashboard/get_recent_activity.php?ac_unit_id=${acId}`);
                const result   = await response.json();

                if (result.status === 'success' && result.data.length > 0) {
                    timeline.innerHTML = result.data.map(log => {
                        const label = ACTION_LABELS[log.action] || log.action;
                        const desc  = log.old_value && log.new_value
                            ? `${log.old_value} → ${log.new_value}`
                            : log.action;
                        return `
                            <div class="timeline-item">
                                <div class="timeline-label">${label}</div>
                                <div class="timeline-value">${desc} &mdash; ${log.performed_by} &mdash; ${log.created_at}</div>
                            </div>`;
                    }).join('');
                } else {
                    timeline.innerHTML = '<div class="timeline-item"><div class="timeline-label">Belum ada aktivitas.</div></div>';
                }
            } catch (error) {
                timeline.innerHTML = '<div class="timeline-item"><div class="timeline-label">Gagal memuat aktivitas.</div></div>';
            }
        }

        function closeModal() {
            document.getElementById('acModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            activeAcId = null;
        }

        function closeModalOnOverlay(event) {
            if (event.target.id === 'acModal') closeModal();
        }

        // =============================================
        // SEARCH & PAGINATION
        // =============================================
        function getAllCards() {
            return Array.from(document.querySelectorAll('#cardsGrid .card'));
        }

        function renderPage() {
            const allCards = getAllCards();
            const start    = (currentPage - 1) * CARDS_PER_PAGE;
            const end      = start + CARDS_PER_PAGE;
            allCards.forEach(card => {
                const pos = filteredCards.indexOf(card);
                card.style.display = (filteredCards.includes(card) && pos >= start && pos < end) ? '' : 'none';
            });
            renderPaginationUI();
        }

        function renderPaginationUI() {
            const total      = filteredCards.length;
            const totalPages = Math.max(1, Math.ceil(total / CARDS_PER_PAGE));
            const start      = Math.min((currentPage - 1) * CARDS_PER_PAGE + 1, total);
            const end        = Math.min(currentPage * CARDS_PER_PAGE, total);

            document.getElementById('paginationInfo').textContent =
                total === 0 ? 'Tidak ada hasil' : `Menampilkan ${start}–${end} dari ${total} unit`;

            document.getElementById('paginationWrapper').style.display = totalPages <= 1 ? 'none' : 'flex';

            const controls    = document.getElementById('paginationControls');
            controls.innerHTML = '';

            const prevBtn       = document.createElement('button');
            prevBtn.className   = 'page-btn';
            prevBtn.innerHTML   = '<i class="fas fa-chevron-left"></i>';
            prevBtn.disabled    = currentPage === 1;
            prevBtn.onclick     = () => { currentPage--; renderPage(); };
            controls.appendChild(prevBtn);

            for (let p = 1; p <= totalPages; p++) {
                const btn       = document.createElement('button');
                btn.className   = 'page-btn' + (p === currentPage ? ' active' : '');
                btn.textContent = p;
                btn.onclick     = () => { currentPage = p; renderPage(); };
                controls.appendChild(btn);
            }

            const nextBtn       = document.createElement('button');
            nextBtn.className   = 'page-btn';
            nextBtn.innerHTML   = '<i class="fas fa-chevron-right"></i>';
            nextBtn.disabled    = currentPage === totalPages;
            nextBtn.onclick     = () => { currentPage++; renderPage(); };
            controls.appendChild(nextBtn);
        }

        function searchDashboard() {
            const filter   = document.getElementById('dashboardSearch').value.toLowerCase();
            const allCards = getAllCards();
            filteredCards  = allCards.filter(card => {
                const name = (card.dataset.name || '').toLowerCase();
                const loc  = (card.dataset.loc  || '').toLowerCase();
                return name.includes(filter) || loc.includes(filter);
            });
            currentPage = 1;
            renderPage();
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
            if (event.key === 'Escape') closeModal();
        });

        // =============================================
        // BOOT
        // =============================================
        window.addEventListener('DOMContentLoaded', async () => {
            await loadDashboardData();
            setInterval(refreshSensorData, REFRESH_INTERVAL);
        });
    </script>
</body>
</html>