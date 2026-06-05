<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Faq;

/**
 * FaqController - Quản lý câu hỏi thường gặp (FAQ) trong trang admin.
 *
 * Các route tương ứng (định nghĩa trong config/routes.php):
 *   GET  /admin/faqs              -> index()   - Danh sách FAQ (có phân trang)
 *   GET  /admin/faqs/create       -> create()  - Form thêm mới FAQ
 *   POST /admin/faqs/store        -> store()   - Lưu FAQ mới
 *   GET  /admin/faqs/{id}/edit    -> edit()    - Form sửa FAQ
 *   POST /admin/faqs/{id}/update  -> update()  - Cập nhật FAQ
 *   POST /admin/faqs/{id}/delete  -> delete()  - Xóa FAQ
 *
 * Mỗi FAQ gồm: question (câu hỏi), answer (câu trả lời, hỗ trợ CKEditor HTML),
 * category (danh mục, tùy chọn), sort_order (thứ tự hiển thị), status (ẩn/hiện).
 */
class FaqController extends Controller
{
    /**
     * Constructor: yêu cầu đăng nhập admin cho tất cả các action trong controller này.
     * Nếu chưa đăng nhập hoặc role không phải 'admin', sẽ bị redirect về /login.
     */
    public function __construct()
    {
        $this->requireAdmin();
    }

    /**
     * Danh sách FAQ - GET /admin/faqs
     *
     * Flow:
     *   Input:  Query param ?page=N (mặc định 1)
     *   Xử lý:  Gọi Faq::paginate() lấy danh sách FAQ, sắp xếp theo sort_order tăng dần,
     *           10 mục/trang. Model paginate() tự động tính tổng số trang và offset.
     *   Output: Render view admin/faqs/index với layout admin.
     *           Biến $data chứa items, current_page, total_pages, total, has_next, has_prev.
     */
    public function index(): void
    {
        $model = new Faq();
        // Lấy số trang từ query string, ép kiểu int, mặc định là 1
        $page = (int)($this->input('page', 1));
        // paginate(page, perPage, conditions, orderBy) — kế thừa từ Model
        $data = $model->paginate($page, 10, [], 'sort_order ASC');
        $data['title'] = 'Manage FAQs';
        $data['baseUrl'] = '/admin/faqs';
        $this->view('admin/faqs/index', $data, 'admin');
    }

    /**
     * Form thêm FAQ mới - GET /admin/faqs/create
     *
     * Flow:
     *   Input:  Không có tham số.
     *   Xử lý:  Chuẩn bị tiêu đề trang.
     *   Output: Render view admin/faqs/form (dùng chung form cho cả create và edit).
     *           Form chứa các trường: question, answer (CKEditor), category, sort_order, status.
     */
    public function create(): void
    {
        $data = ['title' => 'Add FAQ'];
        $this->view('admin/faqs/form', $data, 'admin');
    }

    /**
     * Lưu FAQ mới vào database - POST /admin/faqs/store
     *
     * Flow:
     *   Input:  $_POST gồm question, answer, category, sort_order, status (qua form).
     *   Bước 1: Kiểm tra CSRF token — nếu không hợp lệ, dừng xử lý.
     *   Bước 2: Validate server-side với Validator:
     *           - question: bắt buộc, tối đa 500 ký tự.
     *           - answer: bắt buộc (CKEditor luôn gửi ít nhất thẻ HTML rỗng).
     *           Nếu lỗi -> lưu flash message + old_input, redirect về form create.
     *   Bước 3: Gọi Faq::create() với dữ liệu đã lọc:
     *           - category: nullable, mặc định null (có thể để trống danh mục).
     *           - sort_order: ép kiểu int, mặc định 0.
     *           - status: ép kiểu int, mặc định 1 (hiển thị).
     *   Bước 4: Flash message thành công, redirect về danh sách /admin/faqs.
     *   Output: Redirect (sau POST luôn redirect để tránh resubmit form).
     */
    public function store(): void
    {
        // Bảo vệ CSRF: token trong form phải khớp với token trong session
        if (!$this->validateCsrf()) return;

        $validator = new Validator();
        $rules = [
            'question' => 'required|max:500',    // Câu hỏi: bắt buộc, tối đa 500 ký tự
            'answer'   => 'required',             // Câu trả lời: bắt buộc
        ];
        if (!$validator->validate($_POST, $rules)) {
            // Gom tất cả lỗi validate thành chuỗi HTML, ngăn cách bằng <br>
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            Session::set('form_errors', $validator->getErrors());
            Session::set('old_input', $_POST);    // Giữ lại dữ liệu đã nhập để hiển thị lại form
            $this->redirect('/admin/faqs/create');
            return;
        }

        $model = new Faq();
        // create() sử dụng PDO prepared statement — an toàn SQL injection
        $model->create([
            'question'   => $_POST['question'],
            'answer'     => $_POST['answer'],
            'category'   => $_POST['category'] ?? null,      // Danh mục: không bắt buộc
            'sort_order' => (int)($_POST['sort_order'] ?? 0), // Thứ tự: mặc định 0
            'status'     => (int)($_POST['status'] ?? 1),     // Trạng thái: 1=hiện, 0=ẩn
        ]);

        Session::setFlash('success', 'FAQ added successfully.');
        $this->redirect('/admin/faqs');
    }

