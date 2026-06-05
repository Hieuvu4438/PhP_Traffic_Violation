# KIẾN TRÚC HỆ THỐNG

---

## 1. MÔ HÌNH MVC TỰ XÂY DỰNG

### 1.1. Tổng quan

Hệ thống áp dụng mô hình MVC (Model-View-Controller) tự xây dựng, không sử dụng framework bên ngoài. Điều này giúp:
- Hiểu sâu về kiến trúc web PHP
- Kiểm soát hoàn toàn luồng xử lý
- Code tối giản, không thừa
- Phù hợp với yêu cầu đồ án học thuật

### 1.2. Sơ đồ luồng xử lý

```
 REQUEST (HTTP)
     │
     ▼
┌──────────────┐
│  .htaccess   │───── Rewrite tất cả request (trừ static files) vào index.php
└──────┬───────┘
       │
       ▼
┌──────────────┐
│  index.php   │───── Entry point: khởi tạo session, autoload, config
└──────┬───────┘
       │
       ▼
┌──────────────┐
│    Router    │───── Phân tích URL → controller & action
│  (Core)      │     VD: /tra-cuu → TraCuuController@index
└──────┬───────┘
       │
       ▼
┌──────────────┐
│  Controller  │───── Xử lý logic nghiệp vụ
│              │     1. Validate input
│              │     2. Gọi Model (database)
│              │     3. Tổng hợp dữ liệu
│              │     4. Gọi View trả về
└──────┬───────┘
       │
       ├──────────────┐
       ▼              ▼
┌──────────┐   ┌──────────┐
│  Model   │   │   View   │
│          │   │          │
│• Truy vấn│   │• HTML    │
│  MySQL   │   │• Hiển thị│
│• PDO     │   │• Layout  │
│• Validate│   │• Partial │
└──────────┘   └──────────┘
       │              │
       └──────┬───────┘
              ▼
        RESPONSE (HTML)
```

---

## 2. CẤU TRÚC THƯ MỤC CHI TIẾT

