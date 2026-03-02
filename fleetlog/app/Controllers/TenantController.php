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

    public function drivers(): void
    {
        $tenantId = Auth::tenantId();
        $drivers = DB::fetchAll("SELECT * FROM users WHERE tenant_id = ? AND role = 'driver' ORDER BY name ASC", [$tenantId]);
        
        $this->render('tenant/drivers/index', [
            'title' => 'Fleet Drivers',
            'drivers' => $drivers
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
        $tenantId = Auth::tenantId();
        $trips = DB::fetchAll("
            SELECT t.*, u.name as driver_name, v.license_plate 
            FROM trips t
            LEFT JOIN users u ON t.driver_id = u.id
            LEFT JOIN vehicles v ON t.vehicle_id = v.id
            WHERE t.tenant_id = ?
            ORDER BY t.start_time DESC
        ", [$tenantId]);

        $this->render('tenant/trips/index', [
            'title' => 'Fleet Trip Logs',
            'trips' => $trips
        ]);
    }

    public function showAddVehicle(): void
    {
        $this->render('tenant/vehicles/create', ['title' => 'Add New Vehicle']);
    }

    public function storeVehicle(): void
    {
        $repo = new \FleetLog\App\Repositories\VehicleRepository();
        $data = [
            'license_plate' => strtoupper($_POST['license_plate'] ?? ''),
            'make' => $_POST['make'] ?? '',
            'model' => $_POST['model'] ?? '',
            'expiry_rca' => !empty($_POST['expiry_rca']) ? $_POST['expiry_rca'] : null,
            'expiry_itp' => !empty($_POST['expiry_itp']) ? $_POST['expiry_itp'] : null,
            'expiry_rovigneta' => !empty($_POST['expiry_rovigneta']) ? $_POST['expiry_rovigneta'] : null,
            'current_odometer' => (int)($_POST['current_odometer'] ?? 0),
            'is_active' => 1
        ];

        if ($repo->create($data)) {
            $this->redirect('/tenant/vehicles');
        } else {
            $this->render('tenant/vehicles/create', ['title' => 'Add New Vehicle', 'error' => 'Failed to save vehicle. Check if plate exists.']);
        }
    }

    public function showAddDriver(): void
    {
        $this->render('tenant/drivers/create', ['title' => 'Add New Driver']);
    }

    public function storeDriver(): void
    {
        $repo = new \FleetLog\App\Repositories\UserRepository();
        $data = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'role' => 'driver',
            'active' => 1
        ];

        if ($repo->create($data)) {
            $this->redirect('/tenant/drivers');
        } else {
            $this->render('tenant/drivers/create', ['title' => 'Add New Driver', 'error' => 'Failed to save driver. Email might be in use.']);
        }
    }
}
