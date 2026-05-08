<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Helper;
use App\Models\TrafficSign;
use App\Models\TrafficSignGroup;

class SignController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

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
        $pagedItems = array_slice($items, $offset, $perPage);

        $data = [
            'title'       => 'Quản lý biển báo',
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

    public function create(): void
    {
        $groupModel = new TrafficSignGroup();
        $data = [
            'title'  => 'Thêm biển báo',
            'groups' => $groupModel->getAllSorted(),
        ];
        $this->view('admin/signs/form', $data, 'admin');
    }

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

        Session::setFlash('success', 'Thêm biển báo thành công.');
        $this->redirect('/admin/signs');
    }

    public function edit(): void
    {
        $id = (int)($this->input('id', 0));
        $model = new TrafficSign();
        $sign = $model->find($id);
        if (!$sign) {
            Session::setFlash('error', 'Biển báo không tồn tại.');
            $this->redirect('/admin/signs');
            return;
        }

        $groupModel = new TrafficSignGroup();
        $data = [
            'title'  => 'Sửa biển báo',
            'sign'   => $sign,
            'groups' => $groupModel->getAllSorted(),
        ];
        $this->view('admin/signs/form', $data, 'admin');
    }

    public function update(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new TrafficSign();
        $sign = $model->find($id);
        if (!$sign) {
            Session::setFlash('error', 'Biển báo không tồn tại.');
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

        if (!empty($_FILES['image']['tmp_name'])) {
            $image = Helper::upload($_FILES['image'], 'signs');
            if ($image) {
                $updateData['image'] = $image;
            }
        }

        $model->update($id, $updateData);
        Session::setFlash('success', 'Cập nhật biển báo thành công.');
        $this->redirect('/admin/signs');
    }

    public function delete(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new TrafficSign();
        if (!$model->find($id)) {
            Session::setFlash('error', 'Biển báo không tồn tại.');
            $this->redirect('/admin/signs');
            return;
        }

        $model->delete($id);
        Session::setFlash('success', 'Xóa biển báo thành công.');
        $this->redirect('/admin/signs');
    }
}
