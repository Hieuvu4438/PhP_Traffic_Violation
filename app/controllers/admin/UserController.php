<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    public function index(): void
    {
        $model = new User();
        $page = (int)($this->input('page', 1));
        $data = $model->paginate($page, 10, [], 'created_at DESC');
        $data['title'] = 'Quản lý người dùng';
        $data['baseUrl'] = '/admin/users';
        $this->view('admin/users/index', $data, 'admin');
    }

    public function create(): void
    {
        $data = ['title' => 'Thêm người dùng'];
        $this->view('admin/users/form', $data, 'admin');
    }

    public function store(): void
    {
        if (!$this->validateCsrf()) return;

        $validator = new Validator();
        $rules = [
            'fullname' => 'required|min:2|max:100',
            'email'    => 'required|email',
            'phone'    => 'phone',
            'password' => 'required|min:6|max:255',
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            Session::set('form_errors', $validator->getErrors());
            Session::set('old_input', $_POST);
            $this->redirect('/admin/users/create');
            return;
        }

        $model = new User();
        if ($model->findBy('email', $_POST['email'])) {
            Session::setFlash('error', 'Email đã tồn tại.');
            Session::set('old_input', $_POST);
            $this->redirect('/admin/users/create');
            return;
        }
        if (!empty($_POST['phone']) && $model->findBy('phone', $_POST['phone'])) {
            Session::setFlash('error', 'Số điện thoại đã tồn tại.');
            Session::set('old_input', $_POST);
            $this->redirect('/admin/users/create');
            return;
        }

        $model->create([
            'fullname'  => $_POST['fullname'],
            'email'     => $_POST['email'],
            'phone'     => $_POST['phone'] ?? null,
            'password'  => password_hash($_POST['password'], PASSWORD_BCRYPT),
            'role'      => $_POST['role'] ?? 'user',
            'status'    => (int)($_POST['status'] ?? 1),
        ]);

        Session::setFlash('success', 'Thêm người dùng thành công.');
        $this->redirect('/admin/users');
    }

    public function edit(): void
    {
        $id = (int)($this->input('id', 0));
        $model = new User();
        $user = $model->find($id);
        if (!$user) {
            Session::setFlash('error', 'Người dùng không tồn tại.');
            $this->redirect('/admin/users');
            return;
        }

        $data = [
            'title' => 'Sửa người dùng',
            'user'  => $user,
        ];
        $this->view('admin/users/form', $data, 'admin');
    }

    public function update(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new User();
        $user = $model->find($id);
        if (!$user) {
            Session::setFlash('error', 'Người dùng không tồn tại.');
            $this->redirect('/admin/users');
            return;
        }

        $validator = new Validator();
        $rules = [
            'fullname' => 'required|min:2|max:100',
            'email'    => 'required|email',
            'phone'    => 'phone',
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            Session::set('form_errors', $validator->getErrors());
            Session::set('old_input', $_POST);
            $this->redirect("/admin/users/{$id}/edit");
            return;
        }

        $existing = $model->findBy('email', $_POST['email']);
        if ($existing && $existing['id'] != $id) {
            Session::setFlash('error', 'Email đã tồn tại.');
            Session::set('old_input', $_POST);
            $this->redirect("/admin/users/{$id}/edit");
            return;
        }
        if (!empty($_POST['phone'])) {
            $phoneExisting = $model->findBy('phone', $_POST['phone']);
            if ($phoneExisting && $phoneExisting['id'] != $id) {
                Session::setFlash('error', 'Số điện thoại đã tồn tại.');
                Session::set('old_input', $_POST);
                $this->redirect("/admin/users/{$id}/edit");
                return;
            }
        }

        $updateData = [
            'fullname' => $_POST['fullname'],
            'email'    => $_POST['email'],
            'phone'    => $_POST['phone'] ?? null,
            'role'     => $_POST['role'] ?? $user['role'],
            'status'   => (int)($_POST['status'] ?? $user['status']),
        ];

        if (!empty($_POST['password'])) {
            $updateData['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        }

        $model->update($id, $updateData);
        Session::setFlash('success', 'Cập nhật người dùng thành công.');
        $this->redirect('/admin/users');
    }

    public function delete(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new User();
        $user = $model->find($id);
        if (!$user) {
            Session::setFlash('error', 'Người dùng không tồn tại.');
            $this->redirect('/admin/users');
            return;
        }

        $model->delete($id);
        Session::setFlash('success', 'Xóa người dùng thành công.');
        $this->redirect('/admin/users');
    }

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
        $model = new User();
        $user = $model->find($id);
        if (!$user) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Người dùng không tồn tại.'], 404);
                return;
            }
            Session::setFlash('error', 'Người dùng không tồn tại.');
            $this->redirect('/admin/users');
            return;
        }

        $newStatus = $user['status'] ? 0 : 1;
        $model->update($id, ['status' => $newStatus]);

        if ($this->isAjax()) {
            $this->json([
                'success' => true,
                'new_status' => $newStatus,
                'message' => $newStatus ? 'Mở khóa người dùng thành công.' : 'Khóa người dùng thành công.',
            ]);
            return;
        }

        $msg = $newStatus ? 'Mở khóa người dùng thành công.' : 'Khóa người dùng thành công.';
        Session::setFlash('success', $msg);
        $this->redirect('/admin/users');
    }
}
