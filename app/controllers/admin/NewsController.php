<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Helper;
use App\Models\News;
use App\Models\NewsCategory;

/**
 * Controller quản lý tin tức trong admin.
 * Cung cấp đầy đủ CRUD: xem danh sách, thêm, sửa, xóa.
 * Route gốc: /admin/news
 * Mỗi tin tức có: title, slug (tự sinh từ title), content (CKEditor), thumbnail (upload),
 *                 category_id (FK -> news_categories), author_id (FK -> users), status (draft/published).
 */
class NewsController extends Controller
{
    /**
     * Constructor: gọi requireAdmin() để chặn mọi request không có role='admin'.
     * Đảm bảo chỉ admin mới truy cập được các action trong controller này.
     */
    public function __construct()
    {
        $this->requireAdmin();
    }

    // ==================== READ: Danh sách ====================

    /**
     * GET /admin/news?page=N
     * Hiển thị danh sách tin tức có phân trang (10 items/trang).
     *
     * Luồng xử lý:
     * 1. Lấy tham số page từ query string, mặc định 1, ép kiểu int.
     * 2. Đếm tổng số bản ghi, tính tổng số trang = ceil(total / 10).
     * 3. Clamp page trong khoảng [1, totalPages] để tránh page âm/vượt quá.
     * 4. Tính offset = (page - 1) * 10 cho LIMIT/OFFSET.
     * 5. Gọi getAllWithCategory() để lấy danh sách tin kèm tên danh mục:
     *    - JOIN: news n LEFT JOIN news_categories nc ON n.category_id = nc.id
     *    - Sắp xếp giảm dần theo n.created_at, giới hạn 10 dòng từ vị trí offset.
     *    - Mỗi dòng có thêm trường category_name.
     * 6. Truyền biến phân trang (page, total_pages, has_next, has_prev, per_page, baseUrl)
     *    vào view admin/news/index với layout 'admin'.
     */
    public function index(): void
    {
        $model = new News();
        $page = (int)($this->input('page', 1));

        $total = $model->count();
        $totalPages = max(1, (int) ceil($total / 10));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * 10;
        $items = $model->getAllWithCategory([], 'n.created_at DESC', 10, $offset);

        $data = [
            'title'       => 'Manage News',
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => 10,
            'total_pages' => $totalPages,
            'has_next'    => $page < $totalPages,
            'has_prev'    => $page > 1,
            'baseUrl'     => '/admin/news',
        ];
        $this->view('admin/news/index', $data, 'admin');
    }

    // ==================== CREATE: Form thêm + Lưu ====================

    /**
     * GET /admin/news/create
     * Hiển thị form thêm tin tức mới.
     *
     * Chuẩn bị dữ liệu: lấy toàn bộ danh mục tin tức (news_categories) sắp xếp A-Z
     * để hiển thị trong dropdown chọn danh mục.
     * Render view admin/news/form (dùng chung form cho cả thêm và sửa) với layout 'admin'.
     * Trường title và news rỗng -> form ở chế độ "thêm mới".
     */
    public function create(): void
    {
        $categoryModel = new NewsCategory();
        $data = [
            'title'      => 'Add News',
            'categories' => $categoryModel->all([], 'name ASC'),
        ];
        $this->view('admin/news/form', $data, 'admin');
    }

    /**
     * POST /admin/news
     * Lưu tin tức mới vào database.
     *
     * Luồng xử lý:
     * 1. Kiểm tra CSRF token: nếu không hợp lệ -> return ngay (không xử lý tiếp).
     * 2. Validate input bằng Validator:
     *    - title: bắt buộc, tối thiểu 3 ký tự, tối đa 255 ký tự.
     *    - content: bắt buộc (nội dung bài viết từ CKEditor).
     *    - category_id: phải là số (numeric), có thể để trống (null).
     * 3. Nếu validation fail: lưu lỗi + old input vào session, redirect về form thêm.
     * 4. Tạo slug từ title bằng Helper::slug() (bỏ dấu, thay khoảng trắng = dấu gạch ngang).
     * 5. Kiểm tra trùng slug: nếu đã tồn tại -> thêm hậu tố -timestamp để đảm bảo unique.
     * 6. Xử lý upload thumbnail:
     *    - Nếu $_FILES['thumbnail']['tmp_name'] không rỗng -> gọi Helper::upload().
     *    - Ảnh được lưu vào public/assets/uploads/news/.
     *    - Nếu không upload -> thumbnail = null.
     * 7. Gọi $model->create([...]) để INSERT, tự động đặt author_id = user_id hiện tại trong session,
     *    status mặc định 'draft' nếu không được truyền.
     * 8. Flash message thành công, redirect về danh sách /admin/news.
     *
     * Bảo mật: CSRF token, Validator server-side, upload có whitelist extension (trong Helper::upload).
     * Model::create sử dụng prepared statement -> chống SQL injection.
     */
    public function store(): void
    {
        if (!$this->validateCsrf()) return;

        // Validate dữ liệu đầu vào
        $validator = new Validator();
        $rules = [
            'title'       => 'required|min:3|max:255',
            'content'     => 'required',
            'category_id' => 'numeric',
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            Session::set('form_errors', $validator->getErrors());
            Session::set('old_input', $_POST);
            $this->redirect('/admin/news/create');
            return;
        }

        // Tạo slug duy nhất từ title
        $slug = Helper::slug($_POST['title']);
        $model = new News();
        if ($model->findBy('slug', $slug)) {
            $slug .= '-' . time();
        }

        // Upload thumbnail nếu có file được gửi lên
        $thumbnail = null;
        if (!empty($_FILES['thumbnail']['tmp_name'])) {
            $thumbnail = Helper::upload($_FILES['thumbnail'], 'news');
        }

        // INSERT tin tức với PDO prepared statement
        $model->create([
            'title'       => $_POST['title'],
            'slug'        => $slug,
            'content'     => $_POST['content'],
            'thumbnail'   => $thumbnail,
            'category_id' => $_POST['category_id'] ?: null,
            'author_id'   => Session::get('user_id'),
            'status'      => $_POST['status'] ?? 'draft',
        ]);

        Session::setFlash('success', 'News added successfully.');
        $this->redirect('/admin/news');
    }

