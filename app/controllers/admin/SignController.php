<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Helper;
use App\Models\TrafficSign;
use App\Models\TrafficSignGroup;

/**
 * Controller quản lý biển báo giao thông trong admin.
 * Cung cấp CRUD: danh sách phân trang, thêm, sửa, xóa.
 * Route gốc: /admin/signs
 * Mỗi biển báo thuộc về 1 nhóm (TrafficSignGroup):
 *   - FK: traffic_signs.group_id -> traffic_sign_groups.id
 *   - JOIN lấy group_name và sign_prefix khi hiển thị danh sách.
 * Có upload ảnh biển báo vào public/assets/uploads/signs/.
 *
 * Điểm đặc biệt: index() dùng cách phân trang thủ công (array_slice trên mảng PHP)
 * thay vì dùng LIMIT/OFFSET trong SQL, vì getWithGroup() đã JOIN lấy toàn bộ.
 */
class SignController extends Controller
{
    /**
     * Constructor: gọi requireAdmin() để chặn truy cập từ non-admin.
     */
    public function __construct()
    {
        $this->requireAdmin();
    }

    // ==================== READ: Danh sách (phân trang thủ công) ====================

    /**
     * GET /admin/signs?page=N
     * Hiển thị danh sách biển báo kèm tên nhóm, phân trang 10 items/trang.
     *
     * Luồng xử lý:
     * 1. Lấy page từ query string, mặc định 1, ép kiểu int.
     * 2. Gọi $model->paginate() để lấy dữ liệu phân trang chuẩn (dùng LIMIT/OFFSET SQL).
     *    Kết quả trả về mảng có items, total, page, per_page, total_pages, has_next, has_prev.
     * 3. Gọi $model->getWithGroup() để lấy TOÀN BỘ biển báo kèm thông tin nhóm:
     *    - SQL: SELECT s.*, g.name as group_name, g.sign_prefix
     *           FROM traffic_signs s
     *           LEFT JOIN traffic_sign_groups g ON s.group_id = g.id
     *           ORDER BY g.sort_order, s.sign_code
     *    - Mỗi dòng có thêm group_name và sign_prefix.
     * 4. Phân trang thủ công: đếm tổng số items, tính totalPages, clamp page,
     *    dùng array_slice để cắt mảng theo offset và perPage.
     * 5. Ghi đè biến $data bằng kết quả phân trang thủ công.
     * 6. Render view admin/signs/index với layout 'admin'.
     */
    public function index(): void
    {
        $model = new TrafficSign();
        $page = (int)($this->input('page', 1));
        $data = $model->paginate($page, 10, [], 'sign_code ASC');
        $items = $model->getWithGroup();
        $total = count($items);

        $perPage = 10;
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;
        // Cắt mảng PHP để lấy đúng trang (phân trang thủ công, không dùng SQL LIMIT)
        $pagedItems = array_slice($items, $offset, $perPage);

        $data = [
            'title'       => 'Manage Traffic Signs',
            'items'       => $pagedItems,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages,
            'has_next'    => $page < $totalPages,
            'has_prev'    => $page > 1,
            'baseUrl'     => '/admin/signs',
        ];
        $this->view('admin/signs/index', $data, 'admin');
    }

    // ==================== CREATE: Form thêm + Lưu ====================

    /**
     * GET /admin/signs/create
     * Hiển thị form thêm biển báo mới.
     *
     * Chuẩn bị dữ liệu:
     * - Lấy danh sách nhóm biển báo từ TrafficSignGroup::getAllSorted().
     *   Sắp xếp theo sort_order ASC để hiển thị dropdown đúng thứ tự.
     * Render view admin/signs/form (dùng chung cho thêm và sửa) với layout 'admin'.
     */
    public function create(): void
    {
        $groupModel = new TrafficSignGroup();
        $data = [
            'title'  => 'Add Traffic Sign',
            'groups' => $groupModel->getAllSorted(),
        ];
        $this->view('admin/signs/form', $data, 'admin');
    }

    /**
     * POST /admin/signs
     * Lưu biển báo mới vào database.
     *
     * Luồng xử lý:
     * 1. Kiểm tra CSRF token.
     * 2. Validate input:
     *    - sign_code: bắt buộc, tối đa 20 ký tự (mã biển báo, VD: "P.101").
     *    - name: bắt buộc, tối đa 255 ký tự (tên biển báo).
     *    - group_id: phải là số (numeric), có thể để trống (null).
     * 3. Nếu validation fail: lưu lỗi + old input vào session, redirect về form thêm.
     * 4. Xử lý upload ảnh biển báo:
     *    - Nếu có file -> gọi Helper::upload($_FILES['image'], 'signs').
     *    - Ảnh lưu vào public/assets/uploads/signs/.
     *    - Nếu không có file -> image = null.
     * 5. Gọi $model->create([...]) để INSERT:
     *    - sign_code, name, group_id, image, description.
     *    - group_id = null nếu không chọn nhóm (?: toán tử).
     * 6. Flash thành công, redirect về danh sách /admin/signs.
     */
    public function store(): void
    {
        if (!$this->validateCsrf()) return;

        $validator = new Validator();
        $rules = [
            'sign_code' => 'required|max:20',
            'name'      => 'required|max:255',
            'group_id'  => 'numeric',
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            Session::set('form_errors', $validator->getErrors());
            Session::set('old_input', $_POST);
            $this->redirect('/admin/signs/create');
            return;
        }

        // Upload ảnh biển báo (không bắt buộc)
        $image = null;
        if (!empty($_FILES['image']['tmp_name'])) {
            $image = Helper::upload($_FILES['image'], 'signs');
        }

        $model = new TrafficSign();
        $model->create([
            'sign_code'   => $_POST['sign_code'],
            'name'        => $_POST['name'],
            'group_id'    => $_POST['group_id'] ?: null,
            'image'       => $image,
            'description' => $_POST['description'] ?? null,
        ]);

        Session::setFlash('success', 'Traffic sign added successfully.');
        $this->redirect('/admin/signs');
    }

