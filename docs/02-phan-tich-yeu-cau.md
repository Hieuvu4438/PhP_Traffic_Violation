# PHÂN TÍCH YÊU CẦU CHI TIẾT

---

## 1. SƠ ĐỒ USE-CASE TỔNG QUÁT

```
                         ┌─────────────────────────────────────┐
                         │         HỆ THỐNG WEBSITE            │
                         │   TRA CỨU PHƯƠNG TIỆN VI PHẠM GT   │
                         └─────────────────────────────────────┘
                                      │
          ┌───────────────────────────┼───────────────────────────┐
          │                           │                           │
    ┌─────┴─────┐              ┌──────┴──────┐            ┌──────┴──────┐
    │  KHÁCH    │              │  NGƯỜI DÙNG │            │    ADMIN    │
    │ (Guest)   │              │   (User)    │            │   (Admin)   │
    └─────┬─────┘              └──────┬──────┘            └──────┬──────┘
          │                           │                           │
    ┌─────┼─────────────────┐  ┌──────┼──────────────┐  ┌────────┼──────────────┐
    │• Tra cứu phạt nguội   │  │• (Tất cả quyền Guest)│  │• CRUD Người dùng      │
    │• Xem tin tức          │  │• Đăng ký/Đăng nhập   │  │• CRUD Vi phạm         │
    │• Xem biển báo         │  │• Quản lý phương tiện │  │• CRUD Tin tức         │
    │• Xem bản đồ           │  │• Xem lịch sử tra cứu │  │• CRUD Biển báo        │
    │• Xem thống kê         │  │• Nhận thông báo      │  │• CRUD Địa điểm        │
    │• Xem FAQ              │  │• Gửi phản ánh        │  │• CRUD FAQ             │
    │• Xem trang giới thiệu │  │                       │  │• Xem Dashboard        │
    │• Đăng ký tài khoản    │  │                       │  │• Xem thống kê chi tiết│
    │• Đăng nhập            │  │                       │  │• Import/Export dữ liệu│
    └───────────────────────┘  └───────────────────────┘  └───────────────────────┘
```

---

## 2. YÊU CẦU CHỨC NĂNG CHI TIẾT

### 2.1. MODULE 1: TRA CỨU PHẠT NGUỘI [Module trọng tâm]

#### Mô tả:
Người dùng nhập biển số xe và chọn loại phương tiện, hệ thống trả về danh sách các vi phạm giao thông (phạt nguội) liên quan đến biển số đó.

#### Chức năng chi tiết:

| ID | Chức năng | Actor | Mô tả | Priority |
|----|-----------|-------|-------|----------|
| F1.1 | Form tra cứu | Guest, User | Input: biển số (text) + loại xe (select: Ô tô/Xe máy/Xe máy điện) + Captcha | P0 |
| F1.2 | Hiển thị kết quả | Guest, User | Bảng danh sách vi phạm: thời gian, địa điểm, hành vi, mức phạt, trạng thái | P0 |
| F1.3 | Xuất kết quả | User | In / xuất PDF kết quả tra cứu | P2 |
| F1.4 | Lưu lịch sử tra cứu | User | Tự động lưu vào lịch sử khi đã đăng nhập | P1 |
| F1.5 | Validate biển số | Hệ thống | Kiểm tra định dạng biển số hợp lệ (VD: 30A-12345, 59F1-12345,...) | P1 |
| F1.6 | Gợi ý biển số | Hệ thống | Tự động gợi ý các biển số đã từng tra cứu nhiều | P2 |

#### Giao diện dự kiến:

