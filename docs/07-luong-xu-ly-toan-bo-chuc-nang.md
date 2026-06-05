# Luồng xử lý toàn bộ chức năng — Từ Frontend đến Backend

> Tài liệu này mô tả chi tiết mọi luồng thao tác trong hệ thống, đi từ thao tác trên giao diện → file code xử lý → cơ sở dữ liệu → kết quả trả về.

---

# A. HẠ TẦNG CORE — Cách hệ thống khởi động

## A.1. Sơ đồ tổng quát

```
Trình duyệt (URL)
  → public/.htaccess (Rewrite URL)
    → public/index.php (Front Controller — Entry Point duy nhất)
      → spl_autoload_register (PSR-4-like autoload)
      → Session::start()
      → Load config/config.php
      → Router khởi tạo + Load config/routes.php (đăng ký ~80 routes)
      → $router->dispatch($uri, $method)
        → Khớp route → Parse handler → Khởi tạo Controller
          → Controller action xử lý logic
            → Gọi Model (PDO prepared statements)
            → Gọi $this->view() hoặc $this->json()
              → View render HTML + Layout bọc ngoài → Trả về trình duyệt
```

## A.2. Các file core

| File | Vai trò |
|------|--------|
| `public/index.php` | Entry point — mọi request đều qua đây. Đăng ký autoload, khởi tạo session, load config, tạo Router, dispatch |
| `public/.htaccess` | Rewrite URL thân thiện → `index.php?url=xxx` |
| `app/core/Router.php` | Đăng ký route GET/POST, khớp URL pattern + method, parse handler, gọi Controller |
| `app/core/Controller.php` | Base controller: `view()`, `redirect()`, `input()`, `requireLogin()`, `requireAdmin()`, `validateCsrf()`, `isAjax()`, `json()` |
| `app/core/Model.php` | Base model: `all()`, `find()`, `findBy()`, `findAllBy()`, `create()`, `update()`, `delete()`, `count()`, `paginate()`, `query()` |
| `app/core/Database.php` | PDO Singleton — kết nối MySQL, `ATTR_EMULATE_PREPARES = false` |
| `app/core/Session.php` | Session, Flash messages, Auth helpers, CSRF token |
| `app/core/Validator.php` | Validate: `plateNumber()`, `email()`, `phone()`, `required()`, `minLength()`, `maxLength()`, `numeric()` |
| `app/core/Helper.php` | Utilities: `slug()`, `formatCurrency()`, `formatDate()`, `excerpt()`, `upload()` |
| `config/routes.php` | Định nghĩa toàn bộ ~80 routes (URL → Controller@action) |
| `config/config.php` | DB credentials, app settings, session config, upload limits, pagination |

## A.3. Cách đọc code cho bất kỳ chức năng nào

| Bước | Cần tìm | File |
|------|---------|------|
| 1 | URL → Controller nào xử lý | `config/routes.php` |
| 2 | Logic nghiệp vụ | `app/controllers/[client hoặc admin]/XXXController.php` |
| 3 | Truy vấn database | `app/models/XXX.php` |
| 4 | Giao diện HTML | `app/views/[client hoặc admin]/XXX/YYY.php` |
| 5 | Layout bọc ngoài | `app/views/layouts/client.php` hoặc `admin.php` |
| 6 | Components dùng chung | `app/views/partials/header.php`, `footer.php`, `sidebar.php`, `alerts.php`, `pagination.php` |

---

# B. CLIENT — CÁC CHỨC NĂNG CÔNG KHAI (KHÔNG CẦN ĐĂNG NHẬP)

---

## B.1. TRANG CHỦ (Homepage)

**URL:** `GET /`

**File route:** `config/routes.php:14`
```
$router->get('/', 'client/HomeController@index');
```

### Luồng xử lý

```
Giao diện (app/views/client/home/index.php)
  → Người dùng truy cập http://localhost/
    → public/.htaccess → public/index.php
      → Router khớp GET / → client/HomeController@index
```

**Controller:** `app/controllers/client/HomeController.php:10` — method `index()`

```
index() {
    1. new News() → $newsModel->getLatest(6)
       └─ Model: app/models/News.php:66
          → SELECT * FROM news WHERE status='published' ORDER BY created_at DESC LIMIT 6

    2. new Violation() → $violationModel->topOffenses(5)
       └─ Model: app/models/Violation.php:31
          → SELECT o.name, COUNT(*) FROM violations v
            JOIN offenses o ON v.offense_id = o.id
            GROUP BY v.offense_id ORDER BY count DESC LIMIT 5

    3. $violationModel->count()
       └─ Model (kế thừa): app/core/Model.php:121
          → SELECT COUNT(*) FROM violations

    4. $violationModel->countToday()
       └─ Model: app/models/Violation.php:105
          → SELECT COUNT(*) FROM violations WHERE DATE(violation_date) = CURDATE()

    5. $this->view('client/home/index', [
           'title'           → "Tra Cứu Phương Tiện Vi Phạm Giao Thông",
           'latestNews'      → 6 tin mới nhất,
           'topOffenses'     → 5 lỗi phổ biến nhất,
           'totalViolations' → Tổng số vi phạm,
           'todayViolations' → Số vi phạm hôm nay
       ])
       → Render view + layout client.php
}
```

**View:** `app/views/client/home/index.php`
- Form tra cứu nhanh biển số (POST đến `/tra-cuu`)
- 4 ô thống kê (Tổng vi phạm, Hôm nay, Đã xử lý, Camera giám sát)
- Danh sách lỗi vi phạm phổ biến (top 5)
- Danh sách tin tức mới nhất (6 bài)
- Link đến các chức năng khác

**Model sử dụng:**
| Model | Method | SQL |
|-------|--------|-----|
| `News` | `getLatest(6)` | `SELECT * FROM news WHERE status='published' ORDER BY created_at DESC LIMIT 6` |
| `Violation` | `topOffenses(5)` | `SELECT o.name, COUNT(*) FROM violations v JOIN offenses o GROUP BY o.name ORDER BY count DESC LIMIT 5` |
| `Violation` | `count()` | `SELECT COUNT(*) FROM violations` |
| `Violation` | `countToday()` | `SELECT COUNT(*) FROM violations WHERE DATE(violation_date) = CURDATE()` |

---

## B.2. TRA CỨU VI PHẠM (Violation Lookup)

**URLs:**
- `GET /tra-cuu` — Hiển thị form tra cứu
- `POST /tra-cuu` — Thực hiện tra cứu

**File routes:** `config/routes.php:15-16`
```
$router->get('/tra-cuu', 'client/TraCuuController@index');
$router->post('/tra-cuu', 'client/TraCuuController@search');
```

### B.2.1. GET /tra-cuu — Hiển thị form

```
Giao diện (app/views/client/tracuu/index.php)
  → Router → TraCuuController@index()
    → $this->view('client/tracuu/index', ['title' => 'Tra cứu phạt nguội'])
      → Layout client.php bọc ngoài
```

**Giao diện form:**
```html
<form action="/tra-cuu" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
    <input type="text" name="plate_number" placeholder="VD: 30A-12345">
    <select name="vehicle_type">
        <option value="car">Ô tô</option>
        <option value="motorcycle">Xe máy</option>
        <option value="electric_motorcycle">Xe máy điện</option>
    </select>
    <button type="submit">Tra cứu</button>
</form>
```

### B.2.2. POST /tra-cuu — Xử lý tra cứu

```
Người dùng nhập "30A-12345" + chọn "Ô tô" + bấm "Tra cứu"
  → Browser POST /tra-cuu (plate_number=30A-12345, vehicle_type=car, csrf_token=...)
    → public/.htaccess → public/index.php
      → Router khớp POST /tra-cuu → client/TraCuuController@search
```

**Controller:** `app/controllers/client/TraCuuController.php:19` — method `search()`

```
search() {
    ╔══════════════════════════════════════════════════════════╗
    ║ BƯỚC 1: CSRF Token                                      ║
    ║ $this->validateCsrf()                                   ║
    ║ → Session::validateCsrf($token)                         ║
    ║ → hash_equals(session_token, form_token)                ║
    ╚══════════════════════════════════════════════════════════╝

    ╔══════════════════════════════════════════════════════════╗
    ║ BƯỚC 2: Lấy input                                       ║
    ║ $plateNumber = strtoupper($this->input('plate_number')) ║
    ║ $vehicleType = $this->input('vehicle_type')             ║
    ║ → Controller::input() đọc $_POST trước, $_GET sau       ║
    ╚══════════════════════════════════════════════════════════╝

    ╔══════════════════════════════════════════════════════════╗
    ║ BƯỚC 3: Validate server-side                            ║
    ║ - empty($plateNumber) → lỗi "Vui lòng nhập biển số"     ║
    ║ - Validator::plateNumber($plateNumber)                  ║
    ║   → regex: /^\d{2}[A-Z]\d?[-\s]?\d{4,5}$/              ║
    ║ - in_array($vehicleType, ['car','motorcycle',           ║
    ║             'electric_motorcycle'])                      ║
    ╚══════════════════════════════════════════════════════════╝

    ╔══════════════════════════════════════════════════════════╗
    ║ BƯỚC 4: Gọi Model truy vấn database                     ║
    ║ $violationModel = new Violation()                       ║
    ║ $results = $violationModel->searchByPlate('30A-12345',  ║
    ║                                           'car')        ║
    ╚══════════════════════════════════════════════════════════╝
}
```

**Model:** `app/models/Violation.php:13` — method `searchByPlate()`

```sql
SELECT v.*,
       l.name as location_name, l.address as location_address,
       o.name as offense_name, o.penalty as offense_penalty
FROM violations v
LEFT JOIN locations l ON v.location_id = l.id
LEFT JOIN offenses o ON v.offense_id = o.id
WHERE v.plate_number = :plate AND v.vehicle_type = :type
ORDER BY v.violation_date DESC
```

