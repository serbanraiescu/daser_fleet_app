<?php
require_once __DIR__ . '/../fleetlog/core/Autoloader.php';
require_once __DIR__ . '/../fleetlog/core/EnvLoader.php';
\FleetLog\Core\Autoloader::register();
\FleetLog\Core\EnvLoader::load(__DIR__ . '/../fleetlog/.env');

try {
    $res = \FleetLog\Core\DB::fetchAll("PRAGMA table_info(tenants)");
    foreach ($res as $row) {
        echo "{$row['name']} ({$row['type']})\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
