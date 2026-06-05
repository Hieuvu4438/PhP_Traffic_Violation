<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\TrafficAlert;

/**
 * AlertController - Quản lý cảnh báo giao thông (traffic_alerts) trong trang admin.
 *
 * Các route tương ứng:
 *   GET  /admin/alerts              -> index()   - Danh sách cảnh báo (phân trang)
 *   GET  /admin/alerts/create       -> create()  - Form thêm cảnh báo
 *   POST /admin/alerts/store        -> store()   - Lưu cảnh báo mới
 *   GET  /admin/alerts/{id}/edit    -> edit()    - Form sửa cảnh báo
 *   POST /admin/alerts/{id}/update  -> update()  - Cập nhật cảnh báo
 *   POST /admin/alerts/{id}/delete  -> delete()  - Xóa cảnh báo
 *
 * Mỗi cảnh báo gồm: title (tiêu đề), content (nội dung chi tiết, CKEditor HTML),
 * alert_type (phân loại: accident/roadwork/flood/other), expires_at (thời gian hết hạn),
 * created_by (FK -> users, admin tạo cảnh báo), status (ẩn/hiện).
 *
 * Cảnh báo hết hạn tự động ẩn ở phía client (view kiểm tra expires_at < NOW()).
 */
class AlertController extends Controller
{
    /**
     * Constructor: yêu cầu quyền admin cho mọi action.
     */
    public function __construct()
    {
        $this->requireAdmin();
    }

    /**
     * Danh sách cảnh báo - GET /admin/alerts
     *
     * Flow:
     *   Input:  Query param ?page=N.
     *   Xử lý:  Gọi TrafficAlert::paginate() lấy 10 cảnh báo/trang, sắp xếp theo
     *           created_at giảm dần (mới nhất lên đầu).
     *   Output: Render view admin/alerts/index với phân trang.
     */
    public function index(): void
    {
        $model = new TrafficAlert();
        $page = (int)($this->input('page', 1));
        // Sắp xếp theo created_at DESC: cảnh báo mới nhất hiển thị đầu tiên
        $data = $model->paginate($page, 10, [], 'created_at DESC');
        $data['title'] = 'Manage Traffic Alerts';
        $data['baseUrl'] = '/admin/alerts';
        $this->view('admin/alerts/index', $data, 'admin');
    }

    /**
     * Form thêm cảnh báo mới - GET /admin/alerts/create
     *
     * Flow:
     *   Input:  Không có tham số.
     *   Output: Render view admin/alerts/form.
     *           Form có các trường: title, content (CKEditor), alert_type (select),
     *           expires_at (datetime-local input), status (checkbox/radio).
     */
    public function create(): void
    {
        $data = ['title' => 'Add Traffic Alert'];
        $this->view('admin/alerts/form', $data, 'admin');
    }

    /**
     * Lưu cảnh báo mới - POST /admin/alerts/store
     *
     * Flow:
     *   Input:  $_POST (title, content, alert_type, expires_at, status).
     *   Bước 1: Kiểm tra CSRF token.
     *   Bước 2: Validate server-side:
     *           - title: bắt buộc, tối đa 255 ký tự.
     *           - content và alert_type không bắt buộc (có giá trị mặc định).
     *   Bước 3: Gọi TrafficAlert::create():
     *           - created_by: lấy từ Session::get('user_id') — ID của admin đang đăng nhập,
     *             dùng để truy vết ai đã tạo cảnh báo (FK đến bảng users).
     *           - expires_at: nếu để trống thì lưu NULL (cảnh báo không có hạn).
     *           - alert_type: mặc định 'other' nếu không chọn.
     *           - status: mặc định 1 (hiển thị).
     *   Bước 4: Flash success, redirect về danh sách.
     *   Output: Redirect.
     */
    public function store(): void
    {
        if (!$this->validateCsrf()) return;

        $validator = new Validator();
        $rules = [
            'title' => 'required|max:255',    // Tiêu đề: bắt buộc, tối đa 255 ký tự
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            Session::set('form_errors', $validator->getErrors());
            Session::set('old_input', $_POST);
            $this->redirect('/admin/alerts/create');
            return;
        }

        $model = new TrafficAlert();
        $model->create([
            'title'      => $_POST['title'],
            'content'    => $_POST['content'] ?? null,            // Nội dung CKEditor, không bắt buộc
            'alert_type' => $_POST['alert_type'] ?? 'other',      // Mặc định 'other' nếu không chọn
            'expires_at' => $_POST['expires_at'] ?: null,         // NULL nếu không có hạn
            'created_by' => Session::get('user_id'),              // Admin hiện tại (FK -> users.id)
            'status'     => (int)($_POST['status'] ?? 1),         // 1=hiển thị, 0=ẩn
        ]);

        Session::setFlash('success', 'Alert added successfully.');
        $this->redirect('/admin/alerts');
    }

