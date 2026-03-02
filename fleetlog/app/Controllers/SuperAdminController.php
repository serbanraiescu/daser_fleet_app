<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\Auth;
use FleetLog\Core\DB;

class SuperAdminController extends BaseController
{
    public function tenants(): void
    {
        $tenants = DB::fetchAll("
            SELECT t.*, 
                   (SELECT COUNT(*) FROM vehicles v WHERE v.tenant_id = t.id) as vehicles_count 
            FROM tenants t 
            ORDER BY t.created_at DESC
        ");
        $this->render('admin/tenants/index', [
            'title' => 'System Tenants',
            'tenants' => $tenants
        ]);
    }

    public function impersonate(int $id): void
    {
        Auth::impersonate($id);
        $this->redirect('/tenant/dashboard');
    }

    public function stopImpersonation(): void
    {
        Auth::stopImpersonating();
        $this->redirect('/admin/tenants');
    }
}
