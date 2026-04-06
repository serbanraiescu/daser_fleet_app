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
        $timezone = 'Europe/Bucharest'; // Default

        if ($tenantId !== null) {
            // Fetch both language and timezone
            $tenant = DB::fetch("SELECT language, timezone FROM tenants WHERE id = ?", [$tenantId]);
            $lang = $tenant['language'] ?? 'ro';
            $timezone = $tenant['timezone'] ?? 'Europe/Bucharest';
        }

        // Apply PHP Timezone
        date_default_timezone_set($timezone);
        
        // Sync MySQL Session Timezone (using offset for 100% compatibility)
        $now = new \DateTime();
        $offset = $now->format('P'); 
        DB::query("SET time_zone = ?", [$offset]);

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

        // Whitelist of views that provide their own standalone HTML layout (e.g. for printing)
        $standaloneViews = [
            'home',
            'tenant/reports/inventory_shopping_list',
            'tenant/vehicles/qr_print',
            'tenant/documents/inventory_print',
            'tenant/documents/protocol_print',
            'tenant/fuelings/receipts',
            'tenant/fuelings/report',
            'tenant/reports/print_performance'
        ];

        // Check if layout should be used (e.g., skip for login/standalone/print pages)
        if (strpos($view, 'auth/') === 0 || in_array($view, $standaloneViews)) {
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