```
htdocs/
│
├── public/                          # Web root (Apache DocumentRoot trỏ vào đây)
│   ├── index.php                    # Entry point DUY NHẤT của ứng dụng
│   ├── .htaccess                    # URL rewriting rules
│   │
│   └── assets/                      # Static files (CSS, JS, images)
│       ├── css/
│       │   ├── bootstrap.min.css    # Bootstrap 5
│       │   ├── style.css            # Custom styles (client)
│       │   └── admin.css            # Custom styles (admin)
│       ├── js/
│       │   ├── bootstrap.bundle.min.js
│       │   ├── chart.js             # Chart.js
│       │   ├── main.js              # Custom JS (client)
│       │   └── admin.js             # Custom JS (admin)
│       ├── images/
│       │   ├── logo.png
│       │   ├── signs/               # Ảnh biển báo
│       │   ├── news/                # Ảnh tin tức
│       │   └── avatars/             # Ảnh đại diện
│       └── uploads/                 # Files user upload
│
├── app/                             # Code chính của ứng dụng
│   │
│   ├── core/                        # Lõi hệ thống
│   │   ├── Router.php              # Bộ định tuyến URL
│   │   ├── Controller.php          # Base Controller (tất cả controller kế thừa)
│   │   ├── Model.php               # Base Model (PDO wrapper, CRUD chung)
│   │   ├── Database.php            # Kết nối PDO Singleton
│   │   ├── Session.php             # Quản lý session & flash messages
│   │   ├── Validator.php           # Validate input (email, phone, biển số,...)
│   │   └── Helper.php              # Các hàm tiện ích (slug, redirect, upload,...)
│   │
│   ├── controllers/                 # Controllers
│   │   ├── client/                 # Controllers cho phía người dùng
│   │   │   ├── HomeController.php       # Trang chủ
│   │   │   ├── TraCuuController.php     # Tra cứu phạt nguội
│   │   │   ├── TinTucController.php     # Tin tức
│   │   │   ├── BienBaoController.php    # Biển báo giao thông
│   │   │   ├── BanDoController.php      # Bản đồ
│   │   │   ├── ThongKeController.php    # Thống kê
│   │   │   ├── FaqController.php        # FAQ
│   │   │   ├── AuthController.php       # Đăng nhập / Đăng ký
│   │   │   ├── TaiKhoanController.php   # Quản lý tài khoản user
│   │   │   └── LienHeController.php     # Liên hệ
│   │   │
│   │   └── admin/                  # Controllers cho phía admin
│   │       ├── DashboardController.php  # Tổng quan
│   │       ├── UserController.php       # CRUD Users
│   │       ├── ViolationController.php  # CRUD Violations
│   │       ├── NewsController.php       # CRUD News + Categories
│   │       ├── SignController.php       # CRUD Traffic Signs
│   │       ├── LocationController.php   # CRUD Locations
│   │       ├── FaqController.php        # CRUD FAQs
│   │       ├── AlertController.php      # CRUD Traffic Alerts
│   │       └── MessageController.php    # Xem tin nhắn liên hệ
│   │
│   ├── models/                      # Models (1 file = 1 bảng database)
│   │   ├── User.php
│   │   ├── Vehicle.php
│   │   ├── Violation.php
│   │   ├── Offense.php
│   │   ├── OffenseCategory.php
│   │   ├── Location.php
│   │   ├── News.php
│   │   ├── NewsCategory.php
│   │   ├── TrafficSign.php
│   │   ├── TrafficSignGroup.php
│   │   ├── Faq.php
│   │   ├── SearchHistory.php
│   │   ├── TrafficAlert.php
│   │   └── ContactMessage.php
│   │
│   └── views/                       # Giao diện (file .php thuần)
│       │
│       ├── layouts/                 # Layout chung
│       │   ├── client.php          # Layout chính (header + footer + content)
│       │   └── admin.php           # Layout admin (sidebar + header + content)
│       │
│       ├── partials/                # Components tái sử dụng
│       │   ├── header.php          # Navigation bar
│       │   ├── footer.php          # Footer
│       │   ├── sidebar.php         # Admin sidebar
│       │   ├── pagination.php      # Phân trang
│       │   └── alerts.php          # Flash messages / alerts
│       │
│       ├── client/                  # Views cho người dùng
│       │   ├── home/
│       │   │   └── index.php       # Trang chủ
│       │   ├── tracuu/
│       │   │   └── index.php       # Form tra cứu + kết quả
│       │   ├── tintuc/
│       │   │   ├── index.php       # Danh sách tin tức
│       │   │   └── detail.php      # Chi tiết bài viết
│       │   ├── bienbao/
│       │   │   ├── index.php       # Danh sách biển báo
│       │   │   └── detail.php      # Chi tiết biển báo
│       │   ├── bando/
│       │   │   └── index.php       # Bản đồ Google Maps
│       │   ├── thongke/
│       │   │   └── index.php       # Trang thống kê + biểu đồ
│       │   ├── faq/
│       │   │   └── index.php       # FAQ
│       │   ├── taikhoan/
│       │   │   ├── dashboard.php   # Dashboard user
│       │   │   ├── vehicles.php    # Quản lý phương tiện
│       │   │   ├── profile.php     # Cập nhật thông tin
│       │   │   └── history.php     # Lịch sử tra cứu
│       │   ├── auth/
│       │   │   ├── login.php       # Đăng nhập
│       │   │   └── register.php    # Đăng ký
│       │   └── pages/
│       │       ├── gioi-thieu.php  # Trang giới thiệu
│       │       └── lien-he.php     # Trang liên hệ
│       │
│       └── admin/                   # Views cho admin
│           ├── dashboard/
│           │   └── index.php       # Dashboard tổng quan
│           ├── users/
│           │   ├── index.php       # Danh sách users
│           │   └── form.php        # Form thêm/sửa user
│           ├── violations/
│           │   ├── index.php       # Danh sách vi phạm
│           │   └── form.php        # Form thêm/sửa vi phạm
│           ├── news/
│           │   ├── index.php       # Danh sách tin tức
│           │   └── form.php        # Form thêm/sửa (CKEditor)
│           ├── signs/
│           │   ├── index.php       # Danh sách biển báo
│           │   └── form.php        # Form thêm/sửa
│           ├── locations/
│           │   ├── index.php       # Danh sách địa điểm
│           │   └── form.php        # Form thêm/sửa
│           ├── faqs/
│           │   ├── index.php       # Danh sách FAQ
│           │   └── form.php        # Form thêm/sửa
│           ├── categories/
│           │   ├── index.php       # Danh sách danh mục
│           │   └── form.php        # Form thêm/sửa
│           └── messages/
│               └── index.php       # Tin nhắn liên hệ
│
├── config/                          # Cấu hình
│   ├── config.php                  # Constants: DB, URL, APP_NAME,...
│   └── routes.php                  # Bảng định tuyến URL → Controller@action
│
├── database/                        # Database scripts
│   ├── schema.sql                  # Tạo toàn bộ database
│   ├── seed.sql                    # Dữ liệu mẫu
│   └── migrations/                 # Các file SQL cập nhật (nếu cần)
│
├── docs/                            # Tài liệu đồ án (thư mục hiện tại)
│   ├── 01-tong-quan-du-an.md
│   ├── 02-phan-tich-yeu-cau.md
│   ├── 03-thiet-ke-co-so-du-lieu.md
│   ├── 04-thiet-ke-giao-dien.md
│   ├── 05-kien-truc-he-thong.md
│   └── 06-ke-hoach-phat-trien.md
│
├── vendor/                          # Composer dependencies (nếu cần)
├── .htaccess                        # Chặn truy cập trực tiếp vào app/, config/
├── README.md                        # Hướng dẫn cài đặt & chạy
└── .gitignore
```

