<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\Auth;
use FleetLog\Core\DB;

class TenantController extends BaseController
{
    public function dashboard(): void
    {
        $tenantId = Auth::tenantId();
        
        // V1 Basic Stats
        $stats = [
            'vehicles_count' => DB::fetch("SELECT COUNT(*) as count FROM vehicles WHERE tenant_id = ?", [$tenantId])['count'],
            'drivers_count' => DB::fetch("SELECT COUNT(*) as count FROM users WHERE tenant_id = ? AND role = 'driver'", [$tenantId])['count'],
            'active_trips' => DB::fetch("SELECT COUNT(*) as count FROM trips WHERE tenant_id = ? AND status = 'open'", [$tenantId])['count'],
            'recent_damages' => DB::fetch("SELECT COUNT(*) as count FROM damage_reports WHERE tenant_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)", [$tenantId])['count']
        ];

        $this->render('tenant/dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => $stats
        ]);
    }

    public function index(): void
    {
        $tenants = DB::fetchAll("SELECT * FROM tenants ORDER BY created_at DESC");
        $this->render('admin/tenants/index', [
            'title' => 'Manage Tenants',
            'tenants' => $tenants
        ]);
    }

    public function impersonate(int $id): void
    {
        Auth::impersonate($id);
        $this->redirect('/tenant/dashboard');
    }

    public function vehicles(): void
    {
        $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
        $vehicles = $vehicleRepo->all();
        $this->render('tenant/vehicles/index', [
            'title' => 'Manage Vehicles',
            'vehicles' => $vehicles
        ]);
    }

    public function trips(): void
    {
        $tripRepo = new \FleetLog\App\Repositories\TripRepository();
        $trips = $tripRepo->all();
        $this->render('tenant/trips/index', [
            'title' => 'Fleet Trip Logs',
            'trips' => $trips
        ]);
    }
}
