<?php

require_once __DIR__ . '/../core/Autoloader.php';
require_once __DIR__ . '/../core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\CronService;

Autoloader::register();
EnvLoader::load(__DIR__ . '/../.env');

echo "Sending weekly reports...\n";
if (CronService::handleWeeklyReport()) {
    echo "Report queued successfully.\n";
} else {
    echo "No report sent (Check REPORTS_TO_EMAIL in .env).\n";
}
