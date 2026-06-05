<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Violation;
use App\Models\Offense;
use App\Models\Location;

/**
 * ViolationController - Quản lý vi phạm giao thông (violations) trong trang admin.
 *
 * Đây là controller phức tạp nhất về mặt truy vấn dữ liệu: có tìm kiếm theo biển số
 * với JOIN 2 bảng (locations, offenses), import CSV hàng loạt, và toggleStatus()
 * với chu kỳ 3 trạng thái có nhãn tiếng Việt.
 *
 * Các route tương ứng:
 *   GET  /admin/violations                   -> index()        - Danh sách vi phạm (phân trang + tìm kiếm)
 *   GET  /admin/violations/create            -> create()       - Form thêm vi phạm
 *   POST /admin/violations/store             -> store()        - Lưu vi phạm mới
 *   GET  /admin/violations/{id}/edit         -> edit()         - Form sửa vi phạm
 *   POST /admin/violations/{id}/update       -> update()       - Cập nhật vi phạm
 *   POST /admin/violations/{id}/delete       -> delete()       - Xóa vi phạm
 *   POST /admin/violations/{id}/toggle-status-> toggleStatus() - Chuyển trạng thái (AJAX + non-AJAX)
 *   POST /admin/violations/import            -> import()       - Import vi phạm từ file CSV
 *
 * Chu kỳ trạng thái (status cycle):
 *   pending (Chưa xử lý) -> processed (Đã xử lý) -> paid (Đã nộp phạt) -> pending ...
 *   Đây là chu kỳ một chiều vòng tròn, toggle qua từng bước.
 *
 * Cấu trúc JOIN trong index():
 *   violations v
 *     LEFT JOIN locations l ON v.location_id = l.id   -> l.name AS location_name
 *     LEFT JOIN offenses o  ON v.offense_id  = o.id   -> o.name AS offense_name
 *   LEFT JOIN được dùng vì location_id và offense_id có thể NULL.
 */
class ViolationController extends Controller
{
    /**
     * Constructor: yêu cầu quyền admin cho mọi action.
     */
    public function __construct()
    {
        $this->requireAdmin();
    }

