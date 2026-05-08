<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Location;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    public function index(): void
    {
        $model = new Location();
        $page = (int)($this->input('page', 1));
        $data = $model->paginate($page, 10, [], 'name ASC');
        $data['title'] = 'Quản lý địa điểm';
        $data['baseUrl'] = '/admin/locations';
        $this->view('admin/locations/index', $data, 'admin');
    }

    public function create(): void
    {
        $data = ['title' => 'Thêm địa điểm'];
        $this->view('admin/locations/form', $data, 'admin');
    }

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
        $model->create([
            'name'        => $_POST['name'],
            'type'        => $_POST['type'],
            'address'     => $_POST['address'] ?? null,
            'latitude'    => (float)($_POST['latitude'] ?? 0),
            'longitude'   => (float)($_POST['longitude'] ?? 0),
            'description' => $_POST['description'] ?? null,
            'status'      => (int)($_POST['status'] ?? 1),
        ]);

        Session::setFlash('success', 'Thêm địa điểm thành công.');
        $this->redirect('/admin/locations');
    }

    public function edit(): void
    {
        $id = (int)($this->input('id', 0));
        $model = new Location();
        $location = $model->find($id);
        if (!$location) {
            Session::setFlash('error', 'Địa điểm không tồn tại.');
            $this->redirect('/admin/locations');
            return;
        }

        $data = [
            'title'    => 'Sửa địa điểm',
            'location' => $location,
        ];
        $this->view('admin/locations/form', $data, 'admin');
    }

    public function update(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new Location();
        $location = $model->find($id);
        if (!$location) {
            Session::setFlash('error', 'Địa điểm không tồn tại.');
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

        $model->update($id, [
            'name'        => $_POST['name'],
            'type'        => $_POST['type'],
            'address'     => $_POST['address'] ?? null,
            'latitude'    => (float)($_POST['latitude'] ?? 0),
            'longitude'   => (float)($_POST['longitude'] ?? 0),
            'description' => $_POST['description'] ?? null,
            'status'      => (int)($_POST['status'] ?? 1),
        ]);

        Session::setFlash('success', 'Cập nhật địa điểm thành công.');
        $this->redirect('/admin/locations');
    }

    public function delete(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new Location();
        if (!$model->find($id)) {
            Session::setFlash('error', 'Địa điểm không tồn tại.');
            $this->redirect('/admin/locations');
            return;
        }

        $model->delete($id);
        Session::setFlash('success', 'Xóa địa điểm thành công.');
        $this->redirect('/admin/locations');
    }
}
