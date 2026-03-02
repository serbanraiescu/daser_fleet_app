<?php

namespace FleetLog\App\Middleware;

use FleetLog\Core\Auth;
use FleetLog\Core\RBAC;

class SuperAdminMiddleware
{
    public function handle(): void
    {
        if (!RBAC::isSuperAdmin()) {
            http_response_code(403);
            echo "Forbidden: Super Admin access required.";
            exit;
        }
    }
}
