# THIẾT KẾ CƠ SỞ DỮ LIỆU

---

## 1. SƠ ĐỒ QUAN HỆ THỰC THỂ (ERD)

```
┌──────────────┐       ┌─────────────────┐       ┌──────────────────┐
│    users     │       │    vehicles     │       │   violations     │
├──────────────┤       ├─────────────────┤       ├──────────────────┤
│ id (PK)      │──1:N──│ id (PK)         │       │ id (PK)          │
│ fullname     │       │ user_id (FK)    │       │ plate_number     │
│ email        │       │ plate_number    │       │ vehicle_type     │
│ phone        │       │ vehicle_type    │       │ violation_date   │
│ password     │       │ brand           │       │ location_id (FK) │
│ role         │       │ model           │       │ offense_id (FK)  │
│ avatar       │       │ chassis_number  │       │ status           │
│ status       │       │ engine_number   │       │ fine_amount      │
│ created_at   │       │ created_at      │       │ decision_number  │
│ updated_at   │       │ updated_at      │       │ created_at       │
└──────────────┘       └─────────────────┘       │ updated_at       │
       │                                         └───────┬──────────┘
       │                                                 │
       │                                         ┌───────┴──────────┐
       │                                         │                  │
       │                                  ┌──────┴──────┐   ┌──────┴──────┐
       │                                  │  locations  │   │  offenses   │
       │                                  ├─────────────┤   ├─────────────┤
       │                                  │ id (PK)     │   │ id (PK)     │
       │                                  │ name        │   │ name        │
       │                                  │ type        │   │ description │
       │                                  │ address     │   │ penalty     │
       │                                  │ latitude    │   │ category_id │
       │                                  │ longitude   │   │ created_at  │
       │                                  │ description │   └─────────────┘
       │                                  │ status      │
       │                                  └─────────────┘
       │
       │       ┌──────────────────┐       ┌──────────────────┐
       ├──1:N──│      news        │       │  search_history  │
       │       ├──────────────────┤       ├──────────────────┤
       │       │ id (PK)          │       │ id (PK)          │
       │       │ title            │       │ user_id (FK)     │
       │       │ slug             │       │ plate_number     │
       │       │ content          │       │ vehicle_type     │
       │       │ thumbnail        │       │ result_count     │
       │       │ category_id (FK) │       │ searched_at      │
       │       │ author_id (FK)   │       └──────────────────┘
       │       │ status           │
       │       │ views            │       ┌──────────────────┐
       │       │ created_at       │       │ traffic_signs    │
       │       │ updated_at       │       ├──────────────────┤
       │       └──────────────────┘       │ id (PK)          │
       │                                  │ sign_code        │
       │       ┌──────────────────┐       │ name             │
       │       │ news_categories  │       │ group_id (FK)    │
       │       ├──────────────────┤       │ image            │
       │       │ id (PK)          │       │ description      │
       │       │ name             │       │ created_at       │
       │       │ slug             │       │ updated_at       │
       │       └──────────────────┘       └──────────────────┘
       │
       │       ┌──────────────────┐       ┌──────────────────┐
       │       │      faqs        │       │  sign_groups     │
       │       ├──────────────────┤       ├──────────────────┤
       │       │ id (PK)          │       │ id (PK)          │
       │       │ question         │       │ name             │
       │       │ answer           │       │ sign_prefix      │
       │       │ category         │       │ sort_order       │
       │       │ sort_order       │       └──────────────────┘
       │       │ status           │
       │       └──────────────────┘       ┌──────────────────┐
       │                                  │ offense_categories│
       │       ┌──────────────────┐       ├──────────────────┤
       │       │ traffic_alerts   │       │ id (PK)          │
       │       ├──────────────────┤       │ name             │
       │       │ id (PK)          │       │ description      │
       │       │ title            │       └──────────────────┘
       │       │ content          │
       │       │ alert_type       │
       │       │ expires_at       │
       │       │ created_by (FK)  │
       │       │ status           │
       │       │ created_at       │
       │       └──────────────────┘
       │
       ▼
┌──────────────────┐
│ contact_messages │
├──────────────────┤
│ id (PK)          │
│ name             │
│ email            │
│ subject          │
│ message          │
│ is_read          │
│ created_at       │
└──────────────────┘
```