```
    ╔══════════════════════════════════════════════════════════╗
    ║ BƯỚC 5: Lưu lịch sử tra cứu                             ║
    ║ $searchHistory = new SearchHistory()                    ║
    ║ $searchHistory->log($userId, $plateNumber,              ║
    ║                     $vehicleType, count($results))      ║
    ║ → INSERT INTO search_history (user_id, plate_number,    ║
    ║    vehicle_type, result_count, ip_address, searched_at) ║
    ╚══════════════════════════════════════════════════════════╝

    ╔══════════════════════════════════════════════════════════╗
    ║ BƯỚC 6: Trả kết quả                                     ║
    ║ - Nếu AJAX → $this->json([...])  // JSON response       ║
    ║ - Nếu thường → $this->view('client/tracuu/index', [...])║
    ╚══════════════════════════════════════════════════════════╝
```

**View kết quả:** `app/views/client/tracuu/index.php`
- Form tra cứu (giữ nguyên giá trị đã nhập)
- Nếu có kết quả: bảng danh sách vi phạm (Ngày, Địa điểm, Lỗi, Mức phạt, Trạng thái)
- Nếu không có: thông báo "Không tìm thấy vi phạm nào"

**Model sử dụng:**
| Model | Method | SQL |
|-------|--------|-----|
| `Violation` | `searchByPlate($plate, $type)` | JOIN violations + locations + offenses, WHERE plate + type |
| `SearchHistory` | `log($userId, $plate, $type, $count)` | INSERT INTO search_history |

---

## B.3. TIN TỨC (News)

**URLs:**
- `GET /tin-tuc` — Danh sách tin tức (có lọc theo danh mục)
- `GET /tin-tuc/{slug}` — Chi tiết bài viết

**File routes:** `config/routes.php:17-18`
```
$router->get('/tin-tuc', 'client/TinTucController@index');
$router->get('/tin-tuc/{slug}', 'client/TinTucController@detail');
```

### B.3.1. GET /tin-tuc — Danh sách tin tức

```
Giao diện (app/views/client/tintuc/index.php)
  → Người dùng truy cập /tin-tuc (có thể kèm ?cat=1&page=2)
    → Router → TinTucController@index()
```

**Controller:** `app/controllers/client/TinTucController.php:11` — method `index()`

```
index() {
    1. Lấy category_id từ query string (?cat=)
    2. Lấy page từ query string (?page=), mặc định 1
    3. $newsModel->paginate(page, 9, ['status' => 'published'], 'created_at DESC')
       └─ Model (kế thừa): app/core/Model.php:140
          → SELECT COUNT(*) FROM news WHERE status='published'
          → SELECT * FROM news WHERE status='published' ORDER BY created_at DESC LIMIT 9 OFFSET N
    4. $categoryModel->all([], 'name ASC')
       └─ SELECT * FROM news_categories ORDER BY name ASC
    5. Nếu có cat → thêm điều kiện 'category_id'
    6. $this->view('client/tintuc/index', [
           'news'           → danh sách bài viết (9/trang),
           'pagination'     → metadata phân trang,
           'categories'     → danh sách danh mục để filter,
           'currentCategory'→ danh mục đang chọn
       ])
}
```

**View:** `app/views/client/tintuc/index.php`
- Filter bar: danh sách danh mục (link `?cat=id`)
- Grid 9 bài viết (thumbnail, title, excerpt, date)
- Pagination (Previous/Next)

### B.3.2. GET /tin-tuc/{slug} — Chi tiết bài viết

```
Giao diện (app/views/client/tintuc/detail.php)
  → Người dùng click vào bài viết /tin-tuc/tieu-de-bai-viet-123
    → Router khớp GET /tin-tuc/{slug} → TinTucController@detail('tieu-de-bai-viet-123')
```

**Controller:** `app/controllers/client/TinTucController.php:37` — method `detail($slug)`

```
detail($slug) {
    1. getIdFromSlug('tieu-de-bai-viet-123') → 123
       └─ explode('-', $slug) → lấy phần tử cuối, ép int

    2. $newsModel->getWithCategory(123)
       └─ Model: app/models/News.php:16
          → SELECT n.*, nc.name as category_name, u.fullname as author_name
            FROM news n
            LEFT JOIN news_categories nc ON n.category_id = nc.id
            LEFT JOIN users u ON n.author_id = u.id
            WHERE n.id = 123 AND n.status = 'published'

    3. Nếu không tìm thấy → tìm theo slug trực tiếp
       └─ $newsModel->findBy('slug', $slug)

    4. $newsModel->incrementViews(article['id'])
       └─ UPDATE news SET views = views + 1 WHERE id = ?

    5. $newsModel->getRelated(category_id, article_id, 4)
       └─ Model: app/models/News.php:71
          → SELECT * FROM news
            WHERE category_id = :cat AND id != :exc AND status = 'published'
            ORDER BY created_at DESC LIMIT 4

    6. $this->view('client/tintuc/detail', [
           'title'   → tiêu đề bài viết,
           'article' → bài viết đầy đủ,
           'related' → 4 bài liên quan
       ])
}
```

**View:** `app/views/client/tintuc/detail.php`
- Breadcrumb (Trang chủ / Tin tức / Tên bài viết)
- Tiêu đề, ngày đăng, tác giả, lượt xem, danh mục
- Nội dung HTML (từ CKEditor)
- Sidebar: bài viết liên quan (4 bài)

**Model sử dụng:**
| Model | Method | SQL |
|-------|--------|-----|
| `News` | `paginate(page, 9, conditions, orderBy)` | COUNT + SELECT với LIMIT/OFFSET |
| `NewsCategory` | `all([], 'name ASC')` | `SELECT * FROM news_categories ORDER BY name ASC` |
| `News` | `getWithCategory($id)` | JOIN news + categories + users |
| `News` | `findBy('slug', $slug)` | `SELECT * FROM news WHERE slug = :val LIMIT 1` |
| `News` | `incrementViews($id)` | `UPDATE news SET views = views + 1 WHERE id = :id` |
| `News` | `getRelated($catId, $excludeId, 4)` | SELECT news cùng danh mục, loại trừ bài hiện tại |

---

## B.4. BIỂN BÁO GIAO THÔNG (Traffic Signs)

**URLs:**
- `GET /bien-bao` — Danh sách biển báo (có lọc nhóm + tìm kiếm)
- `GET /bien-bao/{id}` — Chi tiết biển báo

**File routes:** `config/routes.php:19-20`
```
$router->get('/bien-bao', 'client/BienBaoController@index');
$router->get('/bien-bao/{id}', 'client/BienBaoController@detail');
```

### B.4.1. GET /bien-bao — Danh sách biển báo

```
Giao diện (app/views/client/bienbao/index.php)
  → Người dùng truy cập /bien-bao (có thể kèm ?group=1 hoặc ?q=tim-kiem)
    → Router → BienBaoController@index()
```

**Controller:** `app/controllers/client/BienBaoController.php:11` — method `index()`

```
index() {
    1. $groupId = $this->input('group', '')      // Lọc theo nhóm
    2. $keyword = $this->input('q', '')           // Tìm kiếm
    3. $groupModel->getAllSorted()
       └─ Model: app/models/TrafficSignGroup.php:10
          → SELECT * FROM traffic_sign_groups ORDER BY sort_order ASC

    4. Phân nhánh:
       a) CÓ keyword → $signModel->search($keyword)
          └─ Model: app/models/TrafficSign.php:24
             → SELECT s.*, g.name as group_name
               FROM traffic_signs s
               LEFT JOIN traffic_sign_groups g ON s.group_id = g.id
               WHERE s.name LIKE :kw OR s.sign_code LIKE :kw2
               ORDER BY s.sign_code
          → Group kết quả theo group_name

       b) CÓ groupId → $signModel->findByGroup($groupId)
          └─ Model (kế thừa): app/core/Model.php:75
             → SELECT * FROM traffic_signs WHERE group_id = :val ORDER BY sign_code ASC

       c) MẶC ĐỊNH (không filter) → $signModel->getWithGroup()
          └─ Model: app/models/TrafficSign.php:10
             → SELECT s.*, g.name as group_name, g.sign_prefix
               FROM traffic_signs s
               LEFT JOIN traffic_sign_groups g ON s.group_id = g.id
               ORDER BY g.sort_order, s.sign_code
          → Group kết quả theo group_name

    5. $this->view('client/bienbao/index', [...])
}
```

**View:** `app/views/client/bienbao/index.php`
- Thanh tìm kiếm biển báo
- Accordion/section theo từng nhóm biển báo (Biển báo cấm, Biển báo nguy hiểm, Biển chỉ dẫn...)
- Mỗi biển báo hiển thị: ảnh (nếu có), mã biển báo, tên biển báo

### B.4.2. GET /bien-bao/{id} — Chi tiết biển báo

```
Giao diện (app/views/client/bienbao/detail.php)
  → Router → BienBaoController@detail($id)
    → $signModel->find($id)
    → $groupModel->find($sign['group_id'])
    → $signModel->findByGroup($sign['group_id'])  // biển báo cùng nhóm
    → $this->view('client/bienbao/detail', [...])
```

**Model sử dụng:**
| Model | Method | SQL |
|-------|--------|-----|
| `TrafficSignGroup` | `getAllSorted()` | `SELECT * FROM traffic_sign_groups ORDER BY sort_order ASC` |
| `TrafficSign` | `search($kw)` | LIKE search trên name và sign_code, JOIN group |
| `TrafficSign` | `getWithGroup()` | JOIN traffic_signs + traffic_sign_groups |
| `TrafficSign` | `findByGroup($id)` | `SELECT * FROM traffic_signs WHERE group_id = :val` |

---

## B.5. BẢN ĐỒ (Map)

**URL:** `GET /ban-do`

**File route:** `config/routes.php:21`
```
$router->get('/ban-do', 'client/BanDoController@index');
```

```
Giao diện (app/views/client/bando/index.php)
  → Người dùng truy cập /ban-do (có thể kèm ?type=camera)
    → Router → BanDoController@index()
```

**Controller:** `app/controllers/client/BanDoController.php:9` — method `index()`

```
index() {
    1. $type = $this->input('type', '')
    2. Nếu CÓ type → $locationModel->findByType($type)
       └─ Model: app/models/Location.php:10
          → SELECT * FROM locations WHERE type = :val ORDER BY name ASC
       → Nếu KHÔNG → $locationModel->getActiveLocations()
          └─ SELECT * FROM locations WHERE status = 1 ORDER BY type, name ASC

    3. Tính centerLat/centerLng (trung bình tọa độ các điểm)
    4. $this->view('client/bando/index', [
           'locations'  → danh sách địa điểm,
           'centerLat'  → tọa độ trung tâm bản đồ,
           'centerLng'  → tọa độ trung tâm bản đồ,
           'currentType'→ loại đang lọc
       ])
}
```