    /**
     * Danh sách vi phạm (có tìm kiếm + phân trang) - GET /admin/violations
     *
     * Flow:
     *   Input:  Query params ?page=N&search=biensoxe (search là biển số, tùy chọn).
     *   Xử lý:
     *     - Nếu CÓ search:
     *         + Dùng raw SQL với JOIN 2 bảng để lấy location_name và offense_name.
     *         + LEFT JOIN locations l ON v.location_id = l.id:
     *           lấy tên địa điểm vi phạm (name) từ bảng locations.
     *         + LEFT JOIN offenses o ON v.offense_id = o.id:
     *           lấy tên lỗi vi phạm (name) từ bảng offenses.
     *         + WHERE v.plate_number LIKE :search — tìm kiếm gần đúng theo biển số.
     *         + ORDER BY v.violation_date DESC — mới nhất trước.
     *         + LIMIT/OFFSET phân trang thủ công (10 dòng/trang).
     *         + Đếm riêng bằng SELECT COUNT(*) với cùng điều kiện để tính tổng số trang.
     *     - Nếu KHÔNG có search:
     *         + Gọi Violation::getAllWithDetails() — phương thức custom trong model
     *           thực hiện JOIN tương tự và trả về tất cả vi phạm.
     *   Output: Render view admin/violations/index với phân trang và kết quả tìm kiếm.
     *
     * Lưu ý về phân trang khi có search:
     *   - Controller tự tính toán page, totalPages, offset thay vì dùng Model::paginate().
     *   - Lý do: cần custom SQL cho JOIN, paginate() chỉ hoạt động với SELECT * FROM table đơn giản.
     *   - Phân trang thủ công: total = SELECT COUNT(*), totalPages = ceil(total / 10),
     *     offset = (page - 1) * 10, LIMIT 10 OFFSET :offset.
     */
    public function index(): void
    {
        $model = new Violation();
        $page = (int)($this->input('page', 1));
        $search = trim($this->input('search', ''));     // Biển số xe cần tìm
        $baseUrl = '/admin/violations';
        $conditions = [];

        // Giữ lại query string search trong baseUrl để phân trang vẫn giữ kết quả tìm kiếm
        if ($search) {
            $baseUrl .= '?search=' . urlencode($search);
        }

        // Đếm tổng số: nếu không search -> count() thường, nếu có search -> raw SQL COUNT
        $total = $model->count($conditions);
        if ($search) {
            // COUNT với LIKE để tính tổng số bản ghi khớp tìm kiếm
            $sql = "SELECT COUNT(*) FROM violations WHERE plate_number LIKE :search";
            $stmt = $model->query($sql, ['search' => "%{$search}%"]);
            $total = (int) $stmt->fetchColumn();
        }

        // Tính toán phân trang thủ công
        $totalPages = max(1, (int) ceil($total / 10));
        $page = max(1, min($page, $totalPages));    // Giới hạn page trong khoảng [1, totalPages]
        $offset = ($page - 1) * 10;

        if ($search) {
            // Raw SQL với JOIN khi có tìm kiếm:
            //   - v.* : tất cả cột từ bảng violations
            //   - l.name AS location_name: tên địa điểm vi phạm
            //   - o.name AS offense_name: tên lỗi vi phạm
            // LEFT JOIN đảm bảo vẫn lấy violation ngay cả khi location_id/offense_id là NULL
            $sql = "SELECT v.*, l.name as location_name, o.name as offense_name
                    FROM violations v
                    LEFT JOIN locations l ON v.location_id = l.id
                    LEFT JOIN offenses o ON v.offense_id = o.id
                    WHERE v.plate_number LIKE :search
                    ORDER BY v.violation_date DESC
                    LIMIT 10 OFFSET :offset";
            $stmt = $model->query($sql, ['search' => "%{$search}%", 'offset' => $offset]);
        } else {
            // Không search: dùng phương thức custom của model
            $items = $model->getAllWithDetails([], 'v.violation_date DESC', 10, $offset);
        }

        $data = [
            'title'       => 'Manage Violations',
            'items'       => $search ? $stmt->fetchAll() : $items,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => 10,
            'total_pages' => $totalPages,
            'has_next'    => $page < $totalPages,
            'has_prev'    => $page > 1,
            'search'      => $search,
            'baseUrl'     => $baseUrl,
        ];
        $this->view('admin/violations/index', $data, 'admin');
    }

    /**
     * Form thêm vi phạm mới - GET /admin/violations/create
     *
     * Flow:
     *   Input:  Không có tham số.
     *   Xử lý:  Load danh sách offenses (lỗi vi phạm) và locations (địa điểm) để
     *           hiển thị trong <select> dropdown của form.
     *           - Offense::getWithCategory(): lấy danh sách lỗi kèm tên danh mục,
     *             phục vụ <optgroup> trong select.
     *           - Location::all(): lấy tất cả địa điểm, sắp xếp theo tên.
     *   Output: Render view admin/violations/form với offenses và locations.
     */
    public function create(): void
    {
        $offenseModel = new Offense();
        $locationModel = new Location();
        $data = [
            'title'     => 'Add Violation',
            'offenses'  => $offenseModel->getWithCategory(),    // JOIN offenses + offense_categories
            'locations' => $locationModel->all([], 'name ASC'),  // Tất cả địa điểm theo thứ tự ABC
        ];
        $this->view('admin/violations/form', $data, 'admin');
    }

