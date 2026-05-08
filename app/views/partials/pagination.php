<?php
/**
 * Partial phân trang (Pagination).
 * Hiển thị các nút điều hướng trang: Trước, số trang, Sau.
 *
 * @var array $pagination Mảng chứa thông tin phân trang:
 *     - page: trang hiện tại (int)
 *     - total_pages: tổng số trang (int)
 *     - has_prev: có trang trước không (bool)
 *     - has_next: có trang sau không (bool)
 *     - total: tổng số bản ghi (int)
 * @var string $baseUrl URL gốc dùng để tạo link phân trang (VD: /admin/users)
 */
/* Nếu chỉ có 1 trang hoặc ít hơn thì không hiển thị phân trang.
   ?? toán tử null coalescing: nếu 'total_pages' không tồn tại, mặc định là 1. */
if (($pagination['total_pages'] ?? 1) <= 1) return;

/* Lấy trang hiện tại, mặc định là 1 */
$page = $pagination['page'] ?? 1;
/* Lấy tổng số trang, mặc định là 1 */
$totalPages = $pagination['total_pages'] ?? 1;
/* URL gốc, mặc định là '?page=' (cho trường hợp không truyền baseUrl) */
$baseUrl = $baseUrl ?? '?page=';
/* Xác định ký tự phân cách tham số URL:
   - Nếu $baseUrl đã chứa '?' (VD: /admin/users?keyword=abc) thì thêm '&' sau đó
   - Ngược lại, thêm '?' trước tham số page */
$separator = str_contains($baseUrl, '?') ? '&' : '?';
?>
<!-- nav: phần tử HTML5 bao bọc thanh điều hướng phân trang.
     aria-label="Phân trang": thuộc tính ARIA mô tả cho trình đọc màn hình.
     mt-4: margin-top 1.5rem (khoảng cách với nội dung phía trên). -->
<nav aria-label="Phân trang" class="mt-4">
    <!-- pagination: class Bootstrap cho thanh phân trang (các nút tròn/viền).
         justify-content-center: căn giữa thanh phân trang theo chiều ngang. -->
    <ul class="pagination justify-content-center">
        <!-- Nút "Trước":
             Nếu không có trang trước ($has_prev = false) thì thêm class 'disabled' (làm mờ, không click được).
             ?? false: an toàn nếu key 'has_prev' không tồn tại trong mảng. -->
        <li class="page-item <?= !($pagination['has_prev'] ?? false) ? 'disabled' : '' ?>">
            <!-- page-link: class Bootstrap cho mỗi link/nút trong pagination.
                 Nếu page > 1: link đến trang trước đó (page - 1).
                 Nếu page = 1: href="#", nút bị vô hiệu hóa (disabled). -->
            <a class="page-link" href="<?= $baseUrl ?><?= $page > 1 ? $separator . 'page=' . ($page - 1) : '#' ?>">Trước</a>
        </li>

        <?php
        /* Tính toán phạm vi số trang hiển thị: tối đa 5 nút số (trang hiện tại + 2 trước + 2 sau).
           max(1, ...): đảm bảo không hiển thị số trang < 1 */
        $start = max(1, $page - 2);
        /* min($totalPages, ...): đảm bảo không hiển thị số trang vượt quá tổng số trang */
        $end = min($totalPages, $page + 2);
        /* Nếu trang bắt đầu > 1: hiển thị nút trang 1 và dấu "..." nếu cần */
        if ($start > 1): ?>
            <!-- Luôn hiển thị nút trang 1 để người dùng có thể về đầu -->
            <li class="page-item"><a class="page-link" href="<?= $baseUrl . $separator . 'page=' . 1 ?>">1</a></li>
            <?php if ($start > 2): ?>
                <!-- Nếu khoảng cách từ 1 đến $start > 1, hiển thị dấu "..." (ellipsis).
                     disabled: làm mờ, không click được. -->
                <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Vòng lặp hiển thị các nút số trang trong phạm vi [$start, $end] -->
        <?php for ($i = $start; $i <= $end; $i++): ?>
            <!-- Nếu $i là trang hiện tại: thêm class 'active' (nền xanh, chữ trắng nổi bật) -->
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="<?= $baseUrl . $separator . 'page=' . $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?>
                <!-- Nếu còn khoảng cách > 1 trang đến cuối, hiển thị dấu "..." -->
                <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
            <!-- Luôn hiển thị nút trang cuối cùng -->
            <li class="page-item"><a class="page-link" href="<?= $baseUrl . $separator . 'page=' . $totalPages ?>"><?= $totalPages ?></a></li>
        <?php endif; ?>

        <!-- Nút "Sau":
             Nếu không có trang sau ($has_next = false) thì disabled. -->
        <li class="page-item <?= !($pagination['has_next'] ?? false) ? 'disabled' : '' ?>">
            <!-- Nếu page < totalPages: link đến trang sau (page + 1).
                 Ngược lại: href="#", nút bị vô hiệu hóa. -->
            <a class="page-link" href="<?= $baseUrl ?><?= $page < $totalPages ? $separator . 'page=' . ($page + 1) : '#' ?>">Sau</a>
        </li>
    </ul>
</nav>
