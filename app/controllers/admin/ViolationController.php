<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Violation;
use App\Models\Offense;
use App\Models\Location;

class ViolationController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    public function index(): void
    {
        $model = new Violation();
        $page = (int)($this->input('page', 1));
        $search = trim($this->input('search', ''));
        $baseUrl = '/admin/violations';
        $conditions = [];

        if ($search) {
            $baseUrl .= '?search=' . urlencode($search);
        }

        $total = $model->count($conditions);
        if ($search) {
            $sql = "SELECT COUNT(*) FROM violations WHERE plate_number LIKE :search";
            $stmt = $model->query($sql, ['search' => "%{$search}%"]);
            $total = (int) $stmt->fetchColumn();
        }

        $totalPages = max(1, (int) ceil($total / 10));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * 10;

        if ($search) {
            $sql = "SELECT v.*, l.name as location_name, o.name as offense_name
                    FROM violations v
                    LEFT JOIN locations l ON v.location_id = l.id
                    LEFT JOIN offenses o ON v.offense_id = o.id
                    WHERE v.plate_number LIKE :search
                    ORDER BY v.violation_date DESC
                    LIMIT 10 OFFSET :offset";
            $stmt = $model->query($sql, ['search' => "%{$search}%", 'offset' => $offset]);
        } else {
            $items = $model->getAllWithDetails([], 'v.violation_date DESC', 10, $offset);
        }

        $data = [
            'title'      => 'Quản lý vi phạm',
            'items'      => $search ? $stmt->fetchAll() : $items,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => 10,
            'total_pages'=> $totalPages,
            'has_next'   => $page < $totalPages,
            'has_prev'   => $page > 1,
            'search'     => $search,
            'baseUrl'    => $baseUrl,
        ];
        $this->view('admin/violations/index', $data, 'admin');
    }

    public function create(): void
    {
        $offenseModel = new Offense();
        $locationModel = new Location();
        $data = [
            'title'     => 'Thêm vi phạm',
            'offenses'  => $offenseModel->getWithCategory(),
            'locations' => $locationModel->all([], 'name ASC'),
        ];
        $this->view('admin/violations/form', $data, 'admin');
    }

    public function store(): void
    {
        if (!$this->validateCsrf()) return;

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
            Session::set('form_errors', $validator->getErrors());
            Session::set('old_input', $_POST);
            $this->redirect('/admin/violations/create');
            return;
        }

        $model = new Violation();
        $model->create([
            'plate_number'   => $_POST['plate_number'],
            'vehicle_type'   => $_POST['vehicle_type'],
            'violation_date' => $_POST['violation_date'],
            'offense_id'     => $_POST['offense_id'] ?: null,
            'location_id'    => $_POST['location_id'] ?: null,
            'status'         => $_POST['status'],
            'fine_amount'    => $_POST['fine_amount'] ?? null,
            'decision_number'=> $_POST['decision_number'] ?? null,
            'decision_date'  => $_POST['decision_date'] ?: null,
            'notes'          => $_POST['notes'] ?? null,
        ]);

        Session::setFlash('success', 'Thêm vi phạm thành công.');
        $this->redirect('/admin/violations');
    }

    public function edit(): void
    {
        $id = (int)($this->input('id', 0));
        $model = new Violation();
        $violation = $model->find($id);
        if (!$violation) {
            Session::setFlash('error', 'Vi phạm không tồn tại.');
            $this->redirect('/admin/violations');
            return;
        }

        $offenseModel = new Offense();
        $locationModel = new Location();
        $data = [
            'title'     => 'Sửa vi phạm',
            'violation' => $violation,
            'offenses'  => $offenseModel->getWithCategory(),
            'locations' => $locationModel->all([], 'name ASC'),
        ];
        $this->view('admin/violations/form', $data, 'admin');
    }

    public function update(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new Violation();
        $violation = $model->find($id);
        if (!$violation) {
            Session::setFlash('error', 'Vi phạm không tồn tại.');
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
            'plate_number'   => $_POST['plate_number'],
            'vehicle_type'   => $_POST['vehicle_type'],
            'violation_date' => $_POST['violation_date'],
            'offense_id'     => $_POST['offense_id'] ?: null,
            'location_id'    => $_POST['location_id'] ?: null,
            'status'         => $_POST['status'],
            'fine_amount'    => $_POST['fine_amount'] ?? null,
            'decision_number'=> $_POST['decision_number'] ?? null,
            'decision_date'  => $_POST['decision_date'] ?: null,
            'notes'          => $_POST['notes'] ?? null,
        ]);

        Session::setFlash('success', 'Cập nhật vi phạm thành công.');
        $this->redirect('/admin/violations');
    }

    public function delete(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new Violation();
        if (!$model->find($id)) {
            Session::setFlash('error', 'Vi phạm không tồn tại.');
            $this->redirect('/admin/violations');
            return;
        }

        $model->delete($id);
        Session::setFlash('success', 'Xóa vi phạm thành công.');
        $this->redirect('/admin/violations');
    }

    /**
     * AJAX toggle trạng thái vi phạm: pending → processed → paid → pending
     */
    public function toggleStatus(): void
    {
        if (!$this->validateCsrf()) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Phiên làm việc hết hạn.'], 419);
                return;
            }
            return;
        }

        $id = (int)($this->input('id', 0));
        $model = new Violation();
        $violation = $model->find($id);
        if (!$violation) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Vi phạm không tồn tại.'], 404);
                return;
            }
            Session::setFlash('error', 'Vi phạm không tồn tại.');
            $this->redirect('/admin/violations');
            return;
        }

        $cycle = ['pending' => 'processed', 'processed' => 'paid', 'paid' => 'pending'];
        $newStatus = $cycle[$violation['status']] ?? 'pending';
        $model->update($id, ['status' => $newStatus]);

        $labels = ['pending' => 'Chưa xử lý', 'processed' => 'Đã xử lý', 'paid' => 'Đã nộp phạt'];

        if ($this->isAjax()) {
            $this->json([
                'success' => true,
                'new_status' => $newStatus,
                'label' => $labels[$newStatus],
                'message' => "Đã chuyển sang: {$labels[$newStatus]}",
            ]);
            return;
        }

        Session::setFlash('success', "Đã chuyển trạng thái vi phạm sang: {$labels[$newStatus]}");
        $this->redirect('/admin/violations');
    }

    public function import(): void
    {
        if (!$this->validateCsrf()) {
            $this->redirect('/admin/violations');
            return;
        }

        $file = $_FILES['csv_file'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            Session::setFlash('error', 'Vui lòng chọn file CSV hợp lệ.');
            $this->redirect('/admin/violations');
            return;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'csv') {
            Session::setFlash('error', 'Chỉ chấp nhận file CSV.');
            $this->redirect('/admin/violations');
            return;
        }

        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            Session::setFlash('error', 'Không thể đọc file.');
            $this->redirect('/admin/violations');
            return;
        }

        $header = fgetcsv($handle);
        $model = new Violation();
        $imported = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 5) continue;

            $model->create([
                'plate_number'   => trim($row[0]),
                'vehicle_type'   => trim($row[1]),
                'violation_date' => trim($row[2]),
                'offense_id'     => (int) $row[3] ?: null,
                'location_id'    => (int) $row[4] ?: null,
                'status'         => trim($row[5] ?? 'pending'),
                'fine_amount'    => trim($row[6] ?? ''),
                'notes'          => trim($row[7] ?? ''),
            ]);
            $imported++;
        }

        fclose($handle);
        Session::setFlash('success', "Đã nhập {$imported} vi phạm từ file CSV.");
        $this->redirect('/admin/violations');
    }
}
