<?php
/**
 * Cron Job: Check Vehicle Expirations for SMS Alerts
 * Run daily: 0 8 * * * php /path/to/fleetlog/cron/sms_expiry_check.php
 */

require_once __DIR__ . '/../core/Autoloader.php';
require_once __DIR__ . '/../core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\SMSService;

// Initialize
Autoloader::register();
EnvLoader::load(__DIR__ . '/../.env');

// Set time limit
set_time_limit(300); 

echo "[" . date('Y-m-d H:i:s') . "] Starting SMS Expiry Scan...\n";

try {
    list($enqueued, $skipped) = SMSService::processExpiryAlerts();
    echo "[" . date('Y-m-d H:i:s') . "] Scan complete. Enqueued $enqueued new SMS alerts.\n";
    if (!empty($skipped)) {
        echo "[" . date('Y-m-d H:i:s') . "] Warning: Tenants without phone number: " . implode(', ', $skipped) . "\n";
    }
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] CRITICAL ERROR: " . $e->getMessage() . "\n";
}
