<?php
namespace App\Controllers\Client;

use App\Core\Controller;
use App\Models\News;
use App\Models\Violation;

class HomeController extends Controller
{
    public function index(): void
    {
        $newsModel = new News();
        $violationModel = new Violation();

        $latestNews = $newsModel->getLatest(6);
        $topOffenses = $violationModel->topOffenses(5);
        $totalViolations = $violationModel->count();
        $todayViolations = $violationModel->countToday();

        $this->view('client/home/index', [
            'title' => 'Traffic Violation Lookup',
            'latestNews' => $latestNews,
            'topOffenses' => $topOffenses,
            'totalViolations' => $totalViolations,
            'todayViolations' => $todayViolations,
        ]);
    }

    public function about(): void
    {
        $this->view('client/pages/gioi-thieu', [
            'title' => 'About',
        ]);
    }
}