---

## 3. CÁC LỚP QUAN TRỌNG (CORE)

### 3.1. `Router.php`

```php
<?php
namespace App\Core;

class Router {
    private array $routes = [];

    // Đăng ký route GET: /tra-cuu → TraCuuController@index
    public function get(string $uri, string $handler): void { ... }

    // Đăng ký route POST
    public function post(string $uri, string $handler): void { ... }

    // Phân giải URL hiện tại → gọi controller
    public function dispatch(string $uri, string $method): void { ... }
}
```

### 3.2. `Controller.php` (Base)

```php
<?php
namespace App\Core;

class Controller {
    // Render view với layout
    protected function view(string $view, array $data = [], string $layout = 'client'): void { ... }

    // Redirect
    protected function redirect(string $url): void { ... }

    // Flash message
    protected function setFlash(string $key, string $message): void { ... }

    // Lấy dữ liệu từ $_GET, $_POST đã sanitize
    protected function input(string $key, $default = null): mixed { ... }

    // Kiểm tra đăng nhập
    protected function requireLogin(): void { ... }

    // Kiểm tra admin
    protected function requireAdmin(): void { ... }
}
```

### 3.3. `Model.php` (Base)

```php
<?php
namespace App\Core;

class Model {
    protected \PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    // Khởi tạo PDO từ Database singleton
    public function __construct() { ... }

    // ── Các phương thức CRUD chung ──

    // SELECT * FROM table (có WHERE, ORDER, LIMIT, pagination)
    public function all(array $conditions = [], string $orderBy = '', int $limit = 0, int $offset = 0): array { ... }

    // SELECT * FROM table WHERE id = ?
    public function find(int $id): ?array { ... }

    // SELECT * FROM table WHERE column = ? (single row)
    public function findBy(string $column, $value): ?array { ... }

    // INSERT INTO table
    public function create(array $data): int { ... }

    // UPDATE table SET ... WHERE id = ?
    public function update(int $id, array $data): bool { ... }

    // DELETE FROM table WHERE id = ?
    public function delete(int $id): bool { ... }

    // COUNT(*)
    public function count(array $conditions = []): int { ... }

    // Phân trang
    public function paginate(int $page = 1, int $perPage = 10, array $conditions = []): array { ... }
}
```

### 3.4. `Database.php` (Singleton)

