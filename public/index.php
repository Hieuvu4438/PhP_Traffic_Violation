<?php
/**
 * Entry Point — Tất cả request đều vào đây
 */

// Error reporting (ẩn trên production)
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../error.log');

// Autoloader
spl_autoload_register(function (string $class): void {
    // Chuyển namespace thành đường dẫn file
    // VD: App\Core\Database → app/core/Database.php
    //     App\Controllers\Client\HomeController → app/controllers/client/HomeController.php
    //     App\Models\User → app/models/User.php

    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));

    // Tách namespace: Core\Database → ['Core', 'Database']
    //                 Controllers\Client\HomeController → ['Controllers', 'Client', 'HomeController']
    $parts = explode('\\', $relativeClass);

    // Chuyển phần đầu thành chữ thường (Core → core, Controllers → controllers, Models → models)
    $parts[0] = strtolower($parts[0]);

    // Nếu là Controllers, phần thứ 2 cũng lowercase (Client → client)
    if (count($parts) > 1 && $parts[0] === 'controllers') {
        $parts[1] = strtolower($parts[1]);
    }

    $file = $baseDir . implode('/', $parts) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Khởi tạo session
App\Core\Session::start();

// Lấy config
$config = require __DIR__ . '/../config/config.php';

// Khởi tạo Router
$router = new App\Core\Router();

// Load routes
$loadRoutes = require __DIR__ . '/../config/routes.php';
$loadRoutes($router);

// Dispatch request
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Hỗ trợ PUT/DELETE qua POST _method
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

$router->dispatch($uri, $method);
