<?php

require_once __DIR__ . '/../core/Autoloader.php';
require_once __DIR__ . '/../core/EnvLoader.php';

use FleetLog\Core\Autoloader;
use FleetLog\Core\EnvLoader;
use FleetLog\Core\ExpirationService;

Autoloader::register();
EnvLoader::load(__DIR__ . '/../.env');

echo "Checking for expiring vehicle documentation...\n";
$count = ExpirationService::runEmailExpiryChecks();
echo "Done. Enqueued $count alerts.\n";