```php
<?php
namespace App\Core;

class Database {
    private static ?Database $instance = null;
    private \PDO $pdo;

    private function __construct() {
        $config = require __DIR__ . '/../../config/config.php';
        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
        $this->pdo = new \PDO($dsn, $config['db_user'], $config['db_pass'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public static function getInstance(): self { ... }
    public function getConnection(): \PDO { ... }
}
```

### 3.5. `Validator.php`

```php
<?php
namespace App\Core;

class Validator {
    // Validate biển số xe Việt Nam
    public static function plateNumber(string $plate): bool { ... }

    // Validate email
    public static function email(string $email): bool { ... }

    // Validate phone VN (10 số)
    public static function phone(string $phone): bool { ... }

    // Validate required
    public static function required(mixed $value): bool { ... }

    // Validate min/max length
    public static function minLength(string $value, int $min): bool { ... }
    public static function maxLength(string $value, int $max): bool { ... }
}
```

---

## 4. .HTACCESS & ROUTING

### 4.1. `public/.htaccess`

```apache
RewriteEngine On

# Không rewrite nếu file/directory tồn tại thật
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f

# Tất cả request → index.php
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

### 4.2. `config/routes.php` (Ví dụ)

```php
<?php
// Định nghĩa tất cả routes tại đây

// ── Routes công khai ──
$router->get('/',                          'client/HomeController@index');
$router->get('/tra-cuu',                  'client/TraCuuController@index');
$router->post('/tra-cuu',                 'client/TraCuuController@search');
$router->get('/tin-tuc',                  'client/TinTucController@index');
$router->get('/tin-tuc/{slug}',           'client/TinTucController@detail');
$router->get('/bien-bao',                 'client/BienBaoController@index');
$router->get('/bien-bao/{id}',            'client/BienBaoController@detail');
$router->get('/ban-do',                   'client/BanDoController@index');
$router->get('/thong-ke',                 'client/ThongKeController@index');
$router->get('/faq',                      'client/FaqController@index');
$router->get('/gioi-thieu',              'client/HomeController@about');
$router->get('/lien-he',                 'client/LienHeController@index');
$router->post('/lien-he',                'client/LienHeController@send');
$router->get('/dang-nhap',               'client/AuthController@loginForm');
$router->post('/dang-nhap',              'client/AuthController@login');
$router->get('/dang-ky',                 'client/AuthController@registerForm');
$router->post('/dang-ky',                'client/AuthController@register');
$router->get('/dang-xuat',               'client/AuthController@logout');

// ── Routes cần đăng nhập (user) ──
$router->get('/tai-khoan',               'client/TaiKhoanController@dashboard');
$router->get('/tai-khoan/phuong-tien',   'client/TaiKhoanController@vehicles');
$router->post('/tai-khoan/phuong-tien',  'client/TaiKhoanController@addVehicle');
$router->put('/tai-khoan/phuong-tien/{id}',  'client/TaiKhoanController@updateVehicle');
$router->delete('/tai-khoan/phuong-tien/{id}','client/TaiKhoanController@deleteVehicle');
$router->get('/tai-khoan/lich-su',       'client/TaiKhoanController@history');
$router->get('/tai-khoan/ho-so',         'client/TaiKhoanController@profile');
$router->post('/tai-khoan/ho-so',        'client/TaiKhoanController@updateProfile');

// ── Routes admin ──
$router->get('/admin',                   'admin/DashboardController@index');

// CRUD Users
$router->get('/admin/users',             'admin/UserController@index');
$router->get('/admin/users/create',      'admin/UserController@create');
$router->post('/admin/users',            'admin/UserController@store');
$router->get('/admin/users/{id}/edit',   'admin/UserController@edit');
$router->post('/admin/users/{id}',       'admin/UserController@update');
$router->post('/admin/users/{id}/delete','admin/UserController@delete');

// CRUD Violations
$router->get('/admin/violations',           'admin/ViolationController@index');
$router->get('/admin/violations/create',    'admin/ViolationController@create');
$router->post('/admin/violations',          'admin/ViolationController@store');
$router->get('/admin/violations/{id}/edit', 'admin/ViolationController@edit');
$router->post('/admin/violations/{id}',     'admin/ViolationController@update');
$router->post('/admin/violations/{id}/delete','admin/ViolationController@delete');
$router->post('/admin/violations/import',   'admin/ViolationController@import');