---

## 2. SCHEMA CHI TIẾT (SQL)

### 2.1. Database: `traffic_violation_db`

```sql
-- =============================================
-- Tạo database
-- =============================================
CREATE DATABASE IF NOT EXISTS traffic_violation_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE traffic_violation_db;
```

### 2.2. Bảng `users` — Người dùng hệ thống

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'bcrypt hash',
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    avatar VARCHAR(255) NULL,
    status TINYINT NOT NULL DEFAULT 1 COMMENT '1=active, 0=banned',
    reset_token VARCHAR(100) NULL,
    reset_expires DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.3. Bảng `vehicles` — Phương tiện

```sql
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plate_number VARCHAR(20) NOT NULL,
    vehicle_type ENUM('car', 'motorcycle', 'electric_motorcycle') NOT NULL,
    brand VARCHAR(100) NULL,
    model VARCHAR(100) NULL,
    chassis_number VARCHAR(50) NULL,
    engine_number VARCHAR(50) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_plate (plate_number),
    INDEX idx_vehicle_type (vehicle_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.4. Bảng `offense_categories` — Danh mục lỗi vi phạm

```sql
CREATE TABLE offense_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Tên danh mục lỗi',
    description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.5. Bảng `offenses` — Danh sách lỗi vi phạm

```sql
CREATE TABLE offenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Tên lỗi vi phạm',
    description TEXT NULL COMMENT 'Mô tả chi tiết',
    penalty VARCHAR(255) NULL COMMENT 'Mức phạt (VD: 4.000.000 - 6.000.000 VNĐ)',
    category_id INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (category_id) REFERENCES offense_categories(id) ON DELETE SET NULL,
    INDEX idx_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.6. Bảng `locations` — Địa điểm (camera, CSGT, trạm thu phí, đăng kiểm)

```sql
CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('camera', 'csgt', 'toll', 'inspection') NOT NULL,
    address VARCHAR(500) NULL,
    latitude DECIMAL(10,7) NOT NULL,
    longitude DECIMAL(10,7) NOT NULL,
    description TEXT NULL,
    status TINYINT NOT NULL DEFAULT 1 COMMENT '1=active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_coordinates (latitude, longitude)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.7. Bảng `violations` — Vi phạm giao thông (phạt nguội)

```sql
CREATE TABLE violations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plate_number VARCHAR(20) NOT NULL COMMENT 'Biển số xe',
    vehicle_type ENUM('car', 'motorcycle', 'electric_motorcycle') NOT NULL,
    violation_date DATETIME NOT NULL COMMENT 'Thời gian vi phạm',
    location_id INT NULL COMMENT 'Địa điểm vi phạm',
    offense_id INT NULL COMMENT 'Lỗi vi phạm',
    status ENUM('pending', 'processed', 'paid') NOT NULL DEFAULT 'pending'
        COMMENT 'pending=chưa xử lý, processed=đã xử lý, paid=đã nộp phạt',
    fine_amount VARCHAR(100) NULL COMMENT 'Mức phạt cụ thể',
    decision_number VARCHAR(50) NULL COMMENT 'Số quyết định xử phạt',
    decision_date DATE NULL COMMENT 'Ngày ra quyết định',
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL,
    FOREIGN KEY (offense_id) REFERENCES offenses(id) ON DELETE SET NULL,
    INDEX idx_plate (plate_number),
    INDEX idx_violation_date (violation_date),
    INDEX idx_status (status),
    INDEX idx_vehicle_type (vehicle_type),
    INDEX idx_lookup (plate_number, vehicle_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.8. Bảng `news_categories` — Danh mục tin tức

```sql
CREATE TABLE news_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.9. Bảng `news` — Tin tức

