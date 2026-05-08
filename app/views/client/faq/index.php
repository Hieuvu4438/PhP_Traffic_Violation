<!--
  Trang Câu hỏi thường gặp - FAQ (client/faq/index.php)
  - Accordion Bootstrap 5: mỗi câu hỏi là một accordion-item
  - Mục đầu tiên mở sẵn (show), các mục còn lại đóng (collapsed)
  - Sidebar: card hỗ trợ thêm với link đến trang liên hệ
  - Dữ liệu lấy từ bảng faqs: question, answer
-->

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <!-- fa-question-circle: icon dấu hỏi trong vòng tròn -->
        <i class="fas fa-question-circle me-2 text-primary"></i>Câu hỏi thường gặp
    </h2>

    <!-- Partial hiển thị thông báo -->
    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <?php if (empty($faqs)): ?>
        <!-- Trạng thái rỗng -->
        <div class="text-center py-5">
            <i class="fas fa-question-circle fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Chưa có câu hỏi nào</h4>
            <p class="text-muted">Vui lòng quay lại sau.</p>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Cột trái: Accordion (9/12 desktop) -->
            <div class="col-lg-9">
                <!--
                  accordion: wrapper Bootstrap cho accordion
                  id="faqAccordion": id để data-bs-parent tham chiếu
                  shadow-sm: đổ bóng nhẹ
                -->
                <div class="accordion shadow-sm" id="faqAccordion">
                    <!-- $index: vị trí để xác định item đầu tiên (mở sẵn) -->
                    <?php foreach ($faqs as $index => $faq): ?>
                        <div class="accordion-item">
                            <!-- accordion-header: header mỗi accordion item -->
                            <h2 class="accordion-header">
                                <!--
                                  accordion-button: nút bấm toggle accordion
                                  collapsed: class thêm vào nếu không phải item đầu tiên (đóng)
                                  data-bs-toggle="collapse": JS để mở/đóng
                                  data-bs-target="#faq-ID": trỏ đến nội dung câu trả lời
                                  aria-expanded: true (mở) / false (đóng) cho accessibility
                                -->
                                <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq-<?= $faq['id'] ?>"
                                        aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>">
                                    <!-- Số thứ tự + câu hỏi -->
                                    <span class="text-primary me-3 fw-bold"><?= $index + 1 ?>.</span>
                                    <?= htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8') ?>
                                </button>
                            </h2>
                            <!--
                              accordion-collapse: nội dung có thể mở/đóng
                              collapse: class khởi tạo collapse
                              show: class mở sẵn cho item đầu tiên
                              data-bs-parent="#faqAccordion": đảm bảo chỉ 1 item mở cùng lúc
                            -->
                            <div id="faq-<?= $faq['id'] ?>"
                                 class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>"
                                 data-bs-parent="#faqAccordion">
                                <!-- accordion-body text-muted: nội dung câu trả lời, chữ xám -->
                                <div class="accordion-body text-muted">
                                    <!-- nl2br: chuyển newline thành <br> để giữ định dạng -->
                                    <?= nl2br(htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8')) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Cột phải: Sidebar hỗ trợ (3/12 desktop) -->
            <!-- mt-4 mt-lg-0: margin-top trên mobile, 0 trên desktop -->
            <div class="col-lg-3 mt-4 mt-lg-0">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white fw-bold">
                        <!-- fa-info-circle: icon thông tin -->
                        <i class="fas fa-info-circle me-2"></i>Hỗ trợ thêm
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Không tìm thấy câu trả lời? Liên hệ với chúng tôi để được hỗ trợ.</p>
                        <!-- btn-sm w-100: nút nhỏ full width -->
                        <a href="/lien-he" class="btn btn-primary btn-sm w-100">
                            <!-- fa-envelope: icon phong bì thư (liên hệ) -->
                            <i class="fas fa-envelope me-2"></i>Gửi yêu cầu hỗ trợ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
