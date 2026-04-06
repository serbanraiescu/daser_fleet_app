<?php
require_once __DIR__ . '/../fleetlog/core/Autoloader.php';
require_once __DIR__ . '/../fleetlog/core/EnvLoader.php';
\FleetLog\Core\Autoloader::register();
\FleetLog\Core\EnvLoader::load(__DIR__ . '/../fleetlog/.env');

use FleetLog\Core\MigrationRunner;

try {
    $runner = new MigrationRunner();
    $runner->run();
    echo "SUCCESS: Migrations executed.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
