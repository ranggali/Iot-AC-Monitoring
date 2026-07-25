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
    window.location.href = '../../backendserver/logout_data.php';
}

document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('logoutModal');
    if (overlay) {
        overlay.addEventListener('click', function(event) {
            if (event.target === this) {
                hideLogoutModal();
            }
        });
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideLogoutModal();
    }
});