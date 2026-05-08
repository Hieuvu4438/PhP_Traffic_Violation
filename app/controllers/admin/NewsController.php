<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Helper;
use App\Models\News;
use App\Models\NewsCategory;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    public function index(): void
    {
        $model = new News();
        $page = (int)($this->input('page', 1));

        $total = $model->count();
        $totalPages = max(1, (int) ceil($total / 10));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * 10;
        $items = $model->getAllWithCategory([], 'n.created_at DESC', 10, $offset);

        $data = [
            'title'       => 'Quản lý tin tức',
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => 10,
            'total_pages' => $totalPages,
            'has_next'    => $page < $totalPages,
            'has_prev'    => $page > 1,
            'baseUrl'     => '/admin/news',
        ];
        $this->view('admin/news/index', $data, 'admin');
    }

    public function create(): void
    {
        $categoryModel = new NewsCategory();
        $data = [
            'title'      => 'Thêm tin tức',
            'categories' => $categoryModel->all([], 'name ASC'),
        ];
        $this->view('admin/news/form', $data, 'admin');
    }

    public function store(): void
    {
        if (!$this->validateCsrf()) return;

        $validator = new Validator();
        $rules = [
            'title'       => 'required|min:3|max:255',
            'content'     => 'required',
            'category_id' => 'numeric',
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            Session::set('form_errors', $validator->getErrors());
            Session::set('old_input', $_POST);
            $this->redirect('/admin/news/create');
            return;
        }

        $slug = Helper::slug($_POST['title']);
        $model = new News();
        if ($model->findBy('slug', $slug)) {
            $slug .= '-' . time();
        }

        $thumbnail = null;
        if (!empty($_FILES['thumbnail']['tmp_name'])) {
            $thumbnail = Helper::upload($_FILES['thumbnail'], 'news');
        }

        $model->create([
            'title'       => $_POST['title'],
            'slug'        => $slug,
            'content'     => $_POST['content'],
            'thumbnail'   => $thumbnail,
            'category_id' => $_POST['category_id'] ?: null,
            'author_id'   => Session::get('user_id'),
            'status'      => $_POST['status'] ?? 'draft',
        ]);

        Session::setFlash('success', 'Thêm tin tức thành công.');
        $this->redirect('/admin/news');
    }

    public function edit(): void
    {
        $id = (int)($this->input('id', 0));
        $model = new News();
        $news = $model->find($id);
        if (!$news) {
            Session::setFlash('error', 'Tin tức không tồn tại.');
            $this->redirect('/admin/news');
            return;
        }

        $categoryModel = new NewsCategory();
        $data = [
            'title'      => 'Sửa tin tức',
            'news'       => $news,
            'categories' => $categoryModel->all([], 'name ASC'),
        ];
        $this->view('admin/news/form', $data, 'admin');
    }

    public function update(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new News();
        $news = $model->find($id);
        if (!$news) {
            Session::setFlash('error', 'Tin tức không tồn tại.');
            $this->redirect('/admin/news');
            return;
        }

        $validator = new Validator();
        $rules = [
            'title'       => 'required|min:3|max:255',
            'content'     => 'required',
            'category_id' => 'numeric',
        ];
        if (!$validator->validate($_POST, $rules)) {
            Session::setFlash('error', implode('<br>', array_map(fn($e) => implode('<br>', $e), $validator->getErrors())));
            $this->redirect("/admin/news/{$id}/edit");
            return;
        }

        $slug = Helper::slug($_POST['title']);
        $existing = $model->findBy('slug', $slug);
        if ($existing && $existing['id'] != $id) {
            $slug .= '-' . time();
        }

        $updateData = [
            'title'       => $_POST['title'],
            'slug'        => $slug,
            'content'     => $_POST['content'],
            'category_id' => $_POST['category_id'] ?: null,
            'status'      => $_POST['status'] ?? 'draft',
        ];

        if (!empty($_FILES['thumbnail']['tmp_name'])) {
            $thumbnail = Helper::upload($_FILES['thumbnail'], 'news');
            if ($thumbnail) {
                $updateData['thumbnail'] = $thumbnail;
            }
        }

        $model->update($id, $updateData);
        Session::setFlash('success', 'Cập nhật tin tức thành công.');
        $this->redirect('/admin/news');
    }

    public function delete(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $model = new News();
        if (!$model->find($id)) {
            Session::setFlash('error', 'Tin tức không tồn tại.');
            $this->redirect('/admin/news');
            return;
        }

        $model->delete($id);
        Session::setFlash('success', 'Xóa tin tức thành công.');
        $this->redirect('/admin/news');
    }
}
