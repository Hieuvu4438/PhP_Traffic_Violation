<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;

/**
 * UserController - Quản lý người dùng (users) trong trang admin.
 *
 * Đây là controller phức tạp nhất trong nhóm admin vì liên quan đến bảo mật:
 * mật khẩu phải được hash bằng bcrypt, kiểm tra trùng email/số điện thoại,
 * và có toggleStatus() hỗ trợ cả AJAX lẫn form POST thông thường.
 *
 * Các route tương ứng:
 *   GET  /admin/users                  -> index()        - Danh sách user (phân trang)
 *   GET  /admin/users/create           -> create()       - Form thêm user
 *   POST /admin/users/store            -> store()        - Lưu user mới
 *   GET  /admin/users/{id}/edit        -> edit()         - Form sửa user
 *   POST /admin/users/{id}/update      -> update()       - Cập nhật user
 *   POST /admin/users/{id}/delete      -> delete()       - Xóa user
 *   POST /admin/users/{id}/toggle-status -> toggleStatus() - Bật/tắt trạng thái (AJAX + non-AJAX)
 *
 * Bảo mật:
 *   - Mật khẩu luôn được hash bằng password_hash(PASSWORD_BCRYPT) trước khi lưu.
 *   - Khi update, mật khẩu chỉ được cập nhật nếu người dùng nhập mật khẩu mới
 *     (không ghi đè bằng chuỗi rỗng).
 *   - Kiểm tra trùng email và phone khi tạo mới VÀ khi cập nhật (loại trừ chính user đó).
 */
class UserController extends Controller
{
    /**
     * Constructor: yêu cầu quyền admin cho mọi action.
     */
    public function __construct()
    {
        $this->requireAdmin();
    }

    /**
     * Danh sách người dùng - GET /admin/users
     *
     * Flow:
     *   Input:  Query param ?page=N.
     *   Xử lý:  User::paginate() trả về danh sách user kèm phân trang,
     *           sắp xếp theo created_at giảm dần (mới nhất trước).
     *   Output: Render view admin/users/index.
     *           View hiển thị: fullname, email, phone, role, status (badge), action buttons.
     *           KHÔNG hiển thị password hash trong danh sách.
     */
    public function index(): void
    {
        $model = new User();
        $page = (int)($this->input('page', 1));
        $data = $model->paginate($page, 10, [], 'created_at DESC');
        $data['title'] = 'Quản lý người dùng';
        $data['baseUrl'] = '/admin/users';
        $this->view('admin/users/index', $data, 'admin');
    }

    /**
     * Form thêm người dùng mới - GET /admin/users/create
     *
     * Flow:
     *   Input:  Không có tham số.
     *   Output: Render view admin/users/form.
     *           Form có các trường: fullname, email, phone, password (bắt buộc khi tạo),
     *           role (select: user/admin), status (checkbox).
     *           Khi tạo mới, password là bắt buộc (khác với edit).
     */
    public function create(): void
    {
        $data = ['title' => 'Thêm người dùng'];
        $this->view('admin/users/form', $data, 'admin');
    }

    /**
     * Lưu người dùng mới - POST /admin/users/store
     *
     * Flow:
     *   Input:  $_POST (fullname, email, phone, password, role, status).
     *   Bước 1: CSRF token.
     *   Bước 2: Validate server-side:
     *           - fullname: bắt buộc, 2-100 ký tự.
     *           - email: bắt buộc, định dạng email hợp lệ.
     *           - phone: không bắt buộc, nếu có thì phải đúng định dạng số điện thoại VN.
     *           - password: bắt buộc khi tạo mới, 6-255 ký tự.
     *   Bước 3: Kiểm tra trùng lặp (business logic):
     *           - User::findBy('email', ...) — nếu email đã tồn tại, báo lỗi.
     *           - User::findBy('phone', ...) — nếu phone đã tồn tại và không rỗng, báo lỗi.
     *           Việc kiểm tra riêng email và phone cho phép hiển thị thông báo lỗi
     *           cụ thể (email đã tồn tại / phone đã tồn tại) thay vì lỗi SQL duplicate key mơ hồ.
     *   Bước 4: Hash mật khẩu bằng password_hash(PASSWORD_BCRYPT) trước khi lưu.
     *   Bước 5: User::create() với prepared statement — an toàn SQL injection.
     *   Bước 6: Flash success, redirect.
     *   Output: Redirect.
     *
     * Lưu ý: role mặc định là 'user', KHÔNG ai có thể tự đăng ký làm admin.
     * Chỉ admin hiện tại mới có thể tạo tài khoản admin khác qua form này.
     */
    public function store(): void
    {
        if (!$this->validateCsrf()) return;

        $validator = new Validator();
        $rules = [
            'fullname' => 'required|min:2|max:100',    // Họ tên: bắt buộc, 2-100 ký tự
            'email'    => 'required|email',             // Email: bắt buộc, định dạng email
            'phone'    => 'phone',                      // SĐT: không bắt buộc, định dạng VN nếu có
            'password' => 'required|min:6|max:255',     // Mật khẩu: bắt buộc, 6-255 ký tự
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            Session::set('form_errors', $validator->getErrors());
            Session::set('old_input', $_POST);
            $this->redirect('/admin/users/create');
            return;
        }

        $model = new User();

        // Kiểm tra trùng email — tránh duplicate key violation trên UNIQUE constraint
        if ($model->findBy('email', $_POST['email'])) {
            Session::setFlash('error', 'Email đã tồn tại.');
            Session::set('old_input', $_POST);    // Giữ lại dữ liệu đã nhập, trừ mật khẩu
            $this->redirect('/admin/users/create');
            return;
        }

        // Kiểm tra trùng số điện thoại — chỉ kiểm tra nếu người dùng có nhập
        if (!empty($_POST['phone']) && $model->findBy('phone', $_POST['phone'])) {
            Session::setFlash('error', 'Số điện thoại đã tồn tại.');
            Session::set('old_input', $_POST);
            $this->redirect('/admin/users/create');
            return;
        }

        $model->create([
            'fullname'  => $_POST['fullname'],
            'email'     => $_POST['email'],
            'phone'     => $_POST['phone'] ?? null,
            // Bảo mật: hash mật khẩu bằng bcrypt trước khi lưu — KHÔNG lưu plaintext
            'password'  => password_hash($_POST['password'], PASSWORD_BCRYPT),
            'role'      => $_POST['role'] ?? 'user',       // Mặc định 'user'
            'status'    => (int)($_POST['status'] ?? 1),    // 1=hoạt động, 0=bị khóa
        ]);

        Session::setFlash('success', 'Thêm người dùng thành công.');
        $this->redirect('/admin/users');
    }

