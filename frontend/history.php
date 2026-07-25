<?php require_once '../config/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AcController - History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/basic/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/history.css">
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
            <!-- Header -->
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="logo-text">
                        <h1>IT <span style="color: #3b82f6;">AC</span> CONTROLLER</h1>
                        <p>History</p>
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
                        <a class="menu-link active">
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

        <!-- History Page -->
        <div class="main-content" id="historyPage">

            <div class="content-header-acunit">
                <h2>Activity History</h2>
                <p>Track all AC changes and control activity</p>
            </div>

            <!-- Filter Bar -->
            <div class="history-filter-bar">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input history-search" id="historySearch"
                        placeholder="Search description..." oninput="filterHistory()">
                </div>
                <select class="form-select history-select" id="filterUnit" onchange="filterHistory()">
                    <option value="">All AC Units</option>
                </select>
                <select class="form-select history-select" id="filterTrigger" onchange="filterHistory()">
                    <option value="">All Actions</option>
                    <option value="temperature_change">Temperature Change</option>
                    <option value="power_on">Power On</option>
                    <option value="power_off">Power Off</option>
                    <option value="boost_on">Boost On</option>
                    <option value="boost_off">Boost Off</option>
                    <option value="schedule_on">Schedule On</option>
                    <option value="schedule_off">Schedule Off</option>
                    <option value="schedule_update">Schedule Update</option>
                </select>
                <div class="date-range-wrapper">
                    <input type="date" class="form-input history-date" id="dateFrom" onchange="filterHistory()">
                    <span class="date-separator">—</span>
                    <input type="date" class="form-input history-date" id="dateTo" onchange="filterHistory()">
                </div>
                <button class="btn-clear-filter" id="clearFilterBtn" onclick="clearFilters()">
                    <i class="fas fa-times"></i> Clear
                </button>
            </div>

            <!-- Summary Stats -->
            <div class="history-stats">
                <div class="stat-chip">
                    <span class="stat-chip-val" id="statTotal">0</span>
                    <span class="stat-chip-label">Total</span>
                </div>
                <div class="stat-chip manual">
                    <span class="stat-chip-val" id="statManual">0</span>
                    <span class="stat-chip-label">Manual</span>
                </div>
                <div class="stat-chip auto">
                    <span class="stat-chip-val" id="statAuto">0</span>
                    <span class="stat-chip-label">System</span>
                </div>
                <div class="stat-chip reset">
                    <span class="stat-chip-val" id="statSchedule">0</span>
                    <span class="stat-chip-label">Schedule</span>
                </div>
            </div>

            <!-- Table -->
            <div class="table-container">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width:160px" onclick="sortHistory('timestamp')" class="sortable-col">
                                    Timestamp <i class="fas fa-sort sort-icon" id="sort-timestamp"></i>
                                </th>
                                <th onclick="sortHistory('unit')" class="sortable-col">
                                    AC Unit <i class="fas fa-sort sort-icon" id="sort-unit"></i>
                                </th>
                                <th>Location</th>
                                <th onclick="sortHistory('trigger_by')" class="sortable-col">
                                    Trigger By <i class="fas fa-sort sort-icon" id="sort-trigger_by"></i>
                                </th>
                                <th>Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <!-- Rendered via JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty state -->
                <div class="history-empty" id="historyEmpty" style="display:none">
                    <i class="fas fa-clock-rotate-left"></i>
                    <p>No activity found</p>
                    <span>Try adjusting your filters</span>
                </div>

                <!-- Pagination -->
                <div class="history-pagination" id="historyPagination">
                    <span class="pagination-info" id="paginationInfo"></span>
                    <div class="pagination-btns">
                        <button class="pagination-btn" id="btnPrev" onclick="changePage(-1)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <div class="pagination-pages" id="paginationPages"></div>
                        <button class="pagination-btn" id="btnNext" onclick="changePage(1)">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Detail AC -->
    <div class="modal-overlay" id="acModal" onclick="closeModalOnOverlay(event)">
        <div class="modal-content">
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
                    <div class="detail-row">
                        <span class="detail-label">Power</span>
                        <div class="toggle-switch active" id="powerToggle" onclick="togglePower()">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                </div>

                <!-- Temperature Control -->
                <div class="detail-section">
                    <div class="section-title">Temperature Control</div>
                    <div class="temp-control">
                        <div class="temp-display" id="tempDisplay">22°C</div>
                        <div class="temp-buttons">
                            <button class="temp-btn" onclick="decreaseTemp()">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button class="temp-btn" onclick="increaseTemp()">
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
                        <div class="mode-btn" onclick="selectMode(this, 'fan')">
                            <i class="fas fa-wind"></i>
                            <span>Fan</span>
                        </div>
                        <div class="mode-btn" onclick="selectMode(this, 'dry')">
                            <i class="fas fa-tint"></i>
                            <span>Dry</span>
                        </div>
                    </div>
                </div>

                <!-- Command Summary Section -->
                <div class="detail-section">
                    <div class="section-title">Command Summary</div>
                    <div class="cmd-summary-wrapper">

                        <!-- Current Command -->
                        <div class="cmd-card current">
                            <div class="cmd-card-icon"><i class="fas fa-broadcast-tower"></i></div>
                            <div class="cmd-card-body">
                                <div class="cmd-card-label">Current Command</div>
                                <div class="cmd-tags" id="currentTags"></div>
                            </div>
                        </div>

                        <!-- Reset Command -->
                        <div class="cmd-card reset">
                            <div class="cmd-card-icon"><i class="fas fa-undo-alt"></i></div>
                            <div class="cmd-card-body">
                                <div class="cmd-card-label">Reset Command (Default)</div>
                                <div class="cmd-tags" id="resetTags"></div>
                            </div>
                        </div>

                        <!-- Collapsible Hex Details -->
                        <div class="hex-collapsible">
                            <button class="hex-toggle" id="hexToggleBtn" onclick="toggleHex()">
                                <span>Hex Details</span>
                                <div class="hex-toggle-right">
                                    <span class="hex-toggle-hint">tap to expand</span>
                                    <i class="fas fa-chevron-down arrow"></i>
                                </div>
                            </button>
                            <div class="hex-body" id="hexBody">
                                <div class="hex-tabs">
                                    <button class="hex-tab active-tab" onclick="switchHexTab('current')">Current</button>
                                    <button class="hex-tab" onclick="switchHexTab('reset')">Reset</button>
                                </div>
                                <div class="hex-tab-panel active" id="panel-current">
                                    <div class="hex-rows" id="currentHexRows"></div>
                                    <div class="hex-raw-row">
                                        <span class="hex-raw-label">RAW</span>
                                        <span class="hex-raw-val" id="currentRaw"></span>
                                        <button class="hex-copy-btn" onclick="copyRaw('current')"><i class="fas fa-copy"></i></button>
                                    </div>
                                </div>
                                <div class="hex-tab-panel" id="panel-reset">
                                    <div class="hex-rows" id="resetHexRows"></div>
                                    <div class="hex-raw-row">
                                        <span class="hex-raw-label">RAW</span>
                                        <span class="hex-raw-val" id="resetRaw"></span>
                                        <button class="hex-copy-btn" onclick="copyRaw('reset')"><i class="fas fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeModal()">
                    Close
                </button>
                <button class="btn btn-warning" id="applyResetBtn" onclick="applyReset()">
                    <i class="fas fa-undo-alt"></i> Apply Reset
                </button>
                <button class="btn btn-primary" id="editActionBtn" onclick="handleEditAction()">
                    Unlock Control
                </button>

            </div>
        </div>
    </div>

    <!-- Modal Add AC -->
    <div class="modal-overlay" id="formModal" onclick="closeFormModalOnOverlay(event)">
        <div class="modal-content read-only" id="detailModalContent">

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
                        <label class="form-label">AC ID</label>
                        <input type="text" class="form-input" id="acId" placeholder="e.g., AC 007" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">AC Name</label>
                        <input type="text" class="form-input" id="acName" placeholder="e.g., Conference Room AC" required>
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
                <button class="btn btn-primary" onclick="submitACForm()">
                    <i class="fas fa-save"></i> <span id="formSubmitText">Add AC Unit</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div class="success-message" id="successMessage"><i class="bx bx-check-circle"></i><span></span></div>
    <div class="error-message" id="errorMessage"><i class="bx bx-error-circle"></i><span></span></div>

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
        let historyData     = [];
        let filteredHistory = [];
        let currentPage     = 1;
        let sortKey         = 'timestamp';
        let sortAsc         = false;
        const PAGE_SIZE     = 10;

        const MANUAL_ACTIONS   = ['temperature_change', 'power_on', 'power_off', 'schedule_update'];
        const SYSTEM_ACTIONS   = ['boost_on', 'boost_off'];
        const SCHEDULE_ACTIONS = ['schedule_on', 'schedule_off'];

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

        // =============================================
        // LOAD DATA DARI BACKEND
        // =============================================
        async function loadActivityLogs() {
            try {
                const response = await fetch('../backend/history/get_activity_logs.php');
                const result   = await response.json();
                if (result.status === 'success') {
                    historyData = result.data;
                    populateUnitFilter();
                    initHistory();
                } else {
                    showErrorMessage('Gagal memuat data history.');
                }
            } catch (error) {
                showErrorMessage('Gagal terhubung ke server.');
            }
        }

        function populateUnitFilter() {
            const select = document.getElementById('filterUnit');
            const units  = [...new Set(historyData.map(r => r.ac_name))].sort();
            units.forEach(unit => {
                const opt       = document.createElement('option');
                opt.value       = unit;
                opt.textContent = unit;
                select.appendChild(opt);
            });
        }

        // =============================================
        // INIT
        // =============================================
        function initHistory() {
            filteredHistory = [...historyData];
            currentPage     = 1;
            sortHistoryData();
            updateStats();
            renderHistoryTable();
            renderPagination();
            updateClearBtn();
        }

        // =============================================
        // FILTER
        // =============================================
        function hasActiveFilter() {
            return !!(
                document.getElementById('historySearch').value ||
                document.getElementById('filterUnit').value    ||
                document.getElementById('filterTrigger').value ||
                document.getElementById('dateFrom').value      ||
                document.getElementById('dateTo').value
            );
        }

        function updateClearBtn() {
            document.getElementById('clearFilterBtn').classList.toggle('visible', hasActiveFilter());
        }

        function filterHistory() {
            const search    = document.getElementById('historySearch').value.toLowerCase();
            const unitVal   = document.getElementById('filterUnit').value;
            const actionVal = document.getElementById('filterTrigger').value;
            const dateFrom  = document.getElementById('dateFrom').value;
            const dateTo    = document.getElementById('dateTo').value;

            filteredHistory = historyData.filter(row => {
                const matchSearch  = !search    ||
                    row.ac_name.toLowerCase().includes(search)     ||
                    row.performed_by.toLowerCase().includes(search) ||
                    (ACTION_LABELS[row.action] || row.action).toLowerCase().includes(search) ||
                    (row.old_value || '').toLowerCase().includes(search) ||
                    (row.new_value || '').toLowerCase().includes(search);
                const matchUnit    = !unitVal   || row.ac_name === unitVal;
                const matchAction  = !actionVal || row.action === actionVal;
                const rowDate      = row.timestamp.split(' ')[0];
                const matchFrom    = !dateFrom  || rowDate >= dateFrom;
                const matchTo      = !dateTo    || rowDate <= dateTo;
                return matchSearch && matchUnit && matchAction && matchFrom && matchTo;
            });

            currentPage = 1;
            sortHistoryData();
            updateStats();
            renderHistoryTable();
            renderPagination();
            updateClearBtn();
        }

        // =============================================
        // SORT
        // =============================================
        function sortHistory(key) {
            if (sortKey === key) sortAsc = !sortAsc;
            else { sortKey = key; sortAsc = false; }
            document.querySelectorAll('.sort-icon').forEach(el => el.className = 'fas fa-sort sort-icon');
            const icon = document.getElementById('sort-' + key);
            if (icon) icon.className = `fas fa-sort-${sortAsc ? 'up' : 'down'} sort-icon`;
            sortHistoryData();
            renderHistoryTable();
            renderPagination();
        }

        function sortHistoryData() {
            filteredHistory.sort((a, b) => {
                const valA = a[sortKey] || '';
                const valB = b[sortKey] || '';
                return sortAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
            });
        }

        // =============================================
        // STATS
        // =============================================
        function updateStats() {
            const base = filteredHistory;
            document.getElementById('statTotal')   .textContent = base.length;
            document.getElementById('statManual')  .textContent = base.filter(r => MANUAL_ACTIONS.includes(r.action)).length;
            document.getElementById('statAuto')    .textContent = base.filter(r => SYSTEM_ACTIONS.includes(r.action)).length;
            document.getElementById('statSchedule').textContent = base.filter(r => SCHEDULE_ACTIONS.includes(r.action)).length;
        }

        // =============================================
        // RENDER TABLE
        // =============================================
        function getActionBadgeClass(action) {
            if (MANUAL_ACTIONS.includes(action))   return 'manual';
            if (SYSTEM_ACTIONS.includes(action))   return 'auto';
            if (SCHEDULE_ACTIONS.includes(action)) return 'reset';
            return '';
        }

        function formatDescription(row) {
            if (row.old_value && row.new_value) {
                return `${ACTION_LABELS[row.action] || row.action}: ${row.old_value} &rarr; ${row.new_value}`;
            }
            return ACTION_LABELS[row.action] || row.action;
        }

        function renderHistoryTable() {
            const tbody = document.getElementById('historyTableBody');
            const empty = document.getElementById('historyEmpty');
            const start = (currentPage - 1) * PAGE_SIZE;
            const rows  = filteredHistory.slice(start, start + PAGE_SIZE);

            if (rows.length === 0) {
                tbody.innerHTML     = '';
                empty.style.display = 'block';
                return;
            }

            empty.style.display = 'none';
            tbody.innerHTML = rows.map(row => {
                const [date, time] = row.timestamp.split(' ');
                const badgeClass   = getActionBadgeClass(row.action);
                const description  = formatDescription(row);
                return `
                    <tr>
                        <td>
                            <div class="timestamp-cell">
                                <span class="timestamp-date">${date}</span>
                                <span class="timestamp-time">${time}</span>
                            </div>
                        </td>
                        <td><span style="font-weight:600">${row.ac_name}</span></td>
                        <td style="color:#94a3b8">${row.ac_location}</td>
                        <td style="color:#94a3b8">${row.performed_by}</td>
                        <td><span class="trigger-badge ${badgeClass}">${ACTION_LABELS[row.action] || row.action}</span></td>
                        <td style="color:#cbd5e1;font-size:13px">${description}</td>
                    </tr>`;
            }).join('');
        }

        // =============================================
        // PAGINATION
        // =============================================
        function renderPagination() {
            const totalPages = Math.ceil(filteredHistory.length / PAGE_SIZE);
            const start      = (currentPage - 1) * PAGE_SIZE + 1;
            const end        = Math.min(currentPage * PAGE_SIZE, filteredHistory.length);

            document.getElementById('paginationInfo').textContent =
                filteredHistory.length === 0 ? '' : `Showing ${start}–${end} of ${filteredHistory.length}`;

            document.getElementById('btnPrev').disabled = currentPage === 1;
            document.getElementById('btnNext').disabled = currentPage === totalPages || totalPages === 0;

            const pagesEl     = document.getElementById('paginationPages');
            pagesEl.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                const btn       = document.createElement('button');
                btn.className   = 'page-num' + (i === currentPage ? ' active' : '');
                btn.textContent = i;
                btn.onclick     = () => { currentPage = i; renderHistoryTable(); renderPagination(); };
                pagesEl.appendChild(btn);
            }
        }

        function changePage(dir) {
            const totalPages = Math.ceil(filteredHistory.length / PAGE_SIZE);
            currentPage      = Math.max(1, Math.min(currentPage + dir, totalPages));
            renderHistoryTable();
            renderPagination();
        }

        // =============================================
        // CLEAR FILTERS
        // =============================================
        function clearFilters() {
            document.getElementById('historySearch').value  = '';
            document.getElementById('filterUnit').value     = '';
            document.getElementById('filterTrigger').value  = '';
            document.getElementById('dateFrom').value       = '';
            document.getElementById('dateTo').value         = '';
            filterHistory();
        }

        // =============================================
        // BOOT
        // =============================================
        window.addEventListener('DOMContentLoaded', loadActivityLogs);

        // Sidebar toggle
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }
        function toggleMobileSidebar() {
            document.getElementById('sidebar').classList.toggle('mobile-open');
            document.querySelector('.mobile-overlay').classList.toggle('active');
        }
    </script>
</body>
</html>