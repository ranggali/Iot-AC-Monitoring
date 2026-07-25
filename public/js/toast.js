// =============================================
// FILE: js/toast.js
// Fungsi toast notification (success & error)
// =============================================

/**
 * Tampilkan toast notifikasi sukses
 * @param {string} message - Pesan yang ditampilkan
 * @param {number} duration - Durasi tampil dalam ms (default: 3000)
 */
function showSuccessMessage(message, duration = 3000) {
    const el = document.getElementById('successMessage');
    if (!el) return;
    el.querySelector('span').textContent = message;
    el.classList.add('show');
    setTimeout(() => el.classList.remove('show'), duration);
}

/**
 * Tampilkan toast notifikasi error
 * @param {string} message - Pesan yang ditampilkan
 * @param {number} duration - Durasi tampil dalam ms (default: 3000)
 */
function showErrorMessage(message, duration = 3000) {
    const el = document.getElementById('errorMessage');
    if (!el) return;
    el.querySelector('span').textContent = message;
    el.classList.add('show');
    setTimeout(() => el.classList.remove('show'), duration);
}