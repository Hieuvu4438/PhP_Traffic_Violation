# ĐỒ ÁN BÁO CÁO BÀI TẬP LỚN PHP
# WEBSITE TRA CỨU PHƯƠNG TIỆN VI PHẠM GIAO THÔNG

---

## MỤC LỤC TỔNG THỂ BỘ TÀI LIỆU

| # | Tài liệu | Mô tả |
|---|----------|-------|
| 01 | `01-tong-quan-du-an.md` | Tổng quan, mục tiêu, phạm vi, công nghệ |
| 02 | `02-phan-tich-yeu-cau.md` | Yêu cầu chức năng & phi chức năng chi tiết |
| 03 | `03-thiet-ke-co-so-du-lieu.md` | ERD, schema, mô tả từng bảng |
| 04 | `04-thiet-ke-giao-dien.md` | Wireframe, cấu trúc trang, UI components |
| 05 | `05-kien-truc-he-thong.md` | MVC pattern, cấu trúc thư mục, luồng xử lý |
| 06 | `06-ke-hoach-phat-trien.md` | Lộ trình phát triển, phân công, milestones |

---

## 1. GIỚI THIỆU CHUNG

### 1.1. Bối cảnh thực tế

Hiện nay, tình trạng vi phạm giao thông tại Việt Nam diễn ra phổ biến. Người dân có nhu cầu tra cứu thông tin phạt nguội, kiểm tra tình trạng vi phạm của phương tiện một cách nhanh chóng và thuận tiện. Các website của nhà nước như Cục CSGT (`csgt.vn`), Cục Đăng Kiểm (`vr.org.vn`) cung cấp dịch vụ tra cứu nhưng giao diện phức tạp, nhiều bước. Các website bên thứ ba như `vnetraffic.org` đã ra đời để đơn giản hóa trải nghiệm này.

### 1.2. Mục tiêu đồ án

Xây dựng một website PHP hoàn chỉnh với chủ đề "Tra cứu phương tiện vi phạm giao thông", đáp ứng:

- **Yêu cầu tối thiểu:** CRUD (Create, Read, Update, Delete) cho các đối tượng chính
- **Yêu cầu nâng cao:** Các tính năng thực tế như tra cứu phạt nguội, thống kê, tin tức, bản đồ, biển báo giao thông
- **Yêu cầu học thuật:** Báo cáo đầy đủ, code sạch, có tổ chức theo mô hình MVC

### 1.3. Đối tượng người dùng

| Vai trò | Mô tả | Nhu cầu chính |
|---------|-------|---------------|
| Khách (Guest) | Người dùng chưa đăng nhập | Tra cứu phạt nguội, xem tin tức, xem biển báo, xem bản đồ |
| Người dùng (User) | Người dùng đã đăng ký | Quản lý phương tiện cá nhân, lưu lịch sử tra cứu, nhận thông báo |
| Quản trị viên (Admin) | Người quản lý hệ thống | CRUD dữ liệu (vi phạm, tin tức, biển báo, người dùng), xem thống kê |

---

## 2. PHẠM VI DỰ ÁN

### 2.1. Các Module Chính (10 Module)

```
┌──────────────────────────────────────────────────────────────┐
│                  WEBSITE TRA CỨU PHẠT NGUỘI                   │
├──────────────────────────────────────────────────────────────┤
│ 1. Module Tra Cứu Phạt Nguội        (tra cứu theo biển số)    │
│ 2. Module Tin Tức Giao Thông        (blog, danh mục, bài viết) │
│ 3. Module Thống Kê Vi Phạm          (chart, top, báo cáo)     │
│ 4. Module Biển Báo Giao Thông       (danh sách, chi tiết)     │
│ 5. Module Bản Đồ Google Maps        (điểm camera, trạm CSGT)  │
│ 6. Module Quản Lý Phương Tiện       (CRUD phương tiện)        │
│ 7. Module Người Dùng                (đăng ký, đăng nhập, role) │
│ 8. Module Admin Dashboard           (quản trị tổng quan)      │
│ 9. Module FAQ / Giải Đáp            (hỏi đáp giao thông)      │
│ 10. Module Trang Tĩnh               (giới thiệu, liên hệ,...) │
└──────────────────────────────────────────────────────────────┘
```

### 2.2. Tính năng CRUD bắt buộc (tối thiểu 5 Entity)

