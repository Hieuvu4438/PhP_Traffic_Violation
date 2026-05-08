<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Helper;
use App\Models\NewsCategory;
use App\Models\OffenseCategory;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    public function index(): void
    {
        $newsCatModel = new NewsCategory();
        $offenseCatModel = new OffenseCategory();

        $data = [
            'title'            => 'Quản lý danh mục',
            'newsCategories'   => $newsCatModel->all([], 'name ASC'),
            'offenseCategories'=> $offenseCatModel->all([], 'name ASC'),
        ];
        $this->view('admin/categories/index', $data, 'admin');
    }

    public function store(): void
    {
        if (!$this->validateCsrf()) return;

        $type = $_POST['category_type'] ?? '';
        $name = $_POST['name'] ?? '';

        if (!in_array($type, ['news', 'offense'])) {
            Session::setFlash('error', 'Loại danh mục không hợp lệ.');
            $this->redirect('/admin/categories');
            return;
        }

        if (!Validator::required($name) || !Validator::minLength($name, 2)) {
            Session::setFlash('error', 'Tên danh mục không được để trống và tối thiểu 2 ký tự.');
            $this->redirect('/admin/categories');
            return;
        }

        if ($type === 'news') {
            $model = new NewsCategory();
            $slug = Helper::slug($name);
            if ($model->findBy('slug', $slug)) {
                $slug .= '-' . time();
            }
            $model->create(['name' => $name, 'slug' => $slug]);
        } else {
            $model = new OffenseCategory();
            $model->create(['name' => $name, 'description' => $_POST['description'] ?? null]);
        }

        Session::setFlash('success', 'Thêm danh mục thành công.');
        $this->redirect('/admin/categories');
    }

    public function update(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $type = $_POST['category_type'] ?? '';
        $name = $_POST['name'] ?? '';

        if (!in_array($type, ['news', 'offense'])) {
            Session::setFlash('error', 'Loại danh mục không hợp lệ.');
            $this->redirect('/admin/categories');
            return;
        }

        if (!Validator::required($name) || !Validator::minLength($name, 2)) {
            Session::setFlash('error', 'Tên danh mục không được để trống và tối thiểu 2 ký tự.');
            $this->redirect('/admin/categories');
            return;
        }

        if ($type === 'news') {
            $model = new NewsCategory();
            $cat = $model->find($id);
            if (!$cat) {
                Session::setFlash('error', 'Danh mục không tồn tại.');
                $this->redirect('/admin/categories');
                return;
            }
            $slug = Helper::slug($name);
            $existing = $model->findBy('slug', $slug);
            if ($existing && $existing['id'] != $id) {
                $slug .= '-' . time();
            }
            $model->update($id, ['name' => $name, 'slug' => $slug]);
        } else {
            $model = new OffenseCategory();
            $cat = $model->find($id);
            if (!$cat) {
                Session::setFlash('error', 'Danh mục không tồn tại.');
                $this->redirect('/admin/categories');
                return;
            }
            $model->update($id, ['name' => $name, 'description' => $_POST['description'] ?? null]);
        }

        Session::setFlash('success', 'Cập nhật danh mục thành công.');
        $this->redirect('/admin/categories');
    }

    public function delete(): void
    {
        if (!$this->validateCsrf()) return;

        $id = (int)($this->input('id', 0));
        $type = $_POST['category_type'] ?? '';

        if (!in_array($type, ['news', 'offense'])) {
            Session::setFlash('error', 'Loại danh mục không hợp lệ.');
            $this->redirect('/admin/categories');
            return;
        }

        if ($type === 'news') {
            $model = new NewsCategory();
        } else {
            $model = new OffenseCategory();
        }

        if (!$model->find($id)) {
            Session::setFlash('error', 'Danh mục không tồn tại.');
            $this->redirect('/admin/categories');
            return;
        }

        $model->delete($id);
        Session::setFlash('success', 'Xóa danh mục thành công.');
        $this->redirect('/admin/categories');
    }
}