// CRUD News, Signs, Locations, FAQs, Categories... (tương tự)
```

---

## 5. MIDDLEWARE / AUTH

### 5.1. Kiểm tra đăng nhập

```php
// Trong Controller base
protected function requireLogin(): void {
    if (!Session::isLoggedIn()) {
        Session::setFlash('error', 'Vui lòng đăng nhập để tiếp tục.');
        $this->redirect('/dang-nhap');
    }
}

protected function requireAdmin(): void {
    $this->requireLogin();
    if (Session::get('role') !== 'admin') {
        $this->redirect('/');
    }
}
```

### 5.2. Sử dụng trong controller con

```php
class TaiKhoanController extends Controller {
    public function dashboard(): void {
        $this->requireLogin();  // Chặn nếu chưa login
        $this->view('taikhoan/dashboard', [...]);
    }
}
```

---

## 6. BẢO MẬT

| Vấn đề | Biện pháp |
|--------|-----------|
| **SQL Injection** | Sử dụng PDO với prepared statement (100% query đều qua PDO) |
| **XSS** | `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` khi hiển thị dữ liệu user |
| **CSRF** | Token ẩn trong form, kiểm tra khi POST |
| **Password** | `password_hash($pass, PASSWORD_BCRYPT)` + `password_verify()` |
| **Session** | `session_regenerate_id()` sau login, `session_destroy()` khi logout |
| **File Upload** | Validate extension + MIME type + kích thước, đổi tên file |
| **Direct Access** | `.htaccess` chặn truy cập trực tiếp vào `app/`, `config/`, `database/` |
| **Error Display** | `display_errors=Off` trên production, log error thay vì hiển thị |

---

## 7. PHÂN TÁCH FRONTEND / BACKEND

```
FRONTEND (Client-side)                 BACKEND (Server-side)
─────────────────────────              ────────────────────────
• HTML (views/*.php)                  • PHP Controllers (xử lý logic)
• Bootstrap 5 (giao diện)             • PHP Models (truy vấn DB)
• JavaScript (Vanilla)                • MySQL (lưu trữ dữ liệu)
  - Chart.js (biểu đồ)                • PDO (database abstraction)
  - Google Maps API (bản đồ)           • File system (lưu ảnh upload)
  - Fetch API (AJAX gọi backend)      • Session management
• CSS (style.css, admin.css)          • Input validation (server-side)
```

---

## 8. DATA FLOW VÍ DỤ

### 8.1. Luồng tra cứu phạt nguội

```
1. User nhập biển số + chọn loại xe → click "Tra cứu"
2. Browser → POST /tra-cuu (plate_number=30A-12345&vehicle_type=car)
3. Router → TraCuuController@search
4. Controller:
   - Validate biển số (Validator::plateNumber)
   - Gọi ViolationModel::findByPlateAndType('30A-12345', 'car')
5. Model:
   - SELECT * FROM violations
     WHERE plate_number = '30A-12345' AND vehicle_type = 'car'
     ORDER BY violation_date DESC
6. Controller nhận kết quả → truyền vào View
7. Nếu user đã login → lưu vào search_history
8. View render HTML table kết quả → trả về browser
```

### 8.2. Luồng thêm vi phạm (Admin)

```
1. Admin click "Thêm mới" → GET /admin/violations/create
2. Router → ViolationController@create → View form
3. Admin điền form → POST /admin/violations
4. Controller:
   - Validate input
   - Gọi ViolationModel::create([...data...])
5. Model:
   - INSERT INTO violations (...) VALUES (...)
6. Controller:
   - setFlash('success', 'Thêm vi phạm thành công')
   - redirect('/admin/violations')
7. Browser → GET /admin/violations (hiển thị danh sách + flash message)
```

---

> **Tài liệu tiếp theo:** `06-ke-hoach-phat-trien.md` — Lộ trình phát triển từng giai đoạn.
