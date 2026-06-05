<?php
namespace App\Core;

/**
 * Base Controller — all controllers inherit from this
 */
class Controller
{
    /**
     * Render view with layout
     */
    protected function view(string $view, array $data = [], string $layout = 'client'): void
    {
        // Extract data for direct use in view
        extract($data);

        // Render view content
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            throw new \Exception("View '{$view}' not found at: {$viewPath}");
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        // Embed into layout
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
     * Get input from $_GET or $_POST (trimmed)
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
     * Require login — redirect if not logged in
     */
    protected function requireLogin(): void
    {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Please log in to continue.');
            $this->redirect('/dang-nhap');
        }
    }

    /**
     * Require admin — redirect if not admin
     */
    protected function requireAdmin(): void
    {
        $this->requireLogin();
        if (!Session::isAdmin()) {
            Session::setFlash('error', 'You do not have permission to access this area.');
            $this->redirect('/');
        }
    }

    /**
     * Validate CSRF token for POST requests
     */
    protected function validateCsrf(): bool
    {
        $token = $this->input('csrf_token', '');
        if (!Session::validateCsrf($token)) {
            Session::setFlash('error', 'Session expired, please try again.');
            return false;
        }
        return true;
    }

    /**
     * Check if request is AJAX
     */
    protected function isAjax(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
            || ($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json';
    }

    /**
     * Return JSON response
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