    /**
     * Form sửa FAQ - GET /admin/faqs/{id}/edit
     *
     * Flow:
     *   Input:  Query param ?id=N (ID của FAQ cần sửa).
     *   Bước 1: Ép id về int, gọi Faq::find($id) để lấy bản ghi.
     *   Bước 2: Nếu không tìm thấy -> flash error, redirect về danh sách.
     *   Output: Render view admin/faqs/form với biến $faq chứa dữ liệu hiện tại,
     *           để form hiển thị giá trị cũ cho người dùng chỉnh sửa.
     */
    public function edit(): void
    {
        $id = (int)($this->input('id', 0));
        $model = new Faq();
        $faq = $model->find($id);
        if (!$faq) {
            Session::setFlash('error', 'FAQ does not exist.');
            $this->redirect('/admin/faqs');
            return;
        }

        $data = [
            'title' => 'Edit FAQ',
            'faq'   => $faq,
        ];
        $this->view('admin/faqs/form', $data, 'admin');
    }

    /**
     * Cập nhật FAQ - POST /admin/faqs/{id}/update
     *
     * Flow:
     *   Input:  $_POST (question, answer, category, sort_order, status) + query param ?id=N.
     *   Bước 1: Kiểm tra CSRF token.
     *   Bước 2: Xác minh FAQ tồn tại qua Faq::find($id).
     *   Bước 3: Validate server-side (giống store).
     *   Bước 4: Gọi Faq::update($id, $data) — chỉ cập nhật bản ghi có id tương ứng.
     *   Bước 5: Flash success, redirect về danh sách.
     *   Output: Redirect.
     *
     * Lưu ý: Không dùng Faq::findBy() để kiểm tra trùng question vì FAQ có thể có
     * câu hỏi giống nhau thuộc các danh mục khác nhau (không ràng buộc UNIQUE).
     */
    public function update(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new Faq();
        $faq = $model->find($id);
        if (!$faq) {
            Session::setFlash('error', 'FAQ does not exist.');
            $this->redirect('/admin/faqs');
            return;
        }

        $validator = new Validator();
        $rules = [
            'question' => 'required|max:500',
            'answer'   => 'required',
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            $this->redirect("/admin/faqs/{$id}/edit");
            return;
        }

        $model->update($id, [
            'question'   => $_POST['question'],
            'answer'     => $_POST['answer'],
            'category'   => $_POST['category'] ?? null,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'status'     => (int)($_POST['status'] ?? 1),
        ]);

        Session::setFlash('success', 'FAQ updated successfully.');
        $this->redirect('/admin/faqs');
    }

    /**
     * Xóa FAQ - POST /admin/faqs/{id}/delete
     *
     * Flow:
     *   Input:  Query param ?id=N (ID của FAQ cần xóa).
     *   Bước 1: Kiểm tra CSRF token — chỉ chấp nhận POST để tránh xóa qua link GET.
     *   Bước 2: Xác minh FAQ tồn tại qua Faq::find($id).
     *   Bước 3: Gọi Faq::delete($id) — thực thi DELETE ... WHERE id = ? (prepared statement).
     *   Bước 4: Flash success, redirect về danh sách.
     *   Output: Redirect.
     *
     * Lưu ý bảo mật: Yêu cầu POST + CSRF token để ngăn CSRF attack qua link <img> hoặc form từ site khác.
     * View sẽ dùng form với method="POST" và hidden input csrf_token, có thể kèm JavaScript confirm().
     */
    public function delete(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new Faq();
        if (!$model->find($id)) {
            Session::setFlash('error', 'FAQ does not exist.');
            $this->redirect('/admin/faqs');
            return;
        }

        $model->delete($id);    // DELETE FROM faqs WHERE id = ? (PDO prepared)
        Session::setFlash('success', 'FAQ deleted successfully.');
        $this->redirect('/admin/faqs');
    }
}
