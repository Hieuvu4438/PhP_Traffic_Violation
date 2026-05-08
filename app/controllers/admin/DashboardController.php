<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Models\User;
use App\Models\Violation;
use App\Models\News;
use App\Models\Vehicle;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    public function index(): void
    {
        $userModel = new User();
        $violationModel = new Violation();
        $newsModel = new News();
        $vehicleModel = new Vehicle();

        $data = [
            'title'           => 'Dashboard',
            'totalUsers'      => $userModel->count(),
            'totalViolations' => $violationModel->count(),
            'totalNews'       => $newsModel->count(),
            'totalVehicles'   => $vehicleModel->count(),
            'recentViolations' => $violationModel->getAllWithDetails([], 'v.created_at DESC', 10),
        ];

        $this->view('admin/dashboard/index', $data, 'admin');
    }
}