    // ==================== UPDATE: Form sửa + Cập nhật ====================

    /**
     * GET /admin/news/{id}/edit
     * Hiển thị form sửa tin tức (điền sẵn dữ liệu cũ).
     *
     * Luồng xử lý:
     * 1. Lấy id từ query string (?id=), ép kiểu int, mặc định 0.
     * 2. Gọi $model->find($id) (SELECT * FROM news WHERE id = ? LIMIT 1).
     * 3. Nếu không tìm thấy -> flash lỗi, redirect về danh sách.
     * 4. Lấy danh sách danh mục để hiển thị trong dropdown.
     * 5. Truyền $news vào view -> form tự động điền sẵn các trường title, content, category_id, ...
     *    Render view admin/news/form (dùng chung với create) với layout 'admin'.
     */
    public function edit(): void
    {
        $id = (int)($this->input('id', 0));
        $model = new News();
        $news = $model->find($id);
        if (!$news) {
            Session::setFlash('error', 'News does not exist.');
            $this->redirect('/admin/news');
            return;
        }

        $categoryModel = new NewsCategory();
        $data = [
            'title'      => 'Edit News',
            'news'       => $news,
            'categories' => $categoryModel->all([], 'name ASC'),
        ];
        $this->view('admin/news/form', $data, 'admin');
    }

    /**
     * POST /admin/news/{id}/update
     * Cập nhật tin tức đã tồn tại.
     *
     * Luồng xử lý:
     * 1. Kiểm tra CSRF token.
     * 2. Tìm tin tức theo id từ POST. Nếu không tồn tại -> flash lỗi, redirect danh sách.
     * 3. Validate input giống store(): title required|min:3|max:255, content required, category_id numeric.
     * 4. Tạo slug mới từ title. Kiểm tra trùng slug với bản ghi KHÁC (existing['id'] != $id).
     *    Nếu slug đã bị bản ghi khác dùng -> thêm hậu tố -timestamp.
     * 5. Gom dữ liệu cần update vào $updateData: title, slug, content, category_id, status.
     * 6. Xử lý thumbnail:
     *    - Chỉ upload/ghi đè nếu $_FILES['thumbnail']['tmp_name'] không rỗng.
     *    - Upload thành công -> thêm 'thumbnail' vào $updateData.
     *    - Nếu không upload ảnh mới -> giữ nguyên ảnh cũ (không đụng vào trường thumbnail).
     * 7. Gọi $model->update($id, $updateData) (UPDATE ... WHERE id = ?).
     * 8. Flash thành công, redirect về danh sách.
     */
    public function update(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new News();
        $news = $model->find($id);
        if (!$news) {
            Session::setFlash('error', 'News does not exist.');
            $this->redirect('/admin/news');
            return;
        }

        $validator = new Validator();
        $rules = [
            'title'       => 'required|min:3|max:255',
            'content'     => 'required',
            'category_id' => 'numeric',
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            $this->redirect("/admin/news/{$id}/edit");
            return;
        }

        // Tạo slug duy nhất, bỏ qua slug hiện tại của chính bản ghi đang sửa
        $slug = Helper::slug($_POST['title']);
        $existing = $model->findBy('slug', $slug);
        if ($existing && $existing['id'] != $id) {
            $slug .= '-' . time();
        }

        // Gom dữ liệu cập nhật (không bao gồm thumbnail nếu không upload mới)
        $updateData = [
            'title'       => $_POST['title'],
            'slug'        => $slug,
            'content'     => $_POST['content'],
            'category_id' => $_POST['category_id'] ?: null,
            'status'      => $_POST['status'] ?? 'draft',
        ];

        // Chỉ cập nhật thumbnail khi có file mới được upload
        if (!empty($_FILES['thumbnail']['tmp_name'])) {
            $thumbnail = Helper::upload($_FILES['thumbnail'], 'news');
            if ($thumbnail) {
                $updateData['thumbnail'] = $thumbnail;
            }
        }

        $model->update($id, $updateData);
        Session::setFlash('success', 'News updated successfully.');
        $this->redirect('/admin/news');
    }

    // ==================== DELETE: Xóa ====================

    /**
     * POST /admin/news/{id}/delete
     * Xóa tin tức khỏi database.
     *
     * Luồng xử lý:
     * 1. Kiểm tra CSRF token -> ngăn CSRF attack.
     * 2. Lấy id từ POST, tìm bản ghi. Nếu không tồn tại -> flash lỗi, redirect.
     * 3. Gọi $model->delete($id) (DELETE FROM news WHERE id = ?).
     * 4. Flash thành công, redirect về danh sách.
     */
    public function delete(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new News();
        if (!$model->find($id)) {
            Session::setFlash('error', 'News does not exist.');
            $this->redirect('/admin/news');
            return;
        }

        $model->delete($id);
        Session::setFlash('success', 'News deleted successfully.');
        $this->redirect('/admin/news');
    }
}
