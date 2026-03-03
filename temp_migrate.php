<?php
require_once __DIR__ . '/fleetlog/core/Autoloader.php';
require_once __DIR__ . '/fleetlog/core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\MigrationRunner;

Autoloader::register();
EnvLoader::load(__DIR__ . '/fleetlog/.env');

try {
    echo "Running migrations...\n";
    $runner = new MigrationRunner();
    $runner->run();
    echo "Done.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
unlink(__FILE__);
