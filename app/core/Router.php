<?php
namespace App\Core;

/**
 * URL Router → Controller@action
 */
class Router
{
    private array $routes = [];
    private array $params = [];

    public function get(string $uri, string $handler): void
    {
        $this->addRoute('GET', $uri, $handler);
    }

    public function post(string $uri, string $handler): void
    {
        $this->addRoute('POST', $uri, $handler);
    }

    private function addRoute(string $method, string $uri, string $handler): void
    {
        // Convert {param} to regex named group
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $uri);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method'  => $method,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $uri, string $method): void
    {
        // Strip query string
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }
            
            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named params
                $this->params = array_filter($matches, fn($key) => is_string($key), ARRAY_FILTER_USE_KEY);

                // Merge into $_GET so controller can access via $this->input()
                $_GET = array_merge($_GET, $this->params);

                // Parse handler: "client/HomeController@index"
                [$controllerPath, $action] = explode('@', $route['handler']);

                // Build full namespace
                $parts = explode('/', $controllerPath);
                $subFolder = ucfirst($parts[0]); // client → Client, admin → Admin
                $className = $parts[1];           // HomeController

                $controllerClass = "App\\Controllers\\{$subFolder}\\{$className}";

                if (!class_exists($controllerClass)) {
                    $this->send404("Controller {$controllerClass} not found");
                    return;
                }

                $controller = new $controllerClass();
                if (!method_exists($controller, $action)) {
                    $this->send404("Action {$action} not found in {$controllerClass}");
                    return;
                }

                call_user_func_array([$controller, $action], array_values($this->params));
                return;
            }
        }

        // No matching route
        $this->send404("Route not found: {$method} {$uri}");
    }

    public function getParams(): array
    {
        return $this->params;
    }

    private function send404(string $message): void
    {
        http_response_code(404);
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">'
           . '<title>404 - Not Found</title>'
           . '<style>body{font-family:sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;background:#f3f4f6}'
           . '.box{text-align:center;padding:40px;background:#fff;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,.1)}'
           . 'h1{font-size:72px;color:#1a56db;margin:0}h2{color:#374151}p{color:#6b7280}a{color:#1a56db}</style></head>'
           . '<body><div class="box"><h1>404</h1><h2>Page Not Found</h2>'
           . '<p>' . htmlspecialchars($message) . '</p>'
           . '<p><a href="/">Back to Home</a></p></div></body></html>';
    }
}
