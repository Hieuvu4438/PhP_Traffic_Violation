# HƯỚNG DẪN ĐỌC HIỂU TOÀN BỘ DỰ ÁN

## Website Tra Cứu Phương Tiện Vi Phạm Giao Thông

---

## MỤC LỤC

1. [Tổng quan dự án](#1-tổng-quan-dự-án)
2. [Lộ trình đọc hiểu](#2-lộ-trình-đọc-hiểu)
3. [Kiến trúc hệ thống](#3-kiến-trúc-hệ-thống)
4. [Core Layer — Bộ khung MVC](#4-core-layer--bộ-khung-mvc)
5. [Config & Entry Point](#5-config--entry-point)
6. [Database Layer](#6-database-layer)
7. [Models](#7-models)
8. [Controllers — Client](#8-controllers--client)
9. [Controllers — Admin](#9-controllers--admin)
10. [Views & Layouts](#10-views--layouts)
11. [Assets & Frontend](#11-assets--frontend)
12. [Bảo mật](#12-bảo-mật)
13. [Quy ước & Patterns](#13-quy-ước--patterns)

---

## 1. TỔNG QUAN DỰ ÁN

### Dự án là gì?

Website tra cứu phương tiện vi phạm giao thông — đồ án bài tập lớn PHP đại học. Cho phép người dùng tra cứu phạt nguội theo biển số xe, xem tin tức giao thông, tra cứu biển báo, thống kê, bản đồ địa điểm vi phạm. Admin quản lý toàn bộ dữ liệu qua panel riêng.

### Tech Stack

| Lớp | Công nghệ |
|-----|-----------|
| Backend | **PHP 8.x** — thuần, không framework |
| Database | **MySQL/MariaDB** qua XAMPP, InnoDB, utf8mb4 |
| Frontend | **Bootstrap 5.3**, Vanilla JS, CSS thuần |
| Biểu đồ | **Chart.js 4.x** |
| Bản đồ | **Leaflet + OpenStreetMap** (Google Maps fallback) |
| Editor | **CKEditor 5** (CDN) cho bài viết admin |
| Icons | **Font Awesome 6.5** |
| Server | **Apache** (XAMPP), `public/` là DocumentRoot |

### Quy mô

- **14 bảng** database
- **20 controllers** (10 client + 10 admin)
- **14 models**
- **40+ views**
- **93 routes** (bao gồm 2 route AJAX toggle)
- **~85 file PHP**
- **AJAX**: tra cứu phạt nguội, toggle trạng thái admin

---

## 2. LỘ TRÌNH ĐỌC HIỂU

Đọc theo thứ tự sau để hiểu dự án từ nền tảng đến chi tiết:

### Giai đoạn 1: Nền tảng (bắt buộc đọc trước)

| # | File | Vai trò | Thời gian ước tính |
|---|------|---------|---------------------|
| 1 | `docs/01-tong-quan-du-an.md` | Mục tiêu, phạm vi, công nghệ | 10 phút |
| 2 | `public/.htaccess` | URL rewriting — mọi request vào `index.php` | 2 phút |
| 3 | `public/index.php` | **Entry point** — autoload, session, router, dispatch | 10 phút |
| 4 | `config/config.example.php` | File mẫu cấu hình — copy thành `config.php` | 3 phút |
| 5 | `config/routes.php` | **Bảng định tuyến** 92 URL → Controller@action | 10 phút |

### Giai đoạn 2: Core MVC (hiểu cách framework hoạt động)

| # | File | Vai trò | Thời gian ước tính |
|---|------|---------|---------------------|
| 6 | `app/core/Database.php` | Singleton PDO — 1 kết nối duy nhất | 5 phút |
| 7 | `app/core/Router.php` | Phân tích URL, regex match, gọi controller | 10 phút |
| 8 | `app/core/Controller.php` | Base controller — view, redirect, input, auth guard, CSRF | 10 phút |
| 9 | `app/core/Model.php` | Base model — CRUD tự động, paginate, query builder | 10 phút |
| 10 | `app/core/Session.php` | Session + flash messages + CSRF token + auth check | 5 phút |
| 11 | `app/core/Validator.php` | Validate input: plate, email, phone, required, min/max... | 5 phút |
| 12 | `app/core/Helper.php` | Tiện ích: slug, format date/money, excerpt, upload file | 3 phút |

### Giai đoạn 3: Database

| # | File | Vai trò | Thời gian ước tính |
|---|------|---------|---------------------|
| 13 | `docs/03-thiet-ke-co-so-du-lieu.md` | ERD, mô tả quan hệ 14 bảng | 15 phút |
| 14 | `database/schema.sql` | SQL tạo 14 bảng — đọc cùng với doc trên | 15 phút |
| 15 | `database/seed.sql` | Dữ liệu mẫu: 6 users, 34 violations, 40 signs... | 5 phút |
| 16 | `database/setup.php` | Script import schema + seed 1 lần | 5 phút |

### Giai đoạn 4: Models (từng bảng DB → 1 class)

| # | File | Bảng | Điểm đặc biệt |
|---|------|------|---------------|
| 17 | `app/models/User.php` | `users` | findByEmail, isBanned |
| 18 | `app/models/Violation.php` | `violations` | searchByPlate, topOffenses, countByMonth |
| 19 | `app/models/News.php` | `news` | getPublished, getWithCategory, incrementViews |
| 20 | `app/models/` còn lại | các bảng khác | Đa số kế thừa CRUD từ Model cha |

### Giai đoạn 5: Controllers & Views (theo nhóm chức năng)

| # | Nhóm | Controllers | Views |
|---|------|-------------|-------|
| 21 | **Trang chủ + Tra cứu** | `HomeController`, `TraCuuController` | `home/index`, `tracuu/index` |
| 22 | **Auth** | `AuthController` | `auth/login`, `auth/register` |
| 23 | **Tin tức** | `TinTucController` | `tintuc/index`, `tintuc/detail` |
| 24 | **Biển báo** | `BienBaoController` | `bienbao/index`, `bienbao/detail` |
| 25 | **Tài khoản user** | `TaiKhoanController` | `taikhoan/*` |
| 26 | **Thống kê + Bản đồ + FAQ + Liên hệ** | 4 controllers | 4 views |
| 27 | **Admin Dashboard** | `DashboardController` | `admin/dashboard/index` |
| 28 | **Admin CRUD** | `UserController`, `ViolationController`, `NewsController`... | `admin/*/index`, `admin/*/form` |

---

## 3. KIẾN TRÚC HỆ THỐNG

### Sơ đồ luồng xử lý 1 request

```
Người dùng gõ URL: http://localhost/tra-cuu
            │
            ▼
┌──────────────────────────────────────┐
│  Apache (.htaccess)                  │
│  RewriteRule → index.php?url=tra-cuu │
└──────────────────────────────────────┘
            │
            ▼
┌──────────────────────────────────────┐
│  public/index.php                    │
│  1. spl_autoload_register            │
│  2. Session::start()                 │
│  3. Load config                      │
│  4. Tạo Router, load routes          │
│  5. Router->dispatch('/tra-cuu')     │
└──────────────────────────────────────┘
            │
            ▼
┌──────────────────────────────────────┐
│  Router.php — dispatch()             │
│  1. So khớp URL với regex pattern    │
│  2. Trích xuất tham số từ URL        │
│  3. Merge vào $_GET                  │
│  4. Xác định controller class        │
│  5. Gọi controller->action()         │
└──────────────────────────────────────┘
            │
            ▼
┌──────────────────────────────────────┐
│  TraCuuController@index()            │
│  1. Nhận input từ $_GET/$_POST       │
│  2. Gọi Model (database query)       │
│  3. Chuẩn bị data array              │
│  4. Gọi $this->view() để render      │
└──────────────────────────────────────┘
            │
            ▼
┌──────────────────────────────────────┐
│  Controller->view()                  │
│  1. extract($data) → biến cho view   │
│  2. ob_start() → render view file    │
│  3. Nhúng nội dung vào layout        │
│  4. Output HTML hoàn chỉnh           │
└──────────────────────────────────────┘
```

### Cấu trúc thư mục

```
htdocs/
├── public/                    ← DocumentRoot (CHỈ thư mục này public)
│   ├── index.php              ← Entry point duy nhất
│   ├── .htaccess              ← URL rewriting
│   └── assets/                ← css, js, images, uploads
│       ├── css/style.css
│       ├── css/admin.css
│       ├── js/main.js
│       └── uploads/           ← Ảnh upload (news thumbnails, sign images)
│           ├── news_*.jpg     ← 8 ảnh tin tức
│           └── signs/         ← 40 ảnh biển báo
│
├── app/
│   ├── core/                  ← 7 class nền tảng của framework
│   │   ├── Database.php       ← Singleton PDO connection
│   │   ├── Router.php         ← URL routing engine
│   │   ├── Controller.php     ← Base controller (view, auth, CSRF)
│   │   ├── Model.php          ← Base model (CRUD tự động)
│   │   ├── Session.php        ← Session + flash + auth + CSRF
│   │   ├── Validator.php      ← Input validation
│   │   └── Helper.php         ← Tiện ích (slug, format, upload...)
│   │
│   ├── controllers/
│   │   ├── client/            ← 10 controllers cho guest/user
│   │   │   ├── HomeController.php
│   │   │   ├── AuthController.php
│   │   │   ├── TraCuuController.php
│   │   │   ├── TinTucController.php
│   │   │   ├── BienBaoController.php
│   │   │   ├── TaiKhoanController.php
│   │   │   ├── BanDoController.php
│   │   │   ├── ThongKeController.php
│   │   │   ├── FaqController.php
│   │   │   └── LienHeController.php
│   │   └── admin/             ← 10 controllers cho admin
│   │       ├── DashboardController.php
│   │       ├── UserController.php
│   │       ├── ViolationController.php
│   │       ├── NewsController.php
│   │       ├── CategoryController.php
│   │       ├── SignController.php
│   │       ├── LocationController.php
│   │       ├── FaqController.php
│   │       ├── AlertController.php
│   │       └── MessageController.php
│   │
│   ├── models/                ← 14 model class (1 class = 1 bảng DB)
│   │   ├── User.php
│   │   ├── Vehicle.php
│   │   ├── Violation.php      ← Có query phức tạp (search, stats)
│   │   ├── Offense.php
│   │   ├── OffenseCategory.php
│   │   ├── Location.php
│   │   ├── News.php           ← Có query đặc biệt (published, related)
│   │   ├── NewsCategory.php
│   │   ├── TrafficSign.php
│   │   ├── TrafficSignGroup.php
│   │   ├── Faq.php
│   │   ├── SearchHistory.php
│   │   ├── TrafficAlert.php
│   │   └── ContactMessage.php
│   │
│   └── views/
│       ├── layouts/           ← 2 layout chính
│       │   ├── client.php     ← <html> + navbar + footer cho guest/user
│       │   └── admin.php      ← <html> + sidebar + header cho admin
│       ├── partials/          ← Component tái sử dụng
│       │   ├── header.php     ← Navbar chung (responsive, dropdown user)
│       │   ├── footer.php     ← Footer 3 cột
│       │   ├── sidebar.php    ← Admin sidebar (10 menu items)
│       │   ├── pagination.php ← Phân trang Bootstrap
│       │   └── alerts.php     ← Flash messages (success/error/warning)
│       ├── client/            ← 15+ views cho phía client
│       │   ├── home/index.php
│       │   ├── auth/login.php
│       │   ├── auth/register.php
│       │   ├── tracuu/index.php
│       │   ├── tintuc/index.php
│       │   ├── tintuc/detail.php
│       │   ├── bienbao/index.php
│       │   ├── bienbao/detail.php
│       │   ├── bando/index.php
│       │   ├── thongke/index.php
│       │   ├── faq/index.php
│       │   ├── taikhoan/dashboard.php
│       │   ├── taikhoan/vehicles.php
│       │   ├── taikhoan/history.php
│       │   ├── taikhoan/profile.php
│       │   └── pages/ (gioi-thieu, lien-he)
│       └── admin/             ← 20+ views cho admin panel
│           ├── dashboard/index.php
│           ├── users/index.php, form.php
│           ├── violations/index.php, form.php
│           ├── news/index.php, form.php
│           ├── categories/index.php
│           ├── signs/index.php, form.php
│           ├── locations/index.php, form.php
│           ├── faqs/index.php, form.php
│           ├── alerts/index.php, form.php
│           └── messages/index.php
│
├── config/
│   ├── config.example.php     ← File mẫu cấu hình (copy thành config.php)
│   ├── config.php             ← KHÔNG commit (gitignore) — cấu hình thực tế
│   └── routes.php             ← 92 routes (GET + POST)
│
├── database/
│   ├── schema.sql             ← 14 bảng + indexes + foreign keys
│   ├── seed.sql               ← Dữ liệu mẫu
│   └── setup.php              ← Script import 1 lần
│
└── docs/                      ← Tài liệu thiết kế
    ├── README.md              ← FILE NÀY — hướng dẫn đọc hiểu
    ├── 01-tong-quan-du-an.md
    ├── 02-phan-tich-yeu-cau.md
    ├── 03-thiet-ke-co-so-du-lieu.md
    ├── 04-thiet-ke-giao-dien.md
    ├── 05-kien-truc-he-thong.md
    ├── 06-ke-hoach-phat-trien.md
    └── 08-huong-dan-cai-dat-chay-du-an.md  ← Hướng dẫn cài đặt khi clone về máy mới
```

---

## 4. CORE LAYER — BỘ KHUNG MVC

Đây là 7 class trong `app/core/` tạo thành "framework mini" của dự án. Không dùng Composer, không Laravel — mọi thứ tự xây dựng.

### 4.1. Database.php — Singleton PDO

```
App\Core\Database
```

**Vai trò:** Đảm bảo **chỉ 1 kết nối PDU** tồn tại trong toàn bộ request. Dùng pattern Singleton.

**Cách dùng:**
```php
$db = Database::getInstance()->getConnection();
// $db là PDO object, dùng prepared statements
```

**Cấu hình PDO:**
- `ERRMODE_EXCEPTION` — ném lỗi thành exception
- `FETCH_ASSOC` — trả về mảng kết hợp
- `EMULATE_PREPARES = false` — prepared statement thực sự (bảo mật)

### 4.2. Router.php — Định tuyến URL

```
App\Core\Router
```

**Vai trò:** Nhận URL từ browser, so khớp với bảng routes, gọi đúng controller + action.

**Cơ chế hoạt động:**

1. **Đăng ký route:** `$router->get('/tra-cuu', 'client/TraCuuController@index')`
2. **Biến đổi pattern:** `{id}` trong URL → regex named group `(?P<id>[^/]+)`
3. **Dispatch:** Duyệt tất cả route, so khớp URL với regex
4. **Trích xuất params:** Các `{param}` từ URL được merge vào `$_GET`
5. **Gọi controller:** `new TraCuuController()` → `->index()`

**Ví dụ:**
```
URL: /admin/users/3/edit
Route: GET /admin/users/{id}/edit → admin/UserController@edit
Kết quả: new UserController()->edit('3')
         $_GET['id'] = '3'  ← controller dùng $this->input('id') lấy được
```

### 4.3. Controller.php — Base Controller

```
App\Core\Controller
```

TẤT CẢ controller đều kế thừa class này. Cung cấp:

| Method | Chức năng |
|--------|-----------|
| `view($file, $data, $layout)` | Render view + nhúng vào layout (client/admin) |
| `redirect($url)` | Chuyển hướng HTTP |
| `input($key, $default)` | Lấy giá trị từ `$_POST` hoặc `$_GET` (đã trim) |
| `requireLogin()` | Chặn nếu chưa đăng nhập → redirect `/dang-nhap` |
| `requireAdmin()` | Chặn nếu không phải admin → redirect `/` |
| `validateCsrf()` | Kiểm tra CSRF token trong POST request |
| `isAjax()` | Kiểm tra `X-Requested-With` hoặc `Accept: application/json` header |
| `json($data, $code)` | Trả về JSON response |

**Cơ chế `view()`:**
1. `extract($data)` — biến `['title' => 'ABC']` thành `$title = 'ABC'` trong view
2. `ob_start()` → require view file → `ob_get_clean()` — bắt HTML output
3. Nhúng `$content` vào layout (`layouts/client.php` hoặc `layouts/admin.php`)

### 4.4. Model.php — Base Model

```
App\Core\Model
```

TẤT CẢ model đều kế thừa class này. Cung cấp CRUD tự động:

| Method | SQL tương đương |
|--------|-----------------|
| `all($conditions, $orderBy, $limit, $offset)` | `SELECT * FROM table WHERE ... ORDER BY ... LIMIT ...` |
| `find($id)` | `SELECT * FROM table WHERE id = ?` |
| `findBy($col, $val)` | `SELECT * FROM table WHERE col = ? LIMIT 1` |
| `findAllBy($col, $val)` | `SELECT * FROM table WHERE col = ?` |
| `create($data)` | `INSERT INTO table (...) VALUES (...)` |
| `update($id, $data)` | `UPDATE table SET ... WHERE id = ?` |
| `delete($id)` | `DELETE FROM table WHERE id = ?` |
| `count($conditions)` | `SELECT COUNT(*) FROM table WHERE ...` |
| `paginate($page, $perPage)` | Trả về items + metadata phân trang |
| `query($sql, $params)` | Raw query với prepared statements |

**Tự động suy tên bảng:** Class `User` → table `users`, class `TrafficSign` → table `traffic_signs`.

**TẤT CẢ query dùng prepared statements** — không SQL injection.

### 4.5. Session.php — Quản lý phiên

```
App\Core\Session
```

Toàn bộ là static methods:

| Method | Chức năng |
|--------|-----------|
| `start()` | Khởi tạo session với tên và lifetime từ config |
| `login($user)` | Ghi user_id, name, email, role vào session + `session_regenerate_id(true)` |
| `logout()` | Xóa session + hủy cookie |
| `isLoggedIn()` | Kiểm tra đã đăng nhập |
| `isAdmin()` | Kiểm tra role = admin |
| `get($key)` / `set($key, $val)` | Get/set session variable |
| `setFlash($type, $msg)` | Flash message (chỉ tồn tại 1 request) |
| `getFlash($type)` | Lấy và xóa flash message |
| `csrfToken()` | Tạo CSRF token (32 bytes random) |
| `validateCsrf($token)` | So sánh token với `hash_equals` (timing-safe) |

### 4.6. Validator.php — Kiểm tra dữ liệu

```
App\Core\Validator
```

**Static methods (dùng nhanh):**
- `Validator::plateNumber($plate)` — regex biển số Việt Nam: `\d{2}[A-Z]\d?[-\s]?\d{4,5}`
- `Validator::email($email)` — `filter_var` với `FILTER_VALIDATE_EMAIL`
- `Validator::phone($phone)` — regex 10-11 số VN: `0\d{9,10}`
- `Validator::required($val)` — không rỗng
- `Validator::minLength($val, $min)` / `maxLength($val, $max)`

**Instance method `validate($data, $rules)`:**
```php
$v = new Validator();
$v->validate($_POST, [
    'email' => 'required|email',
    'password' => 'required|min:6|max:255',
    'phone' => 'phone',
]);
if (!$v->passes()) {
    $errors = $v->getErrors(); // ['email' => ['...'], ...]
}
```

### 4.7. Helper.php — Tiện ích

```
App\Core\Helper
```

| Method | Chức năng | Ví dụ |
|--------|-----------|-------|
| `slug($str)` | Tạo slug tiếng Việt | `"Cao tốc Bắc Nam"` → `"cao-toc-bac-nam"` |
| `formatCurrency($amount)` | Format tiền VNĐ | `1500000` → `"1.500.000 ₫"` |
| `formatDate($datetime, $format)` | Format ngày giờ | `"d/m/Y H:i"` |
| `formatDateTime($datetime)` | Format đầy đủ | |
| `excerpt($text, $length)` | Cắt đoạn văn | |
| `truncate($text, $length)` | Cắt chuỗi | |
| `upload($file, $subDir)` | Upload file | Kiểm tra loại, kích thước, tạo tên unique |

---

## 5. CONFIG & ENTRY POINT

### 5.1. `public/index.php` — Entry Point

Mọi HTTP request đều vào đây (nhờ `.htaccess` rewrite). Thực hiện theo thứ tự:

1. **Error handling:** Ghi log vào `error.log`, không hiển thị lỗi ra browser
2. **Autoloader:** `spl_autoload_register` chuyển namespace → file path
   - `App\Core\Database` → `app/core/Database.php`
   - `App\Controllers\Client\HomeController` → `app/controllers/client/HomeController.php`
   - `App\Models\User` → `app/models/User.php`
3. **Session start:** Gọi `Session::start()`
4. **Load config:** `require config/config.php`
5. **Router:** Tạo Router object, load 92 routes từ `config/routes.php`
6. **Dispatch:** `$router->dispatch($uri, $method)` — chạy controller tương ứng

### 5.2. `config/config.example.php` — Cấu hình (file mẫu)

> ⚠️ File `config.php` KHÔNG được commit (gitignore). Khi clone về, copy `config.example.php` thành `config.php` và sửa thông số cho khớp XAMPP trên máy bạn. Xem chi tiết tại [`docs/08-huong-dan-cai-dat-chay-du-an.md`](08-huong-dan-cai-dat-chay-du-an.md).

```php
return [
    'db_host'   => 'localhost',
    'db_name'   => 'traffic_violation_db',
    'db_user'   => 'root',
    'db_pass'   => '',              // XAMPP mặc định = rỗng
    'db_charset' => 'utf8mb4',

    'app_name'    => 'Tra Cứu Phương Tiện Vi Phạm Giao Thông',
    'app_url'     => 'http://localhost',
    'app_version' => '1.0.0',

    'session_lifetime' => 86400,        // 24 giờ
    'session_name'     => 'TRAFFIC_SESSION',

    'upload_max_size'      => 5242880,  // 5MB
    'upload_allowed_types' => ['jpg','jpeg','png','gif','webp'],
    'upload_path'          => 'public/assets/uploads/',

    'per_page'      => 10,             // Số item mỗi trang (admin)
    'news_per_page' => 9,              // Số tin mỗi trang (client)
];
```

### 5.3. `config/routes.php` — Bảng định tuyến

**92 routes** chia làm 3 nhóm:

#### Routes công khai (không cần đăng nhập) — 17 routes
```
GET  /                 → HomeController@index      (Trang chủ)
GET  /tra-cuu          → TraCuuController@index    (Form tra cứu)
POST /tra-cuu          → TraCuuController@search   (Xử lý tra cứu)
GET  /tin-tuc          → TinTucController@index    (Danh sách tin)
GET  /tin-tuc/{slug}   → TinTucController@detail   (Chi tiết tin)
GET  /bien-bao         → BienBaoController@index   (Danh sách biển báo)
GET  /bien-bao/{id}    → BienBaoController@detail  (Chi tiết biển báo)
GET  /ban-do           → BanDoController@index     (Bản đồ)
GET  /thong-ke         → ThongKeController@index   (Thống kê)
GET  /faq              → FaqController@index       (FAQ)
GET  /gioi-thieu       → HomeController@about      (Giới thiệu)
GET  /lien-he          → LienHeController@index    (Form liên hệ)
POST /lien-he          → LienHeController@send     (Gửi liên hệ)
GET  /dang-nhap        → AuthController@loginForm  (Form đăng nhập)
POST /dang-nhap        → AuthController@login      (Xử lý đăng nhập)
GET  /dang-ky          → AuthController@registerForm
POST /dang-ky          → AuthController@register
GET  /dang-xuat        → AuthController@logout
```

#### Routes user (cần đăng nhập) — 8 routes
```
GET  /tai-khoan                    → dashboard
GET  /tai-khoan/phuong-tien        → vehicles (list)
POST /tai-khoan/phuong-tien        → addVehicle
POST /tai-khoan/phuong-tien/{id}/edit   → updateVehicle
POST /tai-khoan/phuong-tien/{id}/delete → deleteVehicle
GET  /tai-khoan/lich-su            → history
GET  /tai-khoan/ho-so              → profile
POST /tai-khoan/ho-so              → updateProfile
POST /tai-khoan/doi-mat-khau       → changePassword
```

#### Routes admin (cần quyền admin) — 67 routes
```
GET  /admin                           → DashboardController@index
GET  /admin/users                     → UserController@index (list)
GET  /admin/users/create              → UserController@create (form)
POST /admin/users                     → UserController@store (thêm mới)
GET  /admin/users/{id}/edit           → UserController@edit (form sửa)
POST /admin/users/{id}                → UserController@update (cập nhật)
POST /admin/users/{id}/delete         → UserController@delete (xóa)
POST /admin/users/{id}/toggle-status  → UserController@toggleStatus

... Tương tự cho violations, news, signs, locations, faqs, alerts
... Thêm /admin/categories (chỉ POST, không có form riêng)
... Thêm /admin/messages (chỉ read + delete, không CRUD)
... Thêm POST /admin/violations/import (import CSV)
```

---

## 6. DATABASE LAYER

### 6.1. 14 bảng — tổng quan

| # | Bảng | Số dòng seed | Vai trò |
|---|------|-------------|---------|
| 1 | `users` | 6 | Người dùng (1 admin + 5 user) |
| 2 | `offense_categories` | 6 | Danh mục nhóm lỗi (tốc độ, đèn tín hiệu...) |
| 3 | `offenses` | 20 | Danh sách lỗi vi phạm + mức phạt |
| 4 | `locations` | 18 | Địa điểm (camera, trạm CSGT, thu phí...) |
| 5 | `violations` | 34 | **Bảng chính** — vi phạm giao thông |
| 6 | `news_categories` | 5 | Danh mục tin tức |
| 7 | `news` | 8 | Bài viết tin tức |
| 8 | `traffic_sign_groups` | 5 | Nhóm biển báo (P/W/R/S/phụ) |
| 9 | `traffic_signs` | 40 | Biển báo giao thông |
| 10 | `faqs` | 8 | Câu hỏi thường gặp |
| 11 | `search_history` | 0 | Lịch sử tra cứu (tạo khi search) |
| 12 | `traffic_alerts` | 2 | Cảnh báo giao thông |
| 13 | `contact_messages` | 0 | Tin nhắn liên hệ (tạo khi gửi form) |
| 14 | `vehicles` | 6 | Phương tiện của user |

### 6.2. Quan hệ chính

```
users (1) ──────< vehicles (N)         user sở hữu phương tiện
users (1) ──────< news (N)             user là tác giả bài viết
users (1) ──────< search_history (N)   user có lịch sử tra cứu

offense_categories (1) ──< offenses (N)   nhóm lỗi chứa các lỗi
offenses (1) ──< violations (N)           lỗi được gán cho vi phạm

locations (1) ──< violations (N)          địa điểm xảy ra vi phạm

news_categories (1) ──< news (N)          danh mục chứa bài viết

traffic_sign_groups (1) ──< traffic_signs (N)   nhóm chứa biển báo
```

### 6.3. ENUMs trong database

| Bảng | Cột | Giá trị |
|------|-----|---------|
| `users` | `role` | `'user'`, `'admin'` |
| `users` | `status` | `1` (active), `0` (banned) |
| `violations` | `vehicle_type` | `'car'`, `'motorcycle'`, `'electric_motorcycle'` |
| `violations` | `status` | `'pending'`, `'processed'`, `'paid'` |
| `locations` | `type` | `'camera'`, `'csgt'`, `'toll'`, `'inspection'` |
| `news` | `status` | `'draft'`, `'published'` |
| `traffic_alerts` | `alert_type` | `'accident'`, `'congestion'`, `'construction'`, `'weather'`, `'other'` |

### 6.4. Indexes quan trọng

- `violations.idx_lookup` — index kép `(plate_number, vehicle_type)` cho tra cứu
- `violations.idx_plate` — index đơn `plate_number`
- `violations.idx_violation_date` — cho thống kê theo thời gian
- `news.idx_slug` — unique, cho friendly URL
- `news.idx_status` + `idx_created` — cho lọc bài published + sắp xếp

---

## 7. MODELS

### 7.1. Pattern chung

Mỗi bảng database có 1 class Model. Model kế thừa `App\Core\Model`, đặt `protected string $table = 'table_name'`.

```php
class User extends Model {
    protected string $table = 'users';
    // Kế thừa: all(), find(), create(), update(), delete(), paginate()...
}
```

### 7.2. Model đặc biệt

**Violation.php** — có các method query phức tạp:
- `searchByPlate($plate, $type)` — JOIN 3 bảng violations + locations + offenses
- `topOffenses($limit)` — GROUP BY offense_id, ORDER BY COUNT DESC
- `topLocations($limit)` — GROUP BY location_id
- `topPlates($limit)` — GROUP BY plate_number
- `countByMonth($year)` — GROUP BY MONTH(violation_date)
- `countByStatus()` — GROUP BY status
- `countToday()` — WHERE DATE(violation_date) = CURDATE()
- `getAllWithDetails()` — JOIN locations + offenses cho admin list

**News.php** — các method đặc biệt:
- `getPublished()` — WHERE status = 'published'
- `getWithCategory($id)` — JOIN news_categories
- `getAllWithCategory()` — JOIN cho admin list
- `incrementViews($id)` — UPDATE views = views + 1
- `getLatest($limit)` — ORDER BY created_at DESC LIMIT
- `getRelated($categoryId, $excludeId, $limit)` — cùng category, không include bài hiện tại

**SearchHistory.php**:
- `log($userId, $plate, $type, $resultCount)` — INSERT + IP address
- `getByUser($userId)` — lịch sử của 1 user

**ContactMessage.php**:
- `markRead($id)` — UPDATE is_read = 1
- `getUnreadCount()` — COUNT WHERE is_read = 0

### 7.3. Tự động map tên class → tên bảng

Model cha dùng Reflection để suy tên bảng:
```
User             → users
NewsCategory     → news_categories
TrafficSign      → traffic_signs
TrafficSignGroup → traffic_sign_groups
```

---

## 8. CONTROLLERS — CLIENT

### 8.1. HomeController (`/`, `/gioi-thieu`)

- `index()` — Load 6 tin mới nhất, top 5 lỗi vi phạm, thống kê tổng + hôm nay → hiển thị homepage
- `about()` — Render trang giới thiệu tĩnh

### 8.2. AuthController (`/dang-nhap`, `/dang-ky`, `/dang-xuat`)

- `loginForm()` — Hiển thị form đăng nhập
- `login()` — Validate email/password, `password_verify()`, kiểm tra banned, gọi `Session::login()`
- `registerForm()` — Hiển thị form đăng ký
- `register()` — Validate input, kiểm tra trùng email/phone, `password_hash(BCRYPT)`, tạo user
- `logout()` — `Session::logout()`, redirect về `/`

### 8.3. TraCuuController (`/tra-cuu`) — Chức năng chính

- `index()` — Form tra cứu với input biển số + select loại xe
- `search()` — **POST handler (hỗ trợ AJAX):**
  1. Validate CSRF token
  2. Validate biển số xe với `Validator::plateNumber()`
  3. Validate loại xe ∈ {car, motorcycle, electric_motorcycle}
  4. Gọi `Violation::searchByPlate()` — JOIN locations + offenses
  5. Log vào `SearchHistory`
  6. Nếu AJAX request (`X-Requested-With: XMLHttpRequest`): trả về JSON {success, count, results, plateNumber, vehicleType}
  7. Nếu request thường: render view với layout (fallback cho non-JS)

### 8.4. TinTucController (`/tin-tuc`, `/tin-tuc/{slug}`)

- `index()` — Lọc theo category (?cat=), phân trang 9 tin/lần
- `detail($slug)` — Lấy chi tiết bài viết, increment views, load bài liên quan

### 8.5. BienBaoController (`/bien-bao`, `/bien-bao/{id}`)

- `index()` — Lọc theo nhóm (?group=), tìm kiếm (?q=), hiển thị grid ảnh
- `detail($id)` — Chi tiết biển báo + danh sách cùng nhóm

### 8.6. TaiKhoanController (`/tai-khoan/*`) — YÊU CẦU ĐĂNG NHẬP

- `dashboard()` — Tổng quan: số phương tiện, lịch sử gần đây
- `vehicles()` — Danh sách phương tiện + form thêm
- `addVehicle()` — POST: validate biển số + loại xe, kiểm tra trùng
- `updateVehicle($id)` — POST: kiểm tra ownership (user_id = session user)
- `deleteVehicle($id)` — POST: kiểm tra ownership → xóa
- `history()` — Lịch sử tra cứu, phân trang
- `profile()` — Form cập nhật thông tin cá nhân
- `updateProfile()` — POST: validate fullname/phone/email, kiểm tra trùng
- `changePassword()` — POST: verify mật khẩu cũ, validate mật khẩu mới, hash

### 8.7. Các controller còn lại

- **BanDoController** — Bản đồ Leaflet/OpenStreetMap với markers từ bảng locations (lọc theo type)
- **ThongKeController** — Biểu đồ Chart.js: violations theo tháng, theo status, top offenses, top locations
- **FaqController** — Danh sách FAQ (câu hỏi thường gặp)
- **LienHeController** — Form liên hệ → lưu vào `contact_messages`

---

## 9. CONTROLLERS — ADMIN

TẤT CẢ admin controller đều gọi `$this->requireAdmin()` trong `__construct()`.

### 9.1. Pattern CRUD chung

Mỗi entity có 5-6 action theo pattern giống hệt nhau:

| Action | Route | Chức năng |
|--------|-------|-----------|
| `index()` | GET /admin/{entity} | Danh sách + phân trang + tìm kiếm |
| `create()` | GET /admin/{entity}/create | Hiển thị form thêm mới |
| `store()` | POST /admin/{entity} | Xử lý thêm mới (CSRF + validate + insert) |
| `edit()` | GET /admin/{entity}/{id}/edit | Hiển thị form sửa |
| `update()` | POST /admin/{entity}/{id} | Xử lý cập nhật (CSRF + validate + update) |
| `delete()` | POST /admin/{entity}/{id}/delete | Xóa (CSRF + kiểm tra tồn tại) |

### 9.2. DashboardController (`/admin`)

Hiển thị trang tổng quan admin:
- Tổng users, violations, news, signs, messages
- Violations chưa xử lý
- Messages chưa đọc
- Top plates vi phạm nhiều nhất

### 9.3. UserController (`/admin/users`)

CRUD users + `toggleStatus()`: khóa/mở khóa user (chuyển `status` 0⇄1) — **hỗ trợ AJAX**, trả về JSON nếu có header `X-Requested-With`. Không cho admin tự xóa chính mình.

### 9.4. ViolationController (`/admin/violations`)

CRUD violations + `toggleStatus()`: xoay vòng trạng thái pending→processed→paid→pending — **AJAX**. + `import()`: import CSV (đọc file, parse, insert từng dòng).

### 9.5. NewsController (`/admin/news`)

CRUD news + CKEditor 5. Lưu ý: tự động tạo slug từ title.

### 9.6. Các controller còn lại

- **CategoryController** — Quản lý danh mục tin (inline edit qua modal, không có form riêng)
- **SignController** — CRUD biển báo + upload ảnh
- **LocationController** — CRUD địa điểm + tọa độ map
- **FaqController** — CRUD FAQ
- **AlertController** — CRUD cảnh báo giao thông
- **MessageController** — Xem + đánh dấu đã đọc + xóa tin nhắn liên hệ

---

## 10. VIEWS & LAYOUTS

### 10.1. Layout system

```
layouts/client.php  ← Cho guest/user (navbar + footer)
layouts/admin.php   ← Cho admin (sidebar + header)
```

**Layout client:** `<html>` → navbar (từ `partials/header.php`) → `<main>$content</main>` → footer (`partials/footer.php`)

**Layout admin:** `<html>` → sidebar (`partials/sidebar.php`) → `<main>$content</main>`

### 10.2. Các partials

- **header.php** — Navbar Bootstrap responsive. Hiển thị menu khác nhau cho guest (đăng nhập/đăng ký) và user (dropdown tài khoản)
- **footer.php** — Footer 3 cột: giới thiệu, liên kết nhanh, thông tin liên hệ
- **sidebar.php** — Admin sidebar: 10 menu items (dashboard, users, violations, news, categories, signs, locations, faqs, alerts, messages)
- **pagination.php** — Phân trang Bootstrap với Previous/Next + số trang
- **alerts.php** — Flash messages: success (xanh), error (đỏ), warning (vàng)

### 10.3. Cấu trúc view điển hình

Mỗi view client:

```php
<!-- File: app/views/client/tintuc/index.php -->
<?php use App\Core\Helper; ?>     ← Import helper

<div class="container py-4">      ← Bootstrap container
    <h2>Tiêu đề trang</h2>

    <?php require __DIR__ . '/../../partials/alerts.php'; ?>  ← Flash messages

    <!-- Nội dung chính -->
    <?php foreach ($news as $item): ?>
        <!-- Card, table, form... -->
    <?php endforeach; ?>

    <?php require __DIR__ . '/../../partials/pagination.php'; ?>
</div>
```

### 10.4. Truyền data từ controller → view

```php
// Trong controller:
$this->view('client/tintuc/index', [
    'title'      => 'Tin tức giao thông',
    'news'       => $newsArray,
    'categories' => $categoriesArray,
    'pagination' => $paginationArray,
]);

// Trong view — dùng trực tiếp biến đã extract:
echo $title;                // "Tin tức giao thông"
foreach ($news as $item)    // $newsArray
```

---

## 11. ASSETS & FRONTEND

### 11.1. CSS

- **`public/assets/css/style.css`** — CSS cho toàn bộ client:
  - CSS variables cho color scheme (primary blue, warning yellow...)
  - Hero section với form tra cứu
  - Stat cards, news cards
  - Responsive breakpoints
  - Custom styles cho bảng, form, badge

- **`public/assets/css/admin.css`** — CSS cho admin panel:
  - Sidebar cố định bên trái (250px)
  - Dashboard stat cards
  - Table styles
  - Responsive: sidebar ẩn trên mobile

### 11.2. JavaScript

- **`public/assets/js/main.js`** — JavaScript cho client:
  - Auto-hide flash alerts sau 5 giây
  - Uppercase tự động cho input biển số
  - AJAX tra cứu phạt nguội (fetch API, loading spinner, render kết quả động)

- **`public/assets/js/admin.js`** — JavaScript cho admin panel:
  - Sidebar toggle trên mobile
  - Confirm dialog cho nút xóa
  - Auto-hide alerts
  - **AJAX toggle trạng thái** — user (lock/unlock) và violation (pending→processed→paid)
    - Gửi POST với CSRF token từ meta tag
    - Cập nhật badge + icon không reload trang
    - Fallback về redirect nếu không có JS

### 11.3. Thư viện bên ngoài (CDN)

- Bootstrap 5.3 CSS + JS bundle
- Font Awesome 6.5 CSS
- Chart.js 4.x (chỉ load ở trang thống kê)
- Leaflet CSS + JS (chỉ load ở trang bản đồ)

---

## 12. BẢO MẬT

### 12.1. Các biện pháp đã triển khai

| Biện pháp | Triển khai ở đâu |
|-----------|-----------------|
| **Prepared Statements** (chống SQL injection) | `Model.php` — TẤT CẢ query |
| **CSRF Token** (chống Cross-Site Request Forgery) | Mọi POST form + AJAX. Form: hidden input. AJAX: gửi FormData + meta tag `<meta name="csrf-token">`. `Controller::validateCsrf()` |
| **Password Hashing** (bcrypt) | `AuthController`, `UserController` — `password_hash(PASSWORD_BCRYPT)` |
| **Session Regeneration** (chống session fixation) | `Session::login()` — `session_regenerate_id(true)` |
| **XSS Prevention** (output escaping) | `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` trong mọi view |
| **Auth Guards** (phân quyền) | `requireLogin()`, `requireAdmin()` |
| **File Upload Validation** | `Helper::upload()` — kiểm tra loại, kích thước |
| **Error Logging** (không lộ lỗi ra ngoài) | `display_errors = 0`, `log_errors = 1` |
| **AJAX Auth Handling** | `toggleStatus()` + `search()` trả `{success: false}` HTTP 419/422 thay vì redirect — không lộ flash message |

### 12.2. Luồng bảo mật cho 1 POST request

```
1. User submits form
2. Controller::validateCsrf() — kiểm tra token
3. Validator::validate() — kiểm tra input rules
4. Model::create() — prepared statement INSERT
5. View hiển thị — htmlspecialchars() mọi output
```

---

## 13. QUY ƯỚC & PATTERNS

### 13.1. Đặt tên

- **Controller:** `PascalCase`, suffix `Controller`, namespace `App\Controllers\Client|Admin`
- **Model:** `PascalCase`, same as table name (singular), namespace `App\Models`
- **Table:** `snake_case`, plural (VD: `traffic_signs`, `news_categories`)
- **View:** `kebab-case` folder, action-based file (VD: `tintuc/index.php`, `tintuc/detail.php`)
- **Route:** Friendly URL tiếng Việt, dùng dấu gạch ngang (VD: `/tra-cuu`, `/tin-tuc`)
- **Method:** `camelCase` (VD: `searchByPlate`, `getWithCategory`)

### 13.2. Pattern cho controller action

```php
public function store(): void    // POST — thêm mới
{
    // 1. CSRF check
    if (!$this->validateCsrf()) return;

    // 2. Validate input
    $validator = new Validator();
    if (!$validator->validate($_POST, $rules)) {
        Session::setFlash('error', ...);
        $this->redirect('/back');
        return;
    }

    // 3. Business logic
    $model = new XxxModel();
    $model->create([...]);

    // 4. Redirect with flash message
    Session::setFlash('success', 'Thêm thành công.');
    $this->redirect('/list');
}
```

### 13.3. Pattern cho view

```php
<!-- Luôn dùng htmlspecialchars cho output từ user/DB -->
<?= htmlspecialchars($var, ENT_QUOTES, 'UTF-8') ?>

<!-- Kiểm tra tồn tại trước khi hiển thị -->
<?php if (!empty($item['thumbnail'])): ?>
    <img src="/<?= htmlspecialchars($item['thumbnail'], ENT_QUOTES, 'UTF-8') ?>" ...>
<?php else: ?>
    <div class="placeholder">...</div>
<?php endif; ?>

<!-- Dùng Helper cho format -->
<?= htmlspecialchars(Helper::formatDate($item['created_at'], 'd/m/Y')) ?>
```

### 13.4. Quy tắc code

- **PSR-12:** 4 spaces indent, `<?php` tag, không closing `?>`
- **Namespace:** `App\...` cho mọi class
- **PHP 8.x features:** named arguments, match expression, constructor promotion, typed properties
- **No Composer:** Autoload qua `spl_autoload_register`

---

## PHỤ LỤC A: SƠ ĐỒ DATABASE (14 BẢNG)

```
users (1) ──────< vehicles (N)
users (1) ──────< news (N)               [author_id]
users (1) ──────< search_history (N)     [user_id]
users (1) ──────< traffic_alerts (N)     [created_by]

offense_categories (1) ──< offenses (N)  [category_id]
offenses (1) ──< violations (N)          [offense_id]

locations (1) ──< violations (N)         [location_id]

news_categories (1) ──< news (N)         [category_id]

traffic_sign_groups (1) ──< traffic_signs (N)  [group_id]

faqs, contact_messages: bảng độc lập
```

## PHỤ LỤC B: CÁC TRANG CHÍNH

| URL | Controller | View | Auth |
|-----|-----------|------|------|
| `/` | HomeController@index | `home/index` | Guest |
| `/tra-cuu` | TraCuuController@index | `tracuu/index` | Guest |
| `/tin-tuc` | TinTucController@index | `tintuc/index` | Guest |
| `/tin-tuc/{slug}` | TinTucController@detail | `tintuc/detail` | Guest |
| `/bien-bao` | BienBaoController@index | `bienbao/index` | Guest |
| `/bien-bao/{id}` | BienBaoController@detail | `bienbao/detail` | Guest |
| `/ban-do` | BanDoController@index | `bando/index` | Guest |
| `/thong-ke` | ThongKeController@index | `thongke/index` | Guest |
| `/faq` | FaqController@index | `faq/index` | Guest |
| `/gioi-thieu` | HomeController@about | `pages/gioi-thieu` | Guest |
| `/lien-he` | LienHeController@index | `pages/lien-he` | Guest |
| `/dang-nhap` | AuthController@loginForm | `auth/login` | Guest |
| `/dang-ky` | AuthController@registerForm | `auth/register` | Guest |
| `/tai-khoan` | TaiKhoanController@dashboard | `taikhoan/dashboard` | **User** |
| `/tai-khoan/phuong-tien` | TaiKhoanController@vehicles | `taikhoan/vehicles` | **User** |
| `/tai-khoan/lich-su` | TaiKhoanController@history | `taikhoan/history` | **User** |
| `/tai-khoan/ho-so` | TaiKhoanController@profile | `taikhoan/profile` | **User** |
| `/admin` | DashboardController@index | `admin/dashboard/index` | **Admin** |
| `/admin/users` | UserController@index | `admin/users/index` | **Admin** |
| `/admin/violations` | ViolationController@index | `admin/violations/index` | **Admin** |
| `/admin/news` | NewsController@index | `admin/news/index` | **Admin** |
| `/admin/signs` | SignController@index | `admin/signs/index` | **Admin** |
| `/admin/locations` | LocationController@index | `admin/locations/index` | **Admin** |
| `/admin/faqs` | FaqController@index | `admin/faqs/index` | **Admin** |
| `/admin/alerts` | AlertController@index | `admin/alerts/index` | **Admin** |
| `/admin/messages` | MessageController@index | `admin/messages/index` | **Admin** |

---

*Tài liệu này được viết cho mục đích học thuật — Đồ án bài tập lớn PHP.*
