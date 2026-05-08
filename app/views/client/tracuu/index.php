<?php
use App\Core\Session;
use App\Core\Helper;

function vehicleTypeBadge(string $type): string {
    return match($type) {
        'car' => '<span class="badge bg-primary">Ô tô</span>',
        'motorcycle' => '<span class="badge bg-info">Xe máy</span>',
        'electric_motorcycle' => '<span class="badge bg-success">Xe máy điện</span>',
        default => htmlspecialchars($type, ENT_QUOTES, 'UTF-8'),
    };
}

function statusBadge(string $status): string {
    return match($status) {
        'pending' => '<span class="badge bg-danger">Chưa xử lý</span>',
        'processed' => '<span class="badge bg-warning text-dark">Đã xử lý</span>',
        'paid' => '<span class="badge bg-success">Đã nộp phạt</span>',
        default => htmlspecialchars($status, ENT_QUOTES, 'UTF-8'),
    };
}

$hasResults = isset($results);
?>

<div class="container py-4">
    <!-- Search Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="card-title mb-4 fw-bold">
                <i class="fas fa-search me-2 text-primary"></i>Tra cứu phạt nguội
            </h2>

            <?php require __DIR__ . '/../../partials/alerts.php'; ?>

            <form id="search-form" method="POST" action="/tra-cuu" class="row g-3">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">

                <div class="col-md-5">
                    <label for="plate_number" class="form-label fw-bold">Biển số xe</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="fas fa-car"></i></span>
                        <input type="text" class="form-control" id="plate_number" name="plate_number"
                               placeholder="VD: 30A-12345"
                               value="<?= htmlspecialchars($plateNumber ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               required>
                    </div>
                    <div class="form-text">VD: 30A-12345, 30A12345, 30A 12345, 59F1-12345.</div>
                </div>

                <div class="col-md-4">
                    <label for="vehicle_type" class="form-label fw-bold">Loại xe</label>
                    <select class="form-select form-select-lg" id="vehicle_type" name="vehicle_type" required>
                        <option value="">-- Chọn loại xe --</option>
                        <option value="car" <?= ($vehicleType ?? '') === 'car' ? 'selected' : '' ?>>Ô tô</option>
                        <option value="motorcycle" <?= ($vehicleType ?? '') === 'motorcycle' ? 'selected' : '' ?>>Xe máy</option>
                        <option value="electric_motorcycle" <?= ($vehicleType ?? '') === 'electric_motorcycle' ? 'selected' : '' ?>>Xe máy điện</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold" id="search-btn">
                        <i class="fas fa-search me-2"></i>Tra cứu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <div id="results-container">
        <?php if ($hasResults): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list-alt me-2"></i>
                        Kết quả tra cứu: <?= htmlspecialchars($plateNumber ?? '', ENT_QUOTES, 'UTF-8') ?>
                        (<?= vehicleTypeBadge($vehicleType ?? '') ?>)
                    </h5>
                    <span class="badge bg-light text-dark">
                        Tìm thấy <?= count($results) ?> kết quả
                    </span>
                </div>

                <?php if (empty($results)): ?>
                    <div class="card-body text-center py-5">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h4 class="text-success">Không tìm thấy vi phạm nào!</h4>
                        <p class="text-muted">Phương tiện của bạn không có vi phạm nào trong hệ thống.</p>
                        <p class="text-muted small">Lưu ý: Dữ liệu có thể chưa cập nhật đầy đủ. Vui lòng kiểm tra lại sau.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 60px;">STT</th>
                                    <th>Thời gian</th>
                                    <th>Địa điểm</th>
                                    <th>Hành vi vi phạm</th>
                                    <th>Mức phạt</th>
                                    <th class="text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $stt = 1; foreach ($results as $row): ?>
                                    <tr>
                                        <td class="text-center"><?= $stt++ ?></td>
                                        <td>
                                            <?= htmlspecialchars(Helper::formatDateTime($row['violation_date']), ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['location_name'])): ?>
                                                <strong><?= htmlspecialchars($row['location_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                                <?php if (!empty($row['location_address'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($row['location_address'], ENT_QUOTES, 'UTF-8') ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">--</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['offense_name'] ?? '---', ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['fine_amount'])): ?>
                                                <strong class="text-danger"><?= htmlspecialchars($row['fine_amount'], ENT_QUOTES, 'UTF-8') ?></strong>
                                            <?php elseif (!empty($row['offense_penalty'])): ?>
                                                <?= htmlspecialchars($row['offense_penalty'], ENT_QUOTES, 'UTF-8') ?>
                                            <?php else: ?>
                                                <span class="text-muted">--</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?= statusBadge($row['status']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Dữ liệu được cập nhật từ Cục CSGT & Cục Đăng Kiểm Việt Nam.
                            </small>
                            <?php if (!Session::isLoggedIn()): ?>
                                <small>
                                    <a href="/dang-ky" class="text-decoration-none">
                                        <i class="fas fa-user-plus me-1"></i>Đăng ký để lưu lịch sử tra cứu
                                    </a>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- No search yet - show info -->
            <div class="card shadow-sm" id="info-card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-info-circle fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Nhập biển số xe và chọn loại xe để tra cứu</h5>
                    <p class="text-muted">Hệ thống sẽ kiểm tra và hiển thị các vi phạm giao thông của phương tiện.</p>
                    <hr class="w-50 mx-auto">
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="p-3">
                                <i class="fas fa-car fa-2x text-primary mb-2"></i>
                                <h6>Ô tô</h6>
                                <p class="text-muted small">Biển số dạng: 30A-12345, 51F-12345</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3">
                                <i class="fas fa-motorcycle fa-2x text-info mb-2"></i>
                                <h6>Xe máy</h6>
                                <p class="text-muted small">Biển số dạng: 59F1-12345</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3">
                                <i class="fas fa-bicycle fa-2x text-success mb-2"></i>
                                <h6>Xe máy điện</h6>
                                <p class="text-muted small">Biển số dạng: 29M1-12345</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('search-form');
    const resultsContainer = document.getElementById('results-container');
    const searchBtn = document.getElementById('search-btn');
    const btnOriginal = searchBtn.innerHTML;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Loading state
        searchBtn.disabled = true;
        searchBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang tra cứu...';

        // Show loading skeleton in results area
        resultsContainer.innerHTML = `
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="text-muted">Đang tra cứu dữ liệu từ Cục CSGT...</p>
                </div>
            </div>`;

        try {
            const formData = new FormData(form);
            const resp = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const data = await resp.json();

            if (!resp.ok || !data.success) {
                resultsContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${data.message || 'Có lỗi xảy ra, vui lòng thử lại.'}
                    </div>`;
                return;
            }

            renderResults(data);
        } catch (err) {
            resultsContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Lỗi kết nối, vui lòng thử lại sau.
                </div>`;
        } finally {
            searchBtn.disabled = false;
            searchBtn.innerHTML = btnOriginal;
        }
    });

    function renderResults(data) {
        const typeBadges = {
            'car': '<span class="badge bg-primary">Ô tô</span>',
            'motorcycle': '<span class="badge bg-info">Xe máy</span>',
            'electric_motorcycle': '<span class="badge bg-success">Xe máy điện</span>'
        };

        const statusBadges = {
            'pending': '<span class="badge bg-danger">Chưa xử lý</span>',
            'processed': '<span class="badge bg-warning text-dark">Đã xử lý</span>',
            'paid': '<span class="badge bg-success">Đã nộp phạt</span>'
        };

        let bodyHtml = '';
        if (data.count === 0) {
            bodyHtml = `
                <div class="card-body text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h4 class="text-success">Không tìm thấy vi phạm nào!</h4>
                    <p class="text-muted">Phương tiện của bạn không có vi phạm nào trong hệ thống.</p>
                    <p class="text-muted small">Lưu ý: Dữ liệu có thể chưa cập nhật đầy đủ. Vui lòng kiểm tra lại sau.</p>
                </div>`;
        } else {
            let rows = '';
            data.results.forEach(function (row, i) {
                const locationCell = row.location_name
                    ? `<strong>${escHtml(row.location_name)}</strong>${row.location_address ? '<br><small class="text-muted">' + escHtml(row.location_address) + '</small>' : ''}`
                    : '<span class="text-muted">--</span>';

                const fineCell = row.fine_amount
                    ? `<strong class="text-danger">${escHtml(row.fine_amount)}</strong>`
                    : (row.offense_penalty ? escHtml(row.offense_penalty) : '<span class="text-muted">--</span>');

                rows += `
                    <tr>
                        <td class="text-center">${i + 1}</td>
                        <td>${escHtml(row.violation_date)}</td>
                        <td>${locationCell}</td>
                        <td>${escHtml(row.offense_name || '---')}</td>
                        <td>${fineCell}</td>
                        <td class="text-center">${statusBadges[row.status] || escHtml(row.status)}</td>
                    </tr>`;
            });

            bodyHtml = `
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">STT</th>
                                <th>Thời gian</th>
                                <th>Địa điểm</th>
                                <th>Hành vi vi phạm</th>
                                <th>Mức phạt</th>
                                <th class="text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Dữ liệu được cập nhật từ Cục CSGT & Cục Đăng Kiểm Việt Nam.
                        </small>
                    </div>
                </div>`;
        }

        resultsContainer.innerHTML = `
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list-alt me-2"></i>
                        Kết quả tra cứu: ${escHtml(data.plateNumber)}
                        (${typeBadges[data.vehicleType] || data.vehicleType})
                    </h5>
                    <span class="badge bg-light text-dark">
                        Tìm thấy ${data.count} kết quả
                    </span>
                </div>
                ${bodyHtml}
            </div>`;
    }

    function escHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
</script>