```
┌──────────────────────────────────────────────┐
│         TRA CỨU PHẠT NGUỘI TOÀN QUỐC         │
│                                              │
│   ┌─────────────────────────┐  ┌──────────┐  │
│   │  Nhập biển số xe        │  │ Ô tô  ▾  │  │
│   │  VD: 30A-12345          │  └──────────┘  │
│   └─────────────────────────┘                │
│                                              │
│   ┌──────────────────────────────┐           │
│   │  🔍  TRA CỨU PHẠT NGUỘI      │           │
│   └──────────────────────────────┘           │
│                                              │
│   Nguồn dữ liệu: Cập nhật từ CSGT & Đăng kiểm │
└──────────────────────────────────────────────┘

Sau khi tra cứu:

┌──────────────────────────────────────────────────────────────┐
│  KẾT QUẢ TRA CỨU CHO BIỂN SỐ: 30A-12345                      │
│  Loại xe: Ô tô con  |  Ngày cập nhật: 08/05/2026             │
├────┬──────────────┬──────────────────┬────────────┬──────────┤
│ STT│ Thời gian    │ Địa điểm          │ Hành vi    │ Mức phạt  │
├────┼──────────────┼──────────────────┼────────────┼──────────┤
│ 1  │ 15/04/2026   │ Nguyễn Trãi, HN  │ Vượt đèn đỏ│ 4-6 tr   │
│ 2  │ 01/03/2026   │ Phạm Văn Đồng, HN│ Chạy quá tốc│ 3-5 tr  │
│ ...│ ...          │ ...              │ ...        │ ...       │
└────┴──────────────┴──────────────────┴────────────┴──────────┘
│  Tổng: 2 vi phạm  |  Đã xử lý: 1  |  Chưa xử lý: 1          │
└──────────────────────────────────────────────────────────────┘
```

---

### 2.2. MODULE 2: TIN TỨC GIAO THÔNG

#### Mô tả:
Hệ thống blog tin tức về giao thông, cho phép admin đăng bài và người dùng đọc.

#### Chức năng chi tiết:

| ID | Chức năng | Actor | Mô tả | Priority |
|----|-----------|-------|-------|----------|
| F2.1 | Danh sách tin tức | Guest, User | Hiển thị danh sách bài viết, phân trang (9 bài/trang), grid 3 cột | P0 |
| F2.2 | Chi tiết bài viết | Guest, User | Nội dung đầy đủ, ảnh, ngày đăng, tác giả, sidebar bài liên quan | P0 |
| F2.3 | Lọc theo danh mục | Guest, User | Filter tin theo danh mục: Tin tức, Giải đáp, Thông báo, Luật GT | P1 |
| F2.4 | Tìm kiếm tin tức | Guest, User | Search bar tìm theo tiêu đề/nội dung | P1 |
| F2.5 | CRUD bài viết | Admin | Tạo/sửa/xóa bài viết với CKEditor, upload ảnh, chọn danh mục | P0 |
| F2.6 | Quản lý danh mục | Admin | CRUD danh mục tin tức | P1 |

#### Database: Bảng `news`

| Cột | Kiểu dữ liệu | Mô tả |
|-----|-------------|-------|
| id | INT (PK, AUTO_INCREMENT) | ID bài viết |
| title | VARCHAR(255) | Tiêu đề |
| slug | VARCHAR(255) | URL slug |
| content | LONGTEXT | Nội dung (HTML từ CKEditor) |
| thumbnail | VARCHAR(255) | Đường dẫn ảnh thumbnail |
| category_id | INT (FK) | ID danh mục |
| author_id | INT (FK) | ID admin viết bài |
| status | ENUM('draft','published') | Trạng thái |
| views | INT DEFAULT 0 | Lượt xem |
| created_at | DATETIME | Ngày tạo |
| updated_at | DATETIME | Ngày cập nhật |

---

### 2.3. MODULE 3: THỐNG KÊ VI PHẠM [Tính năng trọng tâm nâng cao]

#### Mô tả:
Hiển thị các thống kê trực quan về tình hình vi phạm giao thông dưới dạng biểu đồ.

#### Chức năng chi tiết:

| ID | Chức năng | Actor | Mô tả | Priority |
|----|-----------|-------|-------|----------|
| F3.1 | Top lỗi vi phạm | Guest, User | Biểu đồ cột/horizontal bar top 10 lỗi vi phạm phổ biến nhất | P0 |
| F3.2 | Top địa điểm vi phạm | Guest, User | Biểu đồ cột top 10 địa điểm có nhiều vi phạm nhất | P0 |
| F3.3 | Top biển số vi phạm | Guest, User | Danh sách top biển số có nhiều vi phạm nhất (ẩn 1 phần) | P1 |
| F3.4 | Thống kê theo thời gian | Guest, User | Biểu đồ đường: số vụ vi phạm theo tháng trong năm | P1 |
| F3.5 | Thống kê theo khu vực | Guest, User | Biểu đồ tròn: tỉ lệ vi phạm theo tỉnh/thành phố | P2 |
| F3.6 | Lọc thống kê | Guest, User | Filter theo khoảng thời gian, loại phương tiện, khu vực | P1 |
| F3.7 | Dashboard admin | Admin | Tổng quan: tổng số vi phạm, tổng user, tổng phương tiện, chart | P0 |

