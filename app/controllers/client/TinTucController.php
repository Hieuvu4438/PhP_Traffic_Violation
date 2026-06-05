<?php
namespace App\Controllers\Client;

use App\Core\Controller;
use App\Core\Session;
use App\Models\News;
use App\Models\NewsCategory;

class TinTucController extends Controller
{
    public function index(): void
    {
        $newsModel = new News();
        $categoryModel = new NewsCategory();

        $categoryId = $this->input('cat', '');
        $page = max(1, (int) $this->input('page', 1));
        $perPage = 9;

        $conditions = ['status' => 'published'];
        if ($categoryId !== '') {
            $conditions['category_id'] = (int) $categoryId;
        }

        $pagination = $newsModel->paginate($page, $perPage, $conditions, 'created_at DESC');
        $categories = $categoryModel->all([], 'name ASC');

        $this->view('client/tintuc/index', [
            'title' => 'Traffic News',
            'news' => $pagination['items'],
            'pagination' => $pagination,
            'categories' => $categories,
            'currentCategory' => $categoryId,
        ]);
    }

    public function detail(string $slug): void
    {
        $newsModel = new News();

        $article = $newsModel->getWithCategory($this->getIdFromSlug($slug));
        if (!$article) {
            // Try finding by slug directly
            $article = $newsModel->findBy('slug', $slug);
            if (!$article || $article['status'] !== 'published') {
                Session::setFlash('error', 'Article does not exist.');
                $this->redirect('/tin-tuc');
                return;
            }
            // Re-fetch with joins
            $article = $newsModel->getWithCategory($article['id']);
        }

        // Increment views
        $newsModel->incrementViews($article['id']);

        // Related articles
        $related = $newsModel->getRelated(
            $article['category_id'] ?? 0,
            $article['id'],
            4
        );

        $this->view('client/tintuc/detail', [
            'title' => $article['title'],
            'article' => $article,
            'related' => $related,
        ]);
    }

    private function getIdFromSlug(string $slug): int
    {
        // Slug format: "tieu-de-bai-viet-123" - extract ID from end
        $parts = explode('-', $slug);
        $last = end($parts);
        return is_numeric($last) ? (int) $last : 0;
    }
}
