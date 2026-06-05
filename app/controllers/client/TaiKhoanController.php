<?php
namespace App\Controllers\Client;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\SearchHistory;
use App\Models\Violation;

class TaiKhoanController extends Controller
{
    // ============================================================
    // Dashboard
    // ============================================================
    public function dashboard(): void
    {
        $this->requireLogin();

        $userId = Session::get('user_id');

        $vehicleModel = new Vehicle();
        $historyModel = new SearchHistory();
        $violationModel = new Violation();

        $vehicleCount = $vehicleModel->countByUser($userId);
        $recentHistory = $historyModel->getByUser($userId, 5);

        $this->view('client/taikhoan/dashboard', [
            'title' => 'My Account',
            'vehicleCount' => $vehicleCount,
            'recentHistory' => $recentHistory,
        ]);
    }

    // ============================================================
    // Vehicles CRUD
    // ============================================================
    public function vehicles(): void
    {
        $this->requireLogin();

        $userId = Session::get('user_id');
        $vehicleModel = new Vehicle();
        $vehicles = $vehicleModel->findByUser($userId);

        $this->view('client/taikhoan/vehicles', [
            'title' => 'My Vehicles',
            'vehicles' => $vehicles,
        ]);
    }

    public function addVehicle(): void
    {
        $this->requireLogin();

        if (!$this->validateCsrf()) {
            $this->redirect('/tai-khoan/phuong-tien');
            return;
        }

        $plateNumber = strtoupper(trim($this->input('plate_number', '')));
        $vehicleType = $this->input('vehicle_type', '');
        $brand = trim($this->input('brand', ''));
        $model = trim($this->input('model', ''));

        if (empty($plateNumber) || !Validator::plateNumber($plateNumber)) {
            Session::setFlash('error', 'Invalid license plate format.');
            $this->redirect('/tai-khoan/phuong-tien');
            return;
        }

        $validTypes = ['car', 'motorcycle', 'electric_motorcycle'];
        if (!in_array($vehicleType, $validTypes)) {
            Session::setFlash('error', 'Invalid vehicle type.');
            $this->redirect('/tai-khoan/phuong-tien');
            return;
        }

        $vehicleModel = new Vehicle();
        $vehicleModel->create([
            'user_id' => Session::get('user_id'),
            'plate_number' => $plateNumber,
            'vehicle_type' => $vehicleType,
            'brand' => $brand,
            'model' => $model,
        ]);

        Session::setFlash('success', 'Vehicle added successfully.');
        $this->redirect('/tai-khoan/phuong-tien');
    }

    public function updateVehicle(int $id): void
    {
        $this->requireLogin();

        if (!$this->validateCsrf()) {
            $this->redirect('/tai-khoan/phuong-tien');
            return;
        }

        $userId = Session::get('user_id');
        $vehicleModel = new Vehicle();

        $vehicle = $vehicleModel->find($id);
        if (!$vehicle || (int) $vehicle['user_id'] !== $userId) {
            Session::setFlash('error', 'Vehicle does not exist.');
            $this->redirect('/tai-khoan/phuong-tien');
            return;
        }

        $plateNumber = strtoupper(trim($this->input('plate_number', '')));
        $vehicleType = $this->input('vehicle_type', '');
        $brand = trim($this->input('brand', ''));
        $model = trim($this->input('model', ''));

        if (empty($plateNumber) || !Validator::plateNumber($plateNumber)) {
            Session::setFlash('error', 'Invalid license plate format.');
            $this->redirect('/tai-khoan/phuong-tien');
            return;
        }

        $validTypes = ['car', 'motorcycle', 'electric_motorcycle'];
        if (!in_array($vehicleType, $validTypes)) {
            Session::setFlash('error', 'Invalid vehicle type.');
            $this->redirect('/tai-khoan/phuong-tien');
            return;
        }

        $vehicleModel->update($id, [
            'plate_number' => $plateNumber,
            'vehicle_type' => $vehicleType,
            'brand' => $brand,
            'model' => $model,
        ]);

        Session::setFlash('success', 'Vehicle updated successfully.');
        $this->redirect('/tai-khoan/phuong-tien');
    }