**View:** `app/views/client/bando/index.php`
- Filter bar: Camera / CSGT / Trạm thu phí / Đăng kiểm
- Bản đồ (Google Maps JS API hoặc Leaflet + OpenStreetMap fallback)
- Marker tại mỗi địa điểm, click vào xem thông tin chi tiết

**Model sử dụng:**
| Model | Method | SQL |
|-------|--------|-----|
| `Location` | `findByType($type)` | `SELECT * FROM locations WHERE type = :val ORDER BY name ASC` |
| `Location` | `getActiveLocations()` | `SELECT * FROM locations WHERE status = 1 ORDER BY type, name ASC` |

---

## B.6. THỐNG KÊ (Statistics)

**URL:** `GET /thong-ke`

**File route:** `config/routes.php:22`
```
$router->get('/thong-ke', 'client/ThongKeController@index');
```

```
Giao diện (app/views/client/thongke/index.php)
  → Người dùng truy cập /thong-ke (có thể kèm ?year=2026)
    → Router → ThongKeController@index()
```

**Controller:** `app/controllers/client/ThongKeController.php:9` — method `index()`

```
index() {
    1. $year = (int) $this->input('year', date('Y'))

    2. $violationModel->topOffenses(10)
       └─ app/models/Violation.php:31
          → SELECT o.name, COUNT(*) FROM violations v
            JOIN offenses o ON v.offense_id = o.id
            GROUP BY v.offense_id ORDER BY count DESC LIMIT 10

    3. $violationModel->topLocations(10)
       └─ app/models/Violation.php:48
          → SELECT l.name, l.address, COUNT(*) FROM violations v
            JOIN locations l ON v.location_id = l.id
            GROUP BY v.location_id ORDER BY count DESC LIMIT 10

    4. $violationModel->topPlates(10)
       └─ app/models/Violation.php:65
          → SELECT plate_number, COUNT(*) FROM violations
            GROUP BY plate_number ORDER BY count DESC LIMIT 10

    5. $violationModel->countByMonth($year)
       └─ app/models/Violation.php:81
          → SELECT MONTH(violation_date), COUNT(*) FROM violations
            WHERE YEAR(violation_date) = :year GROUP BY MONTH(violation_date)

    6. $violationModel->countByStatus()
       └─ app/models/Violation.php:96
          → SELECT status, COUNT(*) FROM violations GROUP BY status

    7. $this->view('client/thongke/index', [
           'topOffenses'  → Chart.js bar chart: Top 10 lỗi vi phạm,
           'topLocations' → Chart.js bar chart: Top 10 địa điểm,
           'topPlates'    → Bảng: Top 10 biển số vi phạm nhiều nhất,
           'monthlyData'  → Chart.js line chart: Vi phạm theo tháng (12 tháng),
           'statusCounts' → Chart.js pie/doughnut chart: Tỷ lệ trạng thái
       ])
}
```

**View:** `app/views/client/thongke/index.php`
- Filter năm
- 4 biểu đồ Chart.js: Lỗi vi phạm (bar), Địa điểm (bar), Theo tháng (line), Trạng thái (doughnut)
- Bảng top biển số vi phạm

---

## B.7. FAQ (Câu hỏi thường gặp)

**URL:** `GET /faq`

**File route:** `config/routes.php:23`
```
$router->get('/faq', 'client/FaqController@index');
```

```
Giao diện (app/views/client/faq/index.php)
  → Router → FaqController@index()
    → $faqModel->getActive()
      └─ Model: app/models/Faq.php:10
         → SELECT * FROM faqs WHERE status = 1 ORDER BY sort_order ASC
    → $this->view('client/faq/index', ['faqs' => $faqs])
```

**View:** `app/views/client/faq/index.php` — Bootstrap accordion hiển thị câu hỏi/trả lời theo danh mục.

---

## B.8. GIỚI THIỆU (About)

**URL:** `GET /gioi-thieu`

**File route:** `config/routes.php:24`
```
$router->get('/gioi-thieu', 'client/HomeController@about');
```

```
Router → HomeController@about()
  → $this->view('client/pages/gioi-thieu', ['title' => 'Giới thiệu'])
```

**View:** `app/views/client/pages/gioi-thieu.php` — Trang tĩnh giới thiệu về hệ thống.

---

## B.9. LIÊN HỆ (Contact)

**URLs:**
- `GET /lien-he` — Hiển thị form liên hệ
- `POST /lien-he` — Gửi tin nhắn liên hệ

**File routes:** `config/routes.php:25-26`
```
$router->get('/lien-he', 'client/LienHeController@index');
$router->post('/lien-he', 'client/LienHeController@send');
```

### B.9.1. GET /lien-he — Form liên hệ

```
Router → LienHeController@index()
  → $this->view('client/pages/lien-he', ['title' => 'Liên hệ'])
```

### B.9.2. POST /lien-he — Gửi tin nhắn

```
Người dùng điền form (name, email, subject, message) + bấm Gửi
  → POST /lien-he → LienHeController@send()
```

**Controller:** `app/controllers/client/LienHeController.php:18` — method `send()`

```
send() {
    1. CSRF token
    2. Lấy input: name, email, subject, message
    3. Validate:
       - name: required, min 2, max 100
       - email: required, email format
       - subject: required, min 3, max 255
       - message: required, min 10
    4. Nếu lỗi → flash error + redirect về /lien-he

    5. ContactMessage::create([
           'name'    → $name,
           'email'   → $email,
           'subject' → $subject,
           'message' → $message,
           'is_read' → 0
       ])
       └─ INSERT INTO contact_messages

    6. Flash success → redirect /lien-he
}
```

**Model sử dụng:**
| Model | Method | SQL |
|-------|--------|-----|
| `ContactMessage` | `create($data)` | `INSERT INTO contact_messages` |

---

## B.10. ĐĂNG NHẬP / ĐĂNG KÝ / ĐĂNG XUẤT (Authentication)

**URLs:**
- `GET /dang-nhap` — Form đăng nhập
- `POST /dang-nhap` — Xử lý đăng nhập
- `GET /dang-ky` — Form đăng ký
- `POST /dang-ky` — Xử lý đăng ký
- `GET /dang-xuat` — Đăng xuất

**File routes:** `config/routes.php:32-36`
```
$router->get('/dang-nhap', 'client/AuthController@loginForm');
$router->post('/dang-nhap', 'client/AuthController@login');
$router->get('/dang-ky', 'client/AuthController@registerForm');
$router->post('/dang-ky', 'client/AuthController@register');
$router->get('/dang-xuat', 'client/AuthController@logout');
```

### B.10.1. GET/POST /dang-nhap — Đăng nhập

```
Giao diện (app/views/client/auth/login.php)
  → Form: email + password + nút Đăng nhập + link Đăng ký
```

**Controller:** `app/controllers/client/AuthController.php:20` — method `login()`

```
login() {
    1. CSRF token
    2. Lấy $email, $password
    3. $userModel->findBy('email', $email)
       └─ SELECT * FROM users WHERE email = :val LIMIT 1
    4. password_verify($password, $user['password']) → Bcrypt
    5. Kiểm tra status != 0 (tài khoản không bị khóa)
    6. Session::login($user)
       └─ Lưu user_id, user_name, user_email, role vào session
       └─ session_regenerate_id(true) → chống session fixation
    7. Redirect: admin → /admin, user → /tai-khoan
}
```

### B.10.2. GET/POST /dang-ky — Đăng ký

```
Giao diện (app/views/client/auth/register.php)
  → Form: fullname + email + phone + password + confirm password
```

**Controller:** `app/controllers/client/AuthController.php:70` — method `register()`

```
register() {
    1. CSRF token
    2. Validate: fullname (2-100), email, phone (VN format), password (min 6)
    3. password_confirm === password
    4. Kiểm tra email chưa tồn tại → $userModel->findBy('email', $email)
    5. Kiểm tra phone chưa tồn tại → $userModel->findBy('phone', $phone)
    6. $userModel->create([
           'fullname'   → $fullname,
           'email'      → $email,
           'phone'      → $phone,
           'password'   → password_hash($password, PASSWORD_BCRYPT),
           'role'       → 'user',
           'status'     → 1,
           'created_at' → now()
       ])
    7. Auto-login: Session::login($newUser)
    8. Redirect → /tai-khoan
}
```

### B.10.3. GET /dang-xuat — Đăng xuất

```
Router → AuthController@logout()
  → Session::logout()  // Destroy session + clear cookie
  → redirect('/')
```

**Model sử dụng:**
| Model | Method | SQL |
|-------|--------|-----|
| `User` | `findBy('email', $email)` | `SELECT * FROM users WHERE email = :val LIMIT 1` |
| `User` | `findBy('phone', $phone)` | `SELECT * FROM users WHERE phone = :val LIMIT 1` |
| `User` | `create($data)` | `INSERT INTO users` |

---

## B.11. CHAT HỖ TRỢ (Live Chat)

**URLs:**
- `GET /chat` — Mở giao diện chat
- `POST /chat/start` — Bắt đầu cuộc trò chuyện mới
- `POST /chat/restore` — Khôi phục cuộc trò chuyện cũ (guest)
- `GET /chat/{id}/messages` — Lấy tin nhắn (AJAX polling)
- `POST /chat/{id}/send` — Gửi tin nhắn

**File routes:** `config/routes.php:27-31`

### B.11.1. GET /chat — Mở giao diện chat

```
Giao diện (app/views/client/chat/index.php)
  → Router → ChatController@index()
```

**Controller:** `app/controllers/client/ChatController.php:12` — method `index()`

```
index() {
    1. Nếu ĐÃ login → tìm conversation đang mở của user
       └─ ChatConversation::findOpenByUser(userId)
          → SELECT * FROM chat_conversations
            WHERE user_id = :uid AND status = 'open'
            ORDER BY last_message_at DESC LIMIT 1

    2. Nếu CHƯA login → đọc chat_conversation_id từ session
       → Nếu có → ChatConversation::find(id)
       → Kiểm tra status không phải 'closed'
       → Nếu chưa có guest_token → tạo mới và lưu

    3. $this->view('client/chat/index', ['conversation' => $conversation])
}
```