```sql
CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL COMMENT 'Nội dung HTML',
    thumbnail VARCHAR(255) NULL,
    category_id INT NULL,
    author_id INT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    views INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (category_id) REFERENCES news_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_category (category_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.10. Bảng `traffic_sign_groups` — Nhóm biển báo

```sql
CREATE TABLE traffic_sign_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Tên nhóm biển báo',
    sign_prefix VARCHAR(10) NOT NULL COMMENT 'Tiền tố mã hiệu (P, W, R, S)',
    sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.11. Bảng `traffic_signs` — Biển báo giao thông

```sql
CREATE TABLE traffic_signs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sign_code VARCHAR(20) NOT NULL COMMENT 'Mã hiệu (P.101, W.201,...)',
    name VARCHAR(255) NOT NULL COMMENT 'Tên biển báo',
    group_id INT NULL,
    image VARCHAR(255) NULL,
    description TEXT NULL COMMENT 'Ý nghĩa biển báo',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (group_id) REFERENCES traffic_sign_groups(id) ON DELETE SET NULL,
    INDEX idx_code (sign_code),
    INDEX idx_group (group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.12. Bảng `faqs` — Câu hỏi thường gặp

```sql
CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(500) NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(100) NULL COMMENT 'Nhóm chủ đề',
    sort_order INT NOT NULL DEFAULT 0,
    status TINYINT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.13. Bảng `search_history` — Lịch sử tra cứu

