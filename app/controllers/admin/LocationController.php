<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Location;

/**
 * Controller quản lý địa điểm vi phạm giao thông trong admin.
 * Cung cấp CRUD: danh sách phân trang, thêm, sửa, xóa.
 * Route gốc: /admin/locations
 * Mỗi địa điểm có:
 *   - name: tên địa điểm (bắt buộc).
 *   - type: loại địa điểm (camera, csgt, toll, inspection) - enum trong DB.
 *   - address: địa chỉ đầy đủ (không bắt buộc).
 *   - latitude/longitude: tọa độ GPS (float, dùng cho Google Maps / bản đồ).
 *   - description: mô tả thêm (không bắt buộc).
 *   - status: 1=hoạt động, 0=không hoạt động (int).
 * Không có upload file, không có quan hệ JOIN phức tạp.
 */
class LocationController extends Controller
{
    /**
     * Constructor: gọi requireAdmin() để chặn truy cập từ non-admin.
     */
    public function __construct()
    {
        $this->requireAdmin();
    }

    // ==================== READ: Danh sách phân trang ====================

    /**
     * GET /admin/locations?page=N
     * Hiển thị danh sách địa điểm có phân trang (dùng Model::paginate()).
     *
     * Luồng xử lý:
     * 1. Lấy page từ query string, mặc định 1, ép int.
     * 2. Gọi $model->paginate($page, 10, [], 'name ASC'):
     *    - Đếm tổng số bản ghi (SELECT COUNT(*) FROM locations).
     *    - Tính totalPages, clamp page, tính offset.
     *    - Lấy dữ liệu với LIMIT 10 OFFSET N, sắp xếp theo name ASC.
     *    - Trả về mảng: items, total, page, per_page, total_pages, has_next, has_prev.
     * 3. Bổ sung title và baseUrl vào mảng kết quả.
     * 4. Render view admin/locations/index với layout 'admin'.
     */
    public function index(): void
    {
        $model = new Location();
        $page = (int)($this->input('page', 1));
        // paginate() trả về mảng gồm items + metadata phân trang
        $data = $model->paginate($page, 10, [], 'name ASC');
        $data['title'] = 'Manage Locations';
        $data['baseUrl'] = '/admin/locations';
        $this->view('admin/locations/index', $data, 'admin');
    }

    // ==================== CREATE: Form thêm + Lưu ====================

    /**
     * GET /admin/locations/create
     * Hiển thị form thêm địa điểm mới.
     *
     * Không cần chuẩn bị dữ liệu phụ (không có dropdown phụ thuộc).
     * Chỉ truyền title vào view admin/locations/form (dùng chung cho thêm và sửa).
     */
    public function create(): void
    {
        $data = ['title' => 'Add Location'];
        $this->view('admin/locations/form', $data, 'admin');
    }

    /**
     * POST /admin/locations
     * Lưu địa điểm mới vào database.
     *
     * Luồng xử lý:
     * 1. Kiểm tra CSRF token.
     * 2. Validate input:
     *    - name: bắt buộc, tối đa 255 ký tự.
     *    - type: bắt buộc (camera|csgt|toll|inspection).
     *    - address: không bắt buộc, tối đa 500 ký tự.
     *    - latitude: phải là số (numeric), không bắt buộc.
     *    - longitude: phải là số (numeric), không bắt buộc.
     * 3. Nếu validation fail: lưu lỗi + old input, redirect về form thêm.
     * 4. Chuẩn bị dữ liệu INSERT với ép kiểu rõ ràng:
     *    - latitude, longitude: ép về float.
     *    - status: ép về int (1 = hoạt động mặc định).
     *    - address, description: null nếu không có.
     * 5. Gọi $model->create([...]).
     * 6. Flash thành công, redirect về danh sách /admin/locations.
     */
    public function store(): void
    {
        if (!$this->validateCsrf()) return;

        $validator = new Validator();
        $rules = [
            'name'      => 'required|max:255',
            'type'      => 'required',
            'address'   => 'max:500',
            'latitude'  => 'numeric',
            'longitude' => 'numeric',
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            Session::set('form_errors', $validator->getErrors());
            Session::set('old_input', $_POST);
            $this->redirect('/admin/locations/create');
            return;
        }

        $model = new Location();
        // Ép kiểu tọa độ về float, status về int trước khi INSERT
        $model->create([
            'name'        => $_POST['name'],
            'type'        => $_POST['type'],
            'address'     => $_POST['address'] ?? null,
            'latitude'    => (float)($_POST['latitude'] ?? 0),
            'longitude'   => (float)($_POST['longitude'] ?? 0),
            'description' => $_POST['description'] ?? null,
            'status'      => (int)($_POST['status'] ?? 1),
        ]);

        Session::setFlash('success', 'Location added successfully.');
        $this->redirect('/admin/locations');
    }