**View:** `app/views/client/chat/index.php`
- Nếu chưa có conversation → form nhập tên + email để bắt đầu chat
- Nếu đã có → khung chat (danh sách tin nhắn + ô nhập tin nhắn + nút gửi)
- JavaScript polling: gọi AJAX `/chat/{id}/messages?after_id=X` mỗi 3 giây để lấy tin nhắn mới

### B.11.2. POST /chat/start — Bắt đầu chat

```
Người dùng nhập tên + email + bấm "Bắt đầu chat"
  → AJAX POST /chat/start
```

**Controller:** `app/controllers/client/ChatController.php:36` — method `start()`

```
start() {
    1. CSRF token → nếu lỗi → JSON 419
    2. Nếu ĐÃ login → kiểm tra conversation đang mở, nếu có trả về luôn
       → Nếu chưa → tạo conversation mới với user_id

    3. Nếu CHƯA login:
       a) Validate name (required, min 2, max 100), email (required, email)
       b) Validate phone nếu có (VN format)
       c) Tạo guest_token = bin2hex(random_bytes(32))
       d) ChatConversation::create([
              'guest_name'     → $name,
              'guest_email'    → $email,
              'guest_phone'    → $phone,
              'guest_token'    → $guestToken,
              'status'         → 'open',
              'last_message_at'→ now()
          ])
       e) Lưu conversation_id vào session
       f) JSON response: {conversation_id, guest_token}
}
```

### B.11.3. POST /chat/restore — Khôi phục chat (guest)

```
Người dùng có guest_token → AJAX POST /chat/restore
  → ChatController@restore()
    → ChatConversation::findOpenByGuestToken($token)
      → SELECT * FROM chat_conversations WHERE guest_token = :token AND status = 'open' LIMIT 1
    → Lưu vào session → trả JSON
```

### B.11.4. GET /chat/{id}/messages — Lấy tin nhắn (polling)

```
JavaScript gọi AJAX định kỳ → GET /chat/123/messages?after_id=456
  → ChatController@messages(123)
    → Kiểm tra quyền truy cập conversation
    → ChatMessage::markConversationRead(123, 'admin')  // đánh dấu admin đã đọc
    → ChatMessage::getByConversation(123, 456)
      → SELECT m.*, u.fullname FROM chat_messages m
        LEFT JOIN users u ON u.id = m.sender_id
        WHERE m.conversation_id = 123 AND m.id > 456
        ORDER BY m.id ASC
    → JSON: {messages, status}
```

### B.11.5. POST /chat/{id}/send — Gửi tin nhắn

```
Người dùng nhập tin nhắn + bấm Gửi
  → AJAX POST /chat/123/send (message=nội dung)
```

**Controller:** `app/controllers/client/ChatController.php:129` — method `send($id)`

```
send($id) {
    1. CSRF token
    2. Kiểm tra conversation tồn tại + thuộc về user/guest hiện tại
    3. Kiểm tra status != 'closed'
    4. Validate message (required, min 1, max 2000)
    5. ChatMessage::create([
           'conversation_id' → $id,
           'sender_type'     → 'user',
           'sender_id'       → userId hoặc null,
           'message'         → $message,
           'is_read'         → 0
       ])
    6. ChatConversation::touchLastMessage($id)
       └─ UPDATE chat_conversations SET last_message_at = NOW()
    7. JSON: {success, message_id}
}
```

**Model sử dụng:**
| Model | Method | SQL |
|-------|--------|-----|
| `ChatConversation` | `findOpenByUser($uid)` | SELECT WHERE user_id AND status='open' |
| `ChatConversation` | `findOpenByGuestToken($t)` | SELECT WHERE guest_token AND status='open' |
| `ChatConversation` | `create($data)` | INSERT INTO chat_conversations |
| `ChatConversation` | `touchLastMessage($id)` | UPDATE last_message_at |
| `ChatMessage` | `getByConversation($cid, $after)` | SELECT JOIN users WHERE conversation_id AND id > after |
| `ChatMessage` | `markConversationRead($cid, $type)` | UPDATE is_read = 1 |
| `ChatMessage` | `create($data)` | INSERT INTO chat_messages |

---

# C. USER — CÁC CHỨC NĂNG CẦN ĐĂNG NHẬP

Tất cả các route trong phần này bắt đầu bằng `/tai-khoan`. Mỗi action đều gọi `$this->requireLogin()` ở đầu method.

---

## C.1. DASHBOARD TÀI KHOẢN

**URL:** `GET /tai-khoan`

**File route:** `config/routes.php:42`
```
$router->get('/tai-khoan', 'client/TaiKhoanController@dashboard');
```

```
Giao diện (app/views/client/taikhoan/dashboard.php)
  → Router → TaiKhoanController@dashboard()
    → $this->requireLogin()
    → Vehicle::countByUser($userId)
    → SearchHistory::getByUser($userId, 5)
    → $this->view('client/taikhoan/dashboard', [
          'vehicleCount'  → số phương tiện,
          'recentHistory' → 5 lần tra cứu gần nhất
      ])
```

**View:** Sidebar tài khoản (Dashboard, Phương tiện, Lịch sử, Hồ sơ) + Thống kê nhanh + Lịch sử tra cứu gần đây.

---

## C.2. QUẢN LÝ PHƯƠNG TIỆN (Vehicles CRUD)

**URLs:**
- `GET /tai-khoan/phuong-tien` — Danh sách phương tiện
- `POST /tai-khoan/phuong-tien` — Thêm phương tiện
- `POST /tai-khoan/phuong-tien/{id}/edit` — Sửa phương tiện
- `POST /tai-khoan/phuong-tien/{id}/delete` — Xóa phương tiện

**File routes:** `config/routes.php:45-48`

### C.2.1. GET — Danh sách phương tiện

```
Giao diện (app/views/client/taikhoan/vehicles.php)
  → TaiKhoanController@vehicles()
    → $this->requireLogin()
    → Vehicle::findByUser($userId)
      → SELECT * FROM vehicles WHERE user_id = :val ORDER BY created_at DESC
    → $this->view('client/taikhoan/vehicles', ['vehicles' => $vehicles])
```

**View:** Bảng danh sách xe + Form popup/modal thêm xe mới.

### C.2.2. POST — Thêm phương tiện

```
Người dùng nhập biển số + loại xe + hãng + mẫu + bấm Thêm
  → TaiKhoanController@addVehicle()
    → CSRF → requireLogin()
    → Validate plateNumber (regex), vehicleType (enum)
    → Vehicle::create([
          user_id      → Session::get('user_id'),
          plate_number → strtoupper($plateNumber),
          vehicle_type → $vehicleType,
          brand        → $brand,
          model        → $model
      ])
    → Flash success → redirect /tai-khoan/phuong-tien
```

### C.2.3. POST /{id}/edit — Sửa phương tiện

```
TaiKhoanController@updateVehicle($id)
  → CSRF → requireLogin()
  → Tìm vehicle, kiểm tra thuộc về user hiện tại
  → Validate → Vehicle::update($id, $data)
  → Flash success → redirect
```

### C.2.4. POST /{id}/delete — Xóa phương tiện

```
TaiKhoanController@deleteVehicle($id)
  → CSRF → requireLogin()
  → Tìm vehicle, kiểm tra thuộc về user hiện tại
  → Vehicle::delete($id)
  → Flash success → redirect
```

---

## C.3. LỊCH SỬ TRA CỨU

**URL:** `GET /tai-khoan/lich-su`

**File route:** `config/routes.php:51`

```
Giao diện (app/views/client/taikhoan/history.php)
  → TaiKhoanController@history()
    → $this->requireLogin()
    → SearchHistory::paginate(page, 15, ['user_id' => $userId], 'searched_at DESC')
      → SELECT COUNT(*), SELECT * FROM search_history WHERE user_id = :val
        ORDER BY searched_at DESC LIMIT 15 OFFSET N
    → $this->view('client/taikhoan/history', [
          'history'    → kết quả phân trang,
          'pagination' → metadata
      ])
```

**View:** Bảng lịch sử (Thời gian, Biển số, Loại xe, Số kết quả, IP).

---

## C.4. HỒ SƠ CÁ NHÂN + ĐỔI MẬT KHẨU

**URLs:**
- `GET /tai-khoan/ho-so` — Xem/sửa hồ sơ
- `POST /tai-khoan/ho-so` — Cập nhật hồ sơ
- `POST /tai-khoan/doi-mat-khau` — Đổi mật khẩu

**File routes:** `config/routes.php:52-54`

### C.4.1. GET /tai-khoan/ho-so

```
Giao diện (app/views/client/taikhoan/profile.php)
  → TaiKhoanController@profile()
    → $this->requireLogin()
    → User::find($userId)
    → $this->view('client/taikhoan/profile', ['user' => $user])
```

### C.4.2. POST /tai-khoan/ho-so — Cập nhật hồ sơ

```
TaiKhoanController@updateProfile()
  → CSRF → requireLogin()
  → Validate: fullname (2-100), email (email format), phone (VN format)
  → Kiểm tra trùng email (trừ chính mình)
  → Kiểm tra trùng phone (trừ chính mình)
  → User::update($userId, [fullname, email, phone])
  → Cập nhật session: user_name, user_email
  → Flash success → redirect
```

### C.4.3. POST /tai-khoan/doi-mat-khau — Đổi mật khẩu

```
TaiKhoanController@changePassword()
  → CSRF → requireLogin()
  → Lấy old_password, new_password, confirm_password
  → Validate: new_password min 6, new === confirm
  → User::find($userId) → password_verify($oldPassword, $user['password'])
  → User::update($userId, ['password' => password_hash($newPassword, BCRYPT)])
  → Flash success → redirect
```

**Model sử dụng trong phần C:**
| Model | Method | SQL |
|-------|--------|-----|
| `Vehicle` | `findByUser($uid)` | `SELECT * FROM vehicles WHERE user_id = :val ORDER BY created_at DESC` |
| `Vehicle` | `countByUser($uid)` | `SELECT COUNT(*) FROM vehicles WHERE user_id = :val` |
| `Vehicle` | `create/update/delete` | CRUD cơ bản |
| `SearchHistory` | `getByUser($uid, $n)` | `SELECT * FROM search_history WHERE user_id = :val ORDER BY searched_at DESC LIMIT N` |
| `SearchHistory` | `paginate(...)` | Phân trang search_history |
| `User` | `find($id)` | `SELECT * FROM users WHERE id = :id` |
| `User` | `findBy('email', $e)` / `findBy('phone', $p)` | Tìm theo cột |
| `User` | `update($id, $data)` | `UPDATE users SET ... WHERE id = :id` |

