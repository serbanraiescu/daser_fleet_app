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

        // Simple base path handling for cPanel subfolders
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        $uri = str_replace($scriptName, '', $uri);
        $uri = $uri === '' ? '/' : $uri;

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
        $controllerClass = "FleetLog\\App\\Controllers\\" . $controllerName;
        
        $controller = new $controllerClass();
        call_user_func_array([$controller, $method], $params);
    }
}
