<?php
namespace App\Controllers\Client;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Violation;
use App\Models\SearchHistory;

class TraCuuController extends Controller
{
    public function index(): void
    {
        $this->view('client/tracuu/index', [
            'title' => 'Traffic Violation Lookup',
        ]);
    }

    public function search(): void
    {
        if (!$this->validateCsrf()) {
            $this->redirect('/tra-cuu');
            return;
        }

        $plateNumber = strtoupper(trim($this->input('plate_number', '')));
        $vehicleType = $this->input('vehicle_type', '');

        // Validate
        $errors = [];

        if (empty($plateNumber)) {
            $errors[] = 'Please enter a license plate number.';
        } elseif (!Validator::plateNumber($plateNumber)) {
            $errors[] = 'Invalid license plate format (e.g., 30A-12345).';
        }

        $validTypes = ['car', 'motorcycle', 'electric_motorcycle'];
        if (empty($vehicleType) || !in_array($vehicleType, $validTypes)) {
            $errors[] = 'Please select a valid vehicle type.';
        }

        if (!empty($errors)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => $errors[0]], 422);
                return;
            }
            Session::setFlash('error', $errors[0]);
            $this->redirect('/tra-cuu');
            return;
        }

        // Search
        $violationModel = new Violation();
        $results = $violationModel->searchByPlate($plateNumber, $vehicleType);

        // Log search history
        $searchHistory = new SearchHistory();
        $userId = Session::isLoggedIn() ? Session::get('user_id') : null;
        $searchHistory->log($userId, $plateNumber, $vehicleType, count($results));

        // AJAX: trả về JSON
        if ($this->isAjax()) {
            $this->json([
                'success' => true,
                'count' => count($results),
                'plateNumber' => $plateNumber,
                'vehicleType' => $vehicleType,
                'vehicleTypeLabel' => match($vehicleType) {
                    'car' => 'Car',
                    'motorcycle' => 'Motorcycle',
                    'electric_motorcycle' => 'Electric Motorcycle',
                    default => $vehicleType,
                },
                'results' => $results,
            ]);
            return;
        }

        $this->view('client/tracuu/index', [
            'title' => 'License Plate Lookup Results: ' . htmlspecialchars($plateNumber, ENT_QUOTES, 'UTF-8'),
            'results' => $results,
            'plateNumber' => $plateNumber,
            'vehicleType' => $vehicleType,
        ]);
    }
}