---

# D. ADMIN — CÁC CHỨC NĂNG QUẢN TRỊ

Tất cả admin controller đều có `$this->requireAdmin()` trong constructor → mọi action đều yêu cầu role='admin'. Layout sử dụng là `admin.php` (có sidebar, header admin).

---

## D.1. DASHBOARD ADMIN

**URL:** `GET /admin`

**File route:** `config/routes.php:60`
```
$router->get('/admin', 'admin/DashboardController@index');
```

**Controller:** `app/controllers/admin/DashboardController.php:40` — method `index()`

```
index() {
    → $this->requireAdmin() (trong constructor)

    1. User::count()      → SELECT COUNT(*) FROM users
    2. Violation::count() → SELECT COUNT(*) FROM violations
    3. News::count()      → SELECT COUNT(*) FROM news
    4. Vehicle::count()   → SELECT COUNT(*) FROM vehicles

    5. Violation::getAllWithDetails([], 'v.created_at DESC', 10)
       └─ Model: app/models/Violation.php:114
          → SELECT v.*, l.name as location_name, o.name as offense_name
            FROM violations v
            LEFT JOIN locations l ON v.location_id = l.id
            LEFT JOIN offenses o ON v.offense_id = o.id
            ORDER BY v.created_at DESC LIMIT 10

    6. $this->view('admin/dashboard/index', [
           'totalUsers'       → 4 ô thống kê (Users, Vi phạm, Tin tức, Xe),
           'totalViolations'  → 4 ô thống kê có icon + màu,
           'totalNews'        → 4 ô thống kê có icon + màu,
           'totalVehicles'    → 4 ô thống kê có icon + màu,
           'recentViolations' → Bảng 10 vi phạm gần nhất
       ], 'admin')
}
```

**View:** `app/views/admin/dashboard/index.php`
- 4 stat cards (màu xanh/vàng/đỏ/tím)
- Bảng 10 vi phạm gần nhất (Biển số, Loại xe, Ngày, Địa điểm, Lỗi, Trạng thái, Mức phạt)

---

## D.2. QUẢN LÝ NGƯỜI DÙNG (Users)

**URLs:**
- `GET /admin/users` — Danh sách (phân trang)
- `GET /admin/users/create` — Form thêm
- `POST /admin/users` — Lưu mới (store)
- `GET /admin/users/{id}/edit` — Form sửa
- `POST /admin/users/{id}` — Cập nhật (update)
- `POST /admin/users/{id}/delete` — Xóa
- `POST /admin/users/{id}/toggle-status` — Khóa/Mở khóa (AJAX + non-AJAX)

**File routes:** `config/routes.php:63-69`

**Controller:** `app/controllers/admin/UserController.php`

### D.2.1. GET /admin/users — Danh sách

```
Giao diện (app/views/admin/users/index.php)
  → UserController@index()
    → User::paginate(page, 10, [], 'created_at DESC')
      → SELECT COUNT(*) FROM users
      → SELECT * FROM users ORDER BY created_at DESC LIMIT 10 OFFSET N
    → $this->view('admin/users/index', $data, 'admin')
```

**View:** Bảng phân trang: Fullname, Email, Phone, Role (badge), Status (badge xanh/đỏ), Action buttons (Sửa, Xóa, Khóa/Mở khóa).

### D.2.2. GET /admin/users/create + POST /admin/users (store)

```
UserController@create()
  → $this->view('admin/users/form', ['title' => 'Thêm người dùng'], 'admin')
```

**View form:** `app/views/admin/users/form.php`
- Input: fullname, email, phone, password (bắt buộc khi tạo), role (select), status (checkbox)

```
UserController@store()
  1. CSRF token
  2. Validate:
     - fullname: required, min 2, max 100
     - email: required, email format
     - phone: phone format (nếu có)
     - password: required, min 6, max 255
  3. Kiểm tra trùng email → User::findBy('email', $email)
  4. Kiểm tra trùng phone → User::findBy('phone', $phone) (nếu có)
  5. User::create([
         fullname, email, phone,
         password → password_hash($password, PASSWORD_BCRYPT),
         role     → $_POST['role'] ?? 'user',
         status   → (int)($_POST['status'] ?? 1)
     ])
  6. Flash success → redirect /admin/users
```

### D.2.3. GET /admin/users/{id}/edit + POST /admin/users/{id} (update)

```
UserController@edit()
  → User::find($id)
  → $this->view('admin/users/form', ['title' => 'Sửa...', 'user' => $user], 'admin')

UserController@update()
  1. CSRF token
  2. User::find($id) → kiểm tra tồn tại
  3. Validate (password KHÔNG bắt buộc khi update)
  4. Kiểm tra trùng email (loại trừ chính user này)
  5. Kiểm tra trùng phone (loại trừ chính user này)
  6. $updateData = [fullname, email, phone, role, status]
  7. Nếu có nhập password mới → hash + thêm vào $updateData
  8. User::update($id, $updateData)
  9. Flash success → redirect
```

### D.2.4. POST /admin/users/{id}/delete — Xóa

```
UserController@delete()
  → CSRF → User::find($id) → User::delete($id)
  → Flash → redirect
```

### D.2.5. POST /admin/users/{id}/toggle-status — Khóa/Mở khóa

```
UserController@toggleStatus()
  1. CSRF → nếu lỗi & AJAX → JSON 419
  2. User::find($id) → nếu không có & AJAX → JSON 404
  3. $newStatus = $user['status'] ? 0 : 1  // đảo trạng thái
  4. User::update($id, ['status' => $newStatus])
  5. Nếu AJAX → JSON {success, new_status, message}
     Nếu không → Flash + redirect
```

---

## D.3. QUẢN LÝ VI PHẠM (Violations)

**URLs:**
- `GET /admin/violations` — Danh sách (phân trang + tìm kiếm biển số)
- `GET /admin/violations/create` — Form thêm
- `POST /admin/violations` — Lưu mới
- `GET /admin/violations/{id}/edit` — Form sửa
- `POST /admin/violations/{id}` — Cập nhật
- `POST /admin/violations/{id}/delete` — Xóa
- `POST /admin/violations/{id}/toggle-status` — Chuyển trạng thái (pending→processed→paid→pending)
- `POST /admin/violations/import` — Import CSV

**File routes:** `config/routes.php:72-79`

**Controller:** `app/controllers/admin/ViolationController.php`

### D.3.1. GET /admin/violations — Danh sách (có search)

```
Giao diện (app/views/admin/violations/index.php)
  → ViolationController@index()
    → Lấy ?search=biensoxe và ?page=N

    Nếu CÓ search:
      → SELECT COUNT(*) FROM violations WHERE plate_number LIKE :search
      → SELECT v.*, l.name as location_name, o.name as offense_name
        FROM violations v
        LEFT JOIN locations l ON v.location_id = l.id
        LEFT JOIN offenses o ON v.offense_id = o.id
        WHERE v.plate_number LIKE :search
        ORDER BY v.violation_date DESC
        LIMIT 10 OFFSET :offset

    Nếu KHÔNG search:
      → Violation::getAllWithDetails([], 'v.violation_date DESC', 10, offset)
        → JOIN violations + locations + offenses

    → Phân trang thủ công (total, totalPages, clamp page, offset)
    → $this->view('admin/violations/index', $data, 'admin')
```

**View:** Form search (input biển số) + Bảng (Biển số, Loại xe, Ngày, Địa điểm, Lỗi, Trạng thái badge, Mức phạt, Actions) + Nút Import CSV.

### D.3.2. Form thêm + Lưu (create + store)

```
ViolationController@create()
  → Load Offense::getWithCategory() (JOIN offenses + categories)
  → Load Location::all([], 'name ASC')
  → $this->view('admin/violations/form', [...], 'admin')
```

**View form:** `app/views/admin/violations/form.php`
- Input: plate_number, vehicle_type (select), violation_date (datetime), offense_id (select optgroup theo danh mục), location_id (select), status (select), fine_amount, decision_number, decision_date, notes

```
ViolationController@store()
  1. CSRF
  2. Validate:
     - plate_number: required, plateNumber regex, max 20
     - vehicle_type: required
     - violation_date: required
     - offense_id: numeric (nullable)
     - location_id: numeric (nullable)
     - status: required (pending/processed/paid)
  3. Violation::create($data)
     → Các trường nullable (offense_id, location_id, decision_date, notes)
       được set = null nếu rỗng
  4. Flash success → redirect
```

### D.3.3. Sửa + Cập nhật (edit + update)

Tương tự CRUD pattern: find($id) → validate → update($id, $data) → redirect.

### D.3.4. POST /admin/violations/{id}/toggle-status

```
ViolationController@toggleStatus()
  1. CSRF → JSON 419 hoặc return
  2. Violation::find($id) → JSON 404 hoặc flash redirect
  3. Chu kỳ trạng thái:
     $cycle = ['pending' => 'processed', 'processed' => 'paid', 'paid' => 'pending']
     $newStatus = $cycle[$violation['status']] ?? 'pending'
  4. Violation::update($id, ['status' => $newStatus])
  5. Labels: ['pending' => 'Chưa xử lý', 'processed' => 'Đã xử lý', 'paid' => 'Đã nộp phạt']
  6. AJAX → JSON {success, new_status, label, message}
     Non-AJAX → Flash + redirect
```

### D.3.5. POST /admin/violations/import — Import CSV

```
ViolationController@import()
  1. CSRF
  2. Kiểm tra $_FILES['csv_file']: tồn tại, UPLOAD_ERR_OK, đuôi .csv
  3. fopen($file['tmp_name'], 'r')
  4. fgetcsv($handle) → bỏ qua dòng header
  5. while ($row = fgetcsv($handle)):
       - Bỏ qua nếu count($row) < 5
       - Violation::create([
             plate_number   → trim($row[0]),
             vehicle_type   → trim($row[1]),
             violation_date → trim($row[2]),
             offense_id     → (int)$row[3] ?: null,
             location_id    → (int)$row[4] ?: null,
             status         → trim($row[5] ?? 'pending'),
             fine_amount    → trim($row[6] ?? ''),
             notes          → trim($row[7] ?? '')
         ])
       - $imported++
  6. fclose($handle) → Flash success ($imported dòng) → redirect
```

