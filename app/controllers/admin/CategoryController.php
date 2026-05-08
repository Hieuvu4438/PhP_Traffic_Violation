<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Helper;
use App\Models\NewsCategory;
use App\Models\OffenseCategory;

/**
 * Controller quản lý danh mục (categories) trong admin.
 * Quản lý cùng lúc 2 loại danh mục khác nhau trong cùng 1 form:
 *   - NewsCategory: danh mục tin tức (bảng news_categories, có slug).
 *   - OffenseCategory: danh mục lỗi vi phạm (bảng offense_categories, có description).
 * Route gốc: /admin/categories
 * Mỗi action (store/update/delete) đều nhận tham số category_type ('news'|'offense')
 * để xác định đang thao tác trên loại danh mục nào.
 */
class CategoryController extends Controller
{
    /**
     * Constructor: gọi requireAdmin() để chặn truy cập từ non-admin.
     */
    public function __construct()
    {
        $this->requireAdmin();
    }

    // ==================== READ: Danh sách 2 loại ====================

    /**
     * GET /admin/categories
     * Hiển thị danh sách cả 2 loại danh mục trên cùng 1 trang.
     *
     * Luồng xử lý:
     * 1. Khởi tạo NewsCategory model -> lấy toàn bộ bản ghi sắp xếp A-Z.
     * 2. Khởi tạo OffenseCategory model -> lấy toàn bộ bản ghi sắp xếp A-Z.
     * 3. Truyền 2 mảng riêng biệt vào view: newsCategories và offenseCategories.
     *    View sẽ hiển thị 2 bảng hoặc 2 tab riêng cho từng loại.
     * Render view admin/categories/index với layout 'admin'.
     */
    public function index(): void
    {
        $newsCatModel = new NewsCategory();
        $offenseCatModel = new OffenseCategory();

        $data = [
            'title'            => 'Quản lý danh mục',
            'newsCategories'   => $newsCatModel->all([], 'name ASC'),
            'offenseCategories'=> $offenseCatModel->all([], 'name ASC'),
        ];
        $this->view('admin/categories/index', $data, 'admin');
    }

    // ==================== CREATE: Lưu danh mục mới ====================

    /**
     * POST /admin/categories
     * Lưu danh mục mới (news hoặc offense) vào database.
     *
     * Luồng xử lý:
     * 1. Kiểm tra CSRF token.
     * 2. Xác định loại danh mục từ $_POST['category_type']: chỉ chấp nhận 'news' hoặc 'offense'.
     *    Nếu sai -> flash lỗi, redirect.
     * 3. Validate tên danh mục: bắt buộc (required), tối thiểu 2 ký tự (minLength).
     *    Sử dụng Validator::required() và Validator::minLength() trực tiếp (không qua rules array).
     * 4. Phân nhánh theo loại:
     *    a) NewsCategory:
     *       - Tạo slug từ name bằng Helper::slug().
     *       - Kiểm tra trùng slug -> thêm hậu tố -time() nếu trùng.
     *       - INSERT với name và slug.
     *    b) OffenseCategory:
     *       - INSERT với name và description (có thể null).
     *       - OffenseCategory KHÔNG có slug.
     * 5. Flash thành công, redirect về danh sách /admin/categories.
     */
    public function store(): void
    {
        if (!$this->validateCsrf()) return;

        $type = $_POST['category_type'] ?? '';
        $name = $_POST['name'] ?? '';

        // Xác thực loại danh mục hợp lệ
        if (!in_array($type, ['news', 'offense'])) {
            Session::setFlash('error', 'Loại danh mục không hợp lệ.');
            $this->redirect('/admin/categories');
            return;
        }

        // Validate tên: không rỗng và tối thiểu 2 ký tự
        if (!Validator::required($name) || !Validator::minLength($name, 2)) {
            Session::setFlash('error', 'Tên danh mục không được để trống và tối thiểu 2 ký tự.');
            $this->redirect('/admin/categories');
            return;
        }

        // Phân nhánh xử lý theo loại danh mục
        if ($type === 'news') {
            $model = new NewsCategory();
            $slug = Helper::slug($name);
            if ($model->findBy('slug', $slug)) {
                $slug .= '-' . time();
            }
            $model->create(['name' => $name, 'slug' => $slug]);
        } else {
            $model = new OffenseCategory();
            $model->create(['name' => $name, 'description' => $_POST['description'] ?? null]);
        }

        Session::setFlash('success', 'Thêm danh mục thành công.');
        $this->redirect('/admin/categories');
    }

