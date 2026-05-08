# KẾ HOẠCH PHÁT TRIỂN

---

## 1. LỘ TRÌNH TỔNG QUAN

Dự án chia làm **4 giai đoạn** (Phase), mỗi giai đoạn kéo dài khoảng 1-2 tuần tùy tiến độ.

```
Phase 1: NỀN MÓNG           Phase 2: CRUD CƠ BẢN         Phase 3: NÂNG CAO          Phase 4: HOÀN THIỆN
(1 tuần)                    (1.5 tuần)                   (1.5 tuần)                  (1 tuần)
───────────────────         ────────────────────         ────────────────────       ────────────────────
• Setup XAMPP              • Auth (login/register)      • Tra cứu phạt nguội        • Google Maps
• Cấu trúc thư mục         • CRUD Users (admin)         • Frontend trang tra cứu    • Biểu đồ thống kê
• Core MVC (Router,        • CRUD Violations (admin)    • Kết quả tra cứu           • Tối ưu responsive
  Controller, Model, DB)   • CRUD News (admin)          • Top biển số vi phạm       • Validate & test
• Database schema          • CRUD Traffic Signs (admin) • Lịch sử tra cứu           • Viết báo cáo
• Layout client + admin    • Client: xem tin tức,       • Tìm kiếm & filter         • Sửa lỗi, polish
• Bootstrap tích hợp         biển báo, trang chủ        • Quản lý phương tiện       • Demo & bảo vệ
                           • Phân quyền auth middleware  • FAQ
                                                        • Import/Export dữ liệu
```

---

## 2. CHI TIẾT TỪNG PHASE

### 2.1. PHASE 1: XÂY DỰNG NỀN MÓNG (Dự kiến: 5-7 ngày)

| Ngày | Công việc | Output | Kiểm tra |
|------|-----------|--------|----------|
| 1 | Cài đặt XAMPP, tạo project folder, git init | Project sẵn sàng | `http://localhost` hoạt động |
| 1-2 | Viết `config/config.php` (DB constants, APP_URL) | File config | DB kết nối thành công |
| 2-3 | Xây dựng Core: `Database.php` (Singleton PDO) | Kết nối DB | Query thử qua phpMyAdmin |
| 3-4 | Xây dựng Core: `Router.php`, `Controller.php` | URL routing hoạt động | `/` → HomeController@index |
| 4-5 | Xây dựng Core: `Model.php` (Base CRUD) | Base model | Test `find()`, `all()` |
| 5 | Xây dựng Core: `Session.php`, `Validator.php`, `Helper.php` | Core hoàn thiện | Đã import đủ namespace |
| 5-6 | Import `database/schema.sql` + `seed.sql` vào MySQL | DB đầy đủ bảng + dữ liệu mẫu | phpMyAdmin kiểm tra |
| 6-7 | Xây dựng layouts: `client.php`, `admin.php`, partials | Layout hoàn chỉnh | Responsive trên mobile |
| 7 | Tích hợp Bootstrap 5, Font Awesome, Chart.js vào assets | Static files sẵn sàng | CDN/local load đúng |

**Milestone Phase 1:** Core MVC hoạt động, database sẵn sàng, layout admin + client hoàn chỉnh.

---

### 2.2. PHASE 2: CRUD CƠ BẢN (Dự kiến: 7-10 ngày)

| Ngày | Công việc | Output | Kiểm tra |
|------|-----------|--------|----------|
| 8-9 | **Auth:** Đăng ký, Đăng nhập, Đăng xuất | AuthController + views | Đăng ký → login → vào tài khoản |
| 9-10 | **Phân quyền:** `requireLogin()`, `requireAdmin()` | Middleware | User thường không vào được /admin |
| 10-11 | **CRUD Users (Admin):** List, Create, Edit, Delete | UserController + views | Thêm/sửa/xóa user thành công |
| 11-12 | **CRUD Violations (Admin):** List, Create, Edit, Delete | ViolationController + views | CRUD vi phạm OK |
| 12-13 | **CRUD News (Admin):** List, Create (CKEditor), Edit, Delete | NewsController + views | Viết bài, upload ảnh, publish |
| 13-14 | **CRUD Traffic Signs (Admin):** List, Create, Edit, Delete | SignController + views | Thêm biển báo + upload ảnh |
| 14-15 | **CRUD Locations (Admin):** List, Create, Edit, Delete | LocationController + views | Nhập tọa độ lat/lng |
| 15-16 | **Client views:** Trang chủ, danh sách tin tức, chi tiết tin | Home, TinTuc views | Hiển thị đúng dữ liệu từ DB |
| 16-17 | **Client views:** Danh sách biển báo, chi tiết biển báo | BienBao views | Filter theo nhóm hoạt động |

**Milestone Phase 2:** Toàn bộ CRUD admin hoạt động, client xem được tin tức & biển báo.

---

### 2.3. PHASE 3: TÍNH NĂNG NÂNG CAO (Dự kiến: 7-10 ngày)

