<?php

/**
 * FleetLog Web Installer
 * This script runs migrations and seeds the database via browser.
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/fleetlog/core/Autoloader.php';
require_once __DIR__ . '/fleetlog/core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\DB;
use FleetLog\Core\MigrationRunner;

Autoloader::register();
EnvLoader::load(__DIR__ . '/fleetlog/.env');

echo "<h1>FleetLog Installer</h1>";

try {
    echo "<h3>1. Running Migrations...</h3>";
    $migrationRunner = new MigrationRunner();
    $migrationRunner->run();
    echo "<p style='color:green'>Done!</p>";

    echo "<h3>2. Seeding Initial Data...</h3>";
    
    // Check if super admin already exists
    $admin = DB::fetch("SELECT id FROM users WHERE role = 'super_admin' LIMIT 1");
    if (!$admin) {
        // Create Tenant
        DB::query("INSERT INTO tenants (name, cui, status) VALUES (?, ?, ?)", ['Daser Design', 'RO123456', 'active']);
        $tenantId = DB::lastInsertId();

        // Create Super Admin
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        DB::query("INSERT INTO users (tenant_id, name, email, password, role) VALUES (?, ?, ?, ?, ?)", 
            [null, 'Super Admin', 'admin@daser.ro', $password, 'super_admin']
        );

        echo "<p style='color:green'>Seeding finished! Credentials: <b>admin@daser.ro / admin123</b></p>";
    } else {
        echo "<p style='color:orange'>Database already seeded.</p>";
    }

    echo "<h3>3. Locking Installation...</h3>";
    if (!is_dir(__DIR__ . '/fleetlog/storage')) {
        mkdir(__DIR__ . '/fleetlog/storage', 0755, true);
    }
    file_put_contents(__DIR__ . '/fleetlog/storage/installed.lock', date('Y-m-d H:i:s'));
    echo "<p style='color:green'>Installation locked. You can now use the app.</p>";

    echo "<hr><a href='/'>Go to Dashboard</a>";
    echo "<br><br><b style='color:red'>RECOMANDARE: Șterge acest fișier (install.php) după ce ai terminat!</b>";

} catch (\Exception $e) {
    echo "<h2 style='color:red'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Verifică setările din <b>fleetlog/.env</b></p>";
}
