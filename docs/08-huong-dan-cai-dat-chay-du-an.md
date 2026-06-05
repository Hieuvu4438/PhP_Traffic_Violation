# 08 — Hướng Dẫn Cài Đặt & Chạy Dự Án

> Dành cho người mới clone dự án về máy. Chỉ cần làm đúng theo các bước bên dưới.

---

## Yêu cầu hệ thống

| Phần mềm | Phiên bản | Ghi chú |
|-----------|-----------|---------|
| **XAMPP** | 8.x trở lên | Bao gồm Apache + MySQL + PHP |
| **Trình duyệt** | Chrome/Firefox/Edge | Mới nhất |
| **Git** | Bất kỳ | Để clone dự án |

> **Lưu ý:** Không cần cài Composer hay Node.js. Dự án dùng PHP thuần, autoload bằng `spl_autoload_register`.

---

## Bước 1 — Clone dự án

Mở Terminal (hoặc Git Bash), chạy:

```bash
cd C:\xampp\htdocs
git clone https://github.com/Hieuvu4438/PhP_Traffic_Violation.git
```

Kết quả: `C:\xampp\htdocs\PhP_Traffic_Violation\`

---

## Bước 2 — Khởi động XAMPP

Mở **XAMPP Control Panel**, nhấn **Start** cả hai dịch vụ:

- ✅ **Apache** (port 80)
- ✅ **MySQL** (port 3306)

Nếu port 80 bị chiếm, đổi sang port khác (ví dụ 8080) trong `Config → Apache (httpd.conf)`.

---

## Bước 3 — Tạo file cấu hình

Vào thư mục `config/`, copy file mẫu:

```
config/config.example.php  →  config/config.php
```

Mở `config/config.php`, kiểm tra và sửa các thông số cho khớp với XAMPP trên máy bạn:

```php
return [
    'db_host' => 'localhost',        // Thường không cần đổi
    'db_name' => 'traffic_violation_db',
    'db_user' => 'root',             // XAMPP mặc định
    'db_pass' => '',                 // XAMPP mặc định = rỗng. Nếu có đặt password thì điền vào đây
    'db_charset' => 'utf8mb4',

    'app_url' => 'http://localhost/PhP_Traffic_Violation/public',
    // Nếu dùng VirtualHost (Bước 5B) thì đổi thành: 'http://traffic.local'
    // ...
];
```

> ⚠️ File `config.php` đã được `.gitignore` — mỗi người tự tạo từ file mẫu, không bị ghi đè khi pull code.

---

## Bước 4 — Tạo database

### Cách A — Dùng script tự động (khuyến nghị)

Mở Terminal, chạy:

```bash
cd C:\xampp\htdocs\PhP_Traffic_Violation
php database/setup.php
```

Script sẽ tự động:
1. Tạo database `traffic_violation_db`
2. Import schema (tất cả bảng)
3. Import dữ liệu mẫu
4. Hiển thị danh sách bảng và số dòng

> **Lưu ý:** Nếu MySQL trên máy bạn có password, mở file `database/setup.php` sửa dòng `$pass = '';` thành password của bạn trước khi chạy.

### Cách B — Import thủ công qua phpMyAdmin

1. Mở trình duyệt: **http://localhost/phpmyadmin**
2. Nhấn **New** → tạo database tên `traffic_violation_db`, chọn collation `utf8mb4_unicode_ci`
3. Chọn database vừa tạo → tab **Import**
4. Import file `database/schema.sql` → nhấn **Go**
5. Import file `database/seed.sql` → nhấn **Go**

---

## Bước 5 — Cấu hình Apache

### Cách A — Truy cập trực tiếp (đơn giản, không cần sửa gì)

Chỉ cần mở trình duyệt vào:

```
http://localhost/PhP_Traffic_Violation/public/
```

✅ Hoạt động ngay, không cần cấu hình thêm.

### Cách B — Dùng VirtualHost (URL đẹp, khuyến nghị nếu làm việc lâu dài)

**B5B.1 — Sửa httpd-vhosts.conf:**

Mở file `C:\xampp\apache\conf\extra\httpd-vhosts.conf`, thêm vào cuối file:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/PhP_Traffic_Violation/public"
    ServerName traffic.local
    <Directory "C:/xampp/htdocs/PhP_Traffic_Violation/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**B5B.2 — Sửa file hosts:**

Mở `C:\Windows\System32\drivers\etc\hosts` (dùng Notepad **Run as Administrator**), thêm:

```
127.0.0.1 traffic.local
```

**B5B.3 — Restart Apache** trong XAMPP Control Panel.

**B5B.4 — Cập nhật app_url:**

Sửa `config/config.php`:

```php
'app_url' => 'http://traffic.local',
```

Truy cập: **http://traffic.local/**

---

## Bước 6 — Kiểm tra kết quả

Mở trình duyệt, truy cập đường dẫn đã cấu hình ở Bước 5. Nếu thấy trang chủ = ✅ thành công!

### Đăng nhập thử

| Vai trò | Email | Password | Trang quản trị |
|---------|-------|----------|----------------|
| **Admin** | `admin@traffic.vn` | `admin123` | `/admin` |
| **User** | `an.nguyen@gmail.com` | `123456` | — |

---

## Cấu trúc thư mục (tóm tắt)

```
PhP_Traffic_Violation/
├── public/                 ← DocumentRoot (Apache trỏ vào đây)
│   ├── index.php           ← Entry point duy nhất
│   ├── .htaccess           ← URL rewriting
│   └── assets/             ← css/, js/, images/, uploads/
├── app/
│   ├── core/               ← Framework: Router, Controller, Model, Database, Session, Validator, Helper
│   ├── controllers/        ← client/ và admin/
│   ├── models/             ← 1 class/bảng DB
│   └── views/              ← layouts/, partials/, client/, admin/
├── config/
│   ├── config.example.php  ← FILE MẪU — copy thành config.php
│   ├── config.php          ← CẤU HÌNH THỰC TẾ — KHÔNG commit (gitignore)
│   └── routes.php          ← Định nghĩa URL routing
├── database/
│   ├── schema.sql          ← Cấu trúc bảng
│   ├── seed.sql            ← Dữ liệu mẫu
│   └── setup.php           ← Script import DB tự động
└── docs/                   ← Tài liệu dự án (9 file)
```

---

## Xử lý lỗi thường gặp

| Lỗi | Nguyên nhân | Cách sửa |
|-----|-------------|----------|
| `SQLSTATE[HY000] [1045] Access denied` | Sai user/password MySQL | Kiểm tra `config.php`, đổi `db_pass` cho khớp |
| `SQLSTATE[HY000] [1049] Unknown database` | Chưa tạo database | Chạy lại Bước 4 |
| Trang trắng / 500 | Apache chưa rewrite URL | Kiểm tra `mod_rewrite` đã bật trong `httpd.conf` |
| `404 Not Found` | DocumentRoot sai | Đảm bảo trỏ vào thư mục `public/`, không phải thư mục gốc |
| CSS/JS không tải | Sai đường dẫn assets | Kiểm tra `app_url` trong `config.php` |
| Hình ảnh upload không hiển thị | Thư mục `uploads` chưa có quyền ghi | Kiểm tra quyền thư mục `public/assets/uploads/` |

---

## Cập nhật dự án khi có code mới

```bash
cd C:\xampp\htdocs\PhP_Traffic_Violation
git pull origin main
```

Nếu có thay đổi cấu trúc database, chạy lại:

```bash
php database/setup.php
```

> ⚠️ `setup.php` sẽ **ghi đè** toàn bộ dữ liệu cũ. Nếu muốn giữ dữ liệu, import chỉ file mới thay vì chạy script.