#### Loại biểu đồ sử dụng (Chart.js):

| Biểu đồ | Dữ liệu | Loại chart |
|---------|---------|------------|
| Top lỗi vi phạm | Tên lỗi + số lượng | `bar` (ngang) |
| Top địa điểm | Tên địa điểm + số lượng | `bar` (dọc) |
| Vi phạm theo thời gian | Tháng + số vụ | `line` |
| Phân bố khu vực | Tỉnh + tỉ lệ % | `doughnut` / `pie` |

---

### 2.4. MODULE 4: BIỂN BÁO GIAO THÔNG [Tính năng nâng cao mới]

#### Mô tả:
Thư viện tra cứu biển báo giao thông Việt Nam, phân loại theo nhóm.

#### Phân loại biển báo:

| Nhóm | Tên nhóm | Số lượng (ước tính) | Ký hiệu |
|------|----------|---------------------|---------|
| 1 | Biển báo cấm | ~40 biển | P.101 - P.140 |
| 2 | Biển báo nguy hiểm | ~45 biển | W.201 - W.245 |
| 3 | Biển hiệu lệnh | ~10 biển | R.301 - R.310 |
| 4 | Biển chỉ dẫn | ~50 biển | S.401 - S.450 |
| 5 | Biển phụ | ~15 biển | S.501 - S.515 |
| 6 | Vạch kẻ đường | ~10 loại | - |

#### Chức năng chi tiết:

| ID | Chức năng | Actor | Mô tả | Priority |
|----|-----------|-------|-------|----------|
| F4.1 | Danh sách biển báo | Guest, User | Grid card theo nhóm, có filter tab | P0 |
| F4.2 | Chi tiết biển báo | Guest, User | Ảnh lớn + tên + mã hiệu + mô tả ý nghĩa | P0 |
| F4.3 | Tìm kiếm biển báo | Guest, User | Search theo tên/mã hiệu | P1 |
| F4.4 | CRUD biển báo | Admin | Thêm/sửa/xóa biển báo (upload ảnh) | P0 |

#### Database: Bảng `traffic_signs`

| Cột | Kiểu dữ liệu | Mô tả |
|-----|-------------|-------|
| id | INT (PK, AUTO_INCREMENT) | ID biển báo |
| sign_code | VARCHAR(20) | Mã hiệu (VD: P.101, W.201) |
| name | VARCHAR(255) | Tên biển báo |
| group_id | INT (FK) | ID nhóm biển báo |
| image | VARCHAR(255) | Đường dẫn ảnh |
| description | TEXT | Mô tả ý nghĩa |
| created_at | DATETIME | Ngày tạo |
| updated_at | DATETIME | Ngày cập nhật |

---

### 2.5. MODULE 5: BẢN ĐỒ GOOGLE MAPS [Tính năng nâng cao mới]

#### Mô tả:
Nhúng Google Maps hiển thị vị trí các điểm liên quan đến giao thông.

#### Chức năng chi tiết:

| ID | Chức năng | Actor | Mô tả | Priority |
|----|-----------|-------|-------|----------|
| F5.1 | Bản đồ camera giao thông | Guest, User | Marker vị trí camera, click xem thông tin | P1 |
| F5.2 | Bản đồ trụ sở CSGT | Guest, User | Marker trụ sở CSGT các quận/huyện | P1 |
| F5.3 | Bản đồ trạm thu phí | Guest, User | Marker trạm thu phí BOT | P2 |
| F5.4 | Bản đồ điểm đăng kiểm | Guest, User | Marker trung tâm đăng kiểm | P2 |
| F5.5 | Filter theo loại điểm | Guest, User | Checkbox/tab lọc hiển thị từng loại marker | P1 |
| F5.6 | Tìm kiếm địa điểm | Guest, User | Search box tìm theo tên/địa chỉ | P2 |
| F5.7 | Chỉ đường | Guest, User | Click "Chỉ đường" mở Google Maps direction | P2 |
| F5.8 | CRUD địa điểm | Admin | Thêm/sửa/xóa điểm trên bản đồ (tọa độ lat/lng) | P1 |

#### Database: Bảng `locations`