    // ==================== UPDATE: Form sửa + Cập nhật ====================

    /**
     * GET /admin/signs/{id}/edit
     * Hiển thị form sửa biển báo (điền sẵn dữ liệu cũ).
     *
     * Luồng xử lý:
     * 1. Lấy id từ query string, ép int.
     * 2. Tìm biển báo theo id. Nếu không thấy -> flash lỗi, redirect danh sách.
     * 3. Lấy danh sách nhóm để hiển thị dropdown chọn nhóm.
     * 4. Truyền $sign và $groups vào view admin/signs/form (dùng chung với create).
     */
    public function edit(): void
    {
        $id = (int)($this->input('id', 0));
        $model = new TrafficSign();
        $sign = $model->find($id);
        if (!$sign) {
            Session::setFlash('error', 'Traffic sign does not exist.');
            $this->redirect('/admin/signs');
            return;
        }

        $groupModel = new TrafficSignGroup();
        $data = [
            'title'  => 'Edit Traffic Sign',
            'sign'   => $sign,
            'groups' => $groupModel->getAllSorted(),
        ];
        $this->view('admin/signs/form', $data, 'admin');
    }

    /**
     * POST /admin/signs/{id}/update
     * Cập nhật biển báo đã tồn tại.
     *
     * Luồng xử lý:
     * 1. Kiểm tra CSRF token.
     * 2. Tìm biển báo theo id từ POST. Nếu không thấy -> flash lỗi, redirect.
     * 3. Validate input giống store(): sign_code required|max:20, name required|max:255, group_id numeric.
     * 4. Gom dữ liệu cần update vào $updateData: sign_code, name, group_id, description.
     * 5. Xử lý ảnh: chỉ cập nhật nếu có file mới upload.
     *    - Upload thành công -> thêm 'image' vào $updateData.
     *    - Không upload -> giữ nguyên ảnh cũ.
     * 6. Gọi $model->update($id, $updateData).
     * 7. Flash thành công, redirect về danh sách.
     */
    public function update(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new TrafficSign();
        $sign = $model->find($id);
        if (!$sign) {
            Session::setFlash('error', 'Traffic sign does not exist.');
            $this->redirect('/admin/signs');
            return;
        }

        $validator = new Validator();
        $rules = [
            'sign_code' => 'required|max:20',
            'name'      => 'required|max:255',
            'group_id'  => 'numeric',
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            $this->redirect("/admin/signs/{$id}/edit");
            return;
        }

        $updateData = [
            'sign_code'   => $_POST['sign_code'],
            'name'        => $_POST['name'],
            'group_id'    => $_POST['group_id'] ?: null,
            'description' => $_POST['description'] ?? null,
        ];

        // Chỉ cập nhật ảnh khi có file mới upload (giữ ảnh cũ nếu không upload)
        if (!empty($_FILES['image']['tmp_name'])) {
            $image = Helper::upload($_FILES['image'], 'signs');
            if ($image) {
                $updateData['image'] = $image;
            }
        }

        $model->update($id, $updateData);
        Session::setFlash('success', 'Traffic sign updated successfully.');
        $this->redirect('/admin/signs');
    }

    // ==================== DELETE: Xóa ====================

    /**
     * POST /admin/signs/{id}/delete
     * Xóa biển báo khỏi database.
     *
     * Luồng xử lý:
     * 1. Kiểm tra CSRF token.
     * 2. Lấy id từ POST, tìm bản ghi. Nếu không thấy -> flash lỗi, redirect.
     * 3. Gọi $model->delete($id) (DELETE FROM traffic_signs WHERE id = ?).
     * 4. Flash thành công, redirect về danh sách.
     *
     * Lưu ý: File ảnh trên disk KHÔNG bị xóa tự động. Cần dọn dẹp thủ công nếu muốn.
     */
    public function delete(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new TrafficSign();
        if (!$model->find($id)) {
            Session::setFlash('error', 'Traffic sign does not exist.');
            $this->redirect('/admin/signs');
            return;
        }

        $model->delete($id);
        Session::setFlash('success', 'Traffic sign deleted successfully.');
        $this->redirect('/admin/signs');
    }
}