| Entity | Create | Read | Update | Delete |
|--------|:------:|:----:|:------:|:------:|
| Người dùng (users) | ✅ (đăng ký + admin thêm) | ✅ (danh sách + chi tiết) | ✅ (sửa thông tin) | ✅ (admin xóa) |
| Phương tiện (vehicles) | ✅ (người dùng thêm xe) | ✅ (danh sách xe của user) | ✅ (cập nhật thông tin) | ✅ (xóa xe) |
| Vi phạm (violations) | ✅ (admin nhập liệu) | ✅ (tra cứu + danh sách) | ✅ (admin sửa) | ✅ (admin xóa) |
| Tin tức (news) | ✅ (admin viết bài) | ✅ (danh sách + chi tiết) | ✅ (admin sửa) | ✅ (admin xóa) |
| Biển báo (traffic_signs) | ✅ (admin thêm) | ✅ (danh sách + chi tiết) | ✅ (admin sửa) | ✅ (admin xóa) |

---

## 3. CÔNG NGHỆ SỬ DỤNG

| Lớp | Công nghệ | Lý do lựa chọn |
|-----|-----------|----------------|
| Backend | **PHP 8.x** (thuần, tự xây MVC) | Không phụ thuộc framework, hiểu sâu kiến trúc, phù hợp đồ án |
| Database | **MySQL / MariaDB** (XAMPP) | Phổ biến, dễ cài đặt, phù hợp môi trường học tập |
| Frontend | **HTML5, CSS3, JavaScript (Vanilla)** | Không cần build tool phức tạp |
| CSS Framework | **Bootstrap 5** | Responsive sẵn, component đa dạng, dễ học |
| Biểu đồ | **Chart.js** | Nhẹ, miễn phí, hỗ trợ nhiều loại biểu đồ |
| Bản đồ | **Google Maps JavaScript API** | API chính thức, tài liệu đầy đủ |
| Icons | **Font Awesome 6** (hoặc Bootstrap Icons) | Bộ icon phong phú, miễn phí |
| Editor | **CKEditor 5** (cho admin viết bài) | WYSIWYG, dễ tích hợp |
| Web Server | **Apache** (XAMPP) | Đi kèm XAMPP, cấu hình đơn giản |
| Version Control | **Git** + GitHub | Quản lý mã nguồn, backup |

---

## 4. KIẾN TRÚC TỔNG QUAN

### 4.1. Mô hình MVC tự xây dựng

```
htdocs/
├── app/
│   ├── controllers/       # Xử lý request, gọi model, trả về view
│   ├── models/            # Tương tác database (PDO/MySQLi)
│   ├── views/             # Giao diện HTML (file .php hiển thị)
│   │   ├── layouts/       # Layout chung (header, footer, sidebar)
│   │   ├── admin/         # Giao diện admin
│   │   └── client/        # Giao diện người dùng
│   └── core/              # Lõi: Router, Controller base, Model base
├── public/                # Web root (chỉ chứa index.php + assets)
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── index.php          # Entry point duy nhất
├── config/                # Cấu hình database, constants
├── database/              # File SQL migration/seed
├── docs/                  # Tài liệu dự án (thư mục hiện tại)
└── vendor/                # Thư viện (nếu dùng Composer)
```

### 4.2. Luồng xử lý request

```
Request → public/index.php → Router → Controller → Model (DB) → View → Response
```

---

## 5. TỔNG HỢP TÍNH NĂNG (Tham khảo từ vnetraffic.org & mở rộng)

### 5.1. Bảng so sánh với vnetraffic.org

| Tính năng | vnetraffic.org | Đồ án này | Ghi chú |
|-----------|:---:|:---:|---------|
| Tra cứu phạt nguội theo biển số | ✅ | ✅ | Form nhập biển số + loại xe → kết quả |
| Tin tức giao thông (blog) | ✅ | ✅ | CRUD bài viết, phân trang |
| Giải đáp giao thông (FAQ) | ✅ | ✅ | Accordion FAQ, CRUD admin |
| Biển số xe bị phạt nguội | ✅ | ✅ | Danh sách biển số + lọc |
| Quản lý phương tiện cá nhân | ✅ (app) | ✅ | CRUD, gắn với user |
| Danh sách camera giao thông | ✅ | ✅ | Google Maps + danh sách |
| Danh sách trạm CSGT | ✅ | ✅ | Theo khu vực, Google Maps |
| Danh sách trạm thu phí | ✅ | ✅ | Theo tuyến đường |
| Địa điểm đăng kiểm | ✅ | ✅ | Theo khu vực |
| Thống kê vi phạm | ❌ | ✅ | **TÍNH NĂNG MỚI:** Biểu đồ, top |
| Biển báo giao thông | ❌ | ✅ | **TÍNH NĂNG MỚI:** Danh sách + chi tiết |
| Nhúng Google Map | ❌ | ✅ | **TÍNH NĂNG MỚI:** Map tương tác |
| Cảnh báo giao thông | ✅ | ✅ | Admin đăng, user xem |
| Đấu giá biển số | ✅ | ⚠️ | Có thể làm thêm nếu đủ thời gian |
| Tải app mobile | ✅ (landing page) | ❌ | Không cần thiết cho web |

