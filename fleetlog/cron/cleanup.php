<?php

require_once __DIR__ . '/../core/Autoloader.php';
require_once __DIR__ . '/../core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\CronService;

Autoloader::register();
EnvLoader::load(__DIR__ . '/../.env');

echo "Starting photo cleanup...\n";
$count = CronService::handlePhotoCleanup();
echo "Cleanup finished. Deleted $count old photos.\n";