    // ==================== UPDATE: Form sửa + Cập nhật ====================

    /**
     * GET /admin/locations/{id}/edit
     * Hiển thị form sửa địa điểm (điền sẵn dữ liệu cũ).
     *
     * Luồng xử lý:
     * 1. Lấy id từ query string, ép int.
     * 2. Tìm địa điểm theo id. Nếu không thấy -> flash lỗi, redirect danh sách.
     * 3. Truyền $location vào view admin/locations/form (dùng chung với create).
     *    Form sẽ tự động điền sẵn: name, type, address, lat/lng, description, status.
     */
    public function edit(): void
    {
        $id = (int)($this->input('id', 0));
        $model = new Location();
        $location = $model->find($id);
        if (!$location) {
            Session::setFlash('error', 'Location does not exist.');
            $this->redirect('/admin/locations');
            return;
        }

        $data = [
            'title'    => 'Edit Location',
            'location' => $location,
        ];
        $this->view('admin/locations/form', $data, 'admin');
    }

    /**
     * POST /admin/locations/{id}/update
     * Cập nhật địa điểm đã tồn tại.
     *
     * Luồng xử lý:
     * 1. Kiểm tra CSRF token.
     * 2. Tìm địa điểm theo id. Nếu không thấy -> flash lỗi, redirect.
     * 3. Validate input giống store().
     * 4. Gọi $model->update($id, [...]) với dữ liệu đã ép kiểu:
     *    - latitude/longitude -> float.
     *    - status -> int.
     * 5. Flash thành công, redirect về danh sách.
     */
    public function update(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new Location();
        $location = $model->find($id);
        if (!$location) {
            Session::setFlash('error', 'Location does not exist.');
            $this->redirect('/admin/locations');
            return;
        }

        $validator = new Validator();
        $rules = [
            'name'      => 'required|max:255',
            'type'      => 'required',
            'address'   => 'max:500',
            'latitude'  => 'numeric',
            'longitude' => 'numeric',
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            $this->redirect("/admin/locations/{$id}/edit");
            return;
        }

        // Ép kiểu tọa độ và status trước khi UPDATE
        $model->update($id, [
            'name'        => $_POST['name'],
            'type'        => $_POST['type'],
            'address'     => $_POST['address'] ?? null,
            'latitude'    => (float)($_POST['latitude'] ?? 0),
            'longitude'   => (float)($_POST['longitude'] ?? 0),
            'description' => $_POST['description'] ?? null,
            'status'      => (int)($_POST['status'] ?? 1),
        ]);

        Session::setFlash('success', 'Location updated successfully.');
        $this->redirect('/admin/locations');
    }

    // ==================== DELETE: Xóa ====================

    /**
     * POST /admin/locations/{id}/delete
     * Xóa địa điểm khỏi database.
     *
     * Luồng xử lý:
     * 1. Kiểm tra CSRF token.
     * 2. Lấy id từ POST, tìm bản ghi. Nếu không thấy -> flash lỗi, redirect.
     * 3. Gọi $model->delete($id) (DELETE FROM locations WHERE id = ?).
     * 4. Flash thành công, redirect về danh sách.
     *
     * Lưu ý: Nếu có violations tham chiếu đến location này, DB sẽ throw FK constraint.
     * Cần xử lý thêm (hoặc set ON DELETE SET NULL trong schema) nếu muốn xóa mềm.
     */
    public function delete(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new Location();
        if (!$model->find($id)) {
            Session::setFlash('error', 'Location does not exist.');
            $this->redirect('/admin/locations');
            return;
        }

        $model->delete($id);
        Session::setFlash('success', 'Location deleted successfully.');
        $this->redirect('/admin/locations');
    }
}
