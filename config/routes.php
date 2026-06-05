<?php
/**
 * URL routing table → Controller@action
 */

use App\Core\Router;

return function (Router $router): void {

    // ============================================================
    // PUBLIC ROUTES (NO LOGIN REQUIRED)
    // ============================================================

    $router->get('/', 'client/HomeController@index');
    $router->get('/tra-cuu', 'client/TraCuuController@index');
    $router->post('/tra-cuu', 'client/TraCuuController@search');
    $router->get('/tin-tuc', 'client/TinTucController@index');
    $router->get('/tin-tuc/{slug}', 'client/TinTucController@detail');
    $router->get('/bien-bao', 'client/BienBaoController@index');
    $router->get('/bien-bao/{id}', 'client/BienBaoController@detail');
    $router->get('/ban-do', 'client/BanDoController@index');
    $router->get('/thong-ke', 'client/ThongKeController@index');
    $router->get('/faq', 'client/FaqController@index');
    $router->get('/gioi-thieu', 'client/HomeController@about');
    $router->get('/lien-he', 'client/LienHeController@index');
    $router->post('/lien-he', 'client/LienHeController@send');
    $router->get('/chat', 'client/ChatController@index');
    $router->post('/chat/start', 'client/ChatController@start');
    $router->post('/chat/restore', 'client/ChatController@restore');
    $router->get('/chat/{id}/messages', 'client/ChatController@messages');
    $router->post('/chat/{id}/send', 'client/ChatController@send');
    $router->get('/dang-nhap', 'client/AuthController@loginForm');
    $router->post('/dang-nhap', 'client/AuthController@login');
    $router->get('/dang-ky', 'client/AuthController@registerForm');
    $router->post('/dang-ky', 'client/AuthController@register');
    $router->get('/dang-xuat', 'client/AuthController@logout');

    // ============================================================
    // USER ROUTES (LOGIN REQUIRED)
    // ============================================================

    $router->get('/tai-khoan', 'client/TaiKhoanController@dashboard');

    // Vehicles
    $router->get('/tai-khoan/phuong-tien', 'client/TaiKhoanController@vehicles');
    $router->post('/tai-khoan/phuong-tien', 'client/TaiKhoanController@addVehicle');
    $router->post('/tai-khoan/phuong-tien/{id}/edit', 'client/TaiKhoanController@updateVehicle');
    $router->post('/tai-khoan/phuong-tien/{id}/delete', 'client/TaiKhoanController@deleteVehicle');

    // History & Profile
    $router->get('/tai-khoan/lich-su', 'client/TaiKhoanController@history');
    $router->get('/tai-khoan/ho-so', 'client/TaiKhoanController@profile');
    $router->post('/tai-khoan/ho-so', 'client/TaiKhoanController@updateProfile');
    $router->post('/tai-khoan/doi-mat-khau', 'client/TaiKhoanController@changePassword');

    // ============================================================
    // ROUTES ADMIN
    // ============================================================

    $router->get('/admin', 'admin/DashboardController@index');

    // Users CRUD
    $router->get('/admin/users', 'admin/UserController@index');
    $router->get('/admin/users/create', 'admin/UserController@create');
    $router->post('/admin/users', 'admin/UserController@store');
    $router->get('/admin/users/{id}/edit', 'admin/UserController@edit');
    $router->post('/admin/users/{id}', 'admin/UserController@update');
    $router->post('/admin/users/{id}/delete', 'admin/UserController@delete');
    $router->post('/admin/users/{id}/toggle-status', 'admin/UserController@toggleStatus');

    // Violations CRUD
    $router->get('/admin/violations', 'admin/ViolationController@index');
    $router->get('/admin/violations/create', 'admin/ViolationController@create');
    $router->post('/admin/violations', 'admin/ViolationController@store');
    $router->get('/admin/violations/{id}/edit', 'admin/ViolationController@edit');
    $router->post('/admin/violations/{id}', 'admin/ViolationController@update');
    $router->post('/admin/violations/{id}/delete', 'admin/ViolationController@delete');
    $router->post('/admin/violations/{id}/toggle-status', 'admin/ViolationController@toggleStatus');
    $router->post('/admin/violations/import', 'admin/ViolationController@import');

    // News CRUD
    $router->get('/admin/news', 'admin/NewsController@index');
    $router->get('/admin/news/create', 'admin/NewsController@create');
    $router->post('/admin/news', 'admin/NewsController@store');
    $router->get('/admin/news/{id}/edit', 'admin/NewsController@edit');
    $router->post('/admin/news/{id}', 'admin/NewsController@update');
    $router->post('/admin/news/{id}/delete', 'admin/NewsController@delete');

    // Categories CRUD
    $router->get('/admin/categories', 'admin/CategoryController@index');
    $router->post('/admin/categories', 'admin/CategoryController@store');
    $router->post('/admin/categories/{id}', 'admin/CategoryController@update');
    $router->post('/admin/categories/{id}/delete', 'admin/CategoryController@delete');

    // Traffic Signs CRUD
    $router->get('/admin/signs', 'admin/SignController@index');
    $router->get('/admin/signs/create', 'admin/SignController@create');
    $router->post('/admin/signs', 'admin/SignController@store');
    $router->get('/admin/signs/{id}/edit', 'admin/SignController@edit');
    $router->post('/admin/signs/{id}', 'admin/SignController@update');
    $router->post('/admin/signs/{id}/delete', 'admin/SignController@delete');

    // Locations CRUD
    $router->get('/admin/locations', 'admin/LocationController@index');
    $router->get('/admin/locations/create', 'admin/LocationController@create');
    $router->post('/admin/locations', 'admin/LocationController@store');
    $router->get('/admin/locations/{id}/edit', 'admin/LocationController@edit');
    $router->post('/admin/locations/{id}', 'admin/LocationController@update');
    $router->post('/admin/locations/{id}/delete', 'admin/LocationController@delete');

    // FAQ CRUD
    $router->get('/admin/faqs', 'admin/FaqController@index');
    $router->get('/admin/faqs/create', 'admin/FaqController@create');
    $router->post('/admin/faqs', 'admin/FaqController@store');
    $router->get('/admin/faqs/{id}/edit', 'admin/FaqController@edit');
    $router->post('/admin/faqs/{id}', 'admin/FaqController@update');
    $router->post('/admin/faqs/{id}/delete', 'admin/FaqController@delete');

    // Traffic Alerts CRUD
    $router->get('/admin/alerts', 'admin/AlertController@index');
    $router->get('/admin/alerts/create', 'admin/AlertController@create');
    $router->post('/admin/alerts', 'admin/AlertController@store');
    $router->get('/admin/alerts/{id}/edit', 'admin/AlertController@edit');
    $router->post('/admin/alerts/{id}', 'admin/AlertController@update');
    $router->post('/admin/alerts/{id}/delete', 'admin/AlertController@delete');

    // Contact Messages
    $router->get('/admin/messages', 'admin/MessageController@index');
    $router->post('/admin/messages/{id}/read', 'admin/MessageController@markRead');
    $router->post('/admin/messages/{id}/delete', 'admin/MessageController@delete');

    // Customer Chat
    $router->get('/admin/chat', 'admin/ChatController@index');
    $router->get('/admin/chat/{id}', 'admin/ChatController@show');
    $router->get('/admin/chat/{id}/messages', 'admin/ChatController@messages');
    $router->post('/admin/chat/{id}/reply', 'admin/ChatController@reply');
    $router->post('/admin/chat/{id}/close', 'admin/ChatController@close');
};
