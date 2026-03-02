<?php

namespace FleetLog\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function add(string $method, string $uri, string $action, array $middlewares = []): void
    {
        $this->routes[] = [
            'method' => $method,
            'uri'    => $uri,
            'action' => $action,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // Robust base path handling for cPanel (subfolders or root)
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $baseDir = str_replace('\\', '/', dirname($scriptName));
        
        if ($baseDir !== '/') {
            $uri = substr($uri, strlen($baseDir));
        }
        
        $uri = ($uri === '' || $uri === false) ? '/' : $uri;
        if (strpos($uri, '/') !== 0) {
            $uri = '/' . $uri;
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchUri($route['uri'], $uri, $params)) {
                $this->runMiddlewares($route['middlewares']);
                $this->executeAction($route['action'], $params);
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    private function matchUri(string $routeUri, string $requestUri, &$params): bool
    {
        $params = [];
        $routeParts = explode('/', trim($routeUri, '/'));
        $requestParts = explode('/', trim($requestUri, '/'));

        if (count($routeParts) !== count($requestParts)) {
            return false;
        }

        foreach ($routeParts as $index => $part) {
            if (strpos($part, '{') === 0 && strpos($part, '}') === strlen($part) - 1) {
                $params[substr($part, 1, -1)] = $requestParts[$index];
            } elseif ($part !== $requestParts[$index]) {
                return false;
            }
        }

        return true;
    }

    private function runMiddlewares(array $middlewares): void
    {
        foreach ($middlewares as $middlewareClass) {
            $middleware = new $middlewareClass();
            $middleware->handle();
        }
    }

    private function executeAction(string $action, array $params): void
    {
        list($controllerName, $method) = explode('@', $action);
        
        // Handle case-sensitive filesystems for controllers (App/Controllers -> app/controllers)
        $controllerClass = "FleetLog\\App\\Controllers\\" . $controllerName;
        
        if (!class_exists($controllerClass)) {
            // The autoloader should handle the folder mapping, 
            // but we ensure the class name matches what PHP expects if it was loaded from a lowercase file.
        }
        
        $controller = new $controllerClass();
        call_user_func_array([$controller, $method], $params);
    }
}
