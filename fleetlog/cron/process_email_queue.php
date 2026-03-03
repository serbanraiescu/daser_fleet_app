<?php
/**
 * Cron Job: Process Email Queue
 * Run every minute: * * * * * php /path/to/fleetlog/cron/process_email_queue.php
 */

require_once __DIR__ . '/../core/Autoloader.php';
require_once __DIR__ . '/../core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\EmailService;

// Initialize
Autoloader::register();
EnvLoader::load(__DIR__ . '/../.env');

// Set time limit for processing
set_time_limit(50); 

echo "[" . date('Y-m-d H:i:s') . "] Starting Email Queue processing...\n";

try {
    $processed = EmailService::processQueue(5);
    echo "[" . date('Y-m-d H:i:s') . "] Successfully processed $processed emails.\n";
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] Error processing queue: " . $e->getMessage() . "\n";
}
