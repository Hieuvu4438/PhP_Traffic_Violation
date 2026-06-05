<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Models\User;
use App\Models\Violation;
use App\Models\News;
use App\Models\Vehicle;

/**
 * Controller quản lý trang Dashboard admin.
 * Route: GET /admin
 * Chức năng: Hiển thị thống kê tổng quan gồm 4 số đếm (users, violations, news, vehicles)
 *            và danh sách 10 vi phạm gần nhất kèm thông tin liên quan (địa điểm, lỗi vi phạm).
 */
class DashboardController extends Controller
{
    /**
     * Constructor: gọi requireAdmin() để chặn mọi request không có role='admin'.
     * Nếu chưa đăng nhập -> redirect /login. Nếu role != admin -> redirect /.
     */
    public function __construct()
    {
        $this->requireAdmin();
    }

    /**
     * GET /admin
     * Trang chủ dashboard admin.
     *
     * Luồng xử lý:
     * 1. Khởi tạo 4 Model: User, Violation, News, Vehicle.
     * 2. Gọi count() trên từng model để lấy tổng số bản ghi (SELECT COUNT(*) FROM table).
     * 3. Gọi $violationModel->getAllWithDetails(...) để lấy 10 vi phạm mới nhất:
     *    - JOIN violations v LEFT JOIN locations l ON v.location_id = l.id
     *                        LEFT JOIN offenses o ON v.offense_id = o.id
     *    - Lấy thêm location_name, offense_name từ các bảng liên kết.
     *    - Sắp xếp giảm dần theo v.created_at, giới hạn 10 dòng.
     * 4. Truyền toàn bộ dữ liệu vào view admin/dashboard/index với layout 'admin'.
     */
    public function index(): void
    {
        $userModel = new User();
        $violationModel = new Violation();
        $newsModel = new News();
        $vehicleModel = new Vehicle();

        // Tổng hợp dữ liệu thống kê và 10 vi phạm gần nhất
        $data = [
            'title'           => 'Dashboard',
            'totalUsers'      => $userModel->count(),
            'totalViolations' => $violationModel->count(),
            'totalNews'       => $newsModel->count(),
            'totalVehicles'   => $vehicleModel->count(),
            'recentViolations' => $violationModel->getAllWithDetails([], 'v.created_at DESC', 10),
        ];

        // Render view admin/dashboard/index.php với admin layout
        $this->view('admin/dashboard/index', $data, 'admin');
    }
}
