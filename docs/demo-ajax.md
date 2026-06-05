# Hướng dẫn demo AJAX trong dự án

Tài liệu này dùng để trình bày các chức năng AJAX đang có trong website **Tra cứu Phương tiện Vi phạm Giao thông**.

## 1. AJAX là gì trong dự án này?

Trong dự án, AJAX được dùng để gửi request lên server và nhận dữ liệu JSON mà không cần tải lại toàn bộ trang.

Dự án sử dụng:

```js
fetch()
```

Không sử dụng jQuery AJAX.

Dấu hiệu nhận biết AJAX trong code:

```js
headers: {
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json'
}
```

Phía PHP controller trả JSON thông qua hàm:

```php
$this->json([...]);
```

File nền tảng:

```text
app/core/Controller.php
```

Trong file này có 2 hàm quan trọng:

```php
protected function isAjax(): bool
```

và:

```php
protected function json(array $data, int $statusCode = 200): void
```

---

## 2. Chức năng tra cứu phạt nguội bằng AJAX

### File cần mở khi demo code

```text
app/views/client/tracuu/index.php
app/controllers/client/TraCuuController.php
config/routes.php
```

### Route liên quan

```php
$router->get('/tra-cuu', 'client/TraCuuController@index');
$router->post('/tra-cuu', 'client/TraCuuController@search');
```

### AJAX nằm ở đâu?

Trong file:

```text
app/views/client/tracuu/index.php
```

Tìm form:

```html
#search-form
```

và đoạn JavaScript gửi request:

```js
fetch('/tra-cuu', {
    method: 'POST',
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    },
    body: formData
})
```

### Controller xử lý AJAX

Trong file:

```text
app/controllers/client/TraCuuController.php
```

Phương thức:

```php
search()
```

Nếu request là AJAX thì controller trả dữ liệu JSON thay vì render lại trang.

### Cách demo trên trình duyệt

1. Mở website:

```text
http://localhost/tra-cuu
```

2. Nhập biển số xe và loại xe.
3. Bấm nút tra cứu.
4. Quan sát kết quả hiển thị ngay bên dưới form.
5. Trang không bị reload.

### Cách chứng minh bằng DevTools

1. Nhấn `F12`.
2. Vào tab **Network**.
3. Chọn bộ lọc **Fetch/XHR**.
4. Thực hiện tra cứu.
5. Sẽ thấy request:

```text
POST /tra-cuu
```

6. Bấm vào request đó, xem phần **Response** sẽ thấy dữ liệu JSON.

---

## 3. AJAX bật/tắt trạng thái người dùng trong admin

### File cần mở khi demo code

```text
public/assets/js/admin.js
app/views/admin/users/index.php
app/controllers/admin/UserController.php
config/routes.php
```

### Route liên quan

```php
$router->post('/admin/users/{id}/toggle-status', 'admin/UserController@toggleStatus');
```

### AJAX nằm ở đâu?

Trong file:

```text
public/assets/js/admin.js
```

Tìm đoạn:

```js
document.querySelectorAll('.toggle-status-btn').forEach(btn => {
```

và đoạn:

```js
fetch(url, {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: formData
})
```

### View tạo nút AJAX

Trong file:

```text
app/views/admin/users/index.php
```

Tìm class:

```html
toggle-status-btn
```

Nút này có các thuộc tính như:

```html
data-type="user"
data-id="..."
data-current-status="..."
```

### Controller xử lý

Trong file:

```text
app/controllers/admin/UserController.php
```

Phương thức:

```php
toggleStatus()
```

Khi request là AJAX, controller trả JSON gồm trạng thái mới của user.

### Cách demo trên trình duyệt

1. Đăng nhập admin:

```text
Email: admin@traffic.vn
Mật khẩu: admin123
```

2. Mở trang:

```text
http://localhost/admin/users
```

3. Bấm nút khóa hoặc mở khóa người dùng.
4. Trạng thái trên bảng thay đổi ngay.
5. Trang không bị reload.

