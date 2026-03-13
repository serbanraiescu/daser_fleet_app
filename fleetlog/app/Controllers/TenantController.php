<?php

namespace FleetLog\App\Controllers;

use FleetLog\Core\Auth;
use FleetLog\Core\DB;

class TenantController extends BaseController
{
    public function dashboard(): void
    {
        $tenantId = Auth::tenantId();
        
        if ($tenantId === null) {
            error_log("Dashboard access attempted but Auth::tenantId() is NULL. User ID: " . ($_SESSION['user_id'] ?? 'none'));
            $this->redirect('/login?error=session_error');
        }

        $expenseRepo = new \FleetLog\App\Repositories\ExpenseRepository();
        
        // 1. Service Due Soon (within 1000km)
        $serviceDue = $expenseRepo->getServiceDueVehicles($tenantId, 1000);
        foreach ($serviceDue as &$veh) {
            $lastMaint = $expenseRepo->getLastMaintenance($veh['id']);
            $veh['last_maintenance_notes'] = $lastMaint['notes'] ?? 'No previous notes.';
        }

        // 2. Expiring Documents (RCA, ITP, Rovigneta, Medical Kit, Extinguisher within 30 days)
        $expiringDocs = DB::fetchAll("
            SELECT id, license_plate, make, model, 
                   expiry_rca, expiry_itp, expiry_rovigneta,
                   medical_kit_expiry, extinguisher_expiry
            FROM vehicles 
            WHERE tenant_id = ? 
            AND status != 'archived'
            AND (
                (expiry_rca IS NOT NULL AND expiry_rca <= DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)) OR 
                (expiry_itp IS NOT NULL AND expiry_itp <= DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)) OR 
                (expiry_rovigneta IS NOT NULL AND expiry_rovigneta <= DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)) OR
                (medical_kit_expiry IS NOT NULL AND medical_kit_expiry <= DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)) OR
                (extinguisher_expiry IS NOT NULL AND extinguisher_expiry <= DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY))
            )
            ORDER BY GREATEST(
                COALESCE(expiry_rca, '1970-01-01'), 
                COALESCE(expiry_itp, '1970-01-01'), 
                COALESCE(expiry_rovigneta, '1970-01-01'),
                COALESCE(medical_kit_expiry, '1970-01-01'),
                COALESCE(extinguisher_expiry, '1970-01-01')
            ) ASC
        ", [$tenantId]);

        // 5. Fleet Status Distribution
        $fleetStatus = DB::fetchAll("
            SELECT status, COUNT(*) as count 
            FROM vehicles 
            WHERE tenant_id = ? AND status != 'archived'
            GROUP BY status
        ", [$tenantId]);
        $statusCounts = [];
        foreach ($fleetStatus as $row) { $statusCounts[$row['status']] = $row['count']; }

        // 6. Active Trips
        $activeTrips = DB::fetchAll("
            SELECT t.*, v.license_plate, v.make, v.model, u.name as driver_name 
            FROM trips t 
            JOIN vehicles v ON t.vehicle_id = v.id 
            JOIN users u ON t.driver_id = u.id 
            WHERE t.tenant_id = ? AND t.status = 'open' 
            ORDER BY t.start_time DESC
        ", [$tenantId]);

        // 8. Current Month Expenses
        $currentMonthExpenses = DB::fetch("
            SELECT SUM(cost) as total 
            FROM vehicle_expenses 
            WHERE tenant_id = ? 
            AND MONTH(expense_date) = MONTH(CURRENT_DATE())
            AND YEAR(expense_date) = YEAR(CURRENT_DATE())
        ", [$tenantId])['total'] ?? 0;

        // 9. Top 3 Costly Vehicles (Last 90 days)
        $topCostly = DB::fetchAll("
            SELECT v.id, v.license_plate, v.make, v.model, SUM(e.cost) as total_cost 
            FROM vehicles v 
            JOIN vehicle_expenses e ON v.id = e.vehicle_id 
            WHERE v.tenant_id = ? 
            AND e.expense_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 90 DAY)
            GROUP BY v.id 
            ORDER BY total_cost DESC 
            LIMIT 3
        ", [$tenantId]);

        // 12. Monthly KM Driven
        $monthlyKm = DB::fetch("
            SELECT SUM(end_km - start_km) as total 
            FROM trips 
            WHERE tenant_id = ? 
            AND status = 'closed'
            AND MONTH(end_time) = MONTH(CURRENT_DATE())
            AND YEAR(end_time) = YEAR(CURRENT_DATE())
        ", [$tenantId])['total'] ?? 0;

        $this->render('tenant/dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => [
                'monthly_expenses' => $currentMonthExpenses,
                'monthly_km' => $monthlyKm,
                'active_trips_count' => count($activeTrips),
                'fleet_status' => $statusCounts,
                'total_vehicles' => array_sum($statusCounts)
            ],
            'serviceDue' => $serviceDue,
            'expiringDocs' => $expiringDocs,
            'activeTrips' => $activeTrips,
            'topCostly' => $topCostly
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
        $activeVehicles = $vehicleRepo->getAllNonArchivedByTenant(Auth::tenantId());
        $archivedVehicles = $vehicleRepo->getArchivedByTenant(Auth::tenantId());

        $this->render('tenant/vehicles/index', [
            'title' => 'Manage Vehicles',
            'vehicles' => $activeVehicles,
            'archivedVehicles' => $archivedVehicles
        ]);
    }

    public function showArchiveVehicle(int $id): void
    {
        $tenantId = Auth::tenantId();
        error_log("showArchiveVehicle - ID: $id, Tenant: $tenantId");

        $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
        $vehicle = $vehicleRepo->find($id);

        if (!$vehicle) {
            error_log("showArchiveVehicle - Vehicle not found or not owned by tenant.");
            $this->redirect('/tenant/vehicles?error=vehicle_not_found');
        }

        if ((int)$vehicle['tenant_id'] !== $tenantId) {
            error_log("showArchiveVehicle - Tenant mismatch. Vehicle Tenant: " . $vehicle['tenant_id'] . ", Auth Tenant: " . $tenantId);
            $this->redirect('/tenant/vehicles?error=tenant_mismatch');
        }

        $this->render('tenant/vehicles/archive', [
            'title' => 'Archive / Write-off Vehicle',
            'vehicle' => $vehicle
        ]);
    }

    public function archiveVehicle(int $id): void
    {
        $tenantId = Auth::tenantId();
        error_log("Attempting to archive vehicle ID: $id for Tenant ID: $tenantId");

        $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
        $vehicle = $vehicleRepo->find($id);

        if (!$vehicle) {
            error_log("Archive failed: Vehicle $id not found.");
            $this->redirect('/tenant/vehicles');
        }

        if ((int)$vehicle['tenant_id'] !== $tenantId) {
            error_log("Archive failed: Tenant mismatch. Vehicle Tenant: " . $vehicle['tenant_id'] . ", Auth Tenant: " . $tenantId);
            $this->redirect('/tenant/vehicles');
        }

        $notes = trim($_POST['archive_notes'] ?? '');
        error_log("Archive notes: " . $notes);

        if ($vehicleRepo->archiveVehicle($id, $notes)) {
            error_log("Archive success for vehicle $id");
            $this->redirect('/tenant/vehicles?success=vehicle_archived');
        } else {
            error_log("Archive failed in repository for vehicle $id");
            $this->redirect("/tenant/vehicles/archive/{$id}?error=archiving_failed");
        }
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
            'status' => $_POST['status'] ?? 'active',
            'qr_code' => strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8)),
            'has_triangles' => (int)($_POST['has_triangles'] ?? 0),
            'has_vest' => (int)($_POST['has_vest'] ?? 0),
            'has_jack' => isset($_POST['has_jack']) ? 1 : 0,
            'medical_kit_expiry' => !empty($_POST['medical_kit_expiry']) ? $_POST['medical_kit_expiry'] : null,
            'has_tow_rope' => isset($_POST['has_tow_rope']) ? 1 : 0,
            'has_jumper_cables' => isset($_POST['has_jumper_cables']) ? 1 : 0,
            'extinguisher_expiry' => !empty($_POST['extinguisher_expiry']) ? $_POST['extinguisher_expiry'] : null,
            'has_spare_wheel' => isset($_POST['has_spare_wheel']) ? 1 : 0
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
            'phone' => $_POST['phone'] ?? '',
            'password' => $_POST['password'] ?? '',
            'role' => 'driver',
            'active' => 1,
            'cnp' => $_POST['cnp'] ?? null,
            'id_expiry' => !empty($_POST['id_expiry']) ? $_POST['id_expiry'] : null,
            'license_series' => $_POST['license_series'] ?? null,
            'license_expiry' => !empty($_POST['license_expiry']) ? $_POST['license_expiry'] : null
        ];

        if ($repo->create($data)) {
            $this->redirect('/tenant/drivers');
        } else {
            $this->render('tenant/drivers/create', ['title' => 'Add New Driver', 'error' => 'Failed to save driver. Email might be in use.']);
        }
    }

    public function damages(): void
    {
        $tenantId = Auth::tenantId();
        $damageRepo = new \FleetLog\App\Repositories\DamageReportRepository();
        
        // Mark as seen so notification disappears
        $damageRepo->markAllAsSeen($tenantId);

        $damages = DB::fetchAll("
            SELECT d.*, v.license_plate, u.name as driver_name 
            FROM damage_reports d
            JOIN vehicles v ON d.vehicle_id = v.id
            JOIN users u ON d.driver_id = u.id
            WHERE d.tenant_id = ?
            ORDER BY d.datetime DESC
        ", [$tenantId]);

        $this->render('tenant/damages/index', [
            'title' => 'Damage Reports',
            'damages' => $damages
        ]);
    }

    public function settings(): void
    {
        $tenantId = Auth::tenantId();
        $tenant = DB::fetch("SELECT * FROM tenants WHERE id = ?", [$tenantId]);
        
        $this->render('tenant/settings', [
            'title' => 'Firm Settings',
            'tenant' => $tenant,
            'timezones' => \DateTimeZone::listIdentifiers()
        ]);
    }

    public function updateSettings(): void
    {
        $tenantId = Auth::tenantId();
        $timezone = $_POST['timezone'] ?? 'Europe/Bucharest';
        $tripTypes = $_POST['trip_types'] ?? '';
        $contactPhone = $_POST['contact_phone'] ?? null;
        $notificationPhone = $_POST['notification_phone'] ?? null;
        $notificationEmails = $_POST['notification_emails'] ?? null;

        DB::query("UPDATE tenants SET timezone = ?, trip_types = ?, contact_phone = ?, notification_phone = ?, notification_emails = ? WHERE id = ?", [
            $timezone, $tripTypes, $contactPhone, $notificationPhone, $notificationEmails, $tenantId
        ]);

        $this->redirect('/tenant/settings?success=1');
    }

    public function showEditVehicle(int $id): void
    {
        $repo = new \FleetLog\App\Repositories\VehicleRepository();
        $vehicle = $repo->find($id);
        
        if (!$vehicle) {
            $this->redirect('/tenant/vehicles');
        }

        $this->render('tenant/vehicles/edit', [
            'title' => 'Edit Vehicle',
            'vehicle' => $vehicle
        ]);
    }

    public function updateVehicle(int $id): void
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
            'status' => $_POST['status'] ?? 'active',
            'qr_code' => $_POST['qr_code'] ?? '',
            'has_triangles' => (int)($_POST['has_triangles'] ?? 0),
            'has_vest' => (int)($_POST['has_vest'] ?? 0),
            'has_jack' => isset($_POST['has_jack']) ? 1 : 0,
            'medical_kit_expiry' => !empty($_POST['medical_kit_expiry']) ? $_POST['medical_kit_expiry'] : null,
            'has_tow_rope' => isset($_POST['has_tow_rope']) ? 1 : 0,
            'has_jumper_cables' => isset($_POST['has_jumper_cables']) ? 1 : 0,
            'extinguisher_expiry' => !empty($_POST['extinguisher_expiry']) ? $_POST['extinguisher_expiry'] : null,
            'has_spare_wheel' => isset($_POST['has_spare_wheel']) ? 1 : 0
        ];

        if ($repo->update($id, $data)) {
            $this->redirect('/tenant/vehicles?success=updated');
        } else {
            $this->render('tenant/vehicles/edit', [
                'title' => 'Edit Vehicle',
                'vehicle' => array_merge($data, ['id' => $id]),
                'error' => 'Failed to update vehicle.'
            ]);
        }
    }

    public function showEditDriver(int $id): void
    {
        $repo = new \FleetLog\App\Repositories\UserRepository();
        $driver = $repo->find($id);
        
        if (!$driver || $driver['role'] !== 'driver') {
            $this->redirect('/tenant/drivers');
        }

        $this->render('tenant/drivers/edit', [
            'title' => 'Edit Driver',
            'driver' => $driver
        ]);
    }

    public function updateDriver(int $id): void
    {
        $repo = new \FleetLog\App\Repositories\UserRepository();
        $data = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'active' => (int)($_POST['active'] ?? 0),
            'cnp' => $_POST['cnp'] ?? null,
            'id_expiry' => !empty($_POST['id_expiry']) ? $_POST['id_expiry'] : null,
            'license_series' => $_POST['license_series'] ?? null,
            'license_expiry' => !empty($_POST['license_expiry']) ? $_POST['license_expiry'] : null
        ];

        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }

        if ($repo->update($id, $data)) {
            $this->redirect('/tenant/drivers?success=updated');
        } else {
            $this->render('tenant/drivers/edit', [
                'title' => 'Edit Driver',
                'driver' => array_merge($data, ['id' => $id]),
                'error' => 'Failed to update driver.'
            ]);
        }
    }

    public function quickStatusVehicle(int $id, string $status): void
    {
        if (!in_array($status, ['active', 'inactive', 'service'])) {
            $this->redirect('/tenant/vehicles');
        }

        $repo = new \FleetLog\App\Repositories\VehicleRepository();
        $vehicle = $repo->find($id);

        if ($vehicle) {
            $repo->updateStatus($id, $status);
        }

        $this->redirect('/tenant/vehicles?success=status_updated');
    }

    public function fuelings(): void
    {

        $fuelingRepo = new \FleetLog\App\Repositories\FuelingRepository();
        $fuelings = $fuelingRepo->getByTenant(Auth::tenantId());

        $this->render('tenant/fuelings/index', [
            'title' => 'Fueling Logs',
            'fuelings' => $fuelings
        ]);
    }

    public function showDamage(int $id): void
    {
        $tenantId = Auth::tenantId();
        $damageRepo = new \FleetLog\App\Repositories\DamageReportRepository();
        
        $damage = DB::fetch("
            SELECT d.*, v.license_plate, u.name as driver_name 
            FROM damage_reports d
            JOIN vehicles v ON d.vehicle_id = v.id
            JOIN users u ON d.driver_id = u.id
            WHERE d.id = ? AND d.tenant_id = ?
        ", [$id, $tenantId]);

        if (!$damage) {
            $this->redirect('/tenant/damages');
        }

        $photos = $damageRepo->getPhotos($id);

        $this->render('tenant/damages/edit', [
            'title' => 'Manage Damage Report',
            'damage' => $damage,
            'photos' => $photos
        ]);
    }

    public function updateDamage(int $id): void
    {
        $damageRepo = new \FleetLog\App\Repositories\DamageReportRepository();
        
        $damageRepo->update($id, [
            'status' => $_POST['status'],
            'repair_cost' => $_POST['repair_cost'] ?: 0,
            'admin_notes' => $_POST['admin_notes']
        ]);

        $this->redirect('/tenant/damages?success=damage_updated');
    }

    public function expenses(): void
    {
        $tenantId = Auth::tenantId();
        $expenseRepo = new \FleetLog\App\Repositories\ExpenseRepository();
        
        $expenses = $expenseRepo->getByTenant($tenantId);
        $serviceDue = $expenseRepo->getServiceDueVehicles($tenantId, 1000); // vehicles within 1000km of service or past due

        // Enhance serviceDue with last maintenance notes
        foreach ($serviceDue as &$veh) {
            $lastMaint = $expenseRepo->getLastMaintenance($veh['id']);
            $veh['last_maintenance_notes'] = $lastMaint['notes'] ?? 'No previous notes recorded.';
            $veh['last_maintenance_date'] = $lastMaint['expense_date'] ?? null;
        }

        $this->render('tenant/expenses/index', [
            'title' => 'Vehicle Expenses & Maintenance',
            'expenses' => $expenses,
            'serviceDue' => $serviceDue
        ]);
    }

    public function mechanicReport(int $id): void
    {
        $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
        $vehicle = $vehicleRepo->find($id);

        if (!$vehicle || (int)$vehicle['tenant_id'] !== (int)Auth::tenantId()) {
            $this->redirect('/tenant/vehicles');
        }

        $expenseRepo = new \FleetLog\App\Repositories\ExpenseRepository();
        $damageRepo = new \FleetLog\App\Repositories\DamageReportRepository();

        $history = $expenseRepo->getVehicleHistory($id);
        $activeDamages = $damageRepo->getActiveByVehicle($id);

        $this->render('tenant/vehicles/mechanic_report', [
            'title' => 'Mechanic Report: ' . $vehicle['license_plate'],
            'vehicle' => $vehicle,
            'history' => $history,
            'activeDamages' => $activeDamages
        ]);
    }

    public function showAddExpenseGeneral(): void
    {
        $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
        $vehicles = $vehicleRepo->getActiveByTenant(Auth::tenantId());

        $this->render('tenant/expenses/add_general', [
            'title' => 'Add Vehicle Expense',
            'vehicles' => $vehicles
        ]);
    }

    public function storeExpenseGeneral(): void
    {
        $vehicleId = (int)$_POST['vehicle_id'];
        
        $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
        $vehicle = $vehicleRepo->find($vehicleId);

        if (!$vehicle || (int)$vehicle['tenant_id'] !== (int)Auth::tenantId()) {
            $this->redirect('/tenant/expenses');
        }

        $expenseRepo = new \FleetLog\App\Repositories\ExpenseRepository();
        
        try {
            $expenseRepo->create([
                'vehicle_id' => $vehicleId,
                'expense_type' => $_POST['expense_type'],
                'name' => $_POST['name'],
                'cost' => $_POST['cost'],
                'odometer_at_expense' => $_POST['odometer_at_expense'] !== '' ? $_POST['odometer_at_expense'] : null,
                'expense_date' => $_POST['expense_date'],
                'notes' => trim($_POST['notes'])
            ]);

            if (!empty($_POST['next_service_km'])) {
                $expenseRepo->updateNextServiceKm($vehicleId, (int)$_POST['next_service_km']);
            }

            $this->redirect('/tenant/expenses?success=expense_added');
        } catch (\Exception $e) {
            $this->redirect('/tenant/expenses?error=save_failed');
        }
    }

    public function showAddExpense(int $id): void
    {
        $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
        $vehicle = $vehicleRepo->find($id);

        if (!$vehicle || $vehicle['tenant_id'] !== Auth::tenantId()) {
            $this->redirect('/tenant/vehicles');
        }

        $this->render('tenant/expenses/add', [
            'title' => 'Add Vehicle Expense',
            'vehicle' => $vehicle
        ]);
    }

    public function storeExpense(int $id): void
    {
        $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
        $vehicle = $vehicleRepo->find($id);

        if (!$vehicle || (int)$vehicle['tenant_id'] !== (int)Auth::tenantId()) {
            $this->redirect('/tenant/vehicles');
        }

        $expenseRepo = new \FleetLog\App\Repositories\ExpenseRepository();
        
        try {
            $expenseRepo->create([
                'vehicle_id' => $id,
                'expense_type' => $_POST['expense_type'],
                'name' => $_POST['name'],
                'cost' => $_POST['cost'],
                'odometer_at_expense' => $_POST['odometer_at_expense'] !== '' ? $_POST['odometer_at_expense'] : null,
                'expense_date' => $_POST['expense_date'],
                'notes' => trim($_POST['notes'])
            ]);

            // If a next_service_km was provided, update the vehicle
            if (!empty($_POST['next_service_km'])) {
                $expenseRepo->updateNextServiceKm($id, (int)$_POST['next_service_km']);
            }

            $this->redirect('/tenant/expenses?success=expense_added');
        } catch (\Exception $e) {
            $this->redirect('/tenant/expenses?error=save_failed');
        }
    }
}
