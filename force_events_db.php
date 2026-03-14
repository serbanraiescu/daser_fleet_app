<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Forcing Vehicle Events Table Creation...</h1>";
echo "<pre>";

$appRoot = __DIR__ . '/fleetlog';
require_once $appRoot . '/core/Autoloader.php';
require_once $appRoot . '/core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\DB;

Autoloader::register();
EnvLoader::load($appRoot . '/.env');

try {
    $migration = require $appRoot . '/migrations/043_create_vehicle_events_tables.php';
    echo "Running migration code...\n";
    $migration->up();
    
    // Also ensure it's in the migrations table so it doesn't try to run again normally
    DB::query("INSERT IGNORE INTO migrations (migration) VALUES (?)", ['043_create_vehicle_events_tables.php']);
    
    echo "\n<strong style='color:green;'>SUCCESS! vehicle_events tables created.</strong>";
} catch (Exception $e) {
    echo "\n<strong style='color:red;'>Error:</strong> " . $e->getMessage();
}

echo "</pre>";
echo "<br><a href='/tenant/vehicle-events'>Go to Timeline</a>";
