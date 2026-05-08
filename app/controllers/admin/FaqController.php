<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Faq;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    public function index(): void
    {
        $model = new Faq();
        $page = (int)($this->input('page', 1));
        $data = $model->paginate($page, 10, [], 'sort_order ASC');
        $data['title'] = 'Quản lý FAQ';
        $data['baseUrl'] = '/admin/faqs';
        $this->view('admin/faqs/index', $data, 'admin');
    }

    public function create(): void
    {
        $data = ['title' => 'Thêm FAQ'];
        $this->view('admin/faqs/form', $data, 'admin');
    }

    public function store(): void
    {
        if (!$this->validateCsrf()) return;

        $validator = new Validator();
        $rules = [
            'question' => 'required|max:500',
            'answer'   => 'required',
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            Session::set('form_errors', $validator->getErrors());
            Session::set('old_input', $_POST);
            $this->redirect('/admin/faqs/create');
            return;
        }

        $model = new Faq();
        $model->create([
            'question'   => $_POST['question'],
            'answer'     => $_POST['answer'],
            'category'   => $_POST['category'] ?? null,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'status'     => (int)($_POST['status'] ?? 1),
        ]);

        Session::setFlash('success', 'Thêm FAQ thành công.');
        $this->redirect('/admin/faqs');
    }

    public function edit(): void
    {
        $id = (int)($this->input('id', 0));
        $model = new Faq();
        $faq = $model->find($id);
        if (!$faq) {
            Session::setFlash('error', 'FAQ không tồn tại.');
            $this->redirect('/admin/faqs');
            return;
        }

        $data = [
            'title' => 'Sửa FAQ',
            'faq'   => $faq,
        ];
        $this->view('admin/faqs/form', $data, 'admin');
    }

    public function update(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new Faq();
        $faq = $model->find($id);
        if (!$faq) {
            Session::setFlash('error', 'FAQ không tồn tại.');
            $this->redirect('/admin/faqs');
            return;
        }

        $validator = new Validator();
        $rules = [
            'question' => 'required|max:500',
            'answer'   => 'required',
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            $this->redirect("/admin/faqs/{$id}/edit");
            return;
        }

        $model->update($id, [
            'question'   => $_POST['question'],
            'answer'     => $_POST['answer'],
            'category'   => $_POST['category'] ?? null,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'status'     => (int)($_POST['status'] ?? 1),
        ]);

        Session::setFlash('success', 'Cập nhật FAQ thành công.');
        $this->redirect('/admin/faqs');
    }

    public function delete(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new Faq();
        if (!$model->find($id)) {
            Session::setFlash('error', 'FAQ không tồn tại.');
            $this->redirect('/admin/faqs');
            return;
        }

        $model->delete($id);
        Session::setFlash('success', 'Xóa FAQ thành công.');
        $this->redirect('/admin/faqs');
    }
}
