<?php
namespace App\Controllers\Client;

use App\Core\Controller;
use App\Models\Violation;

class ThongKeController extends Controller
{
    public function index(): void
    {
        $violationModel = new Violation();

        $year = (int) $this->input('year', date('Y'));

        $topOffenses = $violationModel->topOffenses(10);
        $topLocations = $violationModel->topLocations(10);
        $topPlates = $violationModel->topPlates(10);
        $byMonth = $violationModel->countByMonth($year);
        $byStatus = $violationModel->countByStatus();

        // Map status counts
        $statusCounts = ['pending' => 0, 'processed' => 0, 'paid' => 0];
        foreach ($byStatus as $row) {
            $statusCounts[$row['status']] = (int) $row['count'];
        }

        // Build full 12-month array
        $monthlyData = array_fill(0, 12, 0);
        foreach ($byMonth as $row) {
            $monthlyData[(int) $row['month'] - 1] = (int) $row['count'];
        }

        $this->view('client/thongke/index', [
            'title' => 'Thống kê vi phạm giao thông',
            'topOffenses' => $topOffenses,
            'topLocations' => $topLocations,
            'topPlates' => $topPlates,
            'monthlyData' => $monthlyData,
            'statusCounts' => $statusCounts,
            'year' => $year,
        ]);
    }
}
