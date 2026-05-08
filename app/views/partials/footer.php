<?php
/**
 * Partial Footer - Chân trang cho toàn bộ trang client.
 * Gồm 3 cột: Giới thiệu, Liên kết nhanh, Thông tin liên hệ.
 * Được nhúng vào client.php layout.
 */
?>
<!-- footer: chân trang.
     bg-dark: nền màu tối (đen/xám đậm) của Bootstrap.
     text-white: chữ màu trắng.
     pt-5: padding-top 3rem (khoảng cách lớn phía trên).
     pb-3: padding-bottom 1rem.
     mt-5: margin-top 3rem (đẩy footer cách xa nội dung phía trên). -->
<footer class="bg-dark text-white pt-5 pb-3 mt-5">
    <!-- container: căn giữa và giới hạn chiều rộng nội dung footer -->
    <div class="container">
        <!-- row: hàng grid Bootstrap, chia thành 12 cột -->
        <div class="row">
            <!-- Cột 1 - Giới thiệu.
                 col-md-4: trên màn hình ≥768px chiếm 4/12 cột (1/3 chiều rộng).
                 mb-4: margin-bottom 1.5rem (khoảng cách dưới trên mobile khi các cột xếp chồng). -->
            <div class="col-md-4 mb-4">
                <!-- fw-bold: chữ đậm. mb-3: margin-bottom 1rem. -->
                <h5 class="fw-bold mb-3"><i class="fas fa-car me-2"></i>Tra Cứu Phạt Nguội</h5>
                <!-- text-secondary: chữ màu xám (mức độ quan trọng thứ cấp).
                     small: cỡ chữ nhỏ hơn 1 bậc. -->
                <p class="text-secondary small">Website tra cứu phương tiện vi phạm giao thông toàn quốc. Dữ liệu được tổng hợp từ Cục CSGT và Cục Đăng Kiểm Việt Nam, cập nhật thường xuyên.</p>
                <p class="text-secondary small">Website được xây dựng với mục đích học thuật - Đồ án PHP.</p>
            </div>
            <!-- Cột 2 - Liên kết nhanh -->
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3">Liên kết nhanh</h5>
                <!-- list-unstyled: bỏ dấu chấm đầu dòng mặc định của <ul> -->
                <ul class="list-unstyled">
                    <!-- mb-2: margin-bottom 0.5rem giữa các mục.
                         text-secondary: chữ màu xám cho link.
                         text-decoration-none: bỏ gạch chân mặc định của thẻ <a>. -->
                    <li class="mb-2"><a href="/" class="text-secondary text-decoration-none"><i class="fas fa-chevron-right me-2 small"></i>Trang chủ</a></li>
                    <!-- fa-chevron-right: icon mũi tên sang phải, chỉ hướng cho liên kết -->
                    <li class="mb-2"><a href="/tra-cuu" class="text-secondary text-decoration-none"><i class="fas fa-chevron-right me-2 small"></i>Tra cứu phạt nguội</a></li>
                    <li class="mb-2"><a href="/tin-tuc" class="text-secondary text-decoration-none"><i class="fas fa-chevron-right me-2 small"></i>Tin tức giao thông</a></li>
                    <li class="mb-2"><a href="/bien-bao" class="text-secondary text-decoration-none"><i class="fas fa-chevron-right me-2 small"></i>Biển báo giao thông</a></li>
                    <li class="mb-2"><a href="/thong-ke" class="text-secondary text-decoration-none"><i class="fas fa-chevron-right me-2 small"></i>Thống kê</a></li>
                    <li class="mb-2"><a href="/faq" class="text-secondary text-decoration-none"><i class="fas fa-chevron-right me-2 small"></i>Câu hỏi thường gặp</a></li>
                    <li class="mb-2"><a href="/gioi-thieu" class="text-secondary text-decoration-none"><i class="fas fa-chevron-right me-2 small"></i>Giới thiệu</a></li>
                    <li class="mb-2"><a href="/lien-he" class="text-secondary text-decoration-none"><i class="fas fa-chevron-right me-2 small"></i>Liên hệ</a></li>
                </ul>
            </div>
            <!-- Cột 3 - Thông tin liên hệ -->
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3">Thông tin liên hệ</h5>
                <!-- Danh sách thông tin liên hệ không có bullet -->
                <ul class="list-unstyled text-secondary">
                    <!-- fa-map-marker-alt: icon đánh dấu vị trí bản đồ (địa chỉ) -->
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>Hà Nội, Việt Nam</li>
                    <!-- fa-envelope: icon phong bì thư (email) -->
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i>contact@tracuuphatnguoi.vn</li>
                    <!-- fa-phone: icon điện thoại -->
                    <li class="mb-2"><i class="fas fa-phone me-2"></i>1900 xxxx</li>
                </ul>
                <!-- mt-3: margin-top 1rem. Icon mạng xã hội -->
                <div class="mt-3">
                    <!-- fab fa-facebook: icon Facebook (thương hiệu). me-3: margin-right 1rem. fs-5: font-size 1.25rem. -->
                    <a href="#" class="text-secondary me-3 fs-5"><i class="fab fa-facebook"></i></a>
                    <!-- fab fa-youtube: icon YouTube -->
                    <a href="#" class="text-secondary me-3 fs-5"><i class="fab fa-youtube"></i></a>
                    <!-- fab fa-tiktok: icon TikTok -->
                    <a href="#" class="text-secondary fs-5"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
        <!-- Đường kẻ ngang phân cách. border-secondary: viền màu xám. mt-4: margin-top 1.5rem -->
        <hr class="border-secondary mt-4">
        <!-- text-center: căn giữa chữ. Bản quyền -->
        <div class="text-center text-secondary small">
            <!-- date('Y'): hiển thị năm hiện tại (tự động cập nhật).
                 &copy;: ký hiệu bản quyền © (HTML entity). -->
            <p class="mb-0">&copy; <?= date('Y') ?> Tra Cứu Phạt Nguội. Tất cả quyền được bảo lưu.</p>
            <!-- mt-1: margin-top 0.25rem -->
            <p class="mb-0 mt-1">Website được xây dựng cho mục đích học thuật - Đồ án bài tập lớn PHP.</p>
        </div>
    </div>
</footer>
