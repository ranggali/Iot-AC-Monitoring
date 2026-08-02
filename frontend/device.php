<?php require_once '../config/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AcController - Devices</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/basic/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/acunit.css">
    <link rel="stylesheet" href="../public/css/toast.css">
    <link rel="stylesheet" href="../public/css/logout.css">

    <link rel="icon" href="../public/assets/icon.ico" type="image/x-icon">
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
                        <p>Devices</p>
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
                        <a class="menu-link active">
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
            <!-- Toast -->
            <div class="success-message" id="successMessage">
                <i class="bx bx-check-circle"></i>
                <span></span>
            </div>
            <div class="error-message" id="errorMessage">
                <i class="bx bx-error-circle"></i>
                <span></span>
            </div>

            <div class="page-content">
                <div class="content-header-acunit">
                    <h2>Devices</h2>
                    <p>Manage IoT devices connected to the system</p>
                </div>

                <div class="page-actions">
                    <div class="search-filter">
                        <input type="text" class="search-input" placeholder="Search devices..." id="searchInput" onkeyup="searchTable()">
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-wrapper">
                        <table class="data-table" id="deviceTable">
                            <thead>
                                <tr>
                                    <th>Device Name</th>
                                    <th>IP Address</th>
                                    <th>MAC Address</th>
                                    <th>Status</th>
                                    <th>Last Update</th>
                                    <th>AC Units</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="deviceTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Device Detail -->
    <div class="modal-overlay" id="deviceModal" onclick="closeModalOnOverlay(event)">
        <div class="modal-content" id="deviceModalContent">
            <div class="modal-header">
                <h2>
                    <div class="modal-header-icon">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <span id="modalDeviceTitle">Device Details</span>
                    <div id="deviceModeIndicator" class="mode-indicator">
                        🔒 View Mode
                    </div>
                </h2>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">

                <!-- Info Device -->
                <div class="detail-section">
                    <div class="section-title">
                        <i class="fas fa-info-circle" style="margin-right:6px;"></i> Info Device
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Nama Device</span>
                        <input type="text" class="form-input" id="modalDeviceName" placeholder="Nama Device" disabled>
                    </div>
                    <div class="detail-row" style="margin-top:10px;">
                        <span class="detail-label">IP Address</span>
                        <input type="text" class="form-input" id="modalDeviceIp" placeholder="IP Address" disabled>
                    </div>
                    <div class="detail-row" style="margin-top:10px;">
                        <span class="detail-label">MAC Address</span>
                        <input type="text" class="form-input" id="modalDeviceMac" placeholder="MAC Address" disabled>
                    </div>
                    <div class="detail-row" style="margin-top:10px;">
                        <span class="detail-label">Status</span>
                        <span class="status-badge" id="modalDeviceStatus">-</span>
                    </div>
                    <div class="detail-row" style="margin-top:10px;">
                        <span class="detail-label">Last Update</span>
                        <span id="modalDeviceLastSeen" style="color:#94a3b8;font-size:13px;">-</span>
                    </div>
                </div>

                <!-- AC Units terhubung -->
                <div class="detail-section">
                    <div class="section-title">
                        <i class='bx bx-air-conditioner' style="margin-right:6px;"></i> AC Units Terhubung
                    </div>
                    <div id="modalAcList"></div>

                    <!-- Form tambah AC unit baru -->
                    <div id="addAcForm" style="display:none; margin-top:16px; padding:16px; background:rgba(255,255,255,0.03); border-radius:10px; border:1px solid #1f2430;">
                        <div style="font-size:13px; color:#94a3b8; margin-bottom:12px; font-weight:600;">Hubungkan AC Unit</div>

                        <div id="acFormFields">
                            <div class="detail-row" style="margin-bottom:10px;">
                                <span class="detail-label">Nama AC</span>
                                <select class="form-input" id="newAcSelect" onchange="onAcSelectChange()">
                                    <option value="">-- Pilih AC --</option>
                                </select>
                            </div>
                            <div class="detail-row" style="margin-bottom:10px;">
                                <span class="detail-label">Lokasi</span>
                                <input type="text" class="form-input" id="newAcLocation" placeholder="Lokasi" disabled>
                            </div>
                            <div class="detail-row" style="margin-bottom:10px;">
                                <span class="detail-label">Target Suhu (°C)</span>
                                <input type="number" class="form-input" id="newAcTargetTemp" disabled>
                            </div>
                            <div class="detail-row" style="margin-bottom:16px;">
                                <span class="detail-label">Batas Suhu (°C)</span>
                                <input type="number" class="form-input" id="newAcThreshold" disabled>
                            </div>
                        </div>

                        <div id="noAcMessage" style="display:none; color:#64748b; font-size:13px; padding:12px 0; text-align:center;">
                            <i class="fas fa-info-circle" style="margin-right:4px;"></i>
                            Tidak ada unit AC yang kosong, semua AC sudah terhubung ke device.
                        </div>

                        <div style="display:flex; gap:8px;">
                            <button class="btn btn-secondary" onclick="hideAddAcForm()">
                                <i class="fas fa-times"></i> Batal
                            </button>
                            <button class="btn btn-primary" id="submitAttachBtn" onclick="submitAttachAc()" disabled>
                                <i class="fas fa-link"></i> Hubungkan
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button class="btn btn-secondary" id="editDeviceBtn" onclick="enableDeviceEdit()">
                    <i class="fas fa-edit"></i> Edit Nama
                </button>
                <button class="btn btn-primary" id="saveDeviceBtn" onclick="saveDeviceName()" style="display:none;">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <button class="btn btn-primary" id="addAcBtn" onclick="showAddAcForm()">
                    <i class="fas fa-plus"></i> Tambah AC
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
        let allDevices  = [];
        let activeDevice = null;

        // =============================================
        // LOAD DEVICES
        // =============================================
        async function loadDevices() {
            try {
                const response = await fetch('../backend/device/get_device.php');
                const result   = await response.json();
                if (result.status === 'success') {
                    allDevices = result.data;
                    renderTable(allDevices);
                } else {
                    showErrorMessage('Gagal memuat data devices.');
                }
            } catch (error) {
                showErrorMessage('Gagal terhubung ke server.');
            }
        }

        // =============================================
        // RENDER TABLE
        // =============================================
        function renderTable(data) {
            const tbody = document.getElementById('deviceTableBody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#64748b;padding:24px;">Belum ada device terdaftar.</td></tr>';
                return;
            }

            tbody.innerHTML = data.map(device => {
                const statusClass = device.connection_status ? 'active'   : 'inactive';
                const statusLabel = device.connection_status ? 'Online'   : 'Offline';
                const lastSeen    = device.last_seen ? formatTimeAgo(device.last_seen) : 'Belum pernah';
                const acCount     = device.ac_units.length;

                return `
                    <tr>
                        <td><span style="font-weight:600">${device.device_name}</span></td>
                        <td style="color:#4f8ef7;font-family:monospace">${device.ip_address}</td>
                        <td style="color:#94a3b8;font-size:12px;font-family:monospace">${device.mac_address}</td>
                        <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                        <td style="color:#94a3b8;font-size:13px">${lastSeen}</td>
                        <td>
                            <span style="background:rgba(79,142,247,0.1);color:#4f8ef7;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                                ${acCount} AC
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <button class="btn-icon btn-edit" onclick="openModal(${device.id_devices})" title="Detail">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon btn-delete" onclick="confirmDelete(${device.id_devices}, '${device.device_name}')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
            }).join('');
        }

        // =============================================
        // SEARCH
        // =============================================
        function searchTable() {
            const filter   = document.getElementById('searchInput').value.toLowerCase();
            const filtered = allDevices.filter(d =>
                d.device_name.toLowerCase().includes(filter) ||
                d.ip_address.toLowerCase().includes(filter)  ||
                (d.mac_address || '').toLowerCase().includes(filter)
            );
            renderTable(filtered);
        }

        // =============================================
        // FORMAT TIME AGO
        // =============================================
        function formatTimeAgo(dateStr) {
            const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
            if (diff < 60)    return diff + ' detik lalu';
            if (diff < 3600)  return Math.floor(diff / 60) + ' menit lalu';
            if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
            return Math.floor(diff / 86400) + ' hari lalu';
        }

        // =============================================
        // MODAL
        // =============================================
        function openModal(deviceId) {
            activeDevice = allDevices.find(d => d.id_devices === deviceId);
            if (!activeDevice) return;

            document.getElementById('modalDeviceTitle') .textContent = activeDevice.device_name;
            document.getElementById('modalDeviceName')  .value       = activeDevice.device_name;
            document.getElementById('modalDeviceIp')    .value       = activeDevice.ip_address;
            document.getElementById('modalDeviceMac')   .value       = activeDevice.mac_address;
            document.getElementById('modalDeviceLastSeen').textContent = activeDevice.last_seen
                ? formatTimeAgo(activeDevice.last_seen) : 'Belum pernah';

            const statusEl = document.getElementById('modalDeviceStatus');
            statusEl.textContent = activeDevice.connection_status ? 'Online' : 'Offline';
            statusEl.className   = 'status-badge ' + (activeDevice.connection_status ? 'active' : 'inactive');

            renderAcList(activeDevice.ac_units);
            hideAddAcForm();
            setViewMode();

            document.getElementById('deviceModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('deviceModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            activeDevice = null;
        }

        function closeModalOnOverlay(event) {
            if (event.target.id === 'deviceModal') closeModal();
        }

        // =============================================
        // RENDER AC LIST DI MODAL
        // =============================================
        function renderAcList(acUnits) {
            const container = document.getElementById('modalAcList');
            if (acUnits.length === 0) {
                container.innerHTML = '<div style="color:#64748b;font-size:13px;padding:8px 0;">Belum ada AC unit yang terhubung.</div>';
                return;
            }
            container.innerHTML = acUnits.map(ac => `
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:rgba(255,255,255,0.03);border-radius:8px;margin-bottom:6px;border:1px solid #1f2430;">
                    <div>
                        <div style="font-weight:600;font-size:14px;">${ac.ac_name}</div>
                        <div style="color:#64748b;font-size:12px;margin-top:2px;">
                            <i class="fas fa-location-dot" style="margin-right:4px;"></i>${ac.ac_location}
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span class="status-badge ${ac.ac_status ? 'active' : 'inactive'}" style="font-size:11px;">
                            ${ac.ac_status ? 'Aktif' : 'Nonaktif'}
                        </span>
                        <button class="btn-icon btn-delete" onclick="detachAc(${ac.id_ac_units}, '${ac.ac_name}', ${ac.ac_status ? 'true' : 'false'})" title="Lepas dari device" style="width:28px;height:28px;font-size:11px;">
                            <i class="fas fa-unlink"></i>
                        </button>
                    </div>
                </div>`).join('');
        }

        // =============================================
        // EDIT NAMA DEVICE
        // =============================================
        function setViewMode() {
            document.getElementById('modalDeviceName').disabled = true;
            document.getElementById('editDeviceBtn')  .style.display = 'inline-flex';
            document.getElementById('saveDeviceBtn')  .style.display = 'none';
            document.getElementById('deviceModeIndicator').textContent = '🔒 View Mode';
            document.getElementById('deviceModeIndicator').classList.remove('edit');
        }

        function enableDeviceEdit() {
            document.getElementById('modalDeviceName').disabled = false;
            document.getElementById('modalDeviceName').focus();
            document.getElementById('editDeviceBtn')  .style.display = 'none';
            document.getElementById('saveDeviceBtn')  .style.display = 'inline-flex';
            document.getElementById('deviceModeIndicator').textContent = '✏️ Edit Mode';
            document.getElementById('deviceModeIndicator').classList.add('edit');
        }

        async function saveDeviceName() {
            const newName = document.getElementById('modalDeviceName').value.trim();
            if (!newName) { showErrorMessage('Nama device tidak boleh kosong.'); return; }

            const btn     = document.getElementById('saveDeviceBtn');
            btn.disabled  = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

            try {
                const formData = new FormData();
                formData.append('id_devices',  activeDevice.id_devices);
                formData.append('device_name', newName);

                const response = await fetch('../backend/device/update_device.php', { method: 'POST', body: formData });
                const result   = await response.json();

                if (result.status === 'success') {
                    activeDevice.device_name = newName;
                    const row = allDevices.find(d => d.id_devices === activeDevice.id_devices);
                    if (row) row.device_name = newName;
                    document.getElementById('modalDeviceTitle').textContent = newName;
                    renderTable(allDevices);
                    showSuccessMessage(result.message);
                    setViewMode();
                } else {
                    showErrorMessage(result.message);
                }
            } catch (error) {
                showErrorMessage('Gagal terhubung ke server.');
            } finally {
                btn.disabled  = false;
                btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
            }
        }

        // =============================================
        // TAMBAH / HUBUNGKAN AC
        // =============================================
        let unattachedAcList = [];

        async function showAddAcForm() {
            document.getElementById('addAcForm').style.display = 'block';
            document.getElementById('addAcBtn') .style.display = 'none';

            // Reset form
            document.getElementById('newAcSelect')    .value = '';
            document.getElementById('newAcLocation')  .value = '';
            document.getElementById('newAcTargetTemp').value = '';
            document.getElementById('newAcThreshold') .value = '';
            document.getElementById('submitAttachBtn').disabled = true;

            // Fetch AC yang belum terhubung
            try {
                const response = await fetch('../backend/device/get_unattached_acunit.php');
                const result   = await response.json();

                const select  = document.getElementById('newAcSelect');
                const noAcMsg = document.getElementById('noAcMessage');
                select.innerHTML = '<option value="">-- Pilih AC --</option>';

                const formFields = document.getElementById('acFormFields');

                if (result.status === 'success' && result.data.length > 0) {
                    unattachedAcList = result.data;
                    formFields.style.display = 'block';
                    noAcMsg.style.display = 'none';
                    result.data.forEach(ac => {
                        const opt       = document.createElement('option');
                        opt.value       = ac.id_ac_units;
                        opt.textContent = ac.ac_name;
                        select.appendChild(opt);
                    });
                } else {
                    unattachedAcList = [];
                    formFields.style.display = 'none';
                    noAcMsg.style.display = 'block';
                }
            } catch (error) {
                showErrorMessage('Gagal memuat daftar AC.');
            }
        }

        function hideAddAcForm() {
            document.getElementById('addAcForm').style.display = 'none';
            document.getElementById('addAcBtn') .style.display = 'inline-flex';
            document.getElementById('newAcSelect')    .value = '';
            document.getElementById('newAcLocation')  .value = '';
            document.getElementById('newAcTargetTemp').value = '';
            document.getElementById('newAcThreshold') .value = '';
            document.getElementById('submitAttachBtn').disabled = true;
        }

        function onAcSelectChange() {
            const selectedId = parseInt(document.getElementById('newAcSelect').value);
            const ac         = unattachedAcList.find(a => a.id_ac_units === selectedId);

            if (ac) {
                document.getElementById('newAcLocation')  .value    = ac.ac_location;
                document.getElementById('newAcTargetTemp').value    = ac.target_temp;
                document.getElementById('newAcThreshold') .value    = ac.temp_threshold;
                document.getElementById('submitAttachBtn').disabled = false;
            } else {
                document.getElementById('newAcLocation')  .value    = '';
                document.getElementById('newAcTargetTemp').value    = '';
                document.getElementById('newAcThreshold') .value    = '';
                document.getElementById('submitAttachBtn').disabled = true;
            }
        }

        async function submitAttachAc() {
            const selectedId = parseInt(document.getElementById('newAcSelect').value);
            if (!selectedId) { showErrorMessage('Pilih AC terlebih dahulu.'); return; }

            const btn     = document.getElementById('submitAttachBtn');
            btn.disabled  = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghubungkan...';

            try {
                const formData = new FormData();
                formData.append('ac_unit_id', selectedId);
                formData.append('device_id',  activeDevice.id_devices);

                const response = await fetch('../backend/device/attach_acunit.php', { method: 'POST', body: formData });
                const result   = await response.json();

                if (result.status === 'success') {
                    activeDevice.ac_units.push(result.data);
                    renderAcList(activeDevice.ac_units);
                    renderTable(allDevices);
                    hideAddAcForm();
                    showSuccessMessage(result.message);
                } else {
                    showErrorMessage(result.message);
                }
            } catch (error) {
                showErrorMessage('Gagal terhubung ke server.');
            } finally {
                btn.disabled  = false;
                btn.innerHTML = '<i class="fas fa-link"></i> Hubungkan';
            }
        }

        // =============================================
        // LEPAS AC DARI DEVICE
        // =============================================
        async function detachAc(acUnitId, acName, isActive) {
            if (isActive) {
                showErrorMessage(`${acName} masih aktif! Matikan AC terlebih dahulu.`);
                return;
            }

            if (!confirm(`Yakin ingin melepas "${acName}" dari device ini?\nAC tidak akan terhapus dan bisa dihubungkan ke device lain.`)) return;

            try {
                const formData = new FormData();
                formData.append('ac_unit_id', acUnitId);

                const response = await fetch('../backend/device/detach_acunit.php', { method: 'POST', body: formData });
                const result   = await response.json();

                if (result.status === 'success') {
                    activeDevice.ac_units = activeDevice.ac_units.filter(a => a.id_ac_units !== acUnitId);
                    const row = allDevices.find(d => d.id_devices === activeDevice.id_devices);
                    if (row) row.ac_units = row.ac_units.filter(a => a.id_ac_units !== acUnitId);
                    renderAcList(activeDevice.ac_units);
                    renderTable(allDevices);
                    showSuccessMessage(result.message);
                } else {
                    showErrorMessage(result.message);
                }
            } catch (error) {
                showErrorMessage('Gagal terhubung ke server.');
            }
        }

        // =============================================
        // DELETE DEVICE
        // =============================================
        async function confirmDelete(deviceId, deviceName) {
            const device = allDevices.find(d => d.id_devices === deviceId);
            if (!device) return;

            // Cek apakah ada AC yang masih aktif
            const activeAcs = device.ac_units.filter(ac => ac.ac_status);

            if (activeAcs.length > 0) {
                const acNames = activeAcs.map(ac => `• ${ac.ac_name} (${ac.ac_location})`).join('\n');
                showErrorMessage(`Tidak bisa menghapus! AC berikut masih aktif:\n${acNames}`);
                // Tampilkan alert lebih detail
                return;
            }

            if (!confirm(`Yakin ingin menghapus "${deviceName}"?\nSemua AC unit, jadwal, dan data sensor terkait akan ikut terhapus.`)) return;

            try {
                const formData = new FormData();
                formData.append('id_devices', deviceId);

                const response = await fetch('../backend/device/delete_device.php', { method: 'POST', body: formData });
                const result   = await response.json();

                if (result.status === 'success') {
                    allDevices = allDevices.filter(d => d.id_devices !== deviceId);
                    renderTable(allDevices);
                    showSuccessMessage(result.message);
                } else {
                    showErrorMessage(result.message);
                }
            } catch (error) {
                showErrorMessage('Gagal terhubung ke server.');
            }
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

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        // =============================================
        // BOOT
        // =============================================
        window.addEventListener('DOMContentLoaded', loadDevices);
    </script>
</body>
</html>
