<?php
namespace App\Controllers\Client;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;

class AuthController extends Controller
{
    // ── Đăng nhập ──
    public function loginForm(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect('/');
        }
        $this->view('client/auth/login', ['title' => 'Login']);
    }

    public function login(): void
    {
        if (!$this->validateCsrf()) {
            $this->redirect('/dang-nhap');
            return;
        }

        $email = $this->input('email', '');
        $password = $this->input('password', '');

        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'Please enter both email and password.');
            $this->redirect('/dang-nhap');
            return;
        }

        $userModel = new User();
        $user = $userModel->findBy('email', $email);

        if (!$user || !password_verify($password, $user['password'])) {
            Session::setFlash('error', 'Incorrect email or password.');
            $this->redirect('/dang-nhap');
            return;
        }

        if ((int)$user['status'] === 0) {
            Session::setFlash('error', 'Account has been locked. Please contact the administrator.');
            $this->redirect('/dang-nhap');
            return;
        }

        Session::login($user);
        Session::setFlash('success', 'Login successful!');

        if ($user['role'] === 'admin') {
            $this->redirect('/admin');
        } else {
            $this->redirect('/tai-khoan');
        }
    }

    // ── Đăng ký ──
    public function registerForm(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect('/');
        }
        $this->view('client/auth/register', ['title' => 'Register']);
    }

    public function register(): void
    {
        if (!$this->validateCsrf()) {
            $this->redirect('/dang-ky');
            return;
        }

        $validator = new Validator();
        $data = [
            'fullname' => $this->input('fullname', ''),
            'email' => $this->input('email', ''),
            'phone' => $this->input('phone', ''),
            'password' => $this->input('password', ''),
            'password_confirm' => $this->input('password_confirm', ''),
        ];

        $rules = [
            'fullname' => 'required|min:2|max:100',
            'email' => 'required|email',
            'phone' => 'required|phone',
            'password' => 'required|min:6',
            'password_confirm' => 'required',
        ];

        if (!$validator->validate($data, $rules)) {
            Session::setFlash('error', $validator->firstError('fullname') ?? $validator->firstError('email') ?? $validator->firstError('phone') ?? $validator->firstError('password') ?? 'Invalid data.');
            $this->redirect('/dang-ky');
            return;
        }

        if ($data['password'] !== $data['password_confirm']) {
            Session::setFlash('error', 'Password confirmation does not match.');
            $this->redirect('/dang-ky');
            return;
        }

        $userModel = new User();
        if ($userModel->findBy('email', $data['email'])) {
            Session::setFlash('error', 'This email is already registered.');
            $this->redirect('/dang-ky');
            return;
        }

        if ($data['phone'] && $userModel->findBy('phone', $data['phone'])) {
            Session::setFlash('error', 'This phone number is already registered.');
            $this->redirect('/dang-ky');
            return;
        }

        $userId = $userModel->create([
            'fullname' => $data['fullname'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $newUser = $userModel->find($userId);
        Session::login($newUser);
        Session::setFlash('success', 'Registration successful! Welcome to the system.');
        $this->redirect('/tai-khoan');
    }

    // ── Đăng xuất ──
    public function logout(): void
    {
        Session::logout();
        $this->redirect('/');
    }
}
