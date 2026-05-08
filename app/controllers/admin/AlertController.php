<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\TrafficAlert;

class AlertController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    public function index(): void
    {
        $model = new TrafficAlert();
        $page = (int)($this->input('page', 1));
        $data = $model->paginate($page, 10, [], 'created_at DESC');
        $data['title'] = 'Quản lý cảnh báo giao thông';
        $data['baseUrl'] = '/admin/alerts';
        $this->view('admin/alerts/index', $data, 'admin');
    }

    public function create(): void
    {
        $data = ['title' => 'Thêm cảnh báo giao thông'];
        $this->view('admin/alerts/form', $data, 'admin');
    }

    public function store(): void
    {
        if (!$this->validateCsrf()) return;

        $validator = new Validator();
        $rules = [
            'title' => 'required|max:255',
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
            'content'    => $_POST['content'] ?? null,
            'alert_type' => $_POST['alert_type'] ?? 'other',
            'expires_at' => $_POST['expires_at'] ?: null,
            'created_by' => Session::get('user_id'),
            'status'     => (int)($_POST['status'] ?? 1),
        ]);

        Session::setFlash('success', 'Thêm cảnh báo thành công.');
        $this->redirect('/admin/alerts');
    }

    public function edit(): void
    {
        $id = (int)($this->input('id', 0));
        $model = new TrafficAlert();
        $alert = $model->find($id);
        if (!$alert) {
            Session::setFlash('error', 'Cảnh báo không tồn tại.');
            $this->redirect('/admin/alerts');
            return;
        }

        $data = [
            'title' => 'Sửa cảnh báo giao thông',
            'alert' => $alert,
        ];
        $this->view('admin/alerts/form', $data, 'admin');
    }

    public function update(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new TrafficAlert();
        $alert = $model->find($id);
        if (!$alert) {
            Session::setFlash('error', 'Cảnh báo không tồn tại.');
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

        $model->update($id, [
            'title'      => $_POST['title'],
            'content'    => $_POST['content'] ?? null,
            'alert_type' => $_POST['alert_type'] ?? 'other',
            'expires_at' => $_POST['expires_at'] ?: null,
            'status'     => (int)($_POST['status'] ?? 1),
        ]);

        Session::setFlash('success', 'Cập nhật cảnh báo thành công.');
        $this->redirect('/admin/alerts');
    }

    public function delete(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new TrafficAlert();
        if (!$model->find($id)) {
            Session::setFlash('error', 'Cảnh báo không tồn tại.');
            $this->redirect('/admin/alerts');
            return;
        }

        $model->delete($id);
        Session::setFlash('success', 'Xóa cảnh báo thành công.');
        $this->redirect('/admin/alerts');
    }
}
