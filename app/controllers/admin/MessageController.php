<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Models\ContactMessage;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    public function index(): void
    {
        $model = new ContactMessage();
        $page = (int)($this->input('page', 1));
        $data = $model->paginate($page, 10, [], 'created_at DESC');
        $data['title'] = 'Quản lý tin nhắn liên hệ';
        $data['baseUrl'] = '/admin/messages';
        $this->view('admin/messages/index', $data, 'admin');
    }

    public function markRead(int $id): void
    {
        if (!$this->validateCsrf()) return;
        $model = new ContactMessage();
        $msg = $model->find($id);
        if (!$msg) {
            Session::setFlash('error', 'Tin nhắn không tồn tại.');
            $this->redirect('/admin/messages');
            return;
        }

        $model->markRead($id);
        Session::setFlash('success', 'Đã đánh dấu là đã đọc.');
        $this->redirect('/admin/messages');
    }

    public function delete(int $id): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)$id;
        $model = new ContactMessage();
        if (!$model->find($id)) {
            Session::setFlash('error', 'Tin nhắn không tồn tại.');
            $this->redirect('/admin/messages');
            return;
        }

        $model->delete($id);
        Session::setFlash('success', 'Xóa tin nhắn thành công.');
        $this->redirect('/admin/messages');
    }
}
