<?php

/**
 * FleetLog Root Bootstrapper
 * This file allows the app to run directly from the subdomain root.
 */

// Enable error reporting for debugging V1 deployment
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/fleetlog/core/Autoloader.php';
require_once __DIR__ . '/fleetlog/core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\Router;
use FleetLog\Core\MigrationRunner;

// Register Autoloader
Autoloader::register();

// Load Environment Variables
EnvLoader::load(__DIR__ . '/fleetlog/.env');

// Auto-run Migrations
if (getenv('APP_ENV') === 'local' || !file_exists(__DIR__ . '/fleetlog/storage/installed.lock')) {
    $migrationRunner = new MigrationRunner();
    $migrationRunner->run();
    if (getenv('APP_ENV') !== 'local') {
        if (!is_dir(__DIR__ . '/fleetlog/storage')) {
            mkdir(__DIR__ . '/fleetlog/storage', 0755, true);
        }
        file_put_contents(__DIR__ . '/fleetlog/storage/installed.lock', date('Y-m-d H:i:s'));
    }
}

// Initialize Router
$router = new Router();

// Load Routes
require_once __DIR__ . '/fleetlog/config/routes.php';

// Dispatch
try {
    $router->dispatch();
} catch (\Exception $e) {
    echo "<h1>Application Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    if (getenv('APP_DEBUG') === 'true') {
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
}