    /**
     * Form sửa người dùng - GET /admin/users/{id}/edit
     *
     * Flow:
     *   Input:  Query param ?id=N.
     *   Xử lý:  User::find($id) lấy thông tin user để pre-fill form.
     *           View sẽ hiển thị các trường nhưng password để trống
     *           (chỉ cập nhật nếu admin nhập mật khẩu mới).
     *   Output: Render view admin/users/form với biến $user.
     */
    public function edit(): void
    {
        $id = (int)($this->input('id', 0));
        $model = new User();
        $user = $model->find($id);
        if (!$user) {
            Session::setFlash('error', 'Người dùng không tồn tại.');
            $this->redirect('/admin/users');
            return;
        }

        $data = [
            'title' => 'Sửa người dùng',
            'user'  => $user,
        ];
        $this->view('admin/users/form', $data, 'admin');
    }

    /**
     * Cập nhật người dùng - POST /admin/users/{id}/update
     *
     * Flow:
     *   Input:  $_POST + query param ?id=N.
     *   Bước 1: CSRF token.
     *   Bước 2: Xác minh user tồn tại.
     *   Bước 3: Validate (password không bắt buộc khi update).
     *   Bước 4: Kiểm tra trùng email và phone, nhưng loại trừ chính user đang sửa:
     *           - Nếu email đã tồn tại VÀ id khác với user hiện tại -> báo lỗi.
     *           - Tương tự với phone, chỉ báo lỗi nếu thuộc về user khác.
     *   Bước 5: Xây dựng mảng $updateData. Nếu $_POST['password'] không rỗng,
     *           hash mật khẩu mới và thêm vào $updateData.
     *           Nếu không nhập mật khẩu -> giữ nguyên mật khẩu cũ (không đụng vào cột password).
     *   Bước 6: User::update($id, $updateData).
     *   Bước 7: Flash success, redirect.
     *   Output: Redirect.
     *
     * Quyết định nghiệp vụ quan trọng:
     *   - Mật khẩu chỉ được cập nhật khi admin chủ động nhập mật khẩu mới.
     *   - Nếu để trống ô password, cột password trong DB giữ nguyên giá trị cũ.
     *   - Điều này ngăn việc vô tình ghi đè mật khẩu bằng chuỗi rỗng.
     *   - Role mặc định giữ nguyên role cũ nếu không được truyền.
     */
    public function update(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new User();
        $user = $model->find($id);
        if (!$user) {
            Session::setFlash('error', 'Người dùng không tồn tại.');
            $this->redirect('/admin/users');
            return;
        }

        // Khi update, password không bắt buộc — chỉ validate nếu có nhập
        $validator = new Validator();
        $rules = [
            'fullname' => 'required|min:2|max:100',
            'email'    => 'required|email',
            'phone'    => 'phone',
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            Session::set('form_errors', $validator->getErrors());
            Session::set('old_input', $_POST);
            $this->redirect("/admin/users/{$id}/edit");
            return;
        }

        // Kiểm tra trùng email: bỏ qua nếu email thuộc về chính user đang sửa
        $existing = $model->findBy('email', $_POST['email']);
        if ($existing && $existing['id'] != $id) {
            Session::setFlash('error', 'Email đã tồn tại.');
            Session::set('old_input', $_POST);
            $this->redirect("/admin/users/{$id}/edit");
            return;
        }

        // Kiểm tra trùng phone: chỉ kiểm tra nếu có nhập và thuộc về user khác
        if (!empty($_POST['phone'])) {
            $phoneExisting = $model->findBy('phone', $_POST['phone']);
            if ($phoneExisting && $phoneExisting['id'] != $id) {
                Session::setFlash('error', 'Số điện thoại đã tồn tại.');
                Session::set('old_input', $_POST);
                $this->redirect("/admin/users/{$id}/edit");
                return;
            }
        }

        // Xây dựng dữ liệu cập nhật — role và status giữ nguyên nếu không được truyền
        $updateData = [
            'fullname' => $_POST['fullname'],
            'email'    => $_POST['email'],
            'phone'    => $_POST['phone'] ?? null,
            'role'     => $_POST['role'] ?? $user['role'],       // Giữ role cũ nếu không truyền
            'status'   => (int)($_POST['status'] ?? $user['status']), // Giữ status cũ nếu không truyền
        ];

        // Chỉ hash và cập nhật mật khẩu nếu admin nhập mật khẩu mới
        if (!empty($_POST['password'])) {
            $updateData['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        }

        $model->update($id, $updateData);
        Session::setFlash('success', 'Cập nhật người dùng thành công.');
        $this->redirect('/admin/users');
    }

    /**
     * Xóa người dùng - POST /admin/users/{id}/delete
     *
     * Flow:
     *   Input:  Query param ?id=N.
     *   Xử lý:  CSRF -> find() -> delete().
     *   Output: Redirect.
     *
     * Cảnh báo: Xóa user sẽ xóa luôn các bản ghi liên quan nếu DB có thiết lập
     * ON DELETE CASCADE (violations, vehicles, search_history...).
     * Cân nhắc: thay vì xóa cứng, nên soft-delete bằng cách set status = 0.
     */
    public function delete(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new User();
        $user = $model->find($id);
        if (!$user) {
            Session::setFlash('error', 'Người dùng không tồn tại.');
            $this->redirect('/admin/users');
            return;
        }

        $model->delete($id);
        Session::setFlash('success', 'Xóa người dùng thành công.');
        $this->redirect('/admin/users');
    }

    /**
     * Bật/tắt trạng thái người dùng (khóa/mở khóa) - POST /admin/users/{id}/toggle-status
     *
     * Đây là action hỗ trợ CẢ HAI chế độ: AJAX (fetch API từ JavaScript) và
     * non-AJAX (form POST thông thường). View dùng JavaScript để gửi AJAX request
     * tới endpoint này, nhưng nếu JS bị tắt thì form POST vẫn hoạt động.
     *
     * Flow:
     *   Input:  Query param ?id=N.
     *   Bước 1: CSRF token:
     *           - Nếu lỗi CSRF VÀ là AJAX -> trả về JSON lỗi 419 (Session Expired).
     *           - Nếu lỗi CSRF VÀ không phải AJAX -> return (không làm gì).
     *   Bước 2: Tìm user, nếu không tồn tại:
     *           - AJAX -> JSON lỗi 404.
     *           - Non-AJAX -> flash error, redirect.
     *   Bước 3: Đảo trạng thái: nếu status=1 thì chuyển thành 0, ngược lại thành 1.
     *           User::update($id, ['status' => $newStatus]).
     *   Bước 4: Phản hồi:
     *           - AJAX -> JSON {success, new_status, message}.
     *           - Non-AJAX -> flash success kèm message mô tả (Mở khóa/Khóa).
     *   Output: JSON (nếu AJAX) hoặc Redirect (nếu không).
     *
     * Ý nghĩa status:
     *   - 1: Tài khoản đang hoạt động (user có thể đăng nhập).
     *   - 0: Tài khoản bị khóa (user không thể đăng nhập, nên có kiểm tra trong LoginController).
     */
    public function toggleStatus(): void
    {
        // CSRF: nếu là AJAX thì trả JSON, nếu không thì return (không output)
        if (!$this->validateCsrf()) {
            if ($this->isAjax()) {
                // HTTP 419: Authentication Timeout / Session Expired
                $this->json(['success' => false, 'message' => 'Phiên làm việc hết hạn.'], 419);
                return;
            }
            return;
        }

        $id = (int)($this->input('id', 0));
        $model = new User();
        $user = $model->find($id);
        if (!$user) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Người dùng không tồn tại.'], 404);
                return;
            }
            Session::setFlash('error', 'Người dùng không tồn tại.');
            $this->redirect('/admin/users');
            return;
        }

        // Đảo trạng thái: 1 -> 0 (khóa), 0 -> 1 (mở khóa)
        $newStatus = $user['status'] ? 0 : 1;
        $model->update($id, ['status' => $newStatus]);

        // Phản hồi AJAX: JSON để JavaScript cập nhật UI không cần reload trang
        if ($this->isAjax()) {
            $this->json([
                'success'    => true,
                'new_status' => $newStatus,
                'message'    => $newStatus ? 'Mở khóa người dùng thành công.' : 'Khóa người dùng thành công.',
            ]);
            return;
        }

        // Phản hồi non-AJAX: redirect kèm flash message
        $msg = $newStatus ? 'Mở khóa người dùng thành công.' : 'Khóa người dùng thành công.';
        Session::setFlash('success', $msg);
        $this->redirect('/admin/users');
    }
}