**Cấu trúc file CSV:** `plate_number, vehicle_type, violation_date, offense_id, location_id, status, fine_amount, notes`

---

## D.4. QUẢN LÝ TIN TỨC (News)

**URLs:**
- `GET /admin/news` — Danh sách
- `GET /admin/news/create` — Form thêm
- `POST /admin/news` — Lưu mới
- `GET /admin/news/{id}/edit` — Form sửa
- `POST /admin/news/{id}` — Cập nhật
- `POST /admin/news/{id}/delete` — Xóa

**File routes:** `config/routes.php:82-87`

**Controller:** `app/controllers/admin/NewsController.php`

### D.4.1. GET /admin/news — Danh sách

```
NewsController@index()
  → News::count()
  → News::getAllWithCategory([], 'n.created_at DESC', 10, offset)
    → SELECT n.*, nc.name as category_name
      FROM news n LEFT JOIN news_categories nc ON n.category_id = nc.id
      ORDER BY n.created_at DESC LIMIT 10 OFFSET N
  → Phân trang thủ công
  → $this->view('admin/news/index', $data, 'admin')
```

### D.4.2. Form thêm + Lưu

```
NewsController@create()
  → NewsCategory::all([], 'name ASC')  // load dropdown danh mục
  → $this->view('admin/news/form', [...], 'admin')

NewsController@store()
  1. CSRF
  2. Validate: title (required, min 3, max 255), content (required), category_id (numeric)
  3. Helper::slug($title) → tạo slug
  4. Kiểm tra trùng slug → nếu có → slug .= '-' . time()
  5. Upload thumbnail (nếu có):
     Helper::upload($_FILES['thumbnail'], 'news')
     → Lưu vào public/assets/uploads/news/
  6. News::create([
         title, slug, content, thumbnail,
         category_id → $_POST['category_id'] ?: null,
         author_id   → Session::get('user_id'),
         status      → $_POST['status'] ?? 'draft'
     ])
  7. Flash success → redirect
```

### D.4.3. Sửa + Cập nhật

Tương tự CRUD: find → validate → tạo slug (tránh trùng với bản ghi khác) → upload thumbnail mới nếu có → update → redirect.

---

## D.5. QUẢN LÝ DANH MỤC (Categories)

**URLs:**
- `GET /admin/categories` — Danh sách (cả 2 loại: news + offense)
- `POST /admin/categories` — Thêm mới
- `POST /admin/categories/{id}` — Cập nhật
- `POST /admin/categories/{id}/delete` — Xóa

**File routes:** `config/routes.php:90-93`

**Controller:** `app/controllers/admin/CategoryController.php`

### Điểm đặc biệt

Controller này quản lý CÙNG LÚC 2 loại danh mục khác nhau trong 1 form, phân biệt qua hidden input `category_type`:

- `category_type = 'news'` → Model `NewsCategory` (bảng `news_categories`, có trường `slug`)
- `category_type = 'offense'` → Model `OffenseCategory` (bảng `offense_categories`, có trường `description`)

```
CategoryController@index()
  → NewsCategory::all([], 'name ASC')     → $newsCategories
  → OffenseCategory::all([], 'name ASC')  → $offenseCategories
  → $this->view('admin/categories/index', [...], 'admin')

CategoryController@store()
  1. CSRF
  2. Xác định category_type ('news' | 'offense')
  3. Validate name (required, min 2)
  4. Nếu 'news':
     → Helper::slug($name) → kiểm tra trùng slug → NewsCategory::create([name, slug])
     Nếu 'offense':
     → OffenseCategory::create([name, description])
  5. Flash success → redirect

CategoryController@update()
  → Tương tự store, nhưng phân nhánh model theo type, update(id, data)

CategoryController@delete()
  → Phân nhánh model theo type → find(id) → delete(id) → redirect
```

---

## D.6. QUẢN LÝ BIỂN BÁO (Traffic Signs)

**URLs:**
- `GET /admin/signs` — Danh sách (phân trang thủ công = array_slice)
- `GET /admin/signs/create` — Form thêm
- `POST /admin/signs` — Lưu mới
- `GET /admin/signs/{id}/edit` — Form sửa
- `POST /admin/signs/{id}` — Cập nhật
- `POST /admin/signs/{id}/delete` — Xóa

**File routes:** `config/routes.php:96-101`

**Controller:** `app/controllers/admin/SignController.php`

### D.6.1. GET /admin/signs — Danh sách (phân trang thủ công)

```
SignController@index()
  1. TrafficSign::paginate(page, 10, [], 'sign_code ASC')  // lấy metadata
  2. TrafficSign::getWithGroup()  // lấy TOÀN BỘ biển báo kèm tên nhóm
     → SELECT s.*, g.name as group_name, g.sign_prefix
       FROM traffic_signs s
       LEFT JOIN traffic_sign_groups g ON s.group_id = g.id
       ORDER BY g.sort_order, s.sign_code
  3. Đếm tổng → tính totalPages → clamp page → array_slice($items, offset, 10)
  4. $this->view('admin/signs/index', $data, 'admin')
```

**Lý do phân trang thủ công:** getWithGroup() đã JOIN lấy toàn bộ dữ liệu, không dùng SQL LIMIT/OFFSET mà dùng `array_slice()` của PHP.

### D.6.2. Form thêm + Lưu

```
SignController@create()
  → TrafficSignGroup::getAllSorted()  // dropdown chọn nhóm
  → $this->view('admin/signs/form', [...], 'admin')

SignController@store()
  1. CSRF
  2. Validate: sign_code (required, max 20), name (required, max 255), group_id (numeric)
  3. Upload ảnh: Helper::upload($_FILES['image'], 'signs')
     → Lưu vào public/assets/uploads/signs/
  4. TrafficSign::create([
         sign_code, name,
         group_id → $_POST['group_id'] ?: null,
         image, description
     ])
  5. Flash success → redirect
```

### D.6.3. Sửa + Cập nhật + Xóa

CRUD pattern tiêu chuẩn: find → validate → upload ảnh mới nếu có (giữ ảnh cũ nếu không upload) → update/delete → redirect.

---

## D.7. QUẢN LÝ ĐỊA ĐIỂM (Locations)

**URLs:**
- `GET /admin/locations` — Danh sách
- `GET /admin/locations/create` — Form thêm
- `POST /admin/locations` — Lưu mới
- `GET /admin/locations/{id}/edit` — Form sửa
- `POST /admin/locations/{id}` — Cập nhật
- `POST /admin/locations/{id}/delete` — Xóa

**File routes:** `config/routes.php:104-109`

**Controller:** `app/controllers/admin/LocationController.php`

### D.7.1. GET /admin/locations

```
LocationController@index()
  → Location::paginate(page, 10, [], 'name ASC')
    → SELECT COUNT(*), SELECT * FROM locations ORDER BY name ASC LIMIT 10 OFFSET N
  → $this->view('admin/locations/index', $data, 'admin')
```

### D.7.2. Form thêm + Lưu

```
LocationController@store()
  1. CSRF
  2. Validate:
     - name: required, max 255
     - type: required (camera|csgt|toll|inspection)
     - address: max 500
     - latitude: numeric (nullable)
     - longitude: numeric (nullable)
  3. Location::create([
         name, type,
         address   → $_POST['address'] ?? null,
         latitude  → (float)($_POST['latitude'] ?? 0),
         longitude → (float)($_POST['longitude'] ?? 0),
         description → $_POST['description'] ?? null,
         status    → (int)($_POST['status'] ?? 1)
     ])
  4. Flash success → redirect
```

### D.7.3. Sửa + Cập nhật + Xóa

CRUD pattern tiêu chuẩn: find → validate → ép kiểu tọa độ (float) và status (int) → update/delete → redirect.

---

## D.8. QUẢN LÝ FAQ (Câu hỏi thường gặp)

**URLs:**
- `GET /admin/faqs` — Danh sách
- `GET /admin/faqs/create` — Form thêm
- `POST /admin/faqs` — Lưu mới
- `GET /admin/faqs/{id}/edit` — Form sửa
- `POST /admin/faqs/{id}` — Cập nhật
- `POST /admin/faqs/{id}/delete` — Xóa

**File routes:** `config/routes.php:112-117`

**Controller:** `app/controllers/admin/FaqController.php`

```
FaqController@index()
  → Faq::paginate(page, 10, [], 'sort_order ASC')
  → $this->view('admin/faqs/index', $data, 'admin')

FaqController@store()
  1. CSRF
  2. Validate: question (required, max 500), answer (required - CKEditor HTML)
  3. Faq::create([
         question, answer,
         category   → $_POST['category'] ?? null,
         sort_order → (int)($_POST['sort_order'] ?? 0),
         status     → (int)($_POST['status'] ?? 1)
     ])
  4. Flash success → redirect
```

**View form:** `app/views/admin/faqs/form.php` — CKEditor cho trường answer.

---

## D.9. QUẢN LÝ CẢNH BÁO GIAO THÔNG (Traffic Alerts)

**URLs:**
- `GET /admin/alerts` — Danh sách
- `GET /admin/alerts/create` — Form thêm
- `POST /admin/alerts` — Lưu mới
- `GET /admin/alerts/{id}/edit` — Form sửa
- `POST /admin/alerts/{id}` — Cập nhật
- `POST /admin/alerts/{id}/delete` — Xóa

**File routes:** `config/routes.php:120-125`

**Controller:** `app/controllers/admin/AlertController.php`

```
AlertController@index()
  → TrafficAlert::paginate(page, 10, [], 'created_at DESC')
  → $this->view('admin/alerts/index', $data, 'admin')

AlertController@store()
  1. CSRF
  2. Validate: title (required, max 255)
  3. TrafficAlert::create([
         title, content → CKEditor HTML,
         alert_type → $_POST['alert_type'] ?? 'other',
         expires_at → $_POST['expires_at'] ?: null,
         created_by → Session::get('user_id'),  // FK → users
         status     → (int)($_POST['status'] ?? 1)
     ])
  4. Flash success → redirect
```

