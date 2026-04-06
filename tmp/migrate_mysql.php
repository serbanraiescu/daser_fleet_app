<?php
require_once __DIR__ . '/../fleetlog/core/Autoloader.php';
require_once __DIR__ . '/../fleetlog/core/EnvLoader.php';
\FleetLog\Core\Autoloader::register();
\FleetLog\Core\EnvLoader::load(__DIR__ . '/../fleetlog/.env');

use FleetLog\Core\DB;

try {
    DB::query("ALTER TABLE tenants ADD COLUMN daily_report_enabled TINYINT(1) DEFAULT 0");
    DB::query("ALTER TABLE tenants ADD COLUMN daily_report_last_sent DATE DEFAULT NULL");
    echo "SUCCESS: MySQL Migration Completed.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