| Ngày | Công việc | Output | Kiểm tra |
|------|-----------|--------|----------|
| 18-19 | **Tra cứu phạt nguội:** Form + xử lý search | TraCuuController | Nhập biển số → kết quả |
| 19-20 | **Validate biển số:** Regex các định dạng biển số VN | Validator::plateNumber() | Test đúng/sai các format |
| 20-21 | **Trang kết quả:** Bảng kết quả + CSS + responsive | Tra cứu view hoàn chỉnh | Kết quả hiển thị rõ ràng |
| 21-22 | **Lịch sử tra cứu:** Lưu + hiển thị lịch sử | SearchHistory model | Sau khi tra cứu → lưu DB |
| 22-23 | **Quản lý phương tiện cá nhân:** CRUD vehicles | TaiKhoanController | Thêm/xóa xe, tra cứu nhanh |
| 23-24 | **Filter & Search:** Tìm kiếm tin tức, lọc biển báo | Search bar + filter tabs | Tìm kiếm trả đúng kết quả |
| 24-25 | **FAQ:** Accordion hiển thị + CRUD admin | FaqController + view | Mở/đóng accordion mượt |
| 25-26 | **Import/Export:** Import CSV vi phạm, export Excel | Import/Export feature | Upload CSV → insert DB |
| 26-27 | **Top biển số vi phạm:** Widget sidebar trang chủ | Widget top plates | Hiển thị top 5-10 |

**Milestone Phase 3:** Tra cứu phạt nguội hoạt động, quản lý phương tiện cá nhân, FAQ.

---

### 2.4. PHASE 4: HOÀN THIỆN & BÁO CÁO (Dự kiến: 5-7 ngày)

| Ngày | Công việc | Output | Kiểm tra |
|------|-----------|--------|----------|
| 28-29 | **Google Maps:** Nhúng map + markers cho locations | BanDoController | Marker hiển thị đúng tọa độ |
| 29-30 | **Thống kê Chart.js:** Line chart, bar chart, doughnut | ThongKeController | Biểu đồ render đúng |
| 30-31 | **Responsive tối ưu:** Test mobile, tablet, desktop | CSS adjustments | Tất cả trang responsive |
| 31-32 | **Validate toàn hệ thống:** Test input forms, edge cases | Bug fixes | Không crash với input lạ |
| 32-33 | **SEO cơ bản:** Meta tags, friendly URL, sitemap.xml | SEO optimization | Title/description mỗi trang |
| 33-34 | **Bảo mật:** Rà soát SQL injection, XSS, CSRF | Security audit | Tất cả query dùng PDO |
| 34-35 | **Seed data đầy đủ:** Thêm dữ liệu mẫu phong phú | seed.sql update | 100+ violations, 20+ news |
| 35-36 | **Viết báo cáo:** Hoàn thiện docs + word | Báo cáo hoàn chỉnh | Đủ chương, đúng format |
| 36-37 | **Test tổng thể:** Golden path + edge cases | Bug fixes cuối | Flow chính mượt |
| 37 | **Demo & chuẩn bị bảo vệ** | Slide + kịch bản demo | Demo không lỗi |

**Milestone Phase 4:** Website hoàn chỉnh, báo cáo hoàn thiện, sẵn sàng bảo vệ.

---

## 3. PHÂN CÔNG THEO MODULE (Nếu làm nhóm 2-3 người)

| Thành viên | Phụ trách | Modules |
|------------|-----------|---------|
| **Member A** | Backend Core | Router, Controller, Model base, Database, Auth, phân quyền |
| **Member B** | Backend Modules + Admin | CRUD Violations, News, Signs, Locations, FAQ, Import/Export |
| **Member C** | Frontend + Client | Layout, Trang chủ, Tra cứu, Tin tức, Biển báo, Bản đồ, Thống kê |

Nếu làm 1 mình thì điều chỉnh thời gian x2 tất cả các phase.

---

## 4. DANH SÁCH KIỂM TRA TRƯỚC KHI NỘP

### 4.1. Chức năng bắt buộc

- [ ] Có ít nhất 5 Entity CRUD đầy đủ (Users, Vehicles, Violations, News, Signs)
- [ ] Tra cứu phạt nguội theo biển số xe (F1.1, F1.2)
- [ ] Hiển thị danh sách tin tức + chi tiết bài viết (F2.1, F2.2)
- [ ] Hiển thị danh sách biển báo + chi tiết (F4.1, F4.2)
- [ ] Đăng ký / Đăng nhập / Đăng xuất (F7.1, F7.2, F7.3)
- [ ] Phân quyền User / Admin (F7.8)
- [ ] Admin Dashboard với số liệu tổng quan (F3.7)

### 4.2. Chức năng nâng cao (chọn ít nhất 3)

- [ ] Biểu đồ thống kê (Chart.js) — top lỗi, top địa điểm, theo thời gian
- [ ] Google Maps với markers (camera, CSGT, trạm thu phí)
- [ ] Quản lý phương tiện cá nhân
- [ ] Lịch sử tra cứu
- [ ] FAQ Accordion
- [ ] Import/Export dữ liệu
- [ ] Tìm kiếm & filter nâng cao

### 4.3. Yêu cầu kỹ thuật

