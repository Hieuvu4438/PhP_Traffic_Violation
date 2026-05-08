<?php
namespace App\Core;

/**
 * Base Controller — tất cả controller đều kế thừa
 */
class Controller
{
    /**
     * Render view với layout
     */
    protected function view(string $view, array $data = [], string $layout = 'client'): void
    {
        // Extract data để view dùng trực tiếp
        extract($data);

        // Render nội dung view
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            throw new \Exception("View '{$view}' not found at: {$viewPath}");
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        // Nhúng vào layout
        $layoutPath = __DIR__ . '/../views/layouts/' . $layout . '.php';
        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout '{$layout}' not found at: {$layoutPath}");
        }

        require $layoutPath;
    }

    /**
     * Redirect
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Lấy input từ $_GET hoặc $_POST (đã trim)
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        if (is_string($value)) {
            $value = trim($value);
        }
        return $value;
    }

    /**
     * Kiểm tra đăng nhập — redirect nếu chưa login
     */
    protected function requireLogin(): void
    {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Vui lòng đăng nhập để tiếp tục.');
            $this->redirect('/dang-nhap');
        }
    }

    /**
     * Kiểm tra admin — redirect nếu không phải admin
     */
    protected function requireAdmin(): void
    {
        $this->requireLogin();
        if (!Session::isAdmin()) {
            Session::setFlash('error', 'Bạn không có quyền truy cập khu vực này.');
            $this->redirect('/');
        }
    }

    /**
     * Validate CSRF token cho request POST
     */
    protected function validateCsrf(): bool
    {
        $token = $this->input('csrf_token', '');
        if (!Session::validateCsrf($token)) {
            Session::setFlash('error', 'Phiên làm việc hết hạn, vui lòng thử lại.');
            return false;
        }
        return true;
    }

    /**
     * Trả về JSON response
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
