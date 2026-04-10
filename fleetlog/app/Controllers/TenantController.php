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
        // Combined search for both vehicles AND drivers based on tenant equipment config
        $expiringDocs = DB::fetchAll("
            SELECT 'vehicle' as source_type, id, license_plate as entity_label, make, model, 
                   expiry_rca, expiry_itp, expiry_rovigneta, medical_kit_expiry, extinguisher_expiry
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
            UNION ALL
            SELECT 'driver' as source_type, id, name as entity_label, NULL as make, NULL as model,
                   NULL as expiry_rca, NULL as expiry_itp, NULL as expiry_rovigneta, medical_kit_expiry, extinguisher_expiry
            FROM users
            WHERE tenant_id = ? AND role = 'driver' AND active = 1
            AND (
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
        ", [$tenantId, $tenantId]);

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

        // 8. Current Month Expenses (Legacy + Timeline)
        $currentMonthExpenses = DB::fetch("
            SELECT (
                (SELECT IFNULL(SUM(cost), 0) FROM vehicle_expenses WHERE tenant_id = ? AND MONTH(expense_date) = MONTH(CURRENT_DATE()) AND YEAR(expense_date) = YEAR(CURRENT_DATE())) +
                (SELECT IFNULL(SUM(cost), 0) FROM vehicle_events WHERE tenant_id = ? AND MONTH(event_date) = MONTH(CURRENT_DATE()) AND YEAR(event_date) = YEAR(CURRENT_DATE()))
            ) as total
        ", [$tenantId, $tenantId])['total'] ?? 0;

        // 9. Top 3 Costly Vehicles (Last 90 days) - Legacy + Timeline + Fuel (+ Damages if they have cost)
        $topCostly = DB::fetchAll("
            SELECT v.id, v.license_plate, v.make, v.model, 
                (
                    (SELECT IFNULL(SUM(cost), 0) FROM vehicle_expenses WHERE vehicle_id = v.id AND expense_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 90 DAY)) +
                    (SELECT IFNULL(SUM(cost), 0) FROM vehicle_events WHERE vehicle_id = v.id AND event_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 90 DAY)) +
                    (SELECT IFNULL(SUM(total_price), 0) FROM fuelings WHERE vehicle_id = v.id AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 90 DAY)) +
                    (SELECT IFNULL(SUM(repair_cost), 0) FROM damage_reports WHERE vehicle_id = v.id AND datetime >= DATE_SUB(CURRENT_DATE(), INTERVAL 90 DAY))
                ) as total_cost 
            FROM vehicles v 
            WHERE v.tenant_id = ? 
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

        $tenant = DB::fetch("SELECT * FROM tenants WHERE id = ?", [$tenantId]);

        $this->render('tenant/dashboard', [
            'title' => 'Admin Dashboard',
            'tenant' => $tenant,
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
        $userRepo = new \FleetLog\App\Repositories\UserRepository();
        $drivers = $userRepo->getDrivers();
        $archivedDrivers = $userRepo->getArchivedByTenant($tenantId);
        
        $this->render('tenant/drivers/index', [
            'title' => 'Fleet Drivers',
            'drivers' => $drivers,
            'archivedDrivers' => $archivedDrivers
        ]);
    }

    public function impersonate(int $id): void
    {
        Auth::impersonate($id);
        $this->redirect('/tenant/dashboard');
    }

    public function vehicles(): void
    {
        $tenantId = Auth::tenantId();
        $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
        $activeVehicles = $vehicleRepo->getAllNonArchivedByTenant($tenantId);
        $archivedVehicles = $vehicleRepo->getArchivedByTenant($tenantId);
        $tenant = DB::fetch("SELECT * FROM tenants WHERE id = ?", [$tenantId]);

        $this->render('tenant/vehicles/index', [
            'title' => 'Manage Vehicles',
            'vehicles' => $activeVehicles,
            'archivedVehicles' => $archivedVehicles,
            'tenant' => $tenant
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
        $selectedDate = $_GET['date'] ?? date('Y-m-d');
        
        $trips = DB::fetchAll("
            SELECT t.*, u.name as driver_name, v.license_plate 
            FROM trips t
            LEFT JOIN users u ON t.driver_id = u.id
            LEFT JOIN vehicles v ON t.vehicle_id = v.id
            WHERE t.tenant_id = ? AND DATE(t.start_time) = ?
            ORDER BY t.start_time DESC
        ", [$tenantId, $selectedDate]);

        // Calculate mini-report stats for the selected day
        $stats = [
            'total_trips' => count($trips),
            'open_trips' => 0,
            'total_km' => 0
        ];

        foreach ($trips as $t) {
            if ($t['status'] === 'open') {
                $stats['open_trips']++;
            }
            if ($t['end_km'] !== null) {
                $stats['total_km'] += ($t['end_km'] - $t['start_km']);
            }
        }

        // Check for open trips from PREVIOUS days
        $pendingDays = DB::fetchAll("
            SELECT DISTINCT DATE(start_time) as date 
            FROM trips 
            WHERE tenant_id = ? AND DATE(start_time) < ? AND status = 'open'
            ORDER BY date ASC
        ", [$tenantId, $selectedDate]);

        $this->render('tenant/trips/index', [
            'title' => 'Fleet Trip Logs',
            'trips' => $trips,
            'selectedDate' => $selectedDate,
            'stats' => $stats,
            'pendingDays' => array_column($pendingDays, 'date')
        ]);
    }

    public function showAddVehicle(): void
    {
        $tenant = DB::fetch("SELECT equipment_config FROM tenants WHERE id = ?", [Auth::tenantId()]);
        $this->render('tenant/vehicles/create', [
            'title' => 'Add New Vehicle',
            'equipment_config' => json_decode($tenant['equipment_config'] ?? '[]', true)
        ]);
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
        $tenant = DB::fetch("SELECT equipment_config FROM tenants WHERE id = ?", [Auth::tenantId()]);
        $this->render('tenant/drivers/create', [
            'title' => 'Add New Driver',
            'equipment_config' => json_decode($tenant['equipment_config'] ?? '[]', true)
        ]);
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
            'license_expiry' => !empty($_POST['license_expiry']) ? $_POST['license_expiry'] : null,
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
        $language = $_POST['language'] ?? 'ro';
        $tripTypes = $_POST['trip_types'] ?? '';
        $contactPhone = $_POST['contact_phone'] ?? null;
        $notificationPhone = $_POST['notification_phone'] ?? null;
        $notificationEmails = $_POST['notification_emails'] ?? null;
        $signupEnabled = isset($_POST['signup_enabled']) ? 1 : 0;
        $dailyReportEnabled = isset($_POST['daily_report_enabled']) ? 1 : 0;
        $dailyReportPhoneType = $_POST['daily_report_phone_type'] ?? 'notification';
        $openTripReminderEnabled = isset($_POST['open_trip_reminder_enabled']) ? 1 : 0;
        $equipmentConfig = $_POST['equipment_config'] ?? [];
        $equipmentJson = json_encode($equipmentConfig);

        DB::query("UPDATE tenants SET timezone = ?, language = ?, trip_types = ?, contact_phone = ?, notification_phone = ?, notification_emails = ?, signup_enabled = ?, daily_report_enabled = ?, daily_report_phone_type = ?, open_trip_reminder_enabled = ?, equipment_config = ? WHERE id = ?", [
            $timezone, $language, $tripTypes, $contactPhone, $notificationPhone, $notificationEmails, $signupEnabled, $dailyReportEnabled, $dailyReportPhoneType, $openTripReminderEnabled, $equipmentJson, $tenantId
        ]);

        $this->redirect('/tenant/settings?success=1');
    }

    public function testDailyReport(): void
    {
        $tenantId = \FleetLog\Core\Auth::tenantId();
        \FleetLog\Core\SMSService::sendDailyTenantReports($tenantId);
        $this->redirect('/tenant/settings?success=test_sent#notifications');
    }

    public function regenerateSignupToken(): void
    {
        $tenantId = \FleetLog\Core\Auth::tenantId();
        $token = bin2hex(random_bytes(16));
        
        DB::query("UPDATE tenants SET signup_token = ? WHERE id = ?", [$token, $tenantId]);
        
        $this->redirect('/tenant/settings?success=token_regenerated#onboarding');
    }

    public function approveDriver(int $id): void
    {
        $tenantId = \FleetLog\Core\Auth::tenantId();
        $driver = DB::fetch("SELECT * FROM users WHERE id = ? AND tenant_id = ? AND role = 'driver'", [$id, $tenantId]);
        
        if ($driver) {
            DB::query("UPDATE users SET active = 1 WHERE id = ?", [$id]);
            $this->redirect('/tenant/drivers?success=approved');
        } else {
            $this->redirect('/tenant/drivers?error=not_found');
        }
    }

    public function showArchiveDriver(int $id): void
    {
        $tenantId = Auth::tenantId();
        $userRepo = new \FleetLog\App\Repositories\UserRepository();
        $driver = $userRepo->find($id);

        if (!$driver || (int)$driver['tenant_id'] !== $tenantId || $driver['role'] !== 'driver') {
            $this->redirect('/tenant/drivers');
        }

        $this->render('tenant/drivers/archive', [
            'title' => 'Archive Driver',
            'driver' => $driver
        ]);
    }

    public function archiveDriver(int $id): void
    {
        $tenantId = Auth::tenantId();
        $userRepo = new \FleetLog\App\Repositories\UserRepository();
        $driver = $userRepo->find($id);

        if (!$driver || (int)$driver['tenant_id'] !== $tenantId || $driver['role'] !== 'driver') {
            $this->redirect('/tenant/drivers');
        }

        $notes = trim($_POST['archive_notes'] ?? '');
        
        if ($userRepo->archive($id, $notes, $tenantId)) {
            $this->redirect('/tenant/drivers?success=driver_archived');
        } else {
            $this->redirect('/tenant/drivers?error=archive_failed');
        }
    }

    public function restoreDriver(int $id): void
    {
        $tenantId = Auth::tenantId();
        $userRepo = new \FleetLog\App\Repositories\UserRepository();
        $driver = $userRepo->find($id);

        if (!$driver || (int)$driver['tenant_id'] !== $tenantId || $driver['role'] !== 'driver') {
            $this->redirect('/tenant/drivers');
        }

        if ($userRepo->restore($id, $tenantId)) {
            $this->redirect('/tenant/drivers?success=driver_restored');
        } else {
            $this->redirect('/tenant/drivers?error=restore_failed');
        }
    }

    public function showEditVehicle(int $id): void
    {
        $repo = new \FleetLog\App\Repositories\VehicleRepository();
        $vehicle = $repo->find($id);
        
        if (!$vehicle) {
            $this->redirect('/tenant/vehicles');
        }

        $tenant = DB::fetch("SELECT equipment_config FROM tenants WHERE id = ?", [Auth::tenantId()]);

        $this->render('tenant/vehicles/edit', [
            'title' => 'Edit Vehicle',
            'vehicle' => $vehicle,
            'equipment_config' => json_decode($tenant['equipment_config'] ?? '[]', true)
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

        $tenant = DB::fetch("SELECT equipment_config FROM tenants WHERE id = ?", [Auth::tenantId()]);

        $this->render('tenant/drivers/edit', [
            'title' => 'Edit Driver',
            'driver' => $driver,
            'equipment_config' => json_decode($tenant['equipment_config'] ?? '[]', true)
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
            'license_expiry' => !empty($_POST['license_expiry']) ? $_POST['license_expiry'] : null,
            'has_triangles' => (int)($_POST['has_triangles'] ?? 0),
            'has_vest' => (int)($_POST['has_vest'] ?? 0),
            'has_jack' => isset($_POST['has_jack']) ? 1 : 0,
            'medical_kit_expiry' => !empty($_POST['medical_kit_expiry']) ? $_POST['medical_kit_expiry'] : null,
            'has_tow_rope' => isset($_POST['has_tow_rope']) ? 1 : 0,
            'has_jumper_cables' => isset($_POST['has_jumper_cables']) ? 1 : 0,
            'extinguisher_expiry' => !empty($_POST['extinguisher_expiry']) ? $_POST['extinguisher_expiry'] : null,
            'has_spare_wheel' => isset($_POST['has_spare_wheel']) ? 1 : 0
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
        $tenantId = Auth::tenantId();
        $month = (int)($_GET['month'] ?? date('m'));
        $year = (int)($_GET['year'] ?? date('Y'));

        $fuelingRepo = new \FleetLog\App\Repositories\FuelingRepository();
        $fuelings = $fuelingRepo->getByPeriod($tenantId, $month, $year);

        $this->render('tenant/fuelings/index', [
            'title' => 'Fueling Logs',
            'fuelings' => $fuelings,
            'selected_month' => $month,
            'selected_year' => $year
        ]);
    }

    public function fuelingReport(): void
    {
        $tenantId = Auth::tenantId();
        $month = (int)($_GET['month'] ?? date('m'));
        $year = (int)($_GET['year'] ?? date('Y'));

        $fuelingRepo = new \FleetLog\App\Repositories\FuelingRepository();
        $fuelings = $fuelingRepo->getByPeriod($tenantId, $month, $year);

        $this->render('tenant/fuelings/report', [
            'title' => 'Fueling Report - ' . date('F Y', mktime(0, 0, 0, $month, 1, $year)),
            'fuelings' => $fuelings,
            'month' => $month,
            'year' => $year
        ]);
    }

    public function fuelingReceipts(): void
    {
        $tenantId = Auth::tenantId();
        $month = (int)($_GET['month'] ?? date('m'));
        $year = (int)($_GET['year'] ?? date('Y'));

        $fuelingRepo = new \FleetLog\App\Repositories\FuelingRepository();
        $fuelings = $fuelingRepo->getByPeriod($tenantId, $month, $year);

        $this->render('tenant/fuelings/receipts', [
            'title' => 'Fueling Receipts - ' . date('F Y', mktime(0, 0, 0, $month, 1, $year)),
            'fuelings' => $fuelings,
            'month' => $month,
            'year' => $year
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
    public function qrPrint(): void
    {
        $tenantId = Auth::tenantId();
        $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
        $vehicles = $vehicleRepo->getActiveByTenant($tenantId);

        $this->render('tenant/vehicles/qr_print', [
            'title' => 'Print QR Codes',
            'vehicles' => $vehicles
        ]);
    }

    public function documents(): void
    {
        $tenantId = Auth::tenantId();
        $docRepo = new \FleetLog\App\Repositories\DocumentRepository();
        $reports = $docRepo->getAllHandoverByTenant($tenantId);

        $this->render('tenant/documents/index', [
            'title' => __('documents'),
            'reports' => $reports
        ]);
    }

    public function showHandoverForm(): void
    {
        $tenantId = Auth::tenantId();
        $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
        $userRepo = new \FleetLog\App\Repositories\UserRepository();

        $vehicles = $vehicleRepo->getActiveByTenant($tenantId);
        $drivers = $userRepo->getByTenantAndRole($tenantId, 'driver');

        $this->render('tenant/documents/handover_create', [
            'title' => __('new_handover'),
            'vehicles' => $vehicles,
            'drivers' => $drivers
        ]);
    }

    public function generateProtocol(): void
    {
        $tenantId = Auth::tenantId();
        $docRepo = new \FleetLog\App\Repositories\DocumentRepository();
        $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
        
        $vehicleId = (int)$_POST['vehicle_id'];
        $vehicle = $vehicleRepo->find($vehicleId);

        if (!$vehicle || (int)$vehicle['tenant_id'] !== $tenantId) {
            $this->redirect('/tenant/documents/handover/add?error=invalid_vehicle');
        }

        $data = [
            'tenant_id' => $tenantId,
            'document_number' => $docRepo->generateDocumentNumber(),
            'vehicle_id' => $vehicleId,
            'driver_id' => (int)$_POST['driver_id'],
            'vehicle_plate' => $_POST['vehicle_plate'],
            'vehicle_model' => $_POST['vehicle_model'],
            'odometer' => (int)$_POST['odometer'],
            'fuel_level' => $_POST['fuel_level'],
            'doc_registration' => isset($_POST['doc_registration']) ? 1 : 0,
            'doc_insurance' => isset($_POST['doc_insurance']) ? 1 : 0,
            'doc_itp' => isset($_POST['doc_itp']) ? 1 : 0,
            'doc_rovinieta' => isset($_POST['doc_rovinieta']) ? 1 : 0,
            'aesthetic_condition' => $_POST['aesthetic_condition'],
            'mechanical_condition' => $_POST['mechanical_condition'],
            'has_triangles' => $vehicle['has_triangles'] ?? 0,
            'has_vest' => $vehicle['has_vest'] ?? 0,
            'has_jack' => $vehicle['has_jack'] ?? 0,
            'has_tow_rope' => $vehicle['has_tow_rope'] ?? 0,
            'has_jumper_cables' => $vehicle['has_jumper_cables'] ?? 0,
            'has_spare_wheel' => $vehicle['has_spare_wheel'] ?? 0,
            'medical_kit_expiry' => $vehicle['medical_kit_expiry'] ?? null,
            'extinguisher_expiry' => $vehicle['extinguisher_expiry'] ?? null,
            'notes' => $_POST['notes'] ?? ''
        ];

        $id = $docRepo->createHandover($data);

        if ($id) {
            $this->redirect("/tenant/documents/handover/view/$id");
        } else {
            $this->redirect('/tenant/documents/handover/add?error=failed_to_save');
        }
    }

    public function showInventoryProtocol(): void
    {
        $tenantId = Auth::tenantId();
        $userRepo = new \FleetLog\App\Repositories\UserRepository();
        $drivers = $userRepo->getByTenantAndRole($tenantId, 'driver');

        $this->render('tenant/documents/inventory_create', [
            'title' => 'Process Verbal Inventar (Șofer)',
            'drivers' => $drivers
        ]);
    }

    public function generateInventoryProtocol(): void
    {
        $tenantId = Auth::tenantId();
        $docRepo = new \FleetLog\App\Repositories\DocumentRepository();
        $userRepo = new \FleetLog\App\Repositories\UserRepository();
        
        $driverId = (int)$_POST['driver_id'];
        $driver = $userRepo->find($driverId);

        if (!$driver || (int)$driver['tenant_id'] !== $tenantId) {
            $this->redirect('/tenant/documents/inventory/add?error=invalid_driver');
        }

        $data = [
            'tenant_id' => $tenantId,
            'document_number' => $docRepo->generateDocumentNumber('INV'),
            'vehicle_id' => null,
            'driver_id' => $driverId,
            'report_type' => 'inventory',
            'notes' => $_POST['notes'] ?? '',
            'has_triangles' => $driver['has_triangles'] ?? 0,
            'has_vest' => $driver['has_vest'] ?? 0,
            'has_jack' => $driver['has_jack'] ?? 0,
            'has_tow_rope' => $driver['has_tow_rope'] ?? 0,
            'has_jumper_cables' => $driver['has_jumper_cables'] ?? 0,
            'has_spare_wheel' => $driver['has_spare_wheel'] ?? 0,
            'medical_kit_expiry' => $driver['medical_kit_expiry'] ?? null,
            'extinguisher_expiry' => $driver['extinguisher_expiry'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $id = $docRepo->createHandover($data);

        if ($id) {
            $this->redirect("/tenant/documents/inventory/view/$id");
        } else {
            $this->redirect('/tenant/documents/inventory/add?error=failed_to_save');
        }
    }

    public function viewInventoryProtocol(int $id): void
    {
        $tenantId = Auth::tenantId();
        $docRepo = new \FleetLog\App\Repositories\DocumentRepository();
        $report = $docRepo->findHandover($id, $tenantId);

        if (!$report || $report['report_type'] !== 'inventory') {
            $this->redirect('/tenant/documents');
        }

        $userRepo = new \FleetLog\App\Repositories\UserRepository();
        $driver = $userRepo->find($report['driver_id']);

        $this->render('tenant/documents/inventory_print', [
            'title' => 'Inventar: ' . $report['document_number'],
            'report' => $report,
            'driver' => $driver
        ]);
    }

    public function viewProtocol(int $id): void
    {
        $tenantId = Auth::tenantId();
        $docRepo = new \FleetLog\App\Repositories\DocumentRepository();
        $report = $docRepo->findHandover($id, $tenantId);

        if (!$report) {
            $this->redirect('/tenant/documents');
        }

        $this->render('tenant/documents/protocol_print', [
            'title' => $report['document_number'],
            'report' => $report
        ]);
    }

    public function inventoryShoppingList(): void
    {
        $tenantId = Auth::tenantId();
        $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
        $vehicles = $vehicleRepo->getActiveByTenant($tenantId);

        $shoppingList = [];
        $summary = [
            'triangles' => 0,
            'vests' => 0,
            'jacks' => 0,
            'tow_ropes' => 0,
            'jumper_cables' => 0,
            'spare_wheels' => 0,
            'med_kits' => 0,
            'extinguishers' => 0
        ];

        foreach ($vehicles as $vehicle) {
            $missing = [];
            
            // Triangles (need 2)
            $triCount = (int)($vehicle['has_triangles'] ?? 0);
            if ($triCount < 2) {
                $missingCount = 2 - $triCount;
                $missing[] = "Triunghiuri: Lipsește $missingCount buc.";
                $summary['triangles'] += $missingCount;
            }

            // Vests (need 1)
            if ((int)($vehicle['has_vest'] ?? 0) < 1) {
                $missing[] = "Vestă Reflectorizantă";
                $summary['vests']++;
            }

            // Jack
            if (empty($vehicle['has_jack'])) {
                $missing[] = "Cric";
                $summary['jacks']++;
            }

            // Tow Rope
            if (empty($vehicle['has_tow_rope'])) {
                $missing[] = "Șufă Tractare";
                $summary['tow_ropes']++;
            }

            // Jumper Cables
            if (empty($vehicle['has_jumper_cables'])) {
                $missing[] = "Cabluri Curent";
                $summary['jumper_cables']++;
            }

            // Spare Wheel
            if (isset($vehicle['has_spare_wheel']) && !$vehicle['has_spare_wheel']) {
                $missing[] = "Roată Rezervă";
                $summary['spare_wheels']++;
            }

            // Med Kit
            if (empty($vehicle['medical_kit_expiry']) || $vehicle['medical_kit_expiry'] < date('Y-m-d')) {
                $status = empty($vehicle['medical_kit_expiry']) ? "Lipsă" : "Expirată (" . date('d.m.y', strtotime($vehicle['medical_kit_expiry'])) . ")";
                $missing[] = "Trusă Medicală ($status)";
                $summary['med_kits']++;
            }

            // Extinguisher
            if (empty($vehicle['extinguisher_expiry']) || $vehicle['extinguisher_expiry'] < date('Y-m-d')) {
                $status = empty($vehicle['extinguisher_expiry']) ? "Lipsă" : "Expirat (" . date('d.m.y', strtotime($vehicle['extinguisher_expiry'])) . ")";
                $missing[] = "Stingător ($status)";
                $summary['extinguishers']++;
            }

            if (!empty($missing)) {
                $shoppingList[] = [
                    'vehicle' => $vehicle['make'] . ' ' . $vehicle['model'] . ' (' . $vehicle['license_plate'] . ')',
                    'missing' => $missing
                ];
            }
        }

        // Check Driver Custody
        $tenantRaw = DB::fetch("SELECT equipment_config FROM tenants WHERE id = ?", [$tenantId]);
        $eqConfig = json_decode($tenantRaw['equipment_config'] ?? '[]', true);
        
        $userRepo = new \FleetLog\App\Repositories\UserRepository();
        $drivers = $userRepo->getByTenantAndRole($tenantId, 'driver');

        foreach ($drivers as $driver) {
            $missing = [];
            
            if (($eqConfig['triangles'] ?? 'vehicle') === 'driver' && (int)$driver['has_triangles'] < 2) {
                $count = 2 - (int)$driver['has_triangles'];
                $missing[] = "Triunghiuri: Lipsește $count buc.";
                $summary['triangles'] += $count;
            }

            if (($eqConfig['vest'] ?? 'vehicle') === 'driver' && (int)$driver['has_vest'] < 1) {
                $missing[] = "Vestă Reflectorizantă";
                $summary['vests']++;
            }

            if (($eqConfig['jack'] ?? 'vehicle') === 'driver' && empty($driver['has_jack'])) {
                $missing[] = "Cric";
                $summary['jacks']++;
            }

            if (($eqConfig['tow_rope'] ?? 'vehicle') === 'driver' && empty($driver['has_tow_rope'])) {
                $missing[] = "Șufă Tractare";
                $summary['tow_ropes']++;
            }

            if (($eqConfig['jumper_cables'] ?? 'vehicle') === 'driver' && empty($driver['has_jumper_cables'])) {
                $missing[] = "Cabluri Curent";
                $summary['jumper_cables']++;
            }

            if (($eqConfig['spare_wheel'] ?? 'vehicle') === 'driver' && isset($driver['has_spare_wheel']) && !$driver['has_spare_wheel']) {
                $missing[] = "Roată Rezervă";
                $summary['spare_wheels']++;
            }

            if (($eqConfig['medical_kit'] ?? 'vehicle') === 'driver') {
                if (empty($driver['medical_kit_expiry']) || $driver['medical_kit_expiry'] < date('Y-m-d')) {
                    $status = empty($driver['medical_kit_expiry']) ? "Lipsă" : "Expirată (" . date('d.m.y', strtotime($driver['medical_kit_expiry'])) . ")";
                    $missing[] = "Trusă Medicală ($status)";
                    $summary['med_kits']++;
                }
            }

            if (($eqConfig['extinguisher'] ?? 'vehicle') === 'driver') {
                if (empty($driver['extinguisher_expiry']) || $driver['extinguisher_expiry'] < date('Y-m-d')) {
                    $status = empty($driver['extinguisher_expiry']) ? "Lipsă" : "Expirat (" . date('d.m.y', strtotime($driver['extinguisher_expiry'])) . ")";
                    $missing[] = "Stingător ($status)";
                    $summary['extinguishers']++;
                }
            }

            if (!empty($missing)) {
                $shoppingList[] = [
                    'vehicle' => 'ȘOFER: ' . $driver['name'],
                    'missing' => $missing
                ];
            }
        }

        $this->render('tenant/reports/inventory_shopping_list', [
            'title' => 'Inventory Shopping List',
            'shoppingList' => $shoppingList,
            'summary' => $summary
        ]);
    }

    public function showEditTrip(int $id): void
    {
        $tripRepo = new \FleetLog\App\Repositories\TripRepository();
        $trip = $tripRepo->find($id);
        
        if (!$trip) {
            $this->redirect('/tenant/trips?error=trip_not_found');
        }

        $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
        $vehicles = $vehicleRepo->getActiveByTenant(Auth::tenantId());

        $this->render('tenant/trips/edit', [
            'title' => 'Edit Trip Log',
            'trip' => $trip,
            'vehicles' => $vehicles
        ]);
    }

    public function updateTrip(int $id): void
    {
        $tripRepo = new \FleetLog\App\Repositories\TripRepository();
        $trip = $tripRepo->find($id);
        
        if (!$trip) {
            $this->redirect('/tenant/trips?error=trip_not_found');
        }

        $data = [
            'start_km' => (int)$_POST['start_km'],
            'end_km' => $_POST['end_km'] !== '' ? (int)$_POST['end_km'] : null,
            'start_time' => $_POST['start_time'],
            'end_time' => $_POST['end_time'] !== '' ? $_POST['end_time'] : null,
            'type' => $_POST['type'],
            'notes' => $_POST['notes'],
            'status' => $_POST['status']
        ];

        if ($tripRepo->update($id, $data)) {
            // Odometer Sync: If this was the latest trip for the vehicle, update vehicle's current_odometer
            if ($data['status'] === 'closed' && $data['end_km'] !== null) {
                $latest = $tripRepo->getLatestTripForVehicle($trip['vehicle_id']);
                if ($latest && (int)$latest['id'] === $id) {
                    $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
                    $vehicleRepo->updateOdometer($trip['vehicle_id'], $data['end_km']);
                }
            }
            $this->redirect('/tenant/trips?success=updated');
        } else {
            $this->redirect('/tenant/trips?error=update_failed');
        }
    }

    public function closeTripAdmin(int $id): void
    {
        $tripRepo = new \FleetLog\App\Repositories\TripRepository();
        $trip = $tripRepo->find($id);
        
        if (!$trip || $trip['status'] !== 'open') {
            $this->redirect('/tenant/trips?error=not_open');
        }

        $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
        $vehicle = $vehicleRepo->find($trip['vehicle_id']);
        
        // Use vehicle's current odometer as default close KM
        $endKm = $vehicle['current_odometer'];
        $endTime = date('Y-m-d H:i:s');

        if ($tripRepo->closeTrip($id, $endKm, $endTime, "Closed by Admin")) {
            $this->redirect('/tenant/trips?success=closed');
        } else {
            $this->redirect('/tenant/trips?error=close_failed');
        }
    }

    public function showEditFueling(int $id): void
    {
        $fuelingRepo = new \FleetLog\App\Repositories\FuelingRepository();
        $fueling = $fuelingRepo->find($id);
        
        if (!$fueling) {
            $this->redirect('/tenant/fuelings?error=fueling_not_found');
        }

        $this->render('tenant/fuelings/edit', [
            'title' => 'Edit Fueling Record',
            'fueling' => $fueling
        ]);
    }

    public function updateFueling(int $id): void
    {
        $fuelingRepo = new \FleetLog\App\Repositories\FuelingRepository();
        $fueling = $fuelingRepo->find($id);
        
        if (!$fueling) {
            $this->redirect('/tenant/fuelings?error=fueling_not_found');
        }

        $data = [
            'liters' => (float)$_POST['liters'],
            'total_price' => (float)$_POST['total_price'],
            'odometer' => (int)$_POST['odometer'],
            'is_full' => isset($_POST['is_full']) ? 1 : 0,
            'created_at' => $_POST['created_at']
        ];

        if ($fuelingRepo->update($id, $data)) {
            // Odometer Sync: If this was the latest fueling
            $latest = $fuelingRepo->getLatestForVehicle($fueling['vehicle_id']);
            if ($latest && (int)$latest['id'] === $id) {
                $vehicleRepo = new \FleetLog\App\Repositories\VehicleRepository();
                $vehicleRepo->updateOdometer($fueling['vehicle_id'], $data['odometer']);
            }
            $this->redirect('/tenant/fuelings?success=updated');
        } else {
            $this->redirect('/tenant/fuelings?error=update_failed');
        }
    }
}