```sql
CREATE TABLE search_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL COMMENT 'NULL nếu khách chưa đăng nhập',
    plate_number VARCHAR(20) NOT NULL,
    vehicle_type ENUM('car', 'motorcycle', 'electric_motorcycle') NOT NULL,
    result_count INT NOT NULL DEFAULT 0 COMMENT 'Số vi phạm tìm thấy',
    ip_address VARCHAR(45) NULL,
    searched_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_date (searched_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.14. Bảng `traffic_alerts` — Cảnh báo giao thông

```sql
CREATE TABLE traffic_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NULL,
    alert_type ENUM('accident', 'congestion', 'construction', 'weather', 'other')
        NOT NULL DEFAULT 'other',
    expires_at DATETIME NULL COMMENT 'Thời gian hết hiệu lực',
    created_by INT NULL,
    status TINYINT NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_type (alert_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.15. Bảng `contact_messages` — Tin nhắn liên hệ

```sql
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NULL,
    message TEXT NOT NULL,
    is_read TINYINT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 3. DỮ LIỆU MẪU (SEED DATA)

### 3.1. Admin mặc định
```sql
-- Password: admin123 (bcrypt hash)
INSERT INTO users (fullname, email, password, role) VALUES
('Administrator', 'admin@traffic.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
```

### 3.2. Nhóm biển báo
```sql
INSERT INTO traffic_sign_groups (name, sign_prefix, sort_order) VALUES
('Biển báo cấm', 'P', 1),
('Biển báo nguy hiểm', 'W', 2),
('Biển hiệu lệnh', 'R', 3),
('Biển chỉ dẫn', 'S', 4),
('Biển phụ', 'S', 5);
```

### 3.3. Danh mục lỗi vi phạm
```sql
INSERT INTO offense_categories (name, description) VALUES
('Vi phạm tốc độ', 'Các lỗi liên quan đến chạy quá tốc độ quy định'),
('Vi phạm tín hiệu giao thông', 'Vượt đèn đỏ, không tuân thủ biển báo, vạch kẻ đường'),
('Vi phạm nồng độ cồn', 'Điều khiển phương tiện khi có nồng độ cồn vượt mức'),
('Vi phạm làn đường', 'Đi sai làn, lấn làn, vượt ẩu'),
('Vi phạm giấy tờ', 'Không có GPLX, đăng kiểm hết hạn, bảo hiểm'),
('Vi phạm khác', 'Các lỗi vi phạm khác');
```

### 3.4. Danh mục tin tức
```sql
INSERT INTO news_categories (name, slug) VALUES
('Tin tức giao thông', 'tin-tuc'),
('Giải đáp giao thông', 'giai-dap'),
('Thông báo', 'thong-bao'),
('Luật giao thông', 'luat-giao-thong'),
('Biển số xe bị phạt nguội', 'bien-so-xe-bi-phat-nguoi');
```

### 3.5. FAQ mẫu
```sql
INSERT INTO faqs (question, answer, category, sort_order) VALUES
('Tra cứu phạt nguội là gì?',
 '<p>Tra cứu phạt nguội là việc kiểm tra thông tin vi phạm giao thông của phương tiện thông qua biển số xe. Các vi phạm này được ghi nhận bởi hệ thống camera giám sát giao thông và được xử lý sau đó mà không cần dừng xe tại thời điểm vi phạm.</p>',
 'Tra cứu', 1),

('Làm thế nào để tra cứu phạt nguội?',
 '<p>Bạn chỉ cần nhập biển số xe và chọn loại phương tiện (ô tô, xe máy, xe máy điện) vào ô tra cứu trên trang chủ hoặc trang Tra Cứu Phạt Nguội. Hệ thống sẽ trả về danh sách các vi phạm (nếu có).</p>',
 'Tra cứu', 2),

('Dữ liệu tra cứu có chính xác không?',
 '<p>Dữ liệu được tổng hợp từ các nguồn công khai của Cục CSGT và Cục Đăng Kiểm Việt Nam. Tuy nhiên, để có thông tin chính xác nhất, bạn nên kiểm tra trực tiếp trên cổng thông tin của Cục CSGT.</p>',
 'Dữ liệu', 3),

('Thời gian cập nhật dữ liệu là khi nào?',
 '<p>Hệ thống được cập nhật định kỳ 24/7. Dữ liệu mới nhất sẽ được hiển thị ngay khi có thông tin từ cơ quan chức năng.</p>',
 'Dữ liệu', 4);
```

---

## 4. TỔNG KẾT DATABASE

| # | Bảng | Mục đích | Số cột | CRUD |
|---|------|----------|--------|:----:|
| 1 | `users` | Quản lý người dùng, phân quyền | 11 | ✅ |
| 2 | `vehicles` | Phương tiện người dùng quản lý | 9 | ✅ |
| 3 | `offense_categories` | Danh mục nhóm lỗi vi phạm | 4 | ✅ |
| 4 | `offenses` | Danh sách các lỗi vi phạm | 6 | ✅ |
| 5 | `locations` | Địa điểm (camera, CSGT, trạm thu phí, đăng kiểm) | 9 | ✅ |
| 6 | `violations` | Dữ liệu vi phạm (phạt nguội) - **BẢNG CHÍNH** | 12 | ✅ |
| 7 | `news_categories` | Danh mục tin tức | 4 | ✅ |
| 8 | `news` | Bài viết tin tức | 11 | ✅ |
| 9 | `traffic_sign_groups` | Nhóm biển báo giao thông | 4 | ✅ |
| 10 | `traffic_signs` | Biển báo giao thông | 8 | ✅ |
| 11 | `faqs` | Câu hỏi thường gặp | 5 | ✅ |
| 12 | `search_history` | Lịch sử tra cứu | 6 | Read |
| 13 | `traffic_alerts` | Cảnh báo giao thông | 7 | ✅ |
| 14 | `contact_messages` | Tin nhắn liên hệ | 6 | Read/Delete |
| **Tổng** | **14 bảng** | | | |

---

> **Tài liệu tiếp theo:** `04-thiet-ke-giao-dien.md` — Wireframe, cấu trúc layout, UI components.
