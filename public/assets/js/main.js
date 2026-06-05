/**
 * Main JS - Client
 */
document.addEventListener('DOMContentLoaded', function () {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Validate license plate client-side
    const plateInput = document.querySelector('#plate_number');
    if (plateInput) {
        plateInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    }
});
