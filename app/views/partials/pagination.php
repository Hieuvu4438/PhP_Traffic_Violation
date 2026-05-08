<?php
/**
 * Partial phân trang
 * @var array $pagination chứa: page, total_pages, has_prev, has_next, total
 * @var string $baseUrl URL gốc (VD: /admin/users)
 */
if (($pagination['total_pages'] ?? 1) <= 1) return;

$page = $pagination['page'] ?? 1;
$totalPages = $pagination['total_pages'] ?? 1;
$baseUrl = $baseUrl ?? '?page=';
$separator = str_contains($baseUrl, '?') ? '&' : '?';
$param = str_contains($baseUrl, '?') ? 'page=' : '';
?>
<nav aria-label="Phân trang" class="mt-4">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= !($pagination['has_prev'] ?? false) ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?><?= $page > 1 ? $separator . $param . ($page - 1) : '#' ?>">Trước</a>
        </li>

        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        if ($start > 1): ?>
            <li class="page-item"><a class="page-link" href="<?= $baseUrl . $separator . $param . 1 ?>">1</a></li>
            <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="<?= $baseUrl . $separator . $param . $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
            <li class="page-item"><a class="page-link" href="<?= $baseUrl . $separator . $param . $totalPages ?>"><?= $totalPages ?></a></li>
        <?php endif; ?>

        <li class="page-item <?= !($pagination['has_next'] ?? false) ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?><?= $page < $totalPages ? $separator . $param . ($page + 1) : '#' ?>">Sau</a>
        </li>
    </ul>
</nav>
