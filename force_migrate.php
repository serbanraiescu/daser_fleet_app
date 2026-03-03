<?php
require_once __DIR__ . '/fleetlog/core/Autoloader.php';
require_once __DIR__ . '/fleetlog/core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\MigrationRunner;
use FleetLog\Core\DB;

Autoloader::register();
EnvLoader::load(__DIR__ . '/fleetlog/.env');

try {
    echo "Updating existing migrations to force 026 again...\n";
    // Force rerunning migration 026 by removing it from migrations table if exists
    DB::query("DELETE FROM migrations WHERE migration = '026_create_email_delivery_system'");
    
    echo "Running migrations...\n";
    $runner = new MigrationRunner();
    $runner->run();
    echo "Done.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
unlink(__FILE__);
