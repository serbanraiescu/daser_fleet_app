<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\Auth;
use FleetLog\Core\RBAC;
use FleetLog\Core\DB;

abstract class BaseController
{
    public function __construct()
    {
        $tenantId = Auth::tenantId();
        $lang = 'ro'; // Default

        if ($tenantId !== null) {
            // Cache language in session for performance if needed, 
            // but for now fetch it or use a session-stored value if we update Auth
            $tenant = DB::fetch("SELECT language FROM tenants WHERE id = ?", [$tenantId]);
            $lang = $tenant['language'] ?? 'ro';
        }

        \FleetLog\Core\LanguageService::load($lang);
    }

    protected function render(string $view, array $data = []): void
    {
        $viewPath = dirname(dirname(__DIR__)) . '/views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            die("View $view not found.");
        }

        extract($data);
        
        $currentUser = \FleetLog\Core\Auth::user();
        $newDamagesCount = 0;

        if ($currentUser && (\FleetLog\Core\RBAC::isTenantAdmin() || \FleetLog\Core\RBAC::isSuperAdmin())) {
            $tenantId = \FleetLog\Core\Auth::tenantId();
            if ($tenantId !== null) {
                $damageRepo = new \FleetLog\App\Repositories\DamageReportRepository();
                $newDamagesCount = $damageRepo->getNewCount($tenantId);
            }
        }
        
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
