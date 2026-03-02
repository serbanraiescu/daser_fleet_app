<?php

namespace FleetLog\App\Middleware;

use FleetLog\Core\Auth;
use FleetLog\Core\DB;
use FleetLog\Core\RBAC;

class TenantStatusMiddleware
{
    public function handle(): void
    {
        if (RBAC::isSuperAdmin()) {
            return;
        }

        $tenantId = Auth::tenantId();
        if (!$tenantId) {
            return;
        }

        $tenant = DB::fetch("SELECT status FROM tenants WHERE id = ?", [$tenantId]);

        if ($tenant && $tenant['status'] !== 'active') {
            header('Location: /account-suspended');
            exit;
        }
    }
}
