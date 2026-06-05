<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Models\ContactMessage;

/**
 * MessageController - Quản lý tin nhắn liên hệ (contact_messages) trong trang admin.
 *
 * Đây là controller đơn giản nhất trong admin — chỉ có 3 action vì admin không
 * tạo/sửa tin nhắn (chỉ user/guest gửi từ form liên hệ ngoài site), chỉ đọc và xóa.
 *
 * Các route tương ứng:
 *   GET  /admin/messages           -> index()      - Danh sách tin nhắn (phân trang)
 *   POST /admin/messages/{id}/read -> markRead($id) - Đánh dấu đã đọc
 *   POST /admin/messages/{id}/delete-> delete($id)  - Xóa tin nhắn
 *
 * Mỗi tin nhắn gồm: name, email, phone, subject, message (nội dung từ form liên hệ),
 * is_read (0=chưa đọc, 1=đã đọc), created_at.
 *
 * Model ContactMessage có thêm phương thức custom markRead($id) để cập nhật trạng
 * thái đã đọc, không dùng chung với update() của Model cha.
 */
class MessageController extends Controller
{
    /**
     * Constructor: yêu cầu quyền admin cho mọi action.
     */
    public function __construct()
    {
        $this->requireAdmin();
    }

    /**
     * Danh sách tin nhắn - GET /admin/messages
     *
     * Flow:
     *   Input:  Query param ?page=N.
     *   Xử lý:  Gọi ContactMessage::paginate() lấy 10 tin nhắn/trang,
     *           sắp xếp theo created_at giảm dần (mới nhất lên đầu).
     *           View sẽ hiển thị badge trạng thái "Chưa đọc"/"Đã đọc" dựa trên cột is_read.
     *   Output: Render view admin/messages/index.
     */
    public function index(): void
    {
        $model = new ContactMessage();
        $page = (int)($this->input('page', 1));
        $data = $model->paginate($page, 10, [], 'created_at DESC');
        $data['title'] = 'Manage Contact Messages';
        $data['baseUrl'] = '/admin/messages';
        $this->view('admin/messages/index', $data, 'admin');
    }

    /**
     * Đánh dấu tin nhắn đã đọc - POST /admin/messages/{id}/read
     *
     * Flow:
     *   Input:  Route param $id (int) — ID của tin nhắn.
     *   Bước 1: Kiểm tra CSRF token (yêu cầu POST để tránh thay đổi trạng thái qua GET).
     *   Bước 2: Tìm tin nhắn qua ContactMessage::find($id).
     *   Bước 3: Gọi ContactMessage::markRead($id) — phương thức custom trong model,
     *           thực thi UPDATE contact_messages SET is_read = 1 WHERE id = ?.
     *   Bước 4: Flash success, redirect về danh sách.
     *   Output: Redirect về /admin/messages.
     *
     * Tại sao tách markRead() riêng thay vì dùng update():
     *   ContactMessage model kế thừa Model, nhưng markRead() là phương thức chuyên biệt
     *   để chỉ cập nhật duy nhất cột is_read, tránh truyền nhầm dữ liệu khác.
     */
    public function markRead(int $id): void
    {
        if (!$this->validateCsrf()) return;
        $model = new ContactMessage();
        $msg = $model->find($id);
        if (!$msg) {
            Session::setFlash('error', 'Message does not exist.');
            $this->redirect('/admin/messages');
            return;
        }

        // UPDATE contact_messages SET is_read = 1 WHERE id = ?
        $model->markRead($id);
        Session::setFlash('success', 'Marked as read.');
        $this->redirect('/admin/messages');
    }

    /**
     * Xóa tin nhắn - POST /admin/messages/{id}/delete
     *
     * Flow:
     *   Input:  Route param $id (int, ép kiểu lại để an toàn).
     *   Bước 1: CSRF token.
     *   Bước 2: Xác minh tin nhắn tồn tại.
     *   Bước 3: ContactMessage::delete($id) — DELETE FROM contact_messages WHERE id = ?.
     *   Bước 4: Flash success, redirect.
     *   Output: Redirect.
     *
     * Lưu ý: $id được truyền qua route param (không phải query string) nên có kiểu
     * int từ Router. Controller vẫn ép kiểu (int)$id lần nữa để phòng thủ.
     */
    public function delete(int $id): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)$id;    // Ép kiểu lại để đảm bảo an toàn kiểu dữ liệu
        $model = new ContactMessage();
        if (!$model->find($id)) {
            Session::setFlash('error', 'Message does not exist.');
            $this->redirect('/admin/messages');
            return;
        }

        $model->delete($id);
        Session::setFlash('success', 'Message deleted successfully.');
        $this->redirect('/admin/messages');
    }
}
