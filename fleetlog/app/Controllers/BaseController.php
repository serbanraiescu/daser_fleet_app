<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\Auth;
use FleetLog\Core\RBAC;

abstract class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        $viewPath = dirname(dirname(__DIR__)) . '/views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            die("View $view not found.");
        }

        extract($data);
        
        $currentUser = \FleetLog\Core\Auth::user();
        
        // Render content to buffer
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        // Check if layout should be used (e.g., skip for login/home)
        if (strpos($view, 'auth/') === 0 || $view === 'home') {
            echo $content;
            return;
        }

        require dirname(dirname(__DIR__)) . '/views/layouts/main.php';
    }

    protected function json(array $data, int $status = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
