/**
 * Admin JS
 */
document.addEventListener('DOMContentLoaded', function () {
    // Sidebar toggle mobile
    const toggle = document.querySelector('#sidebarToggle');
    const sidebar = document.querySelector('#adminSidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });
    }

    // Confirm delete
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!confirm('Bạn có chắc chắn muốn xóa? Hành động này không thể hoàn tác.')) {
                e.preventDefault();
            }
        });
    });

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