    /**
     * Lưu vi phạm mới - POST /admin/violations/store
     *
     * Flow:
     *   Input:  $_POST (plate_number, vehicle_type, violation_date, offense_id,
     *           location_id, status, fine_amount, decision_number, decision_date, notes).
     *   Bước 1: CSRF token.
     *   Bước 2: Validate server-side:
     *           - plate_number: bắt buộc, định dạng biển số VN (plateNumber rule), tối đa 20 ký tự.
     *           - vehicle_type: bắt buộc (enum: car/motorcycle/electric_motorcycle).
     *           - violation_date: bắt buộc (ngày giờ vi phạm).
     *           - offense_id: phải là số nếu được truyền (numeric).
     *           - location_id: phải là số nếu được truyền (numeric).
     *           - status: bắt buộc (enum: pending/processed/paid).
     *   Bước 3: Violation::create() — các trường tùy chọn (offense_id, location_id,
     *           fine_amount, decision_number, decision_date, notes) nhận giá trị NULL
     *           nếu không được truyền hoặc rỗng.
     *   Output: Redirect về danh sách.
     */
    public function store(): void
    {
        if (!$this->validateCsrf()) return;

        $validator = new Validator();
        $rules = [
            'plate_number'   => 'required|plateNumber|max:20',   // Biển số: bắt buộc, regex VN
            'vehicle_type'   => 'required',                       // Loại xe: bắt buộc
            'violation_date' => 'required',                       // Ngày VP: bắt buộc
            'offense_id'     => 'numeric',                        // Mã lỗi: phải là số nếu có
            'location_id'    => 'numeric',                        // Mã địa điểm: phải là số nếu có
            'status'         => 'required',                       // Trạng thái: bắt buộc
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            Session::set('form_errors', $validator->getErrors());
            Session::set('old_input', $_POST);
            $this->redirect('/admin/violations/create');
            return;
        }

        $model = new Violation();
        $model->create([
            'plate_number'    => $_POST['plate_number'],
            'vehicle_type'    => $_POST['vehicle_type'],
            'violation_date'  => $_POST['violation_date'],
            'offense_id'      => $_POST['offense_id'] ?: null,        // NULL nếu không chọn lỗi
            'location_id'     => $_POST['location_id'] ?: null,       // NULL nếu không chọn địa điểm
            'status'          => $_POST['status'],
            'fine_amount'     => $_POST['fine_amount'] ?? null,       // Số tiền phạt (VNĐ)
            'decision_number' => $_POST['decision_number'] ?? null,   // Số quyết định xử phạt
            'decision_date'   => $_POST['decision_date'] ?: null,     // Ngày ra quyết định
            'notes'           => $_POST['notes'] ?? null,             // Ghi chú thêm
        ]);

        Session::setFlash('success', 'Violation added successfully.');
        $this->redirect('/admin/violations');
    }

    /**
     * Form sửa vi phạm - GET /admin/violations/{id}/edit
     *
     * Flow:
     *   Input:  Query param ?id=N.
     *   Xử lý:  Violation::find($id) lấy bản ghi vi phạm.
     *           Load offenses và locations cho select dropdowns.
     *   Output: Render view admin/violations/form với biến $violation để pre-fill form.
     */
    public function edit(): void
    {
        $id = (int)($this->input('id', 0));
        $model = new Violation();
        $violation = $model->find($id);
        if (!$violation) {
            Session::setFlash('error', 'Violation does not exist.');
            $this->redirect('/admin/violations');
            return;
        }

        $offenseModel = new Offense();
        $locationModel = new Location();
        $data = [
            'title'     => 'Edit Violation',
            'violation' => $violation,
            'offenses'  => $offenseModel->getWithCategory(),
            'locations' => $locationModel->all([], 'name ASC'),
        ];
        $this->view('admin/violations/form', $data, 'admin');
    }

    /**
     * Cập nhật vi phạm - POST /admin/violations/{id}/update
     *
     * Flow:
     *   Input:  $_POST + query param ?id=N.
     *   Xử lý:  CSRF -> find() -> validate -> update().
     *           Quy tắc validate và dữ liệu giống hệt store().
     *   Output: Redirect.
     */
    public function update(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new Violation();
        $violation = $model->find($id);
        if (!$violation) {
            Session::setFlash('error', 'Violation does not exist.');
            $this->redirect('/admin/violations');
            return;
        }

        $validator = new Validator();
        $rules = [
            'plate_number'   => 'required|plateNumber|max:20',
            'vehicle_type'   => 'required',
            'violation_date' => 'required',
            'offense_id'     => 'numeric',
            'location_id'    => 'numeric',
            'status'         => 'required',
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            $this->redirect("/admin/violations/{$id}/edit");
            return;
        }

        $model->update($id, [
            'plate_number'    => $_POST['plate_number'],
            'vehicle_type'    => $_POST['vehicle_type'],
            'violation_date'  => $_POST['violation_date'],
            'offense_id'      => $_POST['offense_id'] ?: null,
            'location_id'     => $_POST['location_id'] ?: null,
            'status'          => $_POST['status'],
            'fine_amount'     => $_POST['fine_amount'] ?? null,
            'decision_number' => $_POST['decision_number'] ?? null,
            'decision_date'   => $_POST['decision_date'] ?: null,
            'notes'           => $_POST['notes'] ?? null,
        ]);

        Session::setFlash('success', 'Violation updated successfully.');
        $this->redirect('/admin/violations');
    }