    /**
     * Form sửa cảnh báo - GET /admin/alerts/{id}/edit
     *
     * Flow:
     *   Input:  Query param ?id=N.
     *   Xử lý:  TrafficAlert::find($id) lấy bản ghi hiện tại để pre-fill form.
     *   Output: Render view admin/alerts/form với biến $alert.
     *           View sẽ hiển thị created_by nhưng không cho sửa (read-only).
     */
    public function edit(): void
    {
        $id = (int)($this->input('id', 0));
        $model = new TrafficAlert();
        $alert = $model->find($id);
        if (!$alert) {
            Session::setFlash('error', 'Alert does not exist.');
            $this->redirect('/admin/alerts');
            return;
        }

        $data = [
            'title' => 'Edit Traffic Alert',
            'alert' => $alert,
        ];
        $this->view('admin/alerts/form', $data, 'admin');
    }

    /**
     * Cập nhật cảnh báo - POST /admin/alerts/{id}/update
     *
     * Flow:
     *   Input:  $_POST + query param ?id=N.
     *   Bước 1: CSRF token.
     *   Bước 2: Xác minh cảnh báo tồn tại.
     *   Bước 3: Validate (giống store).
     *   Bước 4: Gọi TrafficAlert::update($id, $data).
     *           Lưu ý: created_by KHÔNG được cập nhật (giữ nguyên người tạo ban đầu).
     *   Output: Redirect về danh sách.
     */
    public function update(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new TrafficAlert();
        $alert = $model->find($id);
        if (!$alert) {
            Session::setFlash('error', 'Alert does not exist.');
            $this->redirect('/admin/alerts');
            return;
        }

        $validator = new Validator();
        $rules = [
            'title' => 'required|max:255',
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            $this->redirect("/admin/alerts/{$id}/edit");
            return;
        }

        // Chỉ cập nhật các trường được phép thay đổi, created_by giữ nguyên
        $model->update($id, [
            'title'      => $_POST['title'],
            'content'    => $_POST['content'] ?? null,
            'alert_type' => $_POST['alert_type'] ?? 'other',
            'expires_at' => $_POST['expires_at'] ?: null,
            'status'     => (int)($_POST['status'] ?? 1),
        ]);

        Session::setFlash('success', 'Alert updated successfully.');
        $this->redirect('/admin/alerts');
    }

    /**
     * Xóa cảnh báo - POST /admin/alerts/{id}/delete
     *
     * Flow:
     *   Input:  Query param ?id=N.
     *   Xử lý:  CSRF -> find() kiểm tra tồn tại -> delete().
     *   Output: Redirect về danh sách.
     */
    public function delete(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new TrafficAlert();
        if (!$model->find($id)) {
            Session::setFlash('error', 'Alert does not exist.');
            $this->redirect('/admin/alerts');
            return;
        }

        $model->delete($id);    // DELETE FROM traffic_alerts WHERE id = ?
        Session::setFlash('success', 'Alert deleted successfully.');
        $this->redirect('/admin/alerts');
    }
}
