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

    // ============================================================
    // AJAX Toggle Status (users + violations)
    // ============================================================
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    document.querySelectorAll('.toggle-status-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const id = this.dataset.id;
            const type = this.dataset.type;
            const url = type === 'user'
                ? `/admin/users/${id}/toggle-status`
                : `/admin/violations/${id}/toggle-status`;

            // Loading
            this.disabled = true;
            const originalHtml = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);
                formData.append('id', id);

                const resp = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                const data = await resp.json();

                if (!data.success) {
                    alert(data.message || 'Có lỗi xảy ra.');
                    this.disabled = false;
                    this.innerHTML = originalHtml;
                    return;
                }

                if (type === 'user') {
                    updateUserToggle(this, data.new_status);
                } else {
                    updateViolationToggle(this, data);
                }
            } catch (err) {
                alert('Lỗi kết nối, vui lòng thử lại.');
                this.disabled = false;
                this.innerHTML = originalHtml;
            }
        });
    });

    function updateUserToggle(btn, newStatus) {
        btn.disabled = false;
        btn.dataset.currentStatus = newStatus;

        if (newStatus == 1) {
            btn.className = 'btn btn-sm btn-secondary toggle-status-btn';
            btn.title = 'Khóa';
            btn.innerHTML = '<i class="fas fa-lock"></i>';
        } else {
            btn.className = 'btn btn-sm btn-success toggle-status-btn';
            btn.title = 'Mở khóa';
            btn.innerHTML = '<i class="fas fa-unlock"></i>';
        }

        // Update the status badge in the same row
        const row = btn.closest('tr');
        const badge = row.querySelector('td:nth-child(6) .badge');
        if (badge) {
            badge.className = newStatus == 1 ? 'badge bg-success' : 'badge bg-danger';
            badge.textContent = newStatus == 1 ? 'Hoạt động' : 'Bị khóa';
        }
    }

    function updateViolationToggle(btn, data) {
        btn.disabled = false;
        btn.dataset.currentStatus = data.new_status;

        const badgeMap = {
            'pending': 'bg-danger',
            'processed': 'bg-warning text-dark',
            'paid': 'bg-success'
        };

        btn.className = `badge border-0 toggle-status-btn ${badgeMap[data.new_status] || 'bg-secondary'}`;
        btn.textContent = data.label;
    }
});
