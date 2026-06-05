/**
 * Main JS - Client
 */
document.addEventListener('DOMContentLoaded', function () {
    // Tự động ẩn alerts sau 5 giây
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Validate biển số xe client-side
    const plateInput = document.querySelector('#plate_number');
    if (plateInput) {
        plateInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    }
});