    // ==================== UPDATE: Cập nhật danh mục ====================

    /**
     * POST /admin/categories/{id}/update
     * Cập nhật tên (và slug/mô tả) của danh mục đã tồn tại.
     *
     * Luồng xử lý:
     * 1. Kiểm tra CSRF token.
     * 2. Lấy id từ POST (ép int) và category_type.
     * 3. Validate type ('news'|'offense') và tên (required, min 2).
     * 4. Phân nhánh theo loại:
     *    a) NewsCategory:
     *       - Tìm bản ghi theo id. Nếu không thấy -> flash lỗi, redirect.
     *       - Tạo slug mới từ name. Kiểm tra trùng slug với bản ghi KHÁC id.
     *       - UPDATE name và slug.
     *    b) OffenseCategory:
     *       - Tìm bản ghi theo id. Nếu không thấy -> flash lỗi, redirect.
     *       - UPDATE name và description.
     * 5. Flash thành công, redirect về danh sách.
     */
    public function update(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $type = $_POST['category_type'] ?? '';
        $name = $_POST['name'] ?? '';

        if (!in_array($type, ['news', 'offense'])) {
            Session::setFlash('error', 'Loại danh mục không hợp lệ.');
            $this->redirect('/admin/categories');
            return;
        }

        if (!Validator::required($name) || !Validator::minLength($name, 2)) {
            Session::setFlash('error', 'Tên danh mục không được để trống và tối thiểu 2 ký tự.');
            $this->redirect('/admin/categories');
            return;
        }

        if ($type === 'news') {
            $model = new NewsCategory();
            $cat = $model->find($id);
            if (!$cat) {
                Session::setFlash('error', 'Danh mục không tồn tại.');
                $this->redirect('/admin/categories');
                return;
            }
            // Tạo slug mới, tránh trùng với bản ghi khác
            $slug = Helper::slug($name);
            $existing = $model->findBy('slug', $slug);
            if ($existing && $existing['id'] != $id) {
                $slug .= '-' . time();
            }
            $model->update($id, ['name' => $name, 'slug' => $slug]);
        } else {
            $model = new OffenseCategory();
            $cat = $model->find($id);
            if (!$cat) {
                Session::setFlash('error', 'Danh mục không tồn tại.');
                $this->redirect('/admin/categories');
                return;
            }
            $model->update($id, ['name' => $name, 'description' => $_POST['description'] ?? null]);
        }

        Session::setFlash('success', 'Cập nhật danh mục thành công.');
        $this->redirect('/admin/categories');
    }

    // ==================== DELETE: Xóa danh mục ====================

    /**
     * POST /admin/categories/{id}/delete
     * Xóa danh mục khỏi database.
     *
     * Luồng xử lý:
     * 1. Kiểm tra CSRF token.
     * 2. Lấy id và category_type từ POST.
     * 3. Validate type chỉ chấp nhận 'news' hoặc 'offense'.
     * 4. Chọn model tương ứng với type.
     * 5. Tìm bản ghi theo id. Nếu không thấy -> flash lỗi, redirect.
     * 6. Gọi $model->delete($id) (DELETE FROM ... WHERE id = ?).
     * 7. Flash thành công, redirect về danh sách.
     *
     * Lưu ý: Không kiểm tra ràng buộc khóa ngoại (có tin tức/lỗi vi phạm nào
     * đang dùng danh mục này không). Nếu có FK constraint, DB sẽ throw exception.
     */
    public function delete(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $type = $_POST['category_type'] ?? '';

        if (!in_array($type, ['news', 'offense'])) {
            Session::setFlash('error', 'Loại danh mục không hợp lệ.');
            $this->redirect('/admin/categories');
            return;
        }

        // Chọn model dựa trên loại danh mục cần xóa
        if ($type === 'news') {
            $model = new NewsCategory();
        } else {
            $model = new OffenseCategory();
        }

        if (!$model->find($id)) {
            Session::setFlash('error', 'Danh mục không tồn tại.');
            $this->redirect('/admin/categories');
            return;
        }

        $model->delete($id);
        Session::setFlash('success', 'Xóa danh mục thành công.');
        $this->redirect('/admin/categories');
    }
}
