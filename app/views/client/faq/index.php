<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <i class="fas fa-question-circle me-2 text-primary"></i>Câu hỏi thường gặp
    </h2>

    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <?php if (empty($faqs)): ?>
        <div class="text-center py-5">
            <i class="fas fa-question-circle fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Chưa có câu hỏi nào</h4>
            <p class="text-muted">Vui lòng quay lại sau.</p>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-9">
                <div class="accordion shadow-sm" id="faqAccordion">
                    <?php foreach ($faqs as $index => $faq): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq-<?= $faq['id'] ?>"
                                        aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>">
                                    <span class="text-primary me-3 fw-bold"><?= $index + 1 ?>.</span>
                                    <?= htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8') ?>
                                </button>
                            </h2>
                            <div id="faq-<?= $faq['id'] ?>"
                                 class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    <?= nl2br(htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8')) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-3 mt-4 mt-lg-0">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white fw-bold">
                        <i class="fas fa-info-circle me-2"></i>Hỗ trợ thêm
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Không tìm thấy câu trả lời? Liên hệ với chúng tôi để được hỗ trợ.</p>
                        <a href="/lien-he" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-envelope me-2"></i>Gửi yêu cầu hỗ trợ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
