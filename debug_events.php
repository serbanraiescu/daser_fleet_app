<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug Vehicle Events</h1>";
echo "<pre>";

$appRoot = __DIR__ . '/fleetlog';
require_once $appRoot . '/core/Autoloader.php';
require_once $appRoot . '/core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\DB;
use FleetLog\Core\Auth;

Autoloader::register();
EnvLoader::load($appRoot . '/.env');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "Current Session Tenant ID: " . Auth::tenantId() . "\n";
echo "Current Session Role: " . Auth::role() . "\n";

try {
    echo "\n--- Last 20 Events in DB ---\n";
    $events = DB::fetchAll("SELECT * FROM vehicle_events ORDER BY id DESC LIMIT 20");
    print_r($events);

    echo "\n--- Last 10 Photos ---\n";
    $photos = DB::fetchAll("SELECT * FROM vehicle_event_photos ORDER BY id DESC LIMIT 10");
    print_r($photos);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

echo "</pre>";