**Lưu ý:** `created_by` chỉ được set khi tạo (store), KHÔNG được cập nhật khi sửa (update) — giữ nguyên người tạo ban đầu.

**Model:** `TrafficAlert::getActive()` (dùng cho client)
```sql
SELECT * FROM traffic_alerts
WHERE status = 1
AND (expires_at IS NULL OR expires_at > NOW())
ORDER BY created_at DESC
```

---

## D.10. QUẢN LÝ TIN NHẮN LIÊN HỆ (Contact Messages)

**URLs:**
- `GET /admin/messages` — Danh sách
- `POST /admin/messages/{id}/read` — Đánh dấu đã đọc
- `POST /admin/messages/{id}/delete` — Xóa

**File routes:** `config/routes.php:128-130`

**Controller:** `app/controllers/admin/MessageController.php`

### Điểm đặc biệt

Admin KHÔNG tạo/sửa tin nhắn liên hệ — chỉ đọc, đánh dấu đã đọc, và xóa.

```
MessageController@index()
  → ContactMessage::paginate(page, 10, [], 'created_at DESC')
  → $this->view('admin/messages/index', $data, 'admin')

MessageController@markRead($id)
  → CSRF → find($id) → ContactMessage::markRead($id)
    → UPDATE contact_messages SET is_read = 1 WHERE id = ?
  → Flash success → redirect

MessageController@delete($id)
  → CSRF → find($id) → ContactMessage::delete($id)
  → Flash success → redirect
```

**View:** `app/views/admin/messages/index.php`
- Bảng: Tên, Email, Tiêu đề, Ngày gửi, Trạng thái (Chưa đọc/Đã đọc badge), Actions (Đánh dấu đã đọc, Xóa)
- Tin nhắn chưa đọc được highlight

---

## D.11. QUẢN LÝ CHAT KHÁCH HÀNG (Admin Chat)

**URLs:**
- `GET /admin/chat` — Danh sách cuộc trò chuyện
- `GET /admin/chat/{id}` — Chi tiết 1 cuộc trò chuyện
- `GET /admin/chat/{id}/messages` — Lấy tin nhắn (AJAX polling)
- `POST /admin/chat/{id}/reply` — Admin trả lời
- `POST /admin/chat/{id}/close` — Đóng cuộc trò chuyện

**File routes:** `config/routes.php:133-137`

**Controller:** `app/controllers/admin/ChatController.php`

### D.11.1. GET /admin/chat — Danh sách

```
AdminChatController@index()
  → ChatConversation::paginateWithUnreadCount(page, 10)
    → SELECT c.*, u.fullname, u.email,
             COALESCE(unread.unread_count, 0) AS unread_count
      FROM chat_conversations c
      LEFT JOIN users u ON u.id = c.user_id
      LEFT JOIN (
          SELECT conversation_id, COUNT(*) AS unread_count
          FROM chat_messages
          WHERE sender_type = 'user' AND is_read = 0
          GROUP BY conversation_id
      ) unread ON unread.conversation_id = c.id
      ORDER BY COALESCE(c.last_message_at, c.created_at) DESC
      LIMIT 10 OFFSET N
  → $this->view('admin/chat/index', $data, 'admin')
```

**View:** Bảng danh sách conversation (Tên KH, Email, Tin nhắn cuối, Thời gian, Số tin chưa đọc badge, Trạng thái open/closed).

### D.11.2. GET /admin/chat/{id} — Chi tiết chat

```
AdminChatController@show($id)
  → ChatConversation::find($id)
  → ChatMessage::markConversationRead($id, 'user')  // đánh dấu user đã đọc
  → $this->view('admin/chat/show', [...], 'admin')
```

### D.11.3. GET /admin/chat/{id}/messages — AJAX polling

```
AdminChatController@messages($id)
  → ChatConversation::find($id) → kiểm tra tồn tại
  → ChatMessage::markConversationRead($id, 'user')
  → ChatMessage::getByConversation($id, $afterId)
    → SELECT m.*, u.fullname FROM chat_messages m
      LEFT JOIN users u ON u.id = m.sender_id
      WHERE m.conversation_id = :cid AND m.id > :after_id
      ORDER BY m.id ASC
  → JSON {success, messages, status}
```

### D.11.4. POST /admin/chat/{id}/reply — Admin trả lời

```
AdminChatController@reply($id)
  1. CSRF → JSON 419
  2. ChatConversation::find($id) → kiểm tra tồn tại + status != 'closed'
  3. Validate message (required, min 1, max 2000)
  4. ChatMessage::create([
         conversation_id → $id,
         sender_type     → 'admin',
         sender_id       → Session::get('user_id'),
         message, is_read → 0
     ])
  5. ChatConversation::touchLastMessage($id)
  6. JSON {success, message_id}
```

### D.11.5. POST /admin/chat/{id}/close — Đóng chat

```
AdminChatController@close($id)
  → CSRF → ChatConversation::find($id) → ChatConversation::close($id)
    → UPDATE chat_conversations SET status = 'closed'
  → Flash success → redirect
```

---

# E. BẢNG TỔNG HỢP TẤT CẢ CÁC LUỒNG

## E.1. Client Routes (22 routes)

| # | Method | URL | Controller@action | Auth | View |
|---|--------|-----|-------------------|------|------|
| 1 | GET | `/` | `HomeController@index` | Không | `client/home/index` |
| 2 | GET | `/tra-cuu` | `TraCuuController@index` | Không | `client/tracuu/index` |
| 3 | POST | `/tra-cuu` | `TraCuuController@search` | Không | `client/tracuu/index` (hoặc JSON) |
| 4 | GET | `/tin-tuc` | `TinTucController@index` | Không | `client/tintuc/index` |
| 5 | GET | `/tin-tuc/{slug}` | `TinTucController@detail` | Không | `client/tintuc/detail` |
| 6 | GET | `/bien-bao` | `BienBaoController@index` | Không | `client/bienbao/index` |
| 7 | GET | `/bien-bao/{id}` | `BienBaoController@detail` | Không | `client/bienbao/detail` |
| 8 | GET | `/ban-do` | `BanDoController@index` | Không | `client/bando/index` |
| 9 | GET | `/thong-ke` | `ThongKeController@index` | Không | `client/thongke/index` |
| 10 | GET | `/faq` | `FaqController@index` | Không | `client/faq/index` |
| 11 | GET | `/gioi-thieu` | `HomeController@about` | Không | `client/pages/gioi-thieu` |
| 12 | GET | `/lien-he` | `LienHeController@index` | Không | `client/pages/lien-he` |
| 13 | POST | `/lien-he` | `LienHeController@send` | Không | Redirect |
| 14 | GET | `/chat` | `ChatController@index` | Không | `client/chat/index` |
| 15 | POST | `/chat/start` | `ChatController@start` | Không | JSON |
| 16 | POST | `/chat/restore` | `ChatController@restore` | Không | JSON |
| 17 | GET | `/chat/{id}/messages` | `ChatController@messages` | Không | JSON |
| 18 | POST | `/chat/{id}/send` | `ChatController@send` | Không | JSON |
| 19 | GET | `/dang-nhap` | `AuthController@loginForm` | Không | `client/auth/login` |
| 20 | POST | `/dang-nhap` | `AuthController@login` | Không | Redirect |
| 21 | GET | `/dang-ky` | `AuthController@registerForm` | Không | `client/auth/register` |
| 22 | POST | `/dang-ky` | `AuthController@register` | Không | Redirect |
| 23 | GET | `/dang-xuat` | `AuthController@logout` | Không | Redirect |

## E.2. User Routes (8 routes)

| # | Method | URL | Controller@action | Auth |
|---|--------|-----|-------------------|------|
| 1 | GET | `/tai-khoan` | `TaiKhoanController@dashboard` | Login |
| 2 | GET | `/tai-khoan/phuong-tien` | `TaiKhoanController@vehicles` | Login |
| 3 | POST | `/tai-khoan/phuong-tien` | `TaiKhoanController@addVehicle` | Login |
| 4 | POST | `/tai-khoan/phuong-tien/{id}/edit` | `TaiKhoanController@updateVehicle` | Login |
| 5 | POST | `/tai-khoan/phuong-tien/{id}/delete` | `TaiKhoanController@deleteVehicle` | Login |
| 6 | GET | `/tai-khoan/lich-su` | `TaiKhoanController@history` | Login |
| 7 | GET | `/tai-khoan/ho-so` | `TaiKhoanController@profile` | Login |
| 8 | POST | `/tai-khoan/ho-so` | `TaiKhoanController@updateProfile` | Login |
| 9 | POST | `/tai-khoan/doi-mat-khau` | `TaiKhoanController@changePassword` | Login |

## E.3. Admin Routes (47 routes)

