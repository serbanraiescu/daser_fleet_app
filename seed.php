<?php

require_once __DIR__ . '/fleetlog/core/Autoloader.php';
require_once __DIR__ . '/fleetlog/core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\DB;

Autoloader::register();
EnvLoader::load(__DIR__ . '/fleetlog/.env');

echo "Seeding Database...\n";

try {
    // 1. Create Tenant
    DB::query("INSERT INTO tenants (name, cui, status) VALUES (?, ?, ?)", ['Daser Design', 'RO123456', 'active']);
    $tenantId = DB::lastInsertId();

    // 2. Create Super Admin
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    DB::query("INSERT INTO users (tenant_id, name, email, password, role) VALUES (?, ?, ?, ?, ?)", 
        [null, 'Super Admin', 'admin@daser.ro', $password, 'super_admin']
    );

    // 3. Create Tenant Admin
    DB::query("INSERT INTO users (tenant_id, name, email, password, role) VALUES (?, ?, ?, ?, ?)", 
        [$tenantId, 'Manager Daser', 'manager@daser.ro', $password, 'tenant_admin']
    );

    // 4. Create Driver
    DB::query("INSERT INTO users (tenant_id, name, email, password, role) VALUES (?, ?, ?, ?, ?)", 
        [$tenantId, 'Driver One', 'driver1@daser.ro', $password, 'driver']
    );

    echo "Seeding completed successfully.\n";
} catch (Exception $e) {
    echo "Seeding failed: " . $e->getMessage() . "\n";
}
