# Website Tra Cứu Phương Tiện Vi Phạm Giao Thông

Đồ án bài tập lớn PHP — Website tra cứu phạt nguội toàn quốc.

Tham khảo: https://vnetraffic.org/

---

## Cài đặt nhanh

### 1. Clone & cấu hình

```bash
cd C:\xampp\htdocs
git clone https://github.com/Hieuvu4438/PhP_Traffic_Violation.git
cd PhP_Traffic_Violation
copy config\config.example.php config\config.php
```

Mở `config/config.php`, sửa thông số database cho khớp với XAMPP trên máy bạn.

### 2. Khởi động XAMPP

Start **Apache** + **MySQL** trong XAMPP Control Panel.

### 3. Tạo database

```bash
php database/setup.php
```

Hoặc import thủ công qua phpMyAdmin: `database/schema.sql` → `database/seed.sql`.

### 4. Truy cập

```
http://localhost/PhP_Traffic_Violation/public/
```

> Xem hướng dẫn chi tiết (VirtualHost, xử lý lỗi, ...): [`docs/08-huong-dan-cai-dat-chay-du-an.md`](docs/08-huong-dan-cai-dat-chay-du-an.md)

---

## Tài khoản mặc định

| Vai trò | Email | Password |
|---------|-------|----------|
| Admin | `admin@traffic.vn` | `admin123` |
| User | `an.nguyen@gmail.com` | `123456` |

---

## Công nghệ

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.x (vanilla, custom MVC) |
| Database | MySQL/MariaDB (utf8mb4_unicode_ci, InnoDB) |
| Frontend | HTML5, CSS3, Vanilla JS, Bootstrap 5 |
| Charts | Chart.js |
| Maps | Google Maps JS API (Leaflet fallback) |
| Icons | Font Awesome 6 |

---

## Cấu trúc thư mục

```
├── public/              # DocumentRoot — Apache trỏ vào đây
│   ├── index.php        # Entry point
│   └── assets/          # css/, js/, images/, uploads/
├── app/
│   ├── core/            # Router, Controller, Model, Database, Session, Validator, Helper
│   ├── controllers/     # client/ và admin/
│   ├── models/          # 1 class/bảng DB
│   └── views/           # layouts/, partials/, client/, admin/
├── config/              # config.example.php, routes.php
├── database/            # schema.sql, seed.sql, setup.php
└── docs/                # Tài liệu dự án
```

---

## Tài liệu

Xem thư mục [`docs/`](docs/) để biết chi tiết thiết kế, kiến trúc, và hướng dẫn cài đặt.
