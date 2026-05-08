<?php
use App\Core\Session;
use App\Core\Helper;

/**
 * Helper: trả về badge Bootstrap theo loại phương tiện.
 * Dùng match expression (PHP 8.x) - server-side render.
 * JS bên dưới có hàm tương tự để render phía client qua AJAX.
 */
function vehicleTypeBadge(string $type): string {
    return match($type) {
        'car' => '<span class="badge bg-primary">Ô tô</span>',
        'motorcycle' => '<span class="badge bg-info">Xe máy</span>',
        'electric_motorcycle' => '<span class="badge bg-success">Xe máy điện</span>',
        default => htmlspecialchars($type, ENT_QUOTES, 'UTF-8'),
    };
}

/**
 * Helper: trả về badge Bootstrap theo trạng thái vi phạm.
 * pending = chưa xử lý (đỏ) | processed = đã xử lý (vàng) | paid = đã nộp phạt (xanh lá)
 */
function statusBadge(string $status): string {
    return match($status) {
        'pending' => '<span class="badge bg-danger">Chưa xử lý</span>',
        'processed' => '<span class="badge bg-warning text-dark">Đã xử lý</span>',
        'paid' => '<span class="badge bg-success">Đã nộp phạt</span>',
        default => htmlspecialchars($status, ENT_QUOTES, 'UTF-8'),
    };
}

/**
 * Xác định xem view đang hiển thị kết quả hay form tra cứu trống.
 * - Có $results: đã submit form và controller trả về kết quả
 * - Không: hiển thị form + hướng dẫn
 */
$hasResults = isset($results);
?>

<!--
  Trang tra cứu vi phạm (client/tracuu/index.php)
  - Form tìm kiếm bằng biển số + loại xe
  - Xử lý AJAX (fetch API) khi có JS, fallback POST thông thường khi không JS
  - Kết quả trả về dạng bảng table-responsive có thể cuộn ngang
  - Hiển thị trạng thái vi phạm qua badge màu
-->