- [ ] Code theo mô hình MVC rõ ràng
- [ ] PDO prepared statement (không SQL injection)
- [ ] Hash password bằng bcrypt
- [ ] Validate input server-side
- [ ] Friendly URL (không có `.php` trên URL)
- [ ] Responsive design (Bootstrap 5)
- [ ] XSS prevention (`htmlspecialchars`)
- [ ] Session-based authentication

### 4.4. Yêu cầu báo cáo

- [ ] Chương 1: Giới thiệu đề tài
- [ ] Chương 2: Cơ sở lý thuyết
- [ ] Chương 3: Phân tích yêu cầu (có Use-case diagram)
- [ ] Chương 4: Thiết kế hệ thống (ERD, Database Schema, Kiến trúc)
- [ ] Chương 5: Triển khai & kết quả (Screenshots chính + mô tả)
- [ ] Chương 6: Kết luận & hướng phát triển

---

## 5. CÔNG CỤ HỖ TRỢ PHÁT TRIỂN

| Công cụ | Mục đích | Link |
|---------|----------|------|
| XAMPP | Web server local | https://www.apachefriends.org/ |
| VS Code | Code editor | https://code.visualstudio.com/ |
| phpMyAdmin | Quản lý MySQL | Đi kèm XAMPP (http://localhost/phpmyadmin) |
| Git | Version control | https://git-scm.com/ |
| GitHub | Lưu trữ code | https://github.com/ |
| Postman | Test API (nếu cần) | https://www.postman.com/ |
| Draw.io | Vẽ ERD, Use-case | https://app.diagrams.net/ |
| Figma | Thiết kế UI (nếu cần) | https://www.figma.com/ |

---

## 6. ƯỚC TÍNH THỜI GIAN

| Phase | Nội dung | Ngày công (1 người) | Ngày công (nhóm 2-3) |
|-------|----------|:---:|:---:|
| Phase 1 | Nền móng (Core MVC + DB + Layout) | 5-7 | 3-4 |
| Phase 2 | CRUD cơ bản (Auth + Admin CRUD + Client) | 7-10 | 5-7 |
| Phase 3 | Nâng cao (Tra cứu + Thống kê + Map) | 7-10 | 5-7 |
| Phase 4 | Hoàn thiện (Test + Báo cáo) | 5-7 | 4-5 |
| **Tổng** | | **24-34 ngày** | **17-23 ngày** |

---

## 7. RỦI RO & GIẢI PHÁP

| Rủi ro | Xác suất | Tác động | Giải pháp |
|--------|:---:|:---:|-----------|
| Google Maps API cần billing | Cao | Trung bình | Dùng iframe embed miễn phí, hoặc dùng Leaflet (OpenStreetMap) thay thế |
| CKEditor nặng, khó cài | Thấp | Thấp | Dùng phiên bản CDN đơn giản, hoặc thay bằng SimpleMDE / Quill |
| Import CSV lỗi encoding | Trung bình | Thấp | Yêu cầu file UTF-8, thêm bước detect encoding |
| Không đủ thời gian | Trung bình | Cao | Ưu tiên P0 → P1 → P2, cắt tính năng P2 nếu chậm tiến độ |
| Dữ liệu phạt nguội không có thật | Cao | Thấp | Dùng dữ liệu mẫu (seed data) tự tạo, không cần API thật |

---

## 8. HƯỚNG DẪN KHỞI ĐỘNG NHANH

```bash
# 1. Clone project (nếu có)
git clone <repo-url> traffic-violation-lookup
cd traffic-violation-lookup

# 2. Copy vào htdocs của XAMPP
cp -r . E:/XAMPP/htdocs/traffic-lookup/

# 3. Import database
# Mở http://localhost/phpmyadmin
# Tạo database: traffic_violation_db
# Import: database/schema.sql
# Import: database/seed.sql

# 4. Cấu hình database
# Sửa config/config.php:
#   db_host = localhost
#   db_name = traffic_violation_db
#   db_user = root
#   db_pass = ''

# 5. Cấu hình Apache DocumentRoot (httpd.conf)
# DocumentRoot "E:/XAMPP/htdocs/traffic-lookup/public"
# <Directory "E:/XAMPP/htdocs/traffic-lookup/public">

# 6. Mở trình duyệt
# http://localhost/
# Admin: admin@traffic.vn / admin123
```

---

## TỔNG KẾT BỘ TÀI LIỆU

Sau khi đọc xong 6 tài liệu trên, bạn sẽ có:

1. **Tổng quan** về dự án, phạm vi, công nghệ
2. **Yêu cầu chi tiết** từng module, ưu tiên P0-P2
3. **Thiết kế database** đầy đủ 14 bảng + SQL schema + seed data
4. **Thiết kế giao diện** với wireframe ASCII chi tiết từng trang
5. **Kiến trúc hệ thống** MVC, cấu trúc thư mục, routing, security
6. **Kế hoạch phát triển** 4 phase, phân công, checklist, ước tính thời gian

**Bước tiếp theo:** Bạn đọc và review bộ tài liệu này. Sau khi chốt, tôi sẽ bắt đầu code theo từng phase.