| # | Method | URL | Controller@action | Loại |
|---|--------|-----|-------------------|------|
| 1 | GET | `/admin` | `DashboardController@index` | Dashboard |
| 2 | GET | `/admin/users` | `UserController@index` | List |
| 3 | GET | `/admin/users/create` | `UserController@create` | Form |
| 4 | POST | `/admin/users` | `UserController@store` | Create |
| 5 | GET | `/admin/users/{id}/edit` | `UserController@edit` | Form |
| 6 | POST | `/admin/users/{id}` | `UserController@update` | Update |
| 7 | POST | `/admin/users/{id}/delete` | `UserController@delete` | Delete |
| 8 | POST | `/admin/users/{id}/toggle-status` | `UserController@toggleStatus` | Toggle |
| 9 | GET | `/admin/violations` | `ViolationController@index` | List |
| 10 | GET | `/admin/violations/create` | `ViolationController@create` | Form |
| 11 | POST | `/admin/violations` | `ViolationController@store` | Create |
| 12 | GET | `/admin/violations/{id}/edit` | `ViolationController@edit` | Form |
| 13 | POST | `/admin/violations/{id}` | `ViolationController@update` | Update |
| 14 | POST | `/admin/violations/{id}/delete` | `ViolationController@delete` | Delete |
| 15 | POST | `/admin/violations/{id}/toggle-status` | `ViolationController@toggleStatus` | Toggle |
| 16 | POST | `/admin/violations/import` | `ViolationController@import` | Import |
| 17 | GET | `/admin/news` | `NewsController@index` | List |
| 18 | GET | `/admin/news/create` | `NewsController@create` | Form |
| 19 | POST | `/admin/news` | `NewsController@store` | Create |
| 20 | GET | `/admin/news/{id}/edit` | `NewsController@edit` | Form |
| 21 | POST | `/admin/news/{id}` | `NewsController@update` | Update |
| 22 | POST | `/admin/news/{id}/delete` | `NewsController@delete` | Delete |
| 23 | GET | `/admin/categories` | `CategoryController@index` | List |
| 24 | POST | `/admin/categories` | `CategoryController@store` | Create |
| 25 | POST | `/admin/categories/{id}` | `CategoryController@update` | Update |
| 26 | POST | `/admin/categories/{id}/delete` | `CategoryController@delete` | Delete |
| 27 | GET | `/admin/signs` | `SignController@index` | List |
| 28 | GET | `/admin/signs/create` | `SignController@create` | Form |
| 29 | POST | `/admin/signs` | `SignController@store` | Create |
| 30 | GET | `/admin/signs/{id}/edit` | `SignController@edit` | Form |
| 31 | POST | `/admin/signs/{id}` | `SignController@update` | Update |
| 32 | POST | `/admin/signs/{id}/delete` | `SignController@delete` | Delete |
| 33 | GET | `/admin/locations` | `LocationController@index` | List |
| 34 | GET | `/admin/locations/create` | `LocationController@create` | Form |
| 35 | POST | `/admin/locations` | `LocationController@store` | Create |
| 36 | GET | `/admin/locations/{id}/edit` | `LocationController@edit` | Form |
| 37 | POST | `/admin/locations/{id}` | `LocationController@update` | Update |
| 38 | POST | `/admin/locations/{id}/delete` | `LocationController@delete` | Delete |
| 39 | GET | `/admin/faqs` | `FaqController@index` | List |
| 40 | GET | `/admin/faqs/create` | `FaqController@create` | Form |
| 41 | POST | `/admin/faqs` | `FaqController@store` | Create |
| 42 | GET | `/admin/faqs/{id}/edit` | `FaqController@edit` | Form |
| 43 | POST | `/admin/faqs/{id}` | `FaqController@update` | Update |
| 44 | POST | `/admin/faqs/{id}/delete` | `FaqController@delete` | Delete |
| 45 | GET | `/admin/alerts` | `AlertController@index` | List |
| 46 | GET | `/admin/alerts/create` | `AlertController@create` | Form |
| 47 | POST | `/admin/alerts` | `AlertController@store` | Create |
| 48 | GET | `/admin/alerts/{id}/edit` | `AlertController@edit` | Form |
| 49 | POST | `/admin/alerts/{id}` | `AlertController@update` | Update |
| 50 | POST | `/admin/alerts/{id}/delete` | `AlertController@delete` | Delete |
| 51 | GET | `/admin/messages` | `MessageController@index` | List |
| 52 | POST | `/admin/messages/{id}/read` | `MessageController@markRead` | Mark read |
| 53 | POST | `/admin/messages/{id}/delete` | `MessageController@delete` | Delete |
| 54 | GET | `/admin/chat` | `ChatController@index` | List |
| 55 | GET | `/admin/chat/{id}` | `ChatController@show` | Detail |
| 56 | GET | `/admin/chat/{id}/messages` | `ChatController@messages` | AJAX |
| 57 | POST | `/admin/chat/{id}/reply` | `ChatController@reply` | AJAX |
| 58 | POST | `/admin/chat/{id}/close` | `ChatController@close` | Close |

**Tổng cộng: 80 routes** (23 client + 9 user + 48 admin)

---

## E.4. Models (16 models)

| Model | Bảng | Custom methods |
|-------|------|---------------|
| `User` | `users` | (chỉ kế thừa base Model) |
| `Vehicle` | `vehicles` | `findByUser()`, `countByUser()` |
| `Violation` | `violations` | `searchByPlate()`, `topOffenses()`, `topLocations()`, `topPlates()`, `countByMonth()`, `countByStatus()`, `countToday()`, `getAllWithDetails()` |
| `Offense` | `offenses` | `getWithCategory()` |
| `OffenseCategory` | `offense_categories` | (chỉ kế thừa base Model) |
| `Location` | `locations` | `findByType()`, `getActiveLocations()` |
| `News` | `news` | `getPublished()`, `getWithCategory()`, `getAllWithCategory()`, `incrementViews()`, `getLatest()`, `getRelated()` |
| `NewsCategory` | `news_categories` | (chỉ kế thừa base Model) |
| `TrafficSign` | `traffic_signs` | `getWithGroup()`, `findByGroup()`, `search()` |
| `TrafficSignGroup` | `traffic_sign_groups` | `getAllSorted()` |
| `Faq` | `faqs` | `getActive()` |
| `SearchHistory` | `search_history` | `getByUser()`, `log()` |
| `TrafficAlert` | `traffic_alerts` | `getActive()` |
| `ContactMessage` | `contact_messages` | `getUnread()`, `markRead()` |
| `ChatConversation` | `chat_conversations` | `findOpenByUser()`, `findOpenByGuestToken()`, `touchLastMessage()`, `close()`, `paginateWithUnreadCount()` |
| `ChatMessage` | `chat_messages` | `getByConversation()`, `markConversationRead()` |

---

## E.5. Views (thư mục)

| Thư mục | Số file | Mô tả |
|---------|---------|-------|
| `app/views/layouts/` | 2 | `client.php` (navbar + footer), `admin.php` (sidebar + header) |
| `app/views/partials/` | 5 | `header.php`, `footer.php`, `sidebar.php`, `alerts.php`, `pagination.php` |
| `app/views/client/home/` | 1 | `index.php` |
| `app/views/client/tracuu/` | 1 | `index.php` |
| `app/views/client/tintuc/` | 2 | `index.php`, `detail.php` |
| `app/views/client/bienbao/` | 2 | `index.php`, `detail.php` |
| `app/views/client/bando/` | 1 | `index.php` |
| `app/views/client/thongke/` | 1 | `index.php` |
| `app/views/client/faq/` | 1 | `index.php` |
| `app/views/client/taikhoan/` | 4 | `dashboard.php`, `vehicles.php`, `history.php`, `profile.php` |
| `app/views/client/auth/` | 2 | `login.php`, `register.php` |
| `app/views/client/pages/` | 2 | `gioi-thieu.php`, `lien-he.php` |
| `app/views/client/chat/` | 1 | `index.php` |
| `app/views/admin/dashboard/` | 1 | `index.php` |
| `app/views/admin/users/` | 2 | `index.php`, `form.php` |
| `app/views/admin/violations/` | 2 | `index.php`, `form.php` |
| `app/views/admin/news/` | 2 | `index.php`, `form.php` |
| `app/views/admin/categories/` | 1 | `index.php` |
| `app/views/admin/signs/` | 2 | `index.php`, `form.php` |
| `app/views/admin/locations/` | 2 | `index.php`, `form.php` |
| `app/views/admin/faqs/` | 2 | `index.php`, `form.php` |
| `app/views/admin/alerts/` | 2 | `index.php`, `form.php` |
| `app/views/admin/messages/` | 1 | `index.php` |
| `app/views/admin/chat/` | 2 | `index.php`, `show.php` |

**Tổng: 42 view files**

---

## E.6. Trình tự đọc code cho CHUẨN cho mọi chức năng

Khi bạn muốn hiểu hoặc sửa bất kỳ chức năng nào, đi theo 6 bước:

```
Bước 1: config/routes.php
  → Tìm URL → biết Controller@method nào xử lý
  → VD: /tra-cuu → client/TraCuuController@search

Bước 2: app/controllers/[client|admin]/XXXController.php
  → Đọc method tương ứng
  → Xem logic: CSRF → Validate → Model → View/JSON/Redirect

Bước 3: app/models/XXX.php
  → Đọc method được controller gọi
  → Xem SQL query (JOIN, WHERE, GROUP BY, ORDER BY...)
  → Base methods: Model.php (all, find, findBy, create, update, delete, paginate)

Bước 4: app/views/[client|admin]/XXX/YYY.php
  → Xem giao diện HTML hiển thị
  → Biến từ controller (đã extract) được dùng trực tiếp

Bước 5: app/views/layouts/[client|admin].php
  → Layout bọc ngoài (navbar/sidebar, footer)
  → Biến $content chứa HTML từ view

Bước 6: app/views/partials/*.php (nếu cần)
  → Header, Footer, Sidebar, Alerts (flash messages), Pagination
```

---

# F. CÁC PATTERN BẢO MẬT & NGHIỆP VỤ

| Pattern | Mô tả | File liên quan |
|---------|-------|---------------|
| **CSRF Token** | Mọi POST form đều có hidden `csrf_token`, controller check qua `$this->validateCsrf()` | `Session.php:106-119`, `Controller.php:83-91` |
| **Prepared Statements** | 100% query dùng PDO prepare/execute, không string-interpolate SQL | `Model.php`, mọi model con |
| **Password Bcrypt** | `password_hash(PASSWORD_BCRYPT)` khi tạo/sửa, `password_verify()` khi login | `AuthController.php`, `UserController.php`, `TaiKhoanController.php` |
| **XSS Protection** | `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` trên output | Trong các view |
| **Auth Guards** | `$this->requireLogin()` → redirect /dang-nhap; `$this->requireAdmin()` → requireLogin + check role | `Controller.php:60-78` |
| **Session Regeneration** | `session_regenerate_id(true)` sau login → chống session fixation | `Session.php:97` |
| **File Upload Validation** | Whitelist extension + size limit trong `Helper::upload()` | `Helper.php` |
| **AJAX + Non-AJAX** | Nhiều action hỗ trợ cả 2 chế độ, kiểm tra qua `$this->isAjax()` | `Controller.php:96-100` |
| **Flash Messages** | Hiển thị 1 lần rồi xóa (success/error/warning) | `Session.php:59-77`, view `partials/alerts.php` |
| **Slug Generation** | `Helper::slug()` → bỏ dấu tiếng Việt, thay khoảng trắng = dấu gạch ngang | `Helper.php` |
| **Pagination** | Controller tính toán, view hiển thị Bootstrap pagination | `Model.php:140-157`, `partials/pagination.php` |