    /**
     * Xóa vi phạm - POST /admin/violations/{id}/delete
     *
     * Flow:
     *   Input:  Query param ?id=N.
     *   Xử lý:  CSRF -> find() -> delete().
     *   Output: Redirect.
     */
    public function delete(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new Violation();
        if (!$model->find($id)) {
            Session::setFlash('error', 'Violation does not exist.');
            $this->redirect('/admin/violations');
            return;
        }

        $model->delete($id);
        Session::setFlash('success', 'Violation deleted successfully.');
        $this->redirect('/admin/violations');
    }

    /**
     * Chuyển trạng thái vi phạm theo chu kỳ - POST /admin/violations/{id}/toggle-status
     *
     * Hỗ trợ cả AJAX và non-AJAX, tương tự UserController::toggleStatus().
     *
     * Chu kỳ trạng thái (vòng tròn 3 bước):
     *   pending -> processed -> paid -> pending -> ...
     *
     * Flow:
     *   Input:  Query param ?id=N.
     *   Bước 1: CSRF — JSON 419 nếu AJAX, return nếu không.
     *   Bước 2: Tìm violation, nếu không tồn tại: JSON 404 hoặc flash error + redirect.
     *   Bước 3: Xác định trạng thái tiếp theo dựa trên trạng thái hiện tại:
     *           $cycle = ['pending' => 'processed', 'processed' => 'paid', 'paid' => 'pending']
     *           Nếu trạng thái hiện tại không có trong cycle (dữ liệu cũ), mặc định 'pending'.
     *   Bước 4: Violation::update($id, ['status' => $newStatus]).
     *   Bước 5: Phản hồi:
     *           - AJAX: JSON kèm label tiếng Việt ("Chưa xử lý" / "Đã xử lý" / "Đã nộp phạt").
     *           - Non-AJAX: flash success, redirect.
     *   Output: JSON hoặc Redirect.
     *
     * Tại sao dùng chu kỳ 3 bước:
     *   - Mô phỏng quy trình thực tế: vi phạm được ghi nhận -> CSGT xử lý -> người dân nộp phạt.
     *   - Mỗi lần toggle chuyển sang bước tiếp theo, hết chu kỳ quay lại từ đầu
     *     (hữu ích cho việc reset trạng thái test).
     */
    public function toggleStatus(): void
    {
        if (!$this->validateCsrf()) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Session expired.'], 419);
                return;
            }
            return;
        }

        $id = (int)($this->input('id', 0));
        $model = new Violation();
        $violation = $model->find($id);
        if (!$violation) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Violation does not exist.'], 404);
                return;
            }
            Session::setFlash('error', 'Violation does not exist.');
            $this->redirect('/admin/violations');
            return;
        }

        // Chu kỳ trạng thái: mỗi lần toggle chuyển sang trạng thái kế tiếp
        $cycle = ['pending' => 'processed', 'processed' => 'paid', 'paid' => 'pending'];
        $newStatus = $cycle[$violation['status']] ?? 'pending';    // Fallback về pending nếu dữ liệu lạ
        $model->update($id, ['status' => $newStatus]);

        // Nhãn tiếng Việt cho từng trạng thái
        $labels = ['pending' => 'Pending', 'processed' => 'Processed', 'paid' => 'Paid'];

        if ($this->isAjax()) {
            $this->json([
                'success'    => true,
                'new_status' => $newStatus,
                'label'      => $labels[$newStatus],    // Gửi label để JS cập nhật badge UI
                'message'    => "Changed to: {$labels[$newStatus]}",
            ]);
            return;
        }

        Session::setFlash('success', "Violation status changed to: {$labels[$newStatus]}");
        $this->redirect('/admin/violations');
    }

    /**
     * Import vi phạm từ file CSV - POST /admin/violations/import
     *
     * Flow:
     *   Input:  $_FILES['csv_file'] — file CSV upload từ form.
     *   Bước 1: CSRF token.
     *   Bước 2: Kiểm tra file upload:
     *           - Có file không? ($_FILES['csv_file'] tồn tại).
     *           - Upload có lỗi không? (UPLOAD_ERR_OK).
     *           - Đúng định dạng CSV không? (phần mở rộng .csv).
     *   Bước 3: Mở file CSV bằng fopen().
     *   Bước 4: Đọc dòng header đầu tiên (bỏ qua).
     *   Bước 5: Lặp qua từng dòng, gọi Violation::create() cho mỗi dòng:
     *           - Cấu trúc CSV mong đợi (8 cột):
     *             plate_number, vehicle_type, violation_date, offense_id,
     *             location_id, status, fine_amount, notes
     *           - Bỏ qua dòng có ít hơn 5 cột (dữ liệu không đầy đủ).
     *           - Các giá trị được trim() để loại bỏ khoảng trắng thừa.
     *   Bước 6: Đóng file, flash success kèm số lượng đã import.
     *   Output: Redirect.
     *
     * Hạn chế hiện tại:
     *   - Không validate từng dòng CSV (có thể import dữ liệu không hợp lệ).
     *   - Không có transaction rollback nếu một dòng lỗi (nên bọc trong try-catch + beginTransaction).
     *   - Không báo cáo dòng nào bị lỗi, chỉ đếm số dòng import thành công.
     *   - Giới hạn bộ nhớ: file CSV lớn có thể gây timeout (nên xử lý theo batch).
     */
    public function import(): void
    {
        if (!$this->validateCsrf()) {
            $this->redirect('/admin/violations');
            return;
        }

        // Kiểm tra file upload tồn tại và không có lỗi
        $file = $_FILES['csv_file'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            Session::setFlash('error', 'Please select a valid CSV file.');
            $this->redirect('/admin/violations');
            return;
        }

        // Xác thực phần mở rộng file (.csv)
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'csv') {
            Session::setFlash('error', 'Only CSV files are accepted.');
            $this->redirect('/admin/violations');
            return;
        }

        // Mở file CSV từ thư mục tạm của PHP
        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            Session::setFlash('error', 'Cannot read the file.');
            $this->redirect('/admin/violations');
            return;
        }

        // Đọc và bỏ qua dòng header (tiêu đề cột)
        $header = fgetcsv($handle);
        $model = new Violation();
        $imported = 0;

        // Lặp qua từng dòng dữ liệu
        while (($row = fgetcsv($handle)) !== false) {
            // Yêu cầu tối thiểu 5 cột: plate_number, vehicle_type, violation_date, offense_id, location_id
            if (count($row) < 5) continue;

            // Cấu trúc cột CSV (index):
            //   0: plate_number    1: vehicle_type    2: violation_date
            //   3: offense_id      4: location_id     5: status
            //   6: fine_amount     7: notes
            $model->create([
                'plate_number'   => trim($row[0]),
                'vehicle_type'   => trim($row[1]),
                'violation_date' => trim($row[2]),
                'offense_id'     => (int) $row[3] ?: null,
                'location_id'    => (int) $row[4] ?: null,
                'status'         => trim($row[5] ?? 'pending'),    // Mặc định pending nếu không có
                'fine_amount'    => trim($row[6] ?? ''),
                'notes'          => trim($row[7] ?? ''),
            ]);
            $imported++;
        }

        fclose($handle);
        Session::setFlash('success', "Imported {$imported} violations from CSV file.");
        $this->redirect('/admin/violations');
    }
}
