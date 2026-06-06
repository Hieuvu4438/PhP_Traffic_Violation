# 08 — Hướng Dẫn Cài Đặt & Chạy Dự Án

> Dành cho người mới clone dự án về máy. Dự án hỗ trợ **2 cách chạy**: Docker (khuyến nghị) hoặc XAMPP.

---

## Cách 1 — Docker (Khuyến nghị)

Chạy được trên **Windows, macOS, Linux** — chỉ cần cài Docker Desktop. Không cần XAMPP, không cần cấu hình PHP/MySQL.

### Yêu cầu

| Phần mềm | Tải tại | Ghi chú |
|-----------|---------|---------|
| **Docker Desktop** | https://www.docker.com/products/docker-desktop/ | Chọn bản phù hợp Windows/macOS/Linux |
| **Git** | https://git-scm.com/downloads | Để clone dự án |
| **Trình duyệt** | Chrome/Firefox/Edge | Mới nhất |

### Bước 1 — Cài Docker Desktop

**Windows:**
1. Tải Docker Desktop tại https://www.docker.com/products/docker-desktop/
2. Chạy file `.exe` → cài đặt bình thường
3. Khởi động lại máy nếu được yêu cầu
4. Mở Docker Desktop → chờ biểu tượng Docker ở taskbar chuyển sang trạng thái **"Engine running"**

**macOS:**
1. Tải Docker Desktop cho Mac (Intel hoặc Apple Silicon)
2. Kéo vào Applications → mở Docker Desktop
3. Chờ biểu tượng trên thanh menu báo **"Docker Desktop is running"**

**Linux (Ubuntu/Debian):**
```bash
sudo apt update
sudo apt install docker.io docker-compose-plugin
sudo systemctl start docker
sudo usermod -aG docker $USER
# Đăng xuất rồi đăng nhập lại để có quyền chạy docker không cần sudo
```

### Bước 2 — Clone dự án

```bash
git clone https://github.com/Hieuvu4438/PhP_Traffic_Violation.git
cd PhP_Traffic_Violation
```

### Bước 3 — Khởi động

```bash
docker compose up --build
```

Lần đầu sẽ mất **3–5 phút** để tải image MySQL + PHP. Các lần sau chỉ mất vài giây.

Khi thấy dòng sau là thành công:

```
db-1   | ready for connections
web-1  | Apache/2.4.x configured -- resuming normal operations
```

### Bước 4 — Truy cập

Mở trình duyệt: **http://localhost:8080**

### Đăng nhập

| Vai trò | Email | Password | Trang quản trị |
|---------|-------|----------|----------------|
| **Admin** | `admin@traffic.vn` | `admin123` | `/admin` |
| **User** | `an.nguyen@gmail.com` | `123456` | — |

### Dừng & khởi động lại

```bash
# Dừng container (giữ dữ liệu)
docker compose down

# Khởi động lại (không cần --build nếu không sửa Dockerfile)
docker compose up

# Xóa toàn bộ dữ liệu database (reset về dữ liệu mẫu)
docker compose down -v
docker compose up --build
```

### Cấu trúc Docker

```
PhP_Traffic_Violation/
├── Dockerfile               ← Định nghĩa image PHP + Apache
├── docker-compose.yml       ← Cấu hình 2 services: web + db
├── docker-entrypoint.sh     ← Script khởi tạo khi container chạy
├── .dockerignore            ← Loại file thừa khỏi image
└── ...
```

### Sửa code & xem thay đổi

Nhờ bind mount, bạn sửa file trên máy → refresh trình duyệt là thấy ngay, không cần rebuild.

### Lưu ý khi dùng Docker

- Port **8080** cho web, **8081** cho phpMyAdmin, **3307** cho MySQL (tránh trùng XAMPP)
- Dữ liệu database được lưu trong Docker volume, không mất khi `docker compose down`
- Để quản lý cơ sở dữ liệu trực quan bằng Web: Mở trình duyệt vào **http://localhost:8081**
- Nếu muốn kết nối DB từ phần mềm bên ngoài (DBeaver, TablePlus...): host=`localhost`, port=`3307`, user=`root`, password=rỗng

---

## Cách 2 — XAMPP (Truyền thống)

Dành cho người muốn chạy trực tiếp trên máy với XAMPP.

### Yêu cầu

| Phần mềm | Phiên bản | Ghi chú |
|-----------|-----------|---------|
| **XAMPP** | 8.x trở lên | Bao gồm Apache + MySQL + PHP |
| **Trình duyệt** | Chrome/Firefox/Edge | Mới nhất |
| **Git** | Bất kỳ | Để clone dự án |

> **Lưu ý:** Không cần cài Composer hay Node.js. Dự án dùng PHP thuần, autoload bằng `spl_autoload_register`.

### Bước 1 — Clone dự án

Mở Terminal (hoặc Git Bash), chạy:

```bash
cd C:\xampp\htdocs
git clone https://github.com/Hieuvu4438/PhP_Traffic_Violation.git
```