---

## 6. DANH SÁCH CÁC TRANG

### 6.1. Trang Công Khai (Không cần đăng nhập)

| STT | Trang | URL | Mô tả |
|-----|-------|-----|-------|
| 1 | Trang chủ | `/` | Hero + form tra cứu nhanh + tin mới + thống kê nổi bật |
| 2 | Tra cứu phạt nguội | `/tra-cuu` | Form nhập biển số + loại xe → kết quả |
| 3 | Tin tức | `/tin-tuc` | Danh sách bài viết, phân trang |
| 4 | Chi tiết tin tức | `/tin-tuc/{slug}` | Nội dung bài viết + sidebar |
| 5 | Biển báo giao thông | `/bien-bao` | Danh sách biển báo theo nhóm, filter |
| 6 | Chi tiết biển báo | `/bien-bao/{id}` | Hình ảnh + mô tả chi tiết |
| 7 | Bản đồ giao thông | `/ban-do` | Google Maps + marker + filter |
| 8 | Thống kê | `/thong-ke` | Biểu đồ top vi phạm, top địa điểm, top biển số |
| 9 | FAQ | `/faq` | Accordion câu hỏi thường gặp |
| 10 | Giới thiệu | `/gioi-thieu` | Trang tĩnh |
| 11 | Liên hệ | `/lien-he` | Form liên hệ |
| 12 | Đăng nhập | `/dang-nhap` | Form login |
| 13 | Đăng ký | `/dang-ky` | Form register |

### 6.2. Trang Người Dùng (Cần đăng nhập)

| STT | Trang | URL | Mô tả |
|-----|-------|-----|-------|
| 14 | Dashboard cá nhân | `/tai-khoan` | Tổng quan phương tiện, lịch sử tra cứu |
| 15 | Quản lý phương tiện | `/tai-khoan/phuong-tien` | CRUD phương tiện cá nhân |
| 16 | Lịch sử tra cứu | `/tai-khoan/lich-su` | Danh sách các lần tra cứu |

### 6.3. Trang Admin (Cần role admin)

| STT | Trang | URL | Mô tả |
|-----|-------|-----|-------|
| 17 | Dashboard admin | `/admin` | Tổng quan: số user, số xe, số vi phạm, chart |
| 18 | Quản lý người dùng | `/admin/users` | CRUD users |
| 19 | Quản lý vi phạm | `/admin/violations` | CRUD + import/export |
| 20 | Quản lý tin tức | `/admin/news` | CRUD + CKEditor |
| 21 | Quản lý biển báo | `/admin/signs` | CRUD |
| 22 | Quản lý danh mục | `/admin/categories` | CRUD danh mục vi phạm, tin tức |
| 23 | Quản lý địa điểm | `/admin/locations` | CRUD camera, trạm CSGT, trạm thu phí |
| 24 | Quản lý FAQ | `/admin/faq` | CRUD câu hỏi |

---

## 7. BỐ CỤC BÁO CÁO (Dự kiến nộp)

Báo cáo đồ án sẽ bao gồm các chương sau:

- **Chương 1:** Giới thiệu đề tài (lý do chọn đề tài, mục tiêu, đối tượng, phạm vi)
- **Chương 2:** Cơ sở lý thuyết (PHP, MySQL, MVC, Bootstrap, Chart.js, Google Maps API)
- **Chương 3:** Phân tích yêu cầu (Use-case diagram, Actor, chức năng chi tiết)
- **Chương 4:** Thiết kế hệ thống (Kiến trúc MVC, ERD, Database Schema, UI Design)
- **Chương 5:** Triển khai & kết quả (Source code chính, screenshot, mô tả chức năng)
- **Chương 6:** Kết luận & hướng phát triển (Kết quả đạt được, hạn chế, hướng mở rộng)

---

## 8. NGUỒN THAM KHẢO

| # | Nguồn | URL | Mô tả |
|---|-------|-----|-------|
| 1 | VNeTraffic | https://vnetraffic.org/ | Website tham khảo chính về tính năng tra cứu |
| 2 | Cục CSGT | https://csgt.vn/ | Cổng thông tin chính thức |
| 3 | Cục Đăng kiểm VN | https://vr.org.vn/ | Dữ liệu đăng kiểm |
| 4 | Thông tư 73/2024/TT-BCA | - | Quy định xử lý phạt nguội |
| 5 | Traffic Offense System | https://www.sourcecodester.com/php/14909/ | Tham khảo code PHP |
| 6 | GitHub Traffic System | https://github.com/ramsinghal7/traffic_violation_system | Tham khảo cấu trúc DB |

---

> **Tài liệu tiếp theo:** `02-phan-tich-yeu-cau.md` — Phân tích chi tiết từng yêu cầu chức năng và phi chức năng.
