<?php
require_once __DIR__ . '/fleetlog/core/Autoloader.php';
require_once __DIR__ . '/fleetlog/core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\DB;

Autoloader::register();
EnvLoader::load(__DIR__ . '/fleetlog/.env');

if (php_sapi_name() !== 'cli' && !isset($_GET['confirm'])) {
    die("Use CLI or add ?confirm=1 to run.");
}

echo "Starting Seed Process...\n";

try {
    // 1. Create Demo Tenant
    $tenantName = "Demo Logistics SRL";
    $tenantEmail = "contact@demo-fleet.ro";
    
    $existing = DB::fetch("SELECT id FROM tenants WHERE email = ?", [$tenantEmail]);
    if ($existing) {
        $tenantId = $existing['id'];
        echo "Updating existing Demo Tenant (ID: $tenantId)...\n";
    } else {
        DB::query("INSERT INTO tenants (name, email, status) VALUES (?, ?, 'active')", [$tenantName, $tenantEmail]);
        $tenantId = DB::lastInsertId();
        echo "Created Demo Tenant (ID: $tenantId).\n";
    }

    // 2. Create Demo Admin
    $adminEmail = "demo@fleetlog.ro";
    $adminPass = password_hash('demo123', PASSWORD_BCRYPT);
    
    $existingAdmin = DB::fetch("SELECT id FROM users WHERE email = ?", [$adminEmail]);
    if ($existingAdmin) {
        DB::query("UPDATE users SET password = ?, tenant_id = ?, role = 'tenant_admin' WHERE id = ?", [$adminPass, $tenantId, $existingAdmin['id']]);
        $adminId = $existingAdmin['id'];
    } else {
        DB::query("INSERT INTO users (tenant_id, name, email, password, role, active) VALUES (?, 'Admin Demo', ?, ?, 'tenant_admin', 1)", [
            $tenantId, $adminEmail, $adminPass
        ]);
        $adminId = DB::lastInsertId();
    }
    echo "Demo Admin: $adminEmail / demo123\n";

    // 3. Create Drivers
    $drivers = [
        ['name' => 'Ion Popescu', 'email' => 'ion@demo.ro'],
        ['name' => 'Vasile Ionescu', 'email' => 'vasile@demo.ro'],
        ['name' => 'Andrei Georgescu', 'email' => 'andrei@demo.ro']
    ];
    $driverIds = [];
    foreach ($drivers as $d) {
        $exist = DB::fetch("SELECT id FROM users WHERE email = ?", [$d['email']]);
        if (!$exist) {
            DB::query("INSERT INTO users (tenant_id, name, email, password, role, active, cnp, id_expiry, license_expiry) VALUES (?, ?, ?, 'driver123', 'driver', 1, '1800101123456', '2028-10-10', '2030-05-05')", [
                $tenantId, $d['name'], $d['email']
            ]);
            $driverIds[] = DB::lastInsertId();
        } else {
            $driverIds[] = $exist['id'];
        }
    }

    // 4. Create Vehicles
    $vehicles = [
        ['plate' => 'B 101 DEM', 'make' => 'Dacia', 'model' => 'Logan', 'km' => 45000, 'service' => 50000, 'rca' => '2026-05-15'],
        ['plate' => 'B 202 DEM', 'make' => 'Ford', 'model' => 'Transit', 'km' => 120500, 'service' => 120000, 'rca' => '2026-03-10'], // Due
        ['plate' => 'B 303 DEM', 'make' => 'Volkswagen', 'model' => 'Caddy', 'km' => 88000, 'service' => 90000, 'rca' => '2026-04-20']
    ];
    
    foreach ($vehicles as $v) {
        $exist = DB::fetch("SELECT id FROM vehicles WHERE license_plate = ?", [$v['plate']]);
        if (!$exist) {
            DB::query("INSERT INTO vehicles (tenant_id, license_plate, make, model, current_km, next_service_km, expiry_rca, expiry_itp, expiry_rovigneta, status) VALUES (?, ?, ?, ?, ?, ?, ?, '2026-08-01', '2026-12-01', 'active')", [
                $tenantId, $v['plate'], $v['make'], $v['model'], $v['km'], $v['service'], $v['rca']
            ]);
            $vId = DB::lastInsertId();
        } else {
            $vId = $exist['id'];
        }

        // Mock some trips for reports
        for ($i = 0; $i < 5; $i++) {
            $start = rand(10, 100);
            $dId = $driverIds[array_rand($driverIds)];
            DB::query("INSERT INTO trips (tenant_id, vehicle_id, driver_id, start_km, end_km, start_time, end_time, status) VALUES (?, ?, ?, ?, ?, NOW() - INTERVAL ? DAY, NOW() - INTERVAL ? DAY + INTERVAL 2 HOUR, 'closed')", [
                $tenantId, $vId, $dId, $v['km'] - ($i * 150) - $start, $v['km'] - ($i * 150), $i + 1, $i + 1
            ]);
        }

        // Mock fuelings
        DB::query("INSERT INTO fuelings (tenant_id, vehicle_id, user_id, odometer, liters, total_price) VALUES (?, ?, ?, ?, ?, ?)", [
            $tenantId, $vId, $driverIds[0], $v['km'] - 200, rand(40, 60), rand(300, 450)
        ]);
    }

    echo "Seed completed successfully.\n";

} catch (\Exception $e) {
    echo "Seed Error: " . $e->getMessage() . "\n";
}