    public function deleteVehicle(int $id): void
    {
        $this->requireLogin();

        if (!$this->validateCsrf()) {
            $this->redirect('/tai-khoan/phuong-tien');
            return;
        }

        $userId = Session::get('user_id');
        $vehicleModel = new Vehicle();

        $vehicle = $vehicleModel->find($id);
        if (!$vehicle || (int) $vehicle['user_id'] !== $userId) {
            Session::setFlash('error', 'Vehicle does not exist.');
            $this->redirect('/tai-khoan/phuong-tien');
            return;
        }

        $vehicleModel->delete($id);
        Session::setFlash('success', 'Vehicle deleted successfully.');
        $this->redirect('/tai-khoan/phuong-tien');
    }

    // ============================================================
    // Search History
    // ============================================================
    public function history(): void
    {
        $this->requireLogin();

        $userId = Session::get('user_id');
        $historyModel = new SearchHistory();
        $page = max(1, (int) $this->input('page', 1));
        $perPage = 15;

        $pagination = $historyModel->paginate(
            $page,
            $perPage,
            ['user_id' => $userId],
            'searched_at DESC'
        );

        $this->view('client/taikhoan/history', [
            'title' => 'Search History',
            'history' => $pagination['items'],
            'pagination' => $pagination,
        ]);
    }

    // ============================================================
    // Profile
    // ============================================================
    public function profile(): void
    {
        $this->requireLogin();

        $userId = Session::get('user_id');
        $userModel = new User();
        $user = $userModel->find($userId);

        $this->view('client/taikhoan/profile', [
            'title' => 'Profile',
            'user' => $user,
        ]);
    }

    public function updateProfile(): void
    {
        $this->requireLogin();

        if (!$this->validateCsrf()) {
            $this->redirect('/tai-khoan/ho-so');
            return;
        }

        $userId = Session::get('user_id');
        $fullname = trim($this->input('fullname', ''));
        $phone = trim($this->input('phone', ''));
        $email = trim($this->input('email', ''));

        $validator = new Validator();
        $data = ['fullname' => $fullname, 'email' => $email, 'phone' => $phone];
        $rules = [
            'fullname' => 'required|min:2|max:100',
            'email' => 'required|email',
            'phone' => 'required|phone',
        ];

        if (!$validator->validate($data, $rules)) {
            Session::setFlash('error', $validator->firstError('fullname') ?? $validator->firstError('email') ?? $validator->firstError('phone') ?? 'Invalid data.');
            $this->redirect('/tai-khoan/ho-so');
            return;
        }

        $userModel = new User();

        // Check duplicate email
        $existingEmail = $userModel->findBy('email', $email);
        if ($existingEmail && (int) $existingEmail['id'] !== $userId) {
            Session::setFlash('error', 'This email is already used by another account.');
            $this->redirect('/tai-khoan/ho-so');
            return;
        }

        // Check duplicate phone
        $existingPhone = $userModel->findBy('phone', $phone);
        if ($existingPhone && (int) $existingPhone['id'] !== $userId) {
            Session::setFlash('error', 'This phone number is already used by another account.');
            $this->redirect('/tai-khoan/ho-so');
            return;
        }

        $userModel->update($userId, [
            'fullname' => $fullname,
            'email' => $email,
            'phone' => $phone,
        ]);

        // Update session
        Session::set('user_name', $fullname);
        Session::set('user_email', $email);

        Session::setFlash('success', 'Profile updated successfully.');
        $this->redirect('/tai-khoan/ho-so');
    }

    public function changePassword(): void
    {
        $this->requireLogin();

        if (!$this->validateCsrf()) {
            $this->redirect('/tai-khoan/ho-so');
            return;
        }

        $userId = Session::get('user_id');
        $oldPassword = $this->input('old_password', '');
        $newPassword = $this->input('new_password', '');
        $confirmPassword = $this->input('password_confirm', '');

        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            Session::setFlash('error', 'Please fill in all required information.');
            $this->redirect('/tai-khoan/ho-so');
            return;
        }

        if (mb_strlen($newPassword) < 6) {
            Session::setFlash('error', 'New password must be at least 6 characters.');
            $this->redirect('/tai-khoan/ho-so');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            Session::setFlash('error', 'Password confirmation does not match.');
            $this->redirect('/tai-khoan/ho-so');
            return;
        }

        $userModel = new User();
        $user = $userModel->find($userId);

        if (!$user || !password_verify($oldPassword, $user['password'])) {
            Session::setFlash('error', 'Current password is incorrect.');
            $this->redirect('/tai-khoan/ho-so');
            return;
        }

        $userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
        ]);

        Session::setFlash('success', 'Password changed successfully.');
        $this->redirect('/tai-khoan/ho-so');
    }
}
