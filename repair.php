<?php
/**
 * REPAIR SCRIPT - Force Run Migrations
 * Access this via URL to fix missing tables.
 */

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/fleetlog/core/Autoloader.php';
require_once __DIR__ . '/fleetlog/core/EnvLoader.php';

// Register Autoloader
\FleetLog\Core\Autoloader::register();

// Load Environment Variables
\FleetLog\Core\EnvLoader::load(__DIR__ . '/fleetlog/.env');

echo "<h1>System Repair: Migrations</h1>";

try {
    $migrationRunner = new \FleetLog\Core\MigrationRunner();
    
    echo "Checking database connection...<br>";
    \FleetLog\Core\DB::query("SELECT 1");
    echo "Database Connected!<br><br>";

    echo "Running Migrations...<br>";
    $migrationRunner->run();
    
    echo "<br><b style='color:green'>Done! Migrations completed.</b><br>";
    echo "<p>Please delete this file (<code>repair.php</code>) for security.</p>";
    echo "<a href='/admin/dashboard'>Go to Dashboard</a>";

} catch (\Throwable $e) {
    echo "<b style='color:red'>Error:</b> " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