| Cột | Kiểu dữ liệu | Mô tả |
|-----|-------------|-------|
| id | INT (PK, AUTO_INCREMENT) | ID địa điểm |
| name | VARCHAR(255) | Tên địa điểm |
| type | ENUM('camera','csgt','toll','inspection') | Loại địa điểm |
| address | VARCHAR(500) | Địa chỉ |
| latitude | DECIMAL(10,7) | Vĩ độ |
| longitude | DECIMAL(10,7) | Kinh độ |
| description | TEXT | Mô tả thêm |
| status | TINYINT DEFAULT 1 | Trạng thái hiển thị |
| created_at | DATETIME | Ngày tạo |

---

### 2.6. MODULE 6: QUẢN LÝ PHƯƠNG TIỆN CÁ NHÂN

#### Mô tả:
Người dùng đã đăng nhập có thể thêm phương tiện của mình vào hệ thống để theo dõi.

#### Chức năng chi tiết:

| ID | Chức năng | Actor | Mô tả | Priority |
|----|-----------|-------|-------|----------|
| F6.1 | Danh sách phương tiện | User | Hiển thị các xe đã thêm của user | P0 |
| F6.2 | Thêm phương tiện | User | Form: biển số, loại xe, hãng, đời xe, số khung, số máy | P0 |
| F6.3 | Sửa phương tiện | User | Cập nhật thông tin xe | P1 |
| F6.4 | Xóa phương tiện | User | Xóa xe khỏi danh sách quản lý | P1 |
| F6.5 | Tra cứu nhanh | User | Nút "Tra cứu ngay" cho từng xe trong danh sách | P1 |

#### Database: Bảng `vehicles`

| Cột | Kiểu dữ liệu | Mô tả |
|-----|-------------|-------|
| id | INT (PK, AUTO_INCREMENT) | ID phương tiện |
| user_id | INT (FK) | ID chủ sở hữu |
| plate_number | VARCHAR(20) | Biển số xe |
| vehicle_type | ENUM('car','motorcycle','electric_motorcycle') | Loại xe |
| brand | VARCHAR(100) | Hãng xe |
| model | VARCHAR(100) | Đời xe |
| chassis_number | VARCHAR(50) | Số khung |
| engine_number | VARCHAR(50) | Số máy |
| created_at | DATETIME | Ngày thêm |
| updated_at | DATETIME | Ngày cập nhật |

---

### 2.7. MODULE 7: NGƯỜI DÙNG & XÁC THỰC

#### Mô tả:
Hệ thống đăng ký, đăng nhập, phân quyền.

#### Chức năng chi tiết:

| ID | Chức năng | Actor | Mô tả | Priority |
|----|-----------|-------|-------|----------|
| F7.1 | Đăng ký | Guest | Form: họ tên, email, phone, password, confirm password | P0 |
| F7.2 | Đăng nhập | Guest | Form: email/phone + password + "Ghi nhớ" | P0 |
| F7.3 | Đăng xuất | User, Admin | Xóa session, chuyển về trang chủ | P0 |
| F7.4 | Quên mật khẩu | Guest | Gửi email reset password | P2 |
| F7.5 | Đổi mật khẩu | User, Admin | Form đổi password trong tài khoản | P1 |
| F7.6 | Cập nhật profile | User, Admin | Sửa thông tin cá nhân | P1 |
| F7.7 | CRUD users | Admin | Quản lý danh sách user, khóa/mở khóa tài khoản | P1 |
| F7.8 | Phân quyền | Admin | Gán role: user / admin | P1 |

#### Database: Bảng `users`

| Cột | Kiểu dữ liệu | Mô tả |
|-----|-------------|-------|
| id | INT (PK, AUTO_INCREMENT) | ID người dùng |
| fullname | VARCHAR(100) | Họ tên |
| email | VARCHAR(100) UNIQUE | Email |
| phone | VARCHAR(15) UNIQUE | Số điện thoại |
| password | VARCHAR(255) | Mật khẩu (hash bcrypt) |
| role | ENUM('user','admin') DEFAULT 'user' | Vai trò |
| avatar | VARCHAR(255) NULL | Ảnh đại diện |
| status | TINYINT DEFAULT 1 | 1=active, 0=banned |
| reset_token | VARCHAR(100) NULL | Token reset password |
| reset_expires | DATETIME NULL | Hạn token |
| created_at | DATETIME | Ngày đăng ký |
| updated_at | DATETIME | Ngày cập nhật |

---

### 2.8. MODULE 8: FAQ / GIẢI ĐÁP GIAO THÔNG

#### Chức năng chi tiết:

| ID | Chức năng | Actor | Mô tả | Priority |
|----|-----------|-------|-------|----------|
| F8.1 | Hiển thị FAQ | Guest, User | Accordion, nhóm theo chủ đề | P1 |
| F8.2 | CRUD FAQ | Admin | Thêm/sửa/xóa câu hỏi, phân nhóm | P1 |

#### Database: Bảng `faqs`

| Cột | Kiểu dữ liệu | Mô tả |
|-----|-------------|-------|
| id | INT (PK, AUTO_INCREMENT) | ID |
| question | VARCHAR(500) | Câu hỏi |
| answer | TEXT | Câu trả lời |
| category | VARCHAR(100) | Nhóm chủ đề |
| sort_order | INT DEFAULT 0 | Thứ tự hiển thị |
| status | TINYINT DEFAULT 1 | Hiển thị/ẩn |

---

### 2.9. MODULE 9: PHẢN ÁNH VI PHẠM

#### Chức năng chi tiết:

| ID | Chức năng | Actor | Mô tả | Priority |
|----|-----------|-------|-------|----------|
| F9.1 | Gửi phản ánh | User | Form: mô tả, địa điểm, thời gian, upload ảnh | P2 |
| F9.2 | Danh sách phản ánh | User | Xem các phản ánh đã gửi và trạng thái | P2 |
| F9.3 | Quản lý phản ánh | Admin | Xem, cập nhật trạng thái phản ánh | P2 |

---

### 2.10. MODULE 10: CẢNH BÁO GIAO THÔNG

#### Chức năng chi tiết:

| ID | Chức năng | Actor | Mô tả | Priority |
|----|-----------|-------|-------|----------|
| F10.1 | Danh sách cảnh báo | Guest, User | Các cảnh báo: tai nạn, ùn tắc, công trình, thời tiết xấu | P2 |
| F10.2 | CRUD cảnh báo | Admin | Tạo/sửa/xóa cảnh báo, đặt thời gian hiệu lực | P2 |

---

## 3. YÊU CẦU PHI CHỨC NĂNG

| ID | Yêu cầu | Mô tả | Mức độ |
|----|---------|-------|--------|
| NF1 | Responsive | Giao diện hoạt động tốt trên mobile, tablet, desktop | Bắt buộc |
| NF2 | Bảo mật | Hash password (bcrypt), chống SQL injection (PDO), XSS (htmlspecialchars), CSRF (token) | Bắt buộc |
| NF3 | Hiệu năng | Thời gian tải trang < 3 giây, tra cứu < 2 giây | Bắt buộc |
| NF4 | SEO cơ bản | Meta title/description, friendly URL, sitemap, schema markup | Nên có |
| NF5 | UI/UX | Giao diện thân thiện, dễ sử dụng, tiếng Việt | Bắt buộc |
| NF6 | Tương thích | Chrome, Firefox, Edge, Safari (2 phiên bản gần nhất) | Bắt buộc |
| NF7 | Code quality | Tuân thủ PSR-12, comment tiếng Việt, tổ chức MVC rõ ràng | Bắt buộc |
| NF8 | Backup | Database backup định kỳ (thủ công qua phpMyAdmin) | Khuyến khích |

---

## 4. MA TRẬN ƯU TIÊN

```
P0 (Bắt buộc - MVP)          P1 (Quan trọng)              P2 (Nên có nếu đủ thời gian)
─────────────────────    ────────────────────────    ───────────────────────────────
• Tra cứu phạt nguội     • Lưu lịch sử tra cứu       • Xuất PDF kết quả
• Tin tức (CRUD)         • Lọc tin theo danh mục     • Gợi ý biển số
• Thống kê cơ bản        • Tìm kiếm tin tức          • Thống kê theo khu vực (pie)
• Biển báo (CRUD)        • Quản lý danh mục          • Bản đồ trạm thu phí
• CRUD phương tiện       • Top biển số vi phạm       • Bản đồ điểm đăng kiểm
• Auth (đăng ký/login)   • Thống kê theo thời gian    • Tìm kiếm địa điểm (map)
• Dashboard admin        • Bản đồ CSGT + Camera      • Chỉ đường Google Maps
• Giao diện responsive   • FAQ + CRUD                • Quên mật khẩu
                         • Cập nhật profile          • Phản ánh vi phạm
                         • Phân quyền admin/user      • Cảnh báo giao thông
                         • Validate biển số
```

---

> **Tài liệu tiếp theo:** `03-thiet-ke-co-so-du-lieu.md` — Sơ đồ ERD, schema chi tiết, mối quan hệ giữa các bảng.