Kết quả: `C:\xampp\htdocs\PhP_Traffic_Violation\`

### Bước 2 — Khởi động XAMPP

Mở **XAMPP Control Panel**, nhấn **Start** cả hai dịch vụ:

- ✅ **Apache** (port 80)
- ✅ **MySQL** (port 3306)

Nếu port 80 bị chiếm, đổi sang port khác (ví dụ 8080) trong `Config → Apache (httpd.conf)`.

### Bước 3 — Tạo file cấu hình

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

### Bước 4 — Tạo database

#### Cách A — Dùng script tự động (khuyến nghị)

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

#### Cách B — Import thủ công qua phpMyAdmin

1. Mở trình duyệt: **http://localhost/phpmyadmin**
2. Nhấn **New** → tạo database tên `traffic_violation_db`, chọn collation `utf8mb4_unicode_ci`
3. Chọn database vừa tạo → tab **Import**
4. Import file `database/schema.sql` → nhấn **Go**
5. Import file `database/seed.sql` → nhấn **Go**

### Bước 5 — Cấu hình Apache

#### Cách A — Truy cập trực tiếp (đơn giản, không cần sửa gì)

Chỉ cần mở trình duyệt vào:

```
http://localhost/PhP_Traffic_Violation/public/
```

✅ Hoạt động ngay, không cần cấu hình thêm.

#### Cách B — Dùng VirtualHost (URL đẹp, khuyến nghị nếu làm việc lâu dài)

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

### Bước 6 — Kiểm tra kết quả

Mở trình duyệt, truy cập đường dẫn đã cấu hình ở Bước 5. Nếu thấy trang chủ = ✅ thành công!

#### Đăng nhập thử

| Vai trò | Email | Password | Trang quản trị |
|---------|-------|----------|----------------|
| **Admin** | `admin@traffic.vn` | `admin123` | `/admin` |
| **User** | `an.nguyen@gmail.com` | `123456` | — |

---

## So sánh 2 cách

| | Docker | XAMPP |
|--|--------|-------|
| **Cài đặt** | Cài Docker Desktop | Cài XAMPP |
| **Phù hợp** | Mọi OS (Win/Mac/Linux) | Windows (chính), Mac/Linux (có bản) |
| **Khởi động** | `docker compose up` | Start Apache + MySQL trong XAMPP |
| **Cấu hình** | Tự động (không cần sửa config) | Thủ công (copy config, sửa db_pass, ...) |
| **Database** | Tự import khi chạy lần đầu | Chạy script hoặc import phpMyAdmin |
| **Sửa code** | Lưu → refresh ngay | Lưu → refresh ngay |
| **Reset DB** | `docker compose down -v` | Chạy lại `setup.php` |
| **Phù hợp khi** | Muốn chạy nhanh, không cần XAMPP | Đang học PHP, muốn hiểu rõ cấu trúc |

---

## Xử lý lỗi thường gặp

### Docker

| Lỗi | Nguyên nhân | Cách sửa |
|-----|-------------|----------|
| `ports are not available: port 3306` | XAMPP đang chiếm port MySQL | Đã cấu hình dùng port 3307, đảm bảo XAMPP MySQL đang tắt |
| `docker: command not found` | Chưa cài Docker Desktop | Tải và cài tại docker.com |
| `permission denied` (Linux) | Chưa thêm user vào group docker | Chạy `sudo usermod -aG docker $USER` rồi đăng nhập lại |
| Container chạy nhưng 500 | Config chưa đúng | Chạy `docker compose down -v` rồi `docker compose up --build` |
| Không thấy dữ liệu mẫu | Volume cũ, init script không chạy lại | Chạy `docker compose down -v` rồi `docker compose up --build` |

### XAMPP

| Lỗi | Nguyên nhân | Cách sửa |
|-----|-------------|----------|
| `SQLSTATE[HY000] [1045] Access denied` | Sai user/password MySQL | Kiểm tra `config.php`, đổi `db_pass` cho khớp |
| `SQLSTATE[HY000] [1049] Unknown database` | Chưa tạo database | Chạy lại Bước 4 |
| Trang trắng / 500 | Apache chưa rewrite URL | Kiểm tra `mod_rewrite` đã bật trong `httpd.conf` |
| `404 Not Found` | DocumentRoot sai | Đảm bảo trỏ vào thư mục `public/`, không phải thư mục gốc |
| CSS/JS không tải | Sai đường dẫn assets | Kiểm tra `app_url` trong `config.php` |
| Hình ảnh upload không hiển thị | Thư mục `uploads` chưa có quyền ghi | Kiểm tra quyền thư mục `public/assets/uploads/` |
| `caching_sha2_password` error | MySQL 8.x dùng auth plugin mới | Xem hướng dẫn sửa trong mục Docker ở trên, hoặc đổi sang `mysql_native_password` |

---

## Cập nhật dự án khi có code mới

```bash
git pull origin main
```

**Docker:** Không cần làm gì thêm, bind mount tự cập nhật.

**XAMPP:** Nếu có thay đổi cấu trúc database, chạy lại:

```bash
php database/setup.php
```

> ⚠️ `setup.php` sẽ **ghi đè** toàn bộ dữ liệu cũ. Nếu muốn giữ dữ liệu, import chỉ file mới thay vì chạy script.

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
├── Dockerfile              ← Image PHP + Apache (dùng Docker)
├── docker-compose.yml      ← Cấu hình services web + db (dùng Docker)
├── docker-entrypoint.sh    ← Script khởi tạo container (dùng Docker)
└── docs/                   ← Tài liệu dự án
```