<!-- container py-4: căn giữa + padding top/bottom 1.5rem -->
<div class="container py-4">
    <!-- Card: Form tìm kiếm -->
    <!-- card shadow-sm mb-4: hộp đổ bóng nhẹ, margin-bottom 1.5rem -->
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <!-- mb-4 fw-bold: margin-bottom 1.5rem, in đậm -->
            <h2 class="card-title mb-4 fw-bold">
                <!-- fa-search: icon kính lúp tìm kiếm -->
                <i class="fas fa-search me-2 text-primary"></i>Tra cứu phạt nguội
            </h2>

            <!-- Nhúng partial hiển thị thông báo từ session (lỗi, thành công) -->
            <?php require __DIR__ . '/../../partials/alerts.php'; ?>

            <!-- id="search-form" để JS bắt sự kiện submit | row g-3: grid gap 1rem -->
            <form id="search-form" method="POST" action="/tra-cuu" class="row g-3">
                <!-- CSRF token trong hidden input -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">

                <!-- col-md-5: Ô nhập biển số xe -->
                <div class="col-md-5">
                    <!-- form-label fw-bold: nhãn form in đậm -->
                    <label for="plate_number" class="form-label fw-bold">Biển số xe</label>
                    <!-- input-group input-group-lg: nhóm input kích thước lớn -->
                    <div class="input-group input-group-lg">
                        <!-- fa-car: icon xe ô tô -->
                        <span class="input-group-text"><i class="fas fa-car"></i></span>
                        <!-- value giữ lại giá trị đã nhập trước đó khi submit -->
                        <input type="text" class="form-control" id="plate_number" name="plate_number"
                               placeholder="VD: 30A-12345"
                               value="<?= htmlspecialchars($plateNumber ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               required>
                    </div>
                    <!-- form-text: văn bản hướng dẫn nhỏ dưới input -->
                    <div class="form-text">VD: 30A-12345, 30A12345, 30A 12345, 59F1-12345.</div>
                </div>

                <!-- col-md-4: Select loại xe -->
                <div class="col-md-4">
                    <label for="vehicle_type" class="form-label fw-bold">Loại xe</label>
                    <!-- form-select-lg: select kích thước lớn -->
                    <!-- selected: giữ nguyên lựa chọn trước đó -->
                    <select class="form-select form-select-lg" id="vehicle_type" name="vehicle_type" required>
                        <option value="">-- Chọn loại xe --</option>
                        <option value="car" <?= ($vehicleType ?? '') === 'car' ? 'selected' : '' ?>>Ô tô</option>
                        <option value="motorcycle" <?= ($vehicleType ?? '') === 'motorcycle' ? 'selected' : '' ?>>Xe máy</option>
                        <option value="electric_motorcycle" <?= ($vehicleType ?? '') === 'electric_motorcycle' ? 'selected' : '' ?>>Xe máy điện</option>
                    </select>
                </div>

                <!-- col-md-3: Nút tra cứu | d-flex align-items-end: đẩy nút xuống cuối hàng -->
                <div class="col-md-3 d-flex align-items-end">
                    <!-- btn-primary: nút xanh | btn-lg: lớn | w-100: full width | fw-bold: in đậm -->
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold" id="search-btn">
                        <i class="fas fa-search me-2"></i>Tra cứu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Khu vực kết quả tra cứu (id để AJAX cập nhật nội dung) -->
    <div id="results-container">
        <!--
          Luồng hiển thị:
          1. $hasResults = true -> có submit form
             - empty($results) -> không vi phạm: hiển thị thông báo xanh
             - có results -> bảng kết quả
          2. $hasResults = false -> chưa submit: hiển thị hướng dẫn + thông tin 3 loại xe
        -->
        <?php if ($hasResults): ?>
            <div class="card shadow-sm">
                <!-- card-header: header bảng kết quả | bg-primary text-white: nền xanh chữ trắng -->
                <!-- d-flex justify-content-between: tiêu đề trái + số kết quả phải -->
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <!-- fa-list-alt: icon danh sách kết quả -->
                        <i class="fas fa-list-alt me-2"></i>
                        Kết quả tra cứu: <?= htmlspecialchars($plateNumber ?? '', ENT_QUOTES, 'UTF-8') ?>
                        (<?= vehicleTypeBadge($vehicleType ?? '') ?>)
                    </h5>
                    <!-- badge bg-light text-dark: huy hiệu nền xám sáng chữ tối -->
                    <span class="badge bg-light text-dark">
                        Tìm thấy <?= count($results) ?> kết quả
                    </span>
                </div>

                <?php if (empty($results)): ?>
                    <!-- Trường hợp không có vi phạm -->
                    <div class="card-body text-center py-5">
                        <!-- fa-check-circle fa-4x text-success: icon check xanh lớn -->
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h4 class="text-success">Không tìm thấy vi phạm nào!</h4>
                        <p class="text-muted">Phương tiện của bạn không có vi phạm nào trong hệ thống.</p>
                        <p class="text-muted small">Lưu ý: Dữ liệu có thể chưa cập nhật đầy đủ. Vui lòng kiểm tra lại sau.</p>
                    </div>
                <?php else: ?>
                    <!-- table-responsive: cho phép cuộn ngang trên mobile -->
                    <div class="table-responsive">
                        <!-- table table-hover: bảng Bootstrap với hiệu ứng hover đổi màu dòng -->
                        <table class="table table-hover mb-0">
                            <!-- table-light: nền xám nhạt cho header -->
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
                                <!-- $stt: số thứ tự bắt đầu từ 1 -->
                                <?php $stt = 1; foreach ($results as $row): ?>
                                    <tr>
                                        <td class="text-center"><?= $stt++ ?></td>
                                        <td>
                                            <!-- Helper::formatDateTime: định dạng ngày giờ đầy đủ -->
                                            <?= htmlspecialchars(Helper::formatDateTime($row['violation_date']), ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td>
                                            <!-- Hiển thị tên địa điểm + địa chỉ (nếu có) -->
                                            <?php if (!empty($row['location_name'])): ?>
                                                <strong><?= htmlspecialchars($row['location_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                                <?php if (!empty($row['location_address'])): ?>
                                                    <!-- text-muted: chữ xám nhạt cho địa chỉ -->
                                                    <br><small class="text-muted"><?= htmlspecialchars($row['location_address'], ENT_QUOTES, 'UTF-8') ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">--</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <!-- escape HTML output để chống XSS -->
                                            <?= htmlspecialchars($row['offense_name'] ?? '---', ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td>
                                            <!-- Ưu tiên hiển thị fine_amount (tiền phạt), fallback đến offense_penalty -->
                                            <?php if (!empty($row['fine_amount'])): ?>
                                                <!-- text-danger: chữ đỏ cho tiền phạt -->
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

                    <!-- card-footer: footer bảng | bg-light: nền xám nhạt -->
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <!-- fa-info-circle: icon thông tin -->
                                <i class="fas fa-info-circle me-1"></i>
                                Dữ liệu được cập nhật từ Cục CSGT & Cục Đăng Kiểm Việt Nam.
                            </small>
                            <!-- Nếu chưa đăng nhập: gợi ý đăng ký để lưu lịch sử tra cứu -->
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
            <!-- Chưa submit form: hiển thị hướng dẫn sử dụng + thông tin 3 loại xe -->
            <div class="card shadow-sm" id="info-card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-info-circle fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Nhập biển số xe và chọn loại xe để tra cứu</h5>
                    <p class="text-muted">Hệ thống sẽ kiểm tra và hiển thị các vi phạm giao thông của phương tiện.</p>
                    <!-- w-50 mx-auto: đường kẻ ngang rộng 50%, căn giữa -->
                    <hr class="w-50 mx-auto">
                    <!-- 3 cột thông tin hướng dẫn cho từng loại xe -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="p-3">
                                <!-- fa-car: icon ô tô -->
                                <i class="fas fa-car fa-2x text-primary mb-2"></i>
                                <h6>Ô tô</h6>
                                <p class="text-muted small">Biển số dạng: 30A-12345, 51F-12345</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3">
                                <!-- fa-motorcycle: icon xe máy -->
                                <i class="fas fa-motorcycle fa-2x text-info mb-2"></i>
                                <h6>Xe máy</h6>
                                <p class="text-muted small">Biển số dạng: 59F1-12345</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3">
                                <!-- fa-bicycle: icon xe đạp (đại diện xe điện) -->
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

<!--
  JavaScript: Xử lý tra cứu bằng AJAX (fetch API)
  - Bắt sự kiện submit form -> chặn form POST mặc định
  - Gửi fetch POST với header X-Requested-With để controller nhận biết AJAX
  - Controller trả về JSON -> client render HTML động
  - Có trạng thái loading (spinner-border) trong khi chờ kết quả
  - Fallback: nếu tắt JS, form submit bình thường -> PHP xử lý server-side
-->
<script>
// DOMContentLoaded: đảm bảo DOM đã sẵn sàng trước khi gắn event
document.addEventListener('DOMContentLoaded', function () {
    // Tham chiếu đến các element chính
    const form = document.getElementById('search-form');
    const resultsContainer = document.getElementById('results-container');
    const searchBtn = document.getElementById('search-btn');
    // Lưu lại HTML gốc của nút để khôi phục sau khi loading
    const btnOriginal = searchBtn.innerHTML;

    // Bắt sự kiện submit form, dùng async/await cho fetch
    form.addEventListener('submit', async function (e) {
        // Ngăn form submit mặc định (không reload trang)
        e.preventDefault();

        // ---- Trạng thái loading ----
        searchBtn.disabled = true;
        // spinner-border: icon xoay loading của Bootstrap
        searchBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang tra cứu...';

        // Hiển thị skeleton loading trong khu vực kết quả
        resultsContainer.innerHTML = `
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <!-- visually-hidden: ẩn text nhưng vẫn đọc được bằng screen reader -->
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="text-muted">Đang tra cứu dữ liệu từ Cục CSGT...</p>
                </div>
            </div>`;

        try {
            // FormData: lấy tất cả dữ liệu form (bao gồm csrf_token)
            const formData = new FormData(form);
            // fetch POST tới action của form
            const resp = await fetch(form.action, {
                method: 'POST',
                // X-Requested-With: XMLHttpRequest -> controller nhận biết đây là AJAX request
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            // Parse JSON response từ controller
            const data = await resp.json();

            // Kiểm tra response không OK hoặc data.success = false
            if (!resp.ok || !data.success) {
                // Hiển thị alert nguy hiểm (đỏ) với thông báo lỗi
                resultsContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <!-- fa-exclamation-circle: icon dấu chấm than trong vòng tròn -->
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${data.message || 'Có lỗi xảy ra, vui lòng thử lại.'}
                    </div>`;
                return;
            }

            // Thành công: render kết quả từ JSON
            renderResults(data);
        } catch (err) {
            // Lỗi mạng hoặc lỗi parse JSON
            resultsContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Lỗi kết nối, vui lòng thử lại sau.
                </div>`;
        } finally {
            // Luôn khôi phục nút về trạng thái ban đầu
            searchBtn.disabled = false;
            searchBtn.innerHTML = btnOriginal;
        }
    });

    /**
     * Hàm render kết quả từ JSON data.
     * Tạo HTML động bằng template literal ES6.
     * Sao chép cấu trúc giống với PHP render ở trên.
     */
    function renderResults(data) {
        // Map type -> badge HTML (sao chép logic PHP server-side)
        const typeBadges = {
            'car': '<span class="badge bg-primary">Ô tô</span>',
            'motorcycle': '<span class="badge bg-info">Xe máy</span>',
            'electric_motorcycle': '<span class="badge bg-success">Xe máy điện</span>'
        };

        // Map status -> badge HTML
        const statusBadges = {
            'pending': '<span class="badge bg-danger">Chưa xử lý</span>',
            'processed': '<span class="badge bg-warning text-dark">Đã xử lý</span>',
            'paid': '<span class="badge bg-success">Đã nộp phạt</span>'
        };

        let bodyHtml = '';
        // Không có kết quả vi phạm
        if (data.count === 0) {
            bodyHtml = `
                <div class="card-body text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h4 class="text-success">Không tìm thấy vi phạm nào!</h4>
                    <p class="text-muted">Phương tiện của bạn không có vi phạm nào trong hệ thống.</p>
                    <p class="text-muted small">Lưu ý: Dữ liệu có thể chưa cập nhật đầy đủ. Vui lòng kiểm tra lại sau.</p>
                </div>`;
        } else {
            // Có kết quả: duyệt qua mảng data.results
            let rows = '';
            data.results.forEach(function (row, i) {
                // Xử lý cell địa điểm: tên + địa chỉ (nếu có)
                const locationCell = row.location_name
                    ? `<strong>${escHtml(row.location_name)}</strong>${row.location_address ? '<br><small class="text-muted">' + escHtml(row.location_address) + '</small>' : ''}`
                    : '<span class="text-muted">--</span>';

                // Xử lý cell mức phạt: ưu tiên fine_amount, fallback offense_penalty
                const fineCell = row.fine_amount
                    ? `<strong class="text-danger">${escHtml(row.fine_amount)}</strong>`
                    : (row.offense_penalty ? escHtml(row.offense_penalty) : '<span class="text-muted">--</span>');

                // Dựng từng dòng trong bảng
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

            // Bảng kết quả đầy đủ
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

        // Ghép header + body thành card kết quả hoàn chỉnh
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

    /**
     * Hàm escape HTML phía client (tương tự htmlspecialchars trong PHP).
     * Tạo thẻ div ảo -> gán textContent -> lấy innerHTML đã được escape.
     */
    function escHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
</script>