### Cách chứng minh bằng DevTools

Trong tab **Network** sẽ thấy request dạng:

```text
POST /admin/users/{id}/toggle-status
```

Response JSON ví dụ:

```json
{
  "success": true,
  "new_status": 0,
  "message": "Cập nhật trạng thái thành công."
}
```

---

## 4. AJAX đổi trạng thái vi phạm trong admin

### File cần mở khi demo code

```text
public/assets/js/admin.js
app/views/admin/violations/index.php
app/controllers/admin/ViolationController.php
config/routes.php
```

### Route liên quan

```php
$router->post('/admin/violations/{id}/toggle-status', 'admin/ViolationController@toggleStatus');
```

### AJAX nằm ở đâu?

Cũng dùng chung file:

```text
public/assets/js/admin.js
```

Đoạn JS xác định URL theo loại nút:

```js
const url = type === 'user'
    ? `/admin/users/${id}/toggle-status`
    : `/admin/violations/${id}/toggle-status`;
```

### View tạo nút AJAX

Trong file:

```text
app/views/admin/violations/index.php
```

Tìm:

```html
data-type="violation"
```

### Controller xử lý

Trong file:

```text
app/controllers/admin/ViolationController.php
```

Phương thức:

```php
toggleStatus()
```

Controller đổi trạng thái vi phạm và trả JSON về cho JavaScript cập nhật giao diện.

### Cách demo trên trình duyệt

1. Đăng nhập admin.
2. Mở trang:

```text
http://localhost/admin/violations
```

3. Bấm vào badge/trạng thái của một vi phạm.
4. Trạng thái đổi ngay trên bảng.
5. Trang không reload.

### Cách chứng minh bằng DevTools

Trong tab **Network** sẽ thấy request:

```text
POST /admin/violations/{id}/toggle-status
```

Response JSON ví dụ:

```json
{
  "success": true,
  "new_status": "processed",
  "label": "Đã xử lý",
  "message": "Cập nhật trạng thái thành công."
}
```

---

## 5. AJAX chat hai chiều giữa client và admin

Đây là chức năng chat hỗ trợ khách hàng. Chat dùng AJAX polling, nghĩa là JavaScript tự gọi server mỗi vài giây để lấy tin nhắn mới.

### File client cần mở khi demo code

```text
app/views/client/chat/index.php
app/controllers/client/ChatController.php
public/assets/js/chat-client.js
config/routes.php
```

### File admin cần mở khi demo code

```text
app/views/admin/chat/index.php
app/views/admin/chat/show.php
app/controllers/admin/ChatController.php
public/assets/js/chat-admin.js
config/routes.php
```

### Route client liên quan

```php
$router->get('/chat', 'client/ChatController@index');
$router->post('/chat/start', 'client/ChatController@start');
$router->post('/chat/restore', 'client/ChatController@restore');
$router->get('/chat/{id}/messages', 'client/ChatController@messages');
$router->post('/chat/{id}/send', 'client/ChatController@send');
```

### Route admin liên quan

```php
$router->get('/admin/chat', 'admin/ChatController@index');
$router->get('/admin/chat/{id}', 'admin/ChatController@show');
$router->get('/admin/chat/{id}/messages', 'admin/ChatController@messages');
$router->post('/admin/chat/{id}/reply', 'admin/ChatController@reply');
$router->post('/admin/chat/{id}/close', 'admin/ChatController@close');
```

### AJAX phía client nằm ở đâu?

Trong file:

```text
public/assets/js/chat-client.js
```

Các request AJAX chính:

```text
POST /chat/start
POST /chat/restore
GET  /chat/{id}/messages
POST /chat/{id}/send
```

Đoạn polling tự tải tin nhắn mới:

```js
setInterval(loadMessages, 4000);
```

Nghĩa là client tự gọi server mỗi 4 giây để lấy tin mới.

### AJAX phía admin nằm ở đâu?

Trong file:

```text
public/assets/js/chat-admin.js
```

Các request AJAX chính:

```text
GET  /admin/chat/{id}/messages
POST /admin/chat/{id}/reply
```

Admin cũng polling tin nhắn mới mỗi 4 giây:

```js
setInterval(loadMessages, 4000);
```

### Cách demo chat hai chiều

#### Bước 1: Mở client

Mở tab thứ nhất:

```text
http://localhost/chat
```

Nếu chưa đăng nhập:

1. Nhập họ tên.
2. Nhập email.
3. Bấm **Bắt đầu chat**.
4. Gửi một tin nhắn.

Trong DevTools sẽ thấy:

```text
POST /chat/start
POST /chat/{id}/send
GET  /chat/{id}/messages
```

#### Bước 2: Mở admin

Mở tab thứ hai, đăng nhập admin rồi vào:

```text
http://localhost/admin/chat
```

Chọn cuộc trò chuyện vừa tạo.

Hoặc mở trực tiếp dạng:

```text
http://localhost/admin/chat/{id}
```

Gửi phản hồi từ admin.

Trong DevTools sẽ thấy:

```text
GET  /admin/chat/{id}/messages
POST /admin/chat/{id}/reply
```

#### Bước 3: Quay lại tab client

Sau vài giây, tin nhắn admin sẽ tự xuất hiện ở phía client mà không cần reload trang.

Đây là AJAX polling.

---

## 6. Cách trình bày khi demo với giảng viên

Có thể trình bày theo thứ tự sau:

1. Mở `app/core/Controller.php` để chỉ ra hàm nhận biết AJAX và trả JSON.
2. Mở `app/views/client/tracuu/index.php` để chỉ ra `fetch()`.
3. Mở `app/controllers/client/TraCuuController.php` để chỉ ra controller trả JSON.
4. Demo trực tiếp `/tra-cuu`, cho thấy trang không reload.
5. Mở `public/assets/js/admin.js` để chỉ ra AJAX dùng chung cho admin.
6. Demo `/admin/users` hoặc `/admin/violations`, bấm đổi trạng thái.
7. Mở `public/assets/js/chat-client.js` và `public/assets/js/chat-admin.js` để chỉ ra AJAX polling.
8. Demo chat hai chiều giữa `/chat` và `/admin/chat/{id}`.

---

## 7. Dấu hiệu chứng minh đây là AJAX

Khi demo, mở DevTools bằng `F12`, vào tab **Network**, chọn bộ lọc **Fetch/XHR**.

Nếu là AJAX đúng, sẽ thấy:

- Request xuất hiện trong tab **Fetch/XHR**.
- Trang không reload.
- Response trả về JSON.
- Giao diện thay đổi ngay sau khi nhận response.

Các endpoint AJAX cần quan sát:

```text
POST /tra-cuu
POST /admin/users/{id}/toggle-status
POST /admin/violations/{id}/toggle-status
POST /chat/start
POST /chat/restore
GET  /chat/{id}/messages
POST /chat/{id}/send
GET  /admin/chat/{id}/messages
POST /admin/chat/{id}/reply
```

---

## 8. Tóm tắt các chức năng có AJAX

| STT | Chức năng | File JS/View gọi AJAX | Controller xử lý |
|---|---|---|---|
| 1 | Tra cứu phạt nguội | `app/views/client/tracuu/index.php` | `TraCuuController.php` |
| 2 | Bật/tắt user admin | `public/assets/js/admin.js` | `UserController.php` |
| 3 | Đổi trạng thái vi phạm | `public/assets/js/admin.js` | `ViolationController.php` |
| 4 | Client chat | `public/assets/js/chat-client.js` | `client/ChatController.php` |
| 5 | Admin chat | `public/assets/js/chat-admin.js` | `admin/ChatController.php` |

Kết luận: AJAX trong dự án được áp dụng để cải thiện trải nghiệm người dùng ở các thao tác cần phản hồi nhanh, không cần tải lại toàn bộ trang như tra cứu, cập nhật trạng thái và chat hai chiều.
