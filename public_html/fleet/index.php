<?php

require_once __DIR__ . '/../../fleetlog/core/Autoloader.php';
require_once __DIR__ . '/../../fleetlog/core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\Router;
use FleetLog\Core\MigrationRunner;

// Register Autoloader
Autoloader::register();

// Load Environment Variables
EnvLoader::load(__DIR__ . '/../../fleetlog/.env');

// Auto-run Migrations (Simple implementation for V1)
if (getenv('APP_ENV') === 'local' || !file_exists(__DIR__ . '/../../fleetlog/storage/installed.lock')) {
    $migrationRunner = new MigrationRunner();
    $migrationRunner->run();
    if (getenv('APP_ENV') !== 'local') {
        file_put_contents(__DIR__ . '/../../fleetlog/storage/installed.lock', date('Y-m-d H:i:s'));
    }
}

// Initialize Router
$router = new Router();

// Load Routes
require_once __DIR__ . '/../../fleetlog/config/routes.php';

// Dispatch
$router->dispatch();
